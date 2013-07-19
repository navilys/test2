<?php
/**
 * class.pmSLA.php
 *  
 */
G::LoadClass('plugin');

class pmSLAClass extends PMPlugin
{
    public function __construct()
    {
        set_include_path( PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path() );
        if (!defined('PATH_PM_SLA')) {
            define('PATH_PM_SLA ', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
        }
    }

    public static function createTables ()
    {
        $sqlFile = PATH_PM_SLA . 'data'. PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql';
        $handle = fopen( $sqlFile, "r"); // Open file form read.
        $line = '';
        if ($handle) {
            // Loop til end of file.
            while (!feof($handle)) {
                // Read a line.
                $buffer = fgets($handle, 4096);
                // Check for valid lines
                if ($buffer[0] != "#" && strlen(trim($buffer)) >0) {
                    $buffer = trim( $buffer);
                    $line .= $buffer;
                    if ( $buffer[strlen( $buffer)-1] == ';' ) {
                        $con = Propel::getConnection('workflow');
                        $stmt = $con->createStatement();
                        $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
                        $line = '';
                    }
                }
            }
            fclose($handle); // Close the file.
        }
    }

    public static function saveLogSla($sSource, $sType, $sDescription)
    {
        try {
            G::verifyPath(PATH_PM_SLA . 'log' . PATH_SEP, true);

            if ($sType == 'action') {
                $oFile = @fopen(PATH_PM_SLA . 'log' . PATH_SEP . 'cron.log', 'a+');
            } else {
                if (file_exists(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log')) {
                    unlink(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log');
                }
                $oFile = @fopen(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log', 'a+');
            }

            @fwrite($oFile, date('Y-m-d H:i:s') . ' (' . $sSource . ') ' . $sDescription . "\n");
            @fclose($oFile);
        } catch (Exception $oError) {
        }
    }

    public static function minutesToHours ($minutes)
    {
        return ($minutes/60);
    }

    public static function hoursToMinutes ($minutes)
    {
        return ($minutes*60);
    }

    public static function calculateDueDate($dateBase, $timeDuration, $timeDurationType, $proUid = null)
    {
        G::loadClass ( 'dates' );
        $dates = new dates();

        $dueDate = $dates->calculateDate($dateBase, $timeDuration, $timeDurationType, 1, null, $proUid);
        return $dueDate['DUE_DATE'];
    }

    public static function insertAppSla($dataAppSla)
    {
        require_once 'classes/model/AppSla.php';
        $appSla = new AppSla();
        $res = $appSla->create($dataAppSla);
    }

    public static function updateAppSla($dataAppSla)
    {
        require_once 'classes/model/AppSla.php';
        $appSla = new AppSla();
        $res = $appSla->update($dataAppSla);
    }

    public static function createXml($columns)
    {
        require_once 'classes/class.outputDocumentSla.php';

        $dataColumns  = array();

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $dynaForm = $doc->createElement('dynaForm');

        $attribute = $doc->createAttribute('menu');
        $attribute->value = 'reportExcel';
        $dynaForm->appendChild($attribute);

        $attribute = $doc->createAttribute('width');
        $attribute->value = '200';
        $dynaForm->appendChild($attribute);

        $attribute = $doc->createAttribute('rowsPerPage');
        $attribute->value = '99999999999';
        $dynaForm->appendChild($attribute);

        $attribute = $doc->createAttribute('enabletemplate');
        $attribute->value = '0';
        $dynaForm->appendChild($attribute);

        foreach ($columns as $key => $value) {
            $dataColumns[$value->DATAINDEX] = 'char';

            $newRow = $doc->createElement($value->DATAINDEX);
            $attribute = $doc->createAttribute('size');
            $attribute->value = '300';
            $newRow->appendChild($attribute);

            $attribute = $doc->createAttribute('colWidth');
            $attribute->value = '300';
            $newRow->appendChild($attribute);

            $attribute = $doc->createAttribute('type');
            $attribute->value = 'text';
            $newRow->appendChild($attribute);

            $newEn = $doc->createElement('en');
            $newEn->appendChild($doc->createTextNode($value->HEADER));

            $newRow->appendChild($newEn);
            $dynaForm->appendChild($newRow);
        }

        $doc->appendChild($dynaForm);

        if (file_exists(PATH_PM_SLA . 'reportExcel.xml')) {
            unlink(PATH_PM_SLA . 'reportExcel.xml');
        }

        $doc->save(PATH_PM_SLA . 'reportExcel.xml');

        return $dataColumns;
    }

    public static function numberToLabelTime ($timeMinutes)
    {
        $timeSeg = (float)$timeMinutes * 60;

        $timeHrs = floor($timeSeg / 3600);
        $timeMin = floor(($timeSeg - ($timeHrs * 3600)) / 60);

        return number_format($timeHrs, false) . ' H, ' . $timeMin . ' min';
    }
}

