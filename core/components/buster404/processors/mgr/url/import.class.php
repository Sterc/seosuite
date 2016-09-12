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
            return $this->failure($this->modx->lexicon('site.buster404.error.emptyfile'));
        }
        // Check for file extension
        $extension = pathinfo($_FILES['file']['name'])['extension'];
        if (!in_array($extension, $allowedExtensions)) {
            return $this->failure($this->modx->lexicon('site.buster404.error.extension_notallowed'));
        }

        if ($extension == 'csv') {
            $data = $this->parseCsvFile($file);
        } else {
            $data = $this->parseExcelFile($file);
        }

        foreach ($data as $key => $row) {
            $urlObject = $this->modx->getObject($this->classKey, array(
                'url' => $row['url']
            ));
            if (!$urlObject) {
                $urlObject = $this->modx->newObject($this->classKey, array(
                    'url' => $row['url']
                ));
                $this->created++;
            } else {
                $this->updated++;
            }
            $lexicon->save();
        }
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
     * @return array the contents from the sheet as php array
     */
    public function parseExcelFile($filename, $sheetIndex = 0)
    {
        require_once $this->buster404->options['corePath'] . 'PHPExcel/Classes/PHPExcel/IOFactory.php';
        try {
            $filetype = PHPExcel_IOFactory::identify($filename);
            $objReader = PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($filename);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($filename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $sheetData = $objPHPExcel->getSheet($sheetIndex)->toArray(null, true, true, true);

        return $sheetData;
    }

    public function cleanup()
    {
        return $this->success('Updated: '.$this->updated.' - Created: '.$this->created, array('success' => true));
    }
}
return 'Buster404UrlImportProcessor';
