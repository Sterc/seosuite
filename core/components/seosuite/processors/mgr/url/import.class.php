<?php
/**
 * Import lexicons key <> value pairs from csv file
 *
 * @package core
 * @subpackage processors
 */
class SeoSuiteUrlImportProcessor extends modObjectProcessor
{
    public $classKey = 'SeoSuiteUrl';
    public $languageTopics = ['seosuite:default'];
    public $created = 0;
    public $updated = 0;
    public $allowedExtensions = ['csv', 'xls', 'xlsx'];

    public function process()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('seosuite.import.start'));
        $this->modx->setLogLevel(modX::LOG_LEVEL_DEBUG);

        $file     = $this->getProperty('file');
        $siteUrls = false;
        if ($this->getProperty('match_site_url')) {
            $siteUrls = $this->modx->seosuite->getSiteUrls();
        }

        /* Check if file field is set. */
        if (empty($file)) {
            return $this->failure($this->modx->lexicon('seosuite.error.emptyfile'));
        }

        /* Check for file extension. */
        $extension = pathinfo($_FILES['file']['name'])['extension'];
        if (!in_array($extension, $this->allowedExtensions)) {
            $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('seosuite.error.extension_notallowed'));
            $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
            return $this->failure($this->modx->lexicon('seosuite.error.extension_notallowed'));
        }

        if ($extension === 'csv') {
            $data = $this->parseCsvFile($file);
        } else {
            $data = $this->parseExcelFile($file);
        }

        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $row) {
                /* If first column does not exist, continue to next row. */
                if (!isset($row[0])) {
                    continue;
                }

                $url = $row[0];

                /* If not a valid url, continue to next. */
                if (substr($url, 0, 4) !== 'http') {
                    continue;
                }

                $q = $this->modx->newQuery($this->classKey);
                $q->where(['url' => $url]);
                $q->prepare();

                $urlResult = $this->modx->query($q->toSql());
                $urlObject = $urlResult->fetch(PDO::FETCH_ASSOC);
                if ($urlObject !== false) {
                    $this->modx->log(modX::LOG_LEVEL_INFO, 'Skip: ' . $url);
                    $this->updated++;

                    continue;
                }

                $suggestions      = '';
                $redirect_to      = 0;
                $solved           = 0;
                $redirect_handler = 0;
                $findSuggestions  = $this->modx->seosuite->findRedirectSuggestions($url, $siteUrls);
                if (count($findSuggestions)) {
                    if (count($findSuggestions) === 1) {
                        $redirect_to = $findSuggestions[0];
                        $solved      = 1;

                        if (!$this->modx->seosuite->checkSeoTab()) {
                            $redirect_handler = 1;
                        } else {
                            $this->modx->seosuite->addSeoTabRedirect($url, $findSuggestions[0]);
                        }
                    }

                    $suggestions = json_encode(array_values($findSuggestions));
                }

                $this->modx->exec(
                    "INSERT INTO {$this->modx->getTableName($this->classKey)}
                    SET {$this->modx->escape('url')} = {$this->modx->quote($url)},
                        {$this->modx->escape('suggestions')} = {$this->modx->quote($suggestions)},
                        {$this->modx->escape('redirect_to')} = {$this->modx->quote($redirect_to)},
                        {$this->modx->escape('redirect_handler')} = {$this->modx->quote($redirect_handler)},
                        {$this->modx->escape('solved')} = {$this->modx->quote($solved)}"
                );

                $this->modx->log(modX::LOG_LEVEL_INFO, 'Add: ' . $url);
                $this->created++;
            }
        }

        $this->modx->log(modX::LOG_LEVEL_INFO, 'Import successfully completed.');
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->created.' Urls added.');
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->updated.' Urls skipped (existing urls).');
        $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');

        return $this->success('Updated: ' . $this->updated . ' - Created: ' . $this->created, ['success' => true]);
    }

    /**
     * Parse a csv file into an array
     *
     * @param array     $file   The file object
     * @return array    $data   the contents from the csv as php array
     */
    public function parseCsvFile($file)
    {
        ini_set('auto_detect_line_endings', true);

        $delimiter = $this->getCsvFileDelimiter($file['tmp_name']);
        $data      = [];
        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $data[] = $row;
            }

            fclose($handle);
        }

        return $data;
    }

    /**
     * Read an excel file with the PHPExcel library
     * https://github.com/PHPOffice/PHPExcel
     *
     * @param array     $file           The file object
     * @param int       $sheetIndex     Index number of the sheet from the excel file; 0 = 1st sheet, 1 = 2nd sheet etc.
     * @return array    $data           the contents from the sheet as php array
     */
    public function parseExcelFile($file, $sheetIndex = 0)
    {
        /* Check if the ZipArchive extension is installed (needed for PHPExcel). */
        if (!class_exists('ZipArchive')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('seosuite.error.ziparchive_notinstalled'));
            $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');

            return $this->failure($this->modx->lexicon('seosuite.error.ziparchive_notinstalled'));
        }

        require_once $this->modx->seosuite->options['corePath'] . 'PHPExcel/Classes/PHPExcel/IOFactory.php';

        $data = [];
        try {
            $filetype    = PHPExcel_IOFactory::identify($file['tmp_name']);
            $objReader   = PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file['tmp_name']);
        } catch (Exception $e) {
            $message = 'Error loading file "' . pathinfo($file['tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage();

            $this->modx->log(modX::LOG_LEVEL_INFO, $message);

            return $this->failure($message);
        }

        $sheet = $objPHPExcel->getSheet($sheetIndex);
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            foreach ($row->getCellIterator() as $key => $cell) {
                $data[$rowIndex][] = $cell->getCalculatedValue();
            }
        }

        return $data;
    }


    private function getCsvFileDelimiter($file, $checkLines = 2)
    {
        $file       = new SplFileObject($file);
        $delimiters = [',', '\t', ';'];
        $results    = [];

        $i = 0;
        while ($file->valid() && $i <= $checkLines) {
            $line = $file->fgets();
            if (is_array($delimiters) || is_object($delimiters)) {
                foreach ($delimiters as $delimiter) {
                    $regExp = '/[' . $delimiter . ']/';
                    $fields = preg_split($regExp, $line);
                    if (count($fields) > 1) {
                        if (!empty($results[$delimiter])) {
                            $results[$delimiter]++;
                        } else {
                            $results[$delimiter] = 1;
                        }
                    }
                }
            }

            $i++;
        }

        if (count($results)) {
            $results = array_keys($results, max($results));
            $output = $results[0];
        } else {
            $output = ';';
        }

        return $output;
    }

    public function cleanup()
    {
        return $this->success('Updated: '.$this->updated.' - Created: '.$this->created, ['success' => true]);
    }
}

return 'SeoSuiteUrlImportProcessor';
