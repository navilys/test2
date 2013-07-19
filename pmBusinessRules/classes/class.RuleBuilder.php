<?php

if (!defined('PATH_PM_BUSINESS_RULES')) {
    define('PATH_PM_BUSINESS_RULES', PATH_CORE . 'plugins' . PATH_SEP . 'pmBusinessRules' . PATH_SEP );
}

if (!defined('PATH_FILES_BUSINESS_RULES')) {
    define('PATH_FILES_BUSINESS_RULES', PATH_DATA . 'pmBusinessRules' . PATH_SEP );
}

require_once PATH_PM_BUSINESS_RULES . 'classes' . PATH_SEP . 'PHPExcel' . PATH_SEP . 'IOFactory.php';

class RuleBuilder
{
    public $objPHPExcel = '';
    public $myLogs = array();
    public $variables = array();
    public $cellsMerge = array();
    public $fileRead = '';
    public $extensionGlobal = 'G@';
    public $maxRow = 50;
    public $maxCol = 50;

    public function __construct ($nameFile = '')
    {
        $filePath = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'xls' . PATH_SEP;
        if ($nameFile == '') {
            if (isset($_FILES['form']['name']['FILENAME'])) {

                $ext = pathinfo(basename($_FILES['form']['name']['FILENAME'], PATHINFO_EXTENSION));

                if( $ext['extension'] != 'xls' && $ext['extension'] != 'xlsx' ) {
                    die('{"success":"failed","mjs":"File is invalid, only accepts .xls and .xlsx"}');
                }

                $fileTemp = $_FILES['form']['tmp_name']['FILENAME'];
                $fileName = $_FILES['form']['name']['FILENAME'];

                G::verifyPath($filePath, true);
                G::uploadFile($fileTemp, $filePath, $fileName, '7777');

                $this->fileRead = $filePath . $fileName;


            } else {
                die('{"success":"failed","mjs":"No exist File"}');
            }
        } else {
            $this->fileRead = $filePath . $nameFile;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filePath), RecursiveIteratorIterator::SELF_FIRST); 
        foreach($iterator as $item) { 
            chmod($item, '0777'); 
        }

        $this->objPHPExcel = PHPExcel_IOFactory::load($this->fileRead);
        $this->setCellMerge();
        $this->setVariables();
    }

    public function processExcelFile()
    {
        $excelFile = $this->fileRead;

        if (!file_exists($excelFile)) {
            throw( new Exception("Please get DecisionHello.xls file.") );
        }

        $this->saveLog("Load from Excel2007 file");
        $definitions = Array();
        try {

            $this->saveLog("looking for Main worksheet");
            $sheetName = 'Main';
            $sheet = $this->objPHPExcel->getSheetByName($sheetName);
            if ($sheet == null ) {
                throw new Exception("Main worksheet does not exists");
            }

            $this->saveLog("looking for Definitions in Main worksheet");
            $cells = $sheet->toArray();

            //looking for work Define *
            $countRow = 0;
            foreach ($cells as $krow => $row ) {
                if ($countRow >= $this->maxRow) { break; }
                $countCol = 0;
                foreach ($row as $kcol => $cell ) {
                    if ($countCol >= $this->maxCol) { break; }
                    if (preg_match('/^Define .*/',$cell, $matches)) {
                        //now check the next cell for the definition function
                        $defFunction = $cells[$krow][$kcol+1];
                        if (preg_match('/^:= (.*)\(\)/',$defFunction, $matches)) {
                            $definitions[] = $matches[1];
                        }
                    }
                    $countCol++;
                }
                $countRow++;
            }
            if (count($definitions) == 0) {
                throw new Exception("there are no Definition in Main worksheet");
            }
            //print_r ($definitions);
            /*
            foreach (glob(dirname(__FILE__). '/*.pmrl') as $value) {
                if (file_exists($value)) {
                    unlink( $value );
                }
            }
            */
        
            foreach ($definitions as $definition) {
                $this->saveLog("processing worksheet $definition");
                $sheet = $this->objPHPExcel->getSheetByName($definition);
                if ($sheet == null ) {
                    throw new Exception("$definition worksheet does not exists");
                }

                //looking for work DT $definition *
                $cells = $sheet->toArray();
                $countRow = 0;
                $pmrlOutput = '';
                foreach ($cells as $krow => $row) {
                    if ($countRow >= $this->maxRow) { break; }
                    $countCol = 0;
                    foreach ($row as $kcol => $cell ) {
                        if ($countCol >= $this->maxCol) { break; }
                        $pattern = "/^DT ($definition)/";
                        $patternMulti = "/^DTM ($definition)/";
                        if (preg_match($pattern, $cell, $matches) || preg_match($patternMulti, $cell, $matches)) {
                            $headerCell = substr(trim($cell), 0, 3) ;
                            $hit = ($headerCell == 'DTM') ? 'multi' : 'simple';
                            $pmrlOutput .= $this->processDT($cells, $kcol, $krow, $matches[1], $definition, $hit);
                        }
                        $countCol++;
                    }   
                }

                $filePath = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP;
                $nameRuleTest = $filePath . trim($definition) . '.pmrl';
                
                file_put_contents($nameRuleTest, $pmrlOutput);
                
                $pmrlOutput = '';
                $this->saveLog("<span style='color: green'>Create ruletest $nameRuleTest </span>");
            }

            

        } catch ( Exception $e) {
            print "<hr>Error : " . $e->getMessage() . "<br>";
        }

    }


    public function processDT($cells, $baseCol, $baseRow, $dtName, $definition, $hit)
    {
        //counting number of valid condition and conclusion columns
        $col = 0;
        $row = $baseRow+1;
        $col = $baseCol;
        $conditions  = 0;
        $conclusions = 0;

        $header = $cells[$row][$col];
        while ($header == 'Condition') {
            $conditions ++;
            $col+=2;
            $header = $cells[$row][$col];
        }
        while ($header == 'Conclusion') {
            $conclusions ++;
            $col+=2;
            if (isset($cells[$row][$col])) {
                $header = $cells[$row][$col];
            } else {
                $header = '';
            }
        }
        if (!$conditions) {
            throw( new Exception("No conditions found for Decision Table '$dtName'") );
        }
        if (!$conclusions) {
            throw( new Exception("No conclusions found for Decision Table '$dtName'") );
        }
        $this->saveLog( "There are $conditions conditions and $conclusions conclusions in '$dtName'");

        //counting how many rules in this DT
        $rules = 0;
        $row = $baseRow+3;
        $col = $baseCol;
        $ruleRow = trim($cells[$row][$col]);
        
        while ($ruleRow != '') {
            $rules ++;
            $row ++;
            $ruleRow = isset($cells[$row][$col]) ? trim($cells[$row][$col]) : '';
            if ($ruleRow == '') {
                for ($conConclu=0; $conConclu < $conclusions; $conConclu++) { 
                    $iniColConclu = (2*$conditions)+($baseCol+($conConclu*2));
                    if (isset($cells[$row][$iniColConclu])){
                        $ruleRow = $cells[$row][$iniColConclu]; 
                        if ($ruleRow != '') {
                            $conConclu = $conclusions;
                        }
                    }
                }
            }
        }
        $this->saveLog( "There are $rules rules in '$dtName'");
        
        $output = '';
        $row = $baseRow+3;  
        $col = $baseCol;
        for ($ruleId = 0; $ruleId < $rules; $ruleId++) {
            $output .= "rule \"{$dtName}_" . ($ruleId +1) . "\" " . $hit . "-hit\n";
            $output .= "    if\n";
            $condOutput = '';

            for ($colId = 0; $colId < $conditions; $colId ++) {
                $varName = $cells[$row-1][$col+$colId*2] ;
                //todo: change this using a "glosary"
                //$varName = str_replace(' ', '_', $varName);
                $temp = $this->verifyCellMerge($row+$ruleId, $col+$colId*2, $definition);
                if ($temp) {
                    $cells[$row+$ruleId][$col+$colId*2] = $temp;
                }
                $temp = $this->verifyCellMerge($row+$ruleId, $col+$colId*2+1, $definition);
                if ($temp) {
                    $cells[$row+$ruleId][$col+$colId*2+1] = $temp;
                }

                $operator = $cells[$row+$ruleId][$col+$colId*2] ;
                $varValue = $cells[$row+$ruleId][$col+$colId*2+1] ;
                if ($operator != '') {
                    $newOutput = $this->setCondition($operator, $varValue, $varName);
                    $condOutput .= ($condOutput == '' ? '' : "    AND\n" ) . $newOutput;
                }
            }
            $condOutput .= ($condOutput == '') ? '        (1 == 1)' . "\n" : '';
            $output .= $condOutput;
            $output .= "    then\n";
            $concOutput = '';
            for ($colId = 0; $colId < $conclusions; $colId ++) {
                $auxCont = '';
                $varName = $cells[$row-1][$col+$colId*2 + $conditions*2] ;
                //todo: change this using a "glosary"
                //  $varName = str_replace(' ', '_', $varName);
                $temp = $this->verifyCellMerge($row+$ruleId, $col+$colId*2, $definition);
                if ($temp) {
                    $cells[$row+$ruleId][$col+$colId*2] = $temp;
                }
                $temp = $this->verifyCellMerge($row+$ruleId, $col+$colId*2+1, $definition);
                if ($temp) {
                    $cells[$row+$ruleId][$col+$colId*2+1] = $temp;
                }

                $operator = $cells[$row+$ruleId][$col+$colId*2 + $conditions*2] ;
                $varValue = $cells[$row+$ruleId][$col+$colId*2+1 + $conditions*2] ;
                $auxCont = $this->setConclusion($operator, $varValue, $varName);
                if ($auxCont != '' && $concOutput != '') {
                    $concOutput .= $auxCont;
                } else if ($auxCont != '' && $concOutput == '') {
                    $concOutput .= $auxCont;
                }
            }
            $output .= $concOutput;
            $output .= "end\n\n";
        }

        return $output;
    }

    public function setVariables ()
    {
        ///// Open the file
        $sheet = $this->objPHPExcel->getSheetByName('Glossary');
        if ($sheet == null ) {
            $sheet = $this->objPHPExcel->getSheetByName('glossary');
            if ($sheet == null ) {
                throw new Exception("Main worksheet does not exists");
            }
        }
        $cells = $sheet->toArray();
        ///// End open

        ///// Searh variables
        $iniRow = '';
        $iniCol = '';
        $flagFinish = false;
        $countRow = 0;
        $countCol = 0;
        foreach ($cells as $krow => $row ) {
            if ($countRow >= $this->maxRow) { break; }
            $countCol = 0;
            foreach ($row as $kcol => $cell ) {
                if ($countCol >= $this->maxCol) { break; }
                if (preg_match('/^Glossary .*/', $cell, $matches)) {
                    $defFunction = $cells[$krow][$kcol+1];
                    $iniRow = $krow+2;
                    $iniCol = $kcol;
                    break 2;
                }
                $countCol++;
            }
            $countRow++;
        }

        if ($iniRow == '' && $iniCol == '') {
            throw new Exception("variables does not exists");
        }
        ///// End search

        ///// Set names and labes of variables
        $typeVariable = '';
        while ($flagFinish == false) {
            $labelVar  = '';
            $nameVar   = '';
            $typeVar   = 'string';
            $formatVar = 'Y-m-d H:i:s';

            // Set type Variable (GLOBAL - APP_DATA)
            if (isset($cells[$iniRow][$iniCol+1]) && $typeVariable != $cells[$iniRow][$iniCol+1] && $cells[$iniRow][$iniCol+1] != '') {
                $typeVariable = $cells[$iniRow][$iniCol+1];
                $typeVariable = strtoupper(trim($cells[$iniRow][$iniCol+1]));
            }

            // Set label and name Variable
            if ( isset($cells[$iniRow][$iniCol]) && isset($cells[$iniRow][$iniCol+2])) {
                $labelVar = trim($cells[$iniRow][$iniCol]);
                $temp = $this->verifyCellMerge($iniRow, $iniCol+2, 'Glossary');
                if ($temp) {
                    $cells[$iniRow][$iniCol+2] = $temp;
                }
                $nameVar  = trim($cells[$iniRow][$iniCol+2]);
            }

            // Set type Variable
            if ( isset($cells[$iniRow][$iniCol+3]) ) {
                $temp = $this->verifyCellMerge($iniRow, $iniCol+3, 'Glossary');
                if ($temp) {
                    $cells[$iniRow][$iniCol+3] = $temp;
                }
                $typeVar   = strtolower(trim($cells[$iniRow][$iniCol+3]));
            }

            // Set format Variable
            if ( isset($cells[$iniRow][$iniCol+4]) ) {
                $temp = $this->verifyCellMerge($iniRow, $iniCol+4, 'Glossary');
                if ($temp) {
                    $cells[$iniRow][$iniCol+4] = $temp;
                }
                $formatVar = trim($cells[$iniRow][$iniCol+4]);
            }

            if ($labelVar == '' || $nameVar == '') {
                $flagFinish = true;
            } else {
                $this->variables[$typeVariable]['labels'][] = $labelVar;
                $this->variables[$typeVariable]['names'][]  = $nameVar;
                $this->variables[$typeVariable]['types'][]  = $typeVar;
                $this->variables[$typeVariable]['formats'][]  = $formatVar;
            }
            $iniRow++;
        }
        ///// End set
    }

    public function assignVariable($var)
    {
        $varLabel  = trim($var);
        $varName   = '';
        $varType   = '';
        $extension = '';
        foreach ($this->variables as $key => $dataVariables) {
            $extension = ($key == 'APP_DATA') ? '@@' : $this->extensionGlobal;
            foreach ($dataVariables['labels'] as $key => $value) {
                if ($varLabel == $value) {
                    $varName = $extension.$dataVariables['names'][$key];
                    $varType = $dataVariables['types'][$key];
                    //if ($varType == 'string') {
                        //$varName = '"' . $varName . '"';
                    //}
                    if ($varType == 'date') {
                        //date convert format
                        //$varFormat = $dataVariables['formats'][$key];
                        $varName = 'strtotime("' . $varName . '")';
                    }
                    break 2;
                }
            }
        }
        $return['var']  = $varName;
        $return['type'] = $varType;
        return $return;
    }

    public function setCondition($operator, $value, $varLabel)
    {
        $resp = $this->assignVariable($varLabel);
        $var  = $resp['var'];
        $typeEvaluation  = $resp['type'];

        if ($var == '') {
            throw( new Exception("The variable: $varLabel not exists in Glossary.") );
        }
        
        $resp = $this->assignVariable($value);
        $valueNew = $resp['var'];
        if ($valueNew == '') {
            //if ($typeEvaluation == 'string' || $typeEvaluation == 'date') {
                //$value = '"' . $value . '"';
            //}
        } else {
            $value = $valueNew;
        }
        if ($typeEvaluation == 'date') {
            $value = 'strtotime(' . $value . ')';
        }

        $operator = trim(strtolower($operator));

        $resp = '';
        switch ($operator) {
            case 'is':
            case '=':
                $resp .= '        (' . $var . ' == ' . $value . ")\n";
                break;

            case 'within':
                $value = trim($value);
                $values = explode('-', $value);
                /*if ($typeEvaluation == 'string') {
                    $values[0] = str_replace('"', '', $values[0]);
                    $values[1] = str_replace('"', '', $values[1]);
                    $values[0] = '"' . $values[0] . '"';
                    $values[1] = '"' . $values[1] . '"';
                }*/
                if (count($values) != 2) {
                    throw new Exception("Error condition : " . $operator . ' => ' . $value , 1);
                }
                $resp .= '        ((' . $var . ' >= ' . $values[0] . ")\n";
                $resp .= "    AND\n";
                $resp .= '        (' . $var . ' <= ' . $values[1] . "))\n";
                break;
            case 'not within':
                $value = trim($value);
                $values = explode('-', $value);
                /*if ($typeEvaluation == 'string') {
                    $values[0] = str_replace('"', '', $values[0]);
                    $values[1] = str_replace('"', '', $values[1]);
                    $values[0] = '"' . $values[0] . '"';
                    $values[1] = '"' . $values[1] . '"';
                }*/
                if (count($values) != 2) {
                    throw new Exception("Error condition : " . $operator . ' => ' . $value , 1);
                }

                $resp .= '        (!((' . $var . ' >= ' . $values[0] . ")\n";
                $resp .= "    AND\n";
                $resp .= '        (' . $var . ' <= ' . $values[1] . ")))\n";
                break;
            case 'includes':
                $value = trim($value);
                $values = explode(',', $value);
                foreach ($values as &$val) {
                    $val = trim($val);
                }
                $resp .= '        (in_array(' . $var . ', array("' . implode('","', $values) . '"))'. ")\n";
                break;
            case 'not includes':
                $value = trim($value);
                $values = explode(',', $value);
                foreach ($values as &$val) {
                    $val = trim($val);
                }
                $resp .= '        (!(in_array(' . $var . ', array("' . implode('","', $values) . '"))'. "))\n";
                break;
            case 'match':
                $resp .= '        (preg_match("' . $value . '", ' . $var . ')'. ")\n";
                break;
            case 'not match':
                $resp .= '        (!(preg_match("' . $value . '", ' . $var . ')'. "))\n";
                break;
            case '>=':
            case '<=':
            case '>':
            case '<':
                $value = trim($value);
                $resp .= '        (' . $var . ' ' . $operator . ' ' . $value . ")\n";
                break;
            case 'not is':
            case '<>':
            case '!=':
                $value = trim($value);
                $resp .= '        (' . $var . ' != ' . $value . ")\n";
                break;
            default:
                throw new Exception("Error name condition : " . $operator, 1);
                break;
        }
        return $resp;
    }

    public function setConclusion($operator, $value, $varLabel)
    {
        $resp = $this->assignVariable($varLabel);
        $var  = $resp['var'];
        $typeEvaluation  = $resp['type'];

        if ($var == '') {
            throw( new Exception("The variable: $varLabel not exists in Glossary.") );
        }

        $resp = $this->assignVariable($value);
        $valueNew = $resp['var'];
        if ($valueNew == '') {
            //if ($typeEvaluation == 'string' || $typeEvaluation == 'date') {
                $value = '"' . $value . '"';
            //}
        } else {
            $value = $valueNew;
        }
        if ($typeEvaluation == 'date') {
            $value = 'strtotime(' . $value . ')';
        }

        $operator = trim(strtolower($operator));       
        $resp = '';
        switch ($operator) {
            case 'is':
            case '=':
                $resp .= '        ' . $var . ' = ' . $value . ";\n";
                break;
            default:
                $resp = '';
                //throw new Exception("Error name conclusion : " . $operator, 1);
                break;
        }
        return $resp;
    }

    public function pr ($var)
    {
        print ("<pre>") ;
        print_r( $var );
        print ("</pre>") ;
    }

    public function saveLog ($text)
    {
        $this->myLogs[] = 'Time : ' . date('Y-m-d H:i:s') . ' - ' . $text;
    }

    public function showLog ()
    {
        $data = $this->myLogs;
        print '<table width="100%" cellspacing="0" cellpadding="0" border="1">';
        foreach ($data as $value) {
            print '<tr><td>' . $value . '</td></tr>';
        }
        print '</table>';
    }

    public function printContentFile ()
    {
        $cells = $this->objPHPExcel->getActiveSheet()->toArray();
        $sheetCount = $this->objPHPExcel->getSheetCount();
        $sheetNames = $this->objPHPExcel->getSheetNames();

        foreach ($sheetNames as $sheetName) {
            $sheet = $this->objPHPExcel->getSheetByName($sheetName);
            print "<h1>$sheetName</h1>";
            print "<table border=1>";
            $countRow = 0;
            $countCol = 0;
            foreach ($sheet->getRowIterator() as $row) {
                if ($countRow >= $this->maxRow) { break; }
                $countCol = 0;
                print "<tr>";
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    if ($countCol >= $this->maxCol) { break; }
                    print "<td>";
                    print $cell->getValue();
                    print "</td>";
                    $countCol++;
                }
                print "</tr>";
                $countRow++;
            }
            print "</table>";
        }
    }

    public function setCellMerge ()
    {
        $cells = $this->objPHPExcel->getActiveSheet()->toArray();
        $sheetCount = $this->objPHPExcel->getSheetCount();
        $sheetNames = $this->objPHPExcel->getSheetNames();

        foreach ($sheetNames as $sheetName) {
            $sheet = $this->objPHPExcel->getSheetByName($sheetName);
            $cellsMerge = $sheet->getMergeCells();
            if (count($cellsMerge)) {
                $this->cellsMerge[$sheetName] = array();
            }
            $cells = $sheet->toArray();
            foreach ($cellsMerge as $value) {
                $flagRange = false;
                $pRange = strtoupper($value);

                // Is it a cell range or a single cell?
                $rangeA = '';
                $rangeB = '';
                if (strpos($pRange, ':') === false) {
                    $rangeA = $pRange;
                    $rangeB = $pRange;
                } else {
                    list($rangeA, $rangeB) = explode(':', $pRange);
                }

                // Calculate range outer borders
                $rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
                $rangeEnd   = PHPExcel_Cell::coordinateFromString($rangeB);

                // Translate column into index
                $rangeStart[0]  = PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
                $rangeEnd[0]    = PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;

                // Make sure we can loop upwards on rows and columns
                if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
                    $tmp = $rangeStart;
                    $rangeStart = $rangeEnd;
                    $rangeEnd = $tmp;
                }

                // Loop through cells and apply styles
                $nameMerge = '';
                for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        $nowRow = $row-1;
                        $nowCol = $col;
                        if ($flagRange == false) {
                            $nameMerge = trim($cells[$nowRow][$nowCol]);
                            $nameMerge = str_replace(' ', '__', $nameMerge);
                            $this->cellsMerge[$sheetName][$nameMerge] = array();
                            $flagRange = true;
                        }
                        $this->cellsMerge[$sheetName][$nameMerge][] = ($row-1).','.$col;
                        //$this->pr('Fila -> ' .  . ' | Col -> '. $col);
                        //$this->pr($cells[$nowRow][$nowCol]);
                    }
                }
                //$this->pr('-------------------------');
            }
        }
        //$this->pr($this->cellsMerge);
    }

    public function verifyCellMerge ($row, $col, $sheetName)
    {
        $pos = $row.','.$col;
        foreach ($this->cellsMerge[$sheetName] as $key => $value) {
            if (in_array($pos, $value)) {
                $res = str_replace('__', ' ', $key);
                return $res;
            }
        }
        return false;
    }
}