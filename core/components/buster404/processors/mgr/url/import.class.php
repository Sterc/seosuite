<?php
/**
 * Import lexicons key <> value pairs from csv file
 *
 * @package core
 * @subpackage processors
 */
class Buster404UrlImportProcessor extends modObjectProcessor
{
    public $classKey = 'Buster404Url';
    public $languageTopics = array('buster404:default');
    public $created = 0;
    public $updated = 0;
    public $allowedExtensions = array('csv');

    public function process()
    {
        $this->modx->setLogLevel(modX::LOG_LEVEL_DEBUG);
        $file = $this->getProperty('file');
        
        // Check if file field is set
        if (empty($file)) {
            return $this->failure($this->modx->lexicon('buster404.error.emptyfile'));
        }
        // Check for file extension
        $extension = pathinfo($_FILES['file']['name'])['extension'];
        if (!in_array($extension, $this->allowedExtensions)) {
            return $this->failure($this->modx->lexicon('buster404.error.extension_notallowed'));
        }

        if ($extension == 'csv') {
            $data = $this->parseCsvFile($file);
        } else {
            $data = $this->parseExcelFile($file['tmp_name']);
        }

        foreach ($data as $key => $row) {
            // If first column does not exist, continue to next row
            if (!isset($row[0])) {
                continue;
            }

            $url = $row[0];

            // If not a valid url, continue to next
            if (substr($url, 0, 4) != 'http') {
                continue;
            }

            $q = $this->modx->newQuery($this->classKey);
            $q->where(array('url' => $url));
            $urlObject = $this->modx->query($q->toSql());
            if (!is_object($urlObject)) {
                $suggestions = '';
                $redirect_to = 0;
                $solved = 0;
                $findSuggestions = $this->modx->buster404->findRedirectSuggestions($url);
                if (count($findSuggestions)) {
                    if (count($findSuggestions) == 1) {
                        // Try to add the redirect to Seotab
                        $seotabRedirect = $this->modx->buster404->addSeoTabRedirect($url, $findSuggestions[0]);
                        if ($seotabRedirect) {
                            $redirect_to = $findSuggestions[0];
                            $solved = 1;
                        }
                    }
                    $suggestions = json_encode(array_values($findSuggestions));
                }
                $this->modx->exec(
                    "INSERT INTO {$this->modx->getTableName($this->classKey)}
                    SET {$this->modx->escape('url')} = {$this->modx->quote($url)},
                        {$this->modx->escape('suggestions')} = {$this->modx->quote($suggestions)},
                        {$this->modx->escape('redirect_to')} = {$this->modx->quote($redirect_to)},
                        {$this->modx->escape('solved')} = {$this->modx->quote($solved)}"
                );

                $this->created++;
            } else {
                $this->updated++;
            }
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, '=====================');
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Import successfully completed.');
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->created.' Urls added.');
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->updated.' Urls skipped (existing urls).');
        $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
        return $this->success('Updated: '.$this->updated.' - Created: '.$this->created, array('success' => true));
    }

    /**
     * Parse a csv file into an array
     *
     * @param string    $file   The file object
     * @return array    $data   the contents from the csv as php array
     */
    public function parseCsvFile($file)
    {
        ini_set('auto_detect_line_endings', true);
        $data = [];
        if (($handle = fopen($file['tmp_name'], "r")) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
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
     * @param string    $filename       The path to the excel file
     * @param int       $sheetIndex     Index number of the sheet from the excel file; 0 = first sheet, 1 = second sheet etc.
     * @return array    $data           the contents from the sheet as php array
     */
    public function parseExcelFile($filename, $sheetIndex = 0)
    {
        require_once $this->modx->buster404->options['corePath'] . 'PHPExcel/Classes/PHPExcel/IOFactory.php';
        $data = [];
        try {
            $filetype = PHPExcel_IOFactory::identify($filename);
            $objReader = PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($filename);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($filename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet($sheetIndex);

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            foreach ($row->getCellIterator() as $key => $cell) {
                $data[$rowIndex][] = $cell->getCalculatedValue();
            }
        }

        return $data;
    }

    public function cleanup()
    {
        return $this->success('Updated: '.$this->updated.' - Created: '.$this->created, array('success' => true));
    }
}
return 'Buster404UrlImportProcessor';
