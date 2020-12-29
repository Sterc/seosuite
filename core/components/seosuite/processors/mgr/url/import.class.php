<?php
require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';

/**
 * Import lexicons key <> value pairs from csv file.
 *
 * @package core
 * @subpackage processors
 */
class SeoSuiteUrlImportProcessor extends modObjectProcessor
{
    public $classKey       = 'SeoSuiteUrl';
    public $languageTopics = ['seosuite:default'];

    protected $totals = [
        'total'     => 0,
        'redirects' => 0,
        'not_found' => 0
    ];

    public $allowedExtensions = ['csv', 'xls', 'xlsx'];

    /**
     * Process import file.
     * @return array|mixed|string
     */
    public function process()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('seosuite.import.start'));
        $this->modx->setLogLevel(modX::LOG_LEVEL_DEBUG);

        $file     = $this->getProperty('file');
        $siteUrls = false;
        if ($this->getProperty('match_context')) {
            $siteUrls = $this->getSiteUrls();
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
            $this->totals['total'] = count($data);

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

                $params = [
                    'url' => $this->modx->seosuite->formatUrl($url)
                ];

                $context = false;
                if ($siteUrls) {
                    foreach ($siteUrls as $siteUrl => $ctx) {
                        if (strpos($url, $siteUrl) !== false) {
                            $context = $ctx;
                        }
                    }
                }

                if ($context) {
                    $params['context_key'] = $context;
                }

                $seoSuiteUrl = $this->modx->getObject($this->classKey, $params);
                if (!$seoSuiteUrl) {
                    $seoSuiteUrl = $this->modx->newObject($this->classKey);
                    $seoSuiteUrl->fromArray($params);
                }

                /* Context is false or if set, then it contains the context_key. */
                $findSuggestions = $seoSuiteUrl->getRedirectSuggestions($context === false ? false : true);
                $this->modx->log(modX::LOG_LEVEL_INFO, 'Found suggestions: ' . count($findSuggestions));
                if (count($findSuggestions) === 1) {
                    $redirectToResourceId = array_key_first($findSuggestions);

                    $params = [
                        'context_key'   => $context ? $context : '',
                        'resource_id'   => $redirectToResourceId,
                        'old_url'       => $seoSuiteUrl->get('url'),
                        'new_url'       => $redirectToResourceId,
                        'redirect_type' => 'HTTP/1.1 301 Moved Permanently'
                    ];

                        $seoSuiteRedirect = $this->modx->getObject('SeoSuiteRedirect', $params);
                    if (!$seoSuiteRedirect) {
                        $seoSuiteRedirect = $this->modx->newObject('SeoSuiteRedirect');

                        $seoSuiteRedirect->fromArray($params);
                        if ($seoSuiteRedirect->save()) {
                            $this->modx->log(modX::LOG_LEVEL_INFO, 'Added redirect for: ' . $url);
                        }
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_INFO, 'A redirect already exists for: ' . $url);
                    }

                    $this->totals['redirects']++;
                } else {
                    if (count($findSuggestions) > 1) {
                        $seoSuiteUrl->set('suggestions', json_encode($findSuggestions));
                    }

                    /* Add 404 URL if no suggestions where found. */
                    if ($seoSuiteUrl->save()) {
                        $this->modx->log(modX::LOG_LEVEL_INFO, 'Added 404 URL: ' . $url);

                        $this->totals['not_found']++;
                    }
                }
            }
        }

        $this->modx->log(modX::LOG_LEVEL_INFO, 'Import successfully completed.');
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Total rows: ' . $this->totals['total']);
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Total imported redirects: ' . $this->totals['redirects']);
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Total imported 404 URLs: ' . $this->totals['not_found']);
        $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');

        return $this->success('Redirects: ' . $this->totals['redirects'] . ' - 404 URLs: ' . $this->totals['not_found'], ['success' => true]);
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

            return $this->failure($this->modx->lexicon('seosuite.error.ziparchive_notinstalled'));
        }

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

    /**
     * @param $file
     * @param int $checkLines
     * @return mixed|string
     */
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
        return $this->success('Redirects: ' . $this->totals['redirects'].' - 404 URLs: ' . $this->totals['not_found'], ['success' => true]);
    }

    /**
     * Returns a list of all context site urls (if any).
     *
     * @return array
     */
    protected function getSiteUrls()
    {
        $urls = [];

        $query = $this->modx->newQuery('modContextSetting');
        $query->where([
            'key'            => 'site_url',
            'context_key:!=' => 'mgr'
        ]);

        $collection = $this->modx->getCollection('modContextSetting', $query);
        foreach ($collection as $item) {
            $siteurl = rtrim($item->get('value'), '/');
            $siteurl = str_replace(['https://', 'http://'], '', $siteurl);

            $urls[$siteurl] = $item->get('context_key');
        }

        return $urls;
    }
}

return 'SeoSuiteUrlImportProcessor';
