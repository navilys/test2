<?php

if (ini_get ( "pcre.backtrack_limit" ) < 1000000) {
  ini_set ( "pcre.backtrack_limit", 1000000 );
}
if (ini_get ( "memory_limit" ) < 256) {
  //ini_set ( 'memory_limit', '512M' );
  ini_set ( 'memory_limit', '-1' );  
}
@set_time_limit ( 100000 );

G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );
G::LoadClass ( 'ArrayPeer' );

set_include_path( PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path() );
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA ', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
}

require_once PATH_PM_SLA . 'class.pmSLA.php';
require_once 'classes/model/Sla.php';

$typeExport = $_REQUEST['TYPE_EXPORT'];
$columns = G::json_decode($_REQUEST['COLUMNS']);
if (is_string($columns)) {
    $columns = G::json_decode($columns);
}

// Data report
$oAppSla = new AppSla();

if (isset($_REQUEST['SLA_UID']) && $_REQUEST['SLA_UID'] != '- All -' && $_REQUEST['SLA_UID'] != 'ALL') {
    $oAppSla->setSlaUidRep($_REQUEST['SLA_UID']);
}
if (isset($_REQUEST['TYPE_DATE']) && $_REQUEST['TYPE_DATE'] != '- All -') {
    $oAppSla->setTypeDate($_REQUEST['TYPE_DATE']);
}
if (isset($_REQUEST['DATE_START']) && $_REQUEST['DATE_START'] != "") {
    $oAppSla->setDateStart($_REQUEST['DATE_START']);
}
if (isset($_REQUEST['DATE_END']) && $_REQUEST['DATE_END'] != "") {
    $oAppSla->setDateEnd($_REQUEST['DATE_END']);
}
if (isset($_REQUEST['TYPE_EXCEEDED']) && $_REQUEST['TYPE_EXCEEDED'] != "") {
    $oAppSla->setTypeExceeded($_REQUEST['TYPE_EXCEEDED']);
}
if (isset($_REQUEST['EXC_STATUS']) && $_REQUEST['EXC_STATUS'] != "ALL") {
    $oAppSla->setStatus($_REQUEST['EXC_STATUS']);
}
if (isset($_REQUEST['start']) && $_REQUEST['start'] != 0) {
    $oAppSla->setStart($_REQUEST['start']);
}
if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != 0) {
    $oAppSla->setLimit($_REQUEST['limit']);
}

switch ($_REQUEST['TYPE_REPORT']) {
    case 'firstReport':
        $data = $oAppSla->getReportFirstLevel();
        break;
    case 'secondReport':
        $data = $oAppSla->getReportAppSla();
        break;
    case 'thirdReport':
        $slaUid = $_REQUEST['SLA_UID'];
        $nAppNumber = $_REQUEST['APP_NUMBER'];
        $data = $oAppSla->loadDetailReportSel($slaUid, $nAppNumber);
        break;
}
// End data report

if ($typeExport == 'reportXls') {
    $dataReport   = array();
    $dataReport[] = pmSLAClass::createXml($columns);

    $dataExcel = array();
    $pageTable = 'paged-tableExcel';

    switch ($_REQUEST['TYPE_REPORT']) {
        case 'firstReport':
            foreach ($data as $value) {
                $value['SUM_DURATION'] = number_format($value['SUM_DURATION']) . ' Cases';
                $value['SUM_EXCEEDED'] = pmSLAClass::numberToLabelTime($value['SUM_EXCEEDED']);
                $value['AVG_SLA'] = pmSLAClass::numberToLabelTime($value['AVG_SLA']);
                $value['SUM_PEN_VALUE'] = number_format($value['SUM_PEN_VALUE'], 2) . ' ' . $value['SLA_PEN_VALUE_UNIT'];
                $dataReport[] = $value;
            }

            $aReportData['TITLE'] = 'SLA SUMMARY';
            break;
        case 'secondReport':
            foreach ($data as $value) {
                $value['APP_NUMBER'] = 'Cases # ' . number_format($value['APP_NUMBER']);
                $value['TOTAL_EXCEEDED'] = pmSLAClass::numberToLabelTime($value['TOTAL_EXCEEDED']);
                $value['APP_SLA_PEN_VALUE'] = number_format($value['APP_SLA_PEN_VALUE'], 2) . ' ' . $value['SLA_PEN_VALUE_UNIT'];
                $value['APP_SLA_STATUS'] = ($value['APP_SLA_STATUS'] == 'CLOSED') ? 'Closed' : 'In progress';
                $dataReport[] = $value;
            }

            $aReportData['TITLE'] = 'SLA : ' . $_REQUEST['SLA_NAME'];
            break;
        case 'thirdReport':
            foreach ($data as $value) {
                $value['VAL_DURATION'] = pmSLAClass::numberToLabelTime($value['VAL_DURATION']);
                $dataReport[] = $value;
            }

            $aReportData['TITLE'] = 'SLA CASE # ' . $_REQUEST['CAS'];

            $aReportData['SLA'] = $_REQUEST['SLA'];
            $aReportData['Process'] = $_REQUEST['PRO'];
            $aReportData['Type'] = $_REQUEST['TYP'];
            if ($_REQUEST['TYP'] != 'Entire Process') {
                $aReportData['TypeLabel'] = $_REQUEST['TYP'];
                $aReportData['TypeValue'] = $_REQUEST['TAS'];
            } else {
                $aReportData['TypeLabel'] = '';
                $aReportData['TypeValue'] = '';
            }

            $aReportData['Case'] = $_REQUEST['CAS'];
            $aReportData['Duration'] = $_REQUEST['DUR'];
            $aReportData['Exceeded'] = $_REQUEST['DUREXC'];
            $aReportData['Penalty'] = $_REQUEST['PEN'];
            $pageTable = 'paged-tableExcelThird';
            break;
    }

    $aReportData['GENREPORT'] = $_REQUEST['DAT_REP'];

    global $_DBArray;
    $_DBArray['data']  = $dataReport;
    $_SESSION['_DBArray'] = $_DBArray;
    $oCriteria = new Criteria('dbarray');
    $oCriteria->setDBArrayTable('data');
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('propeltable', 'pmSLA/' . $pageTable, 'pmSLA/reportExcel', $oCriteria, $aReportData);

    if (file_exists(PATH_PM_SLA . 'reportExcel.xml')) {
        unlink(PATH_PM_SLA . 'reportExcel.xml');
    }

    header('Content-type: application/vnd.ms-excel;');
    header("Content-Disposition: attachment; filename=reporteExcel.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    G::RenderPage('publish','raw');
} else {
    require_once 'classes/class.outputDocumentSla.php';
    $outputSla = new OutputDocumentSla();

    $html = '';
    $htmlInfo = '';
    $styleTd = 'style="height: 35px; font-size: 12px; text-align: center;"';
    
    $styleTd1 = 'style="height: 50px; font-size: 16px; color: #FFFFFF; background-color:#76848E; text-align: center;"';
    $htmlHeader = ' <p style="font-size: 10px; height: 10px; color:#76848E; text-align: right;">' . $_REQUEST['DAT_REP'] . '</p>';
    $htmlHeader .= '<table width="100%" aling="center" cellspacing="0" cellpadding="0" border="1">';
    $htmlHeader .= '<tr align="center" colspan="12">';
    $columnsView = array();
    foreach ($columns as $value) {
        $htmlHeader .= '<td ' . $styleTd1 . '>' . str_replace('(numeral)', '#', $value->HEADER) . '</td>';
        $columnsView[] = $value->DATAINDEX;
    }
    $htmlHeader .= '</tr>';
    switch ($_REQUEST['TYPE_REPORT']) {
        case 'firstReport':
            $title = 'SLA SUMMARY';
            foreach ($data as $value) {
                $html .= '<tr colspan="12">';
                $html .= '<td width = "20%" ' . $styleTd . '>' . $value['SLA_NAME'] . '</td>';
                $html .= '<td width = "20%" ' . $styleTd . '>' . number_format($value['SUM_DURATION']) . ' Cases' . '</td>';
                $html .= '<td width = "20%" ' . $styleTd . '>' . pmSLAClass::numberToLabelTime($value['SUM_EXCEEDED']) . '</td>';
                $html .= '<td width = "20%" ' . $styleTd . '>' . pmSLAClass::numberToLabelTime($value['AVG_SLA']) . '</td>';
                $html .= '<td width = "20%" ' . $styleTd . '>' . number_format($value['SUM_PEN_VALUE'], 2) . ' ' . $value['SLA_PEN_VALUE_UNIT'] . '</td>';
                $html .= '</tr>';
            }
            break;
        case 'secondReport':
            $title = 'SLA : ' . $_REQUEST['SLA_NAME'];
            foreach ($data as $value) {
                $status = ($value['APP_SLA_STATUS'] == 'CLOSED') ? 'Closed' : 'In progress';
                $html .= '<tr colspan="12">';

                if (in_array('SLA_NAME', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . $value['SLA_NAME'] . '</td>';
                }
                if (in_array('APP_NUMBER', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '> Cases # ' . number_format($value['APP_NUMBER']) . '</td>';
                }
                if (in_array('TOTAL_EXCEEDED', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . pmSLAClass::numberToLabelTime($value['TOTAL_EXCEEDED']) . '</td>';
                }
                if (in_array('APP_SLA_INIT_DATE', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . $value['APP_SLA_INIT_DATE'] . '</td>';
                }
                if (in_array('APP_SLA_DUE_DATE', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . $value['APP_SLA_DUE_DATE'] . '</td>';
                }
                if (in_array('APP_SLA_FINISH_DATE', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . $value['APP_SLA_FINISH_DATE'] . '</td>';
                }
                if (in_array('APP_SLA_PEN_VALUE', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . number_format($value['APP_SLA_PEN_VALUE'], 2) . ' ' . $value['SLA_PEN_VALUE_UNIT'] . '</td>';
                }
                if (in_array('APP_SLA_STATUS', $columnsView)) {
                    $html .= '<td width = "12%" ' . $styleTd . '>' . $status . '</td>';
                }

                $html .= '</tr>';
            }
            break;
        case 'thirdReport':
            $title = 'SLA CASE # ' . $_REQUEST['CAS'];
            foreach ($data as $value) {
                $html .= '<tr colspan="12">';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['TASK_NAME'] . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['USR_NAME'] . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['DEL_DELEGATE_DATE'] . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['DEL_INIT_DATE'] . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['DEL_FINISH_DATE'] . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . pmSLAClass::numberToLabelTime($value['VAL_DURATION']) . '</td>';
                $html .= '<td width = "14%" ' . $styleTd . '>' . $value['APP_TYPE'] . '</td>';
                $html .= '</tr>';
            }

            $styleTd = 'style="height: 15px; font-size: 14px; text-align: left;"';
            $htmlInfo .= '<table width="100%" aling="center" cellspacing="0" cellpadding="0" border="0">';
            $htmlInfo .= '<tr>';
            $htmlInfo .= '<td width = "20%" ' . $styleTd . '><b>SLA :</b></td>';
            $htmlInfo .= '<td width = "25%" ' . $styleTd . '>' . $_REQUEST['SLA'] . '</td>';
            $htmlInfo .= '<td width = "5%" ' . $styleTd . '/>';
            $htmlInfo .= '<td width = "20%" ' . $styleTd . '><b>Case # :</b></td>';
            $htmlInfo .= '<td width = "25%" ' . $styleTd . '>' . $_REQUEST['CAS'] . '</td>';
            $htmlInfo .= '</tr>';

            $htmlInfo .= '<tr>';
            $htmlInfo .= '<td ' . $styleTd . '><b>Process :</b></td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $_REQUEST['PRO'] . '</td>';
            $htmlInfo .= '<td ' . $styleTd . '/>';
            $htmlInfo .= '<td ' . $styleTd . '><b>Duration :</b></td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $_REQUEST['DUR'] . '</td>';
            $htmlInfo .= '</tr>';

            $htmlInfo .= '<tr>';
            $htmlInfo .= '<td ' . $styleTd . '><b>Type :</b></td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $_REQUEST['TYP'] . '</td>';
            $htmlInfo .= '<td ' . $styleTd . '/>';
            $htmlInfo .= '<td ' . $styleTd . '><b>Duration exceeded :</b></td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $_REQUEST['DUREXC'] . '</td>';
            $htmlInfo .= '</tr>';

            if ($_REQUEST['TYP'] != 'Entire Process') {
                $labelType = '<b>' . $_REQUEST['TYP'] .' :</b>';
                $valueType = $_REQUEST['TAS'];
            } else {
                $labelType = '';
                $valueType = '';
            }

            $htmlInfo .= '<tr>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $labelType . '</td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $valueType . '</td>';
            $htmlInfo .= '<td ' . $styleTd . '/>';
            $htmlInfo .= '<td ' . $styleTd . '><b>Penalty :</b></td>';
            $htmlInfo .= '<td ' . $styleTd . '>' . $_REQUEST['PEN'] . '</td>';
            $htmlInfo .= '</tr>';

            $htmlInfo .= '</table>';

            break;
    }

    $htmlTitle = ' <p style="font-size: 30px; height: 40px; color:#76848E; text-align: center;">' . $title . '</p>';
    
    
    
    $template = $htmlTitle . $htmlInfo . $htmlHeader . $html . '</table>';   
/*
    require_once PATH_PM_SLA . 'html2pdf/html2pdf.class.php';
    $html2pdf = new HTML2PDF('L','legal','en',true, 'UTF-8',array(10, 5, 5, 5));
    $output = $html2pdf ->WriteHTML($html);
    $html2pdf ->Output('Report Sla.pdf', 'D');
    die;
 */
    $aProperties = array();

    G::rm_dir(PATH_PM_SLA . 'files');
    $sPath = PATH_PM_SLA . 'files' . PATH_SEP;
    $sFilename = 'Report_' . date('Ymd_His');
    try {       
        $outputSla->generatePdf($template, $sPath, $sFilename, $aProperties);

        $len = filesize($sPath . $sFilename.'.pdf');
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename='.$sFilename.'.pdf');
        header('Content-Length: '.$len);
        readfile($sPath . $sFilename . '.pdf');
    } catch (Exception $e) {
        die('cochalo => ' . $e);
    }
}

