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
    public $allowedExtensions = array('csv','xls','xlsx');

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
            if (!isset($row[0])) {
                continue;
            }
            $url = $row[0];
            if (substr($url, 0, 4) != 'http') {
                continue;
            }
            $this->modx->log(modX::LOG_LEVEL_INFO, $url);
            continue;
            $urlObject = $this->modx->getObject($this->classKey, array(
                'url' => $url
            ));
            if (!$urlObject) {
                $urlObject = $this->modx->newObject($this->classKey, array(
                    'url' => $url
                ));
                $urlObject>save();
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
     *
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
