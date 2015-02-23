<?php
App::uses('Component', 'Controller');

class ExcelReaderComponent extends Component {

        protected $PHPExcelReader;
        protected $PHPExcelLoaded = false;
        private $dataArray;
        private $excel;

        public function initialize($controller)
        {
                parent::initialize($controller);
                App::import('Vendor', 'PHPExcel', array('file' => 'PhpExcel/PHPExcel.php'));
                if (!class_exists('PHPExcel')) {
                        throw new CakeException('Vendor class PHPExcel not found!');
                }
                $this->dataArray = array();
        }

        private function loadExcelFile($filename, $sheetName, $ignoreFormatting = true)
        {
                $this->PHPExcelReader = PHPExcel_IOFactory::createReaderForFile($filename);
                $this->PHPExcelLoaded = true;
                $this->PHPExcelReader->setReadDataOnly($ignoreFormatting);
                $this->PHPExcelReader->setLoadSheetsOnly($sheetName);
                $this->excel = $this->PHPExcelReader->load($filename);
        }
        
        public function readExcelSheet($filename, $sheetName, $formatData = false)
        {
                $sheetData = array();
                $this->loadExcelFile($filename, $sheetName, !($formatData));
                if($this->PHPExcelLoaded) {
                        $sheetData = $this->excel->getSheetByName($sheetName)->toArray(null,true,$formatData,true);
                }
                $this->dataArray = $sheetData;
                return $this->dataArray;
        }

        public function excel_array_search($needle, $haystack, &$searchResult = array())
        {
            $row = null;
            $cell = null;
            foreach($haystack as $rowKey=>$rowValue) {
                $row = $rowKey;
                foreach($rowValue as $cellKey=>$cellValue) {
                    if($needle === $cellValue) {
                        $cell = $cellKey;
                    }
                }
                if($cell) {
                    $searchResult = array($row, $cell);
                    return $searchResult;
                }
            }
            return false;
        }
}
