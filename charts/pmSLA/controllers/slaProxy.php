<?php

/**
 * Controller SLA
 *
 */
ini_set("memory_limit", "256M");

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());

if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP);
}

require_once 'classes/model/Sla.php';
require_once 'classes/model/Task.php';
$functionExec = '';
if (isset($_REQUEST['functionExecute'])) {
    $functionExec = $_REQUEST['functionExecute'];
}

switch ($functionExec) {
    case 'listSla':
        $oSla = new sla();
        if (isset($_REQUEST['query'])) {
            $aSla = $oSla->getListSla($_REQUEST['query']);
        } else {
            $aSla = $oSla->getListSla();
        }

        $start = (isset($_REQUEST['start'])) ? $_REQUEST['start'] : 0;
        $limit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : 20;

        echo G::json_encode(array('total' => count($aSla),'data' => array_splice($aSla,$start,$limit)));
        break;
    case 'processList':
        $oSla = new sla();
        $aProcess = $oSla->getProcessList();
        echo G::json_encode(array('data' => $aProcess, 'total' => count($aProcess)));
        break;
    case 'listTasks':
        $proUid = $_REQUEST['PRO_UID'];
        G::LoadClass('tasks');
        $oTasks = new Tasks();
        $aTaskSource = $oTasks->getAllTasks($proUid);
        $aTasks = array();
        foreach ($aTaskSource as $aTaskTarget) {
            if ($aTaskTarget['TAS_TYPE'] != 'SUBPROCESS') {
                $aTasks[] = array('TAS_UID' => $aTaskTarget['TAS_UID'], 'TAS_TITLE' => $aTaskTarget['TAS_TITLE']);
            }
        }
        echo G::json_encode(array('data' => $aTasks));
        break;
    case 'saveSla':

        if ($_REQUEST['VAL_NAME_SLA'] != '') {
            $oSla = new sla();

            $exist = $oSla->getSlaNameExist($_REQUEST['VAL_NAME_SLA']);
            if (!$exist) {
                $_REQUEST['VAL_ID_SLA'] = G::generateUniqueID();
                $aData['SLA_UID'] = $_REQUEST['VAL_ID_SLA'];
                $aData['PRO_UID'] = $_REQUEST['PRO_UID'];
                $aData['SLA_NAME'] = $_REQUEST['VAL_NAME_SLA'];
                $aData['SLA_DESCRIPTION'] = $_REQUEST['VAL_DESCRIPTION_SLA'];
                $aData['SLA_TYPE'] = $_REQUEST['VAL_TYPE_SLA_ID'];
                $aData['SLA_TAS_START'] = $_REQUEST['VAL_TASK_START_SLA_ID'];
                if ($_REQUEST['VAL_TYPE_SLA_ID'] == 'RANGE') {
                    $aData['SLA_TAS_END'] = $_REQUEST['VAL_TASK_END_SLA_ID'];
                }
                $aData['SLA_TIME_DURATION'] = $_REQUEST['VAL_DURATION_NUMBER_SLA'];
                $aData['SLA_TIME_DURATION_MODE'] = $_REQUEST['VAL_DURATION_TYPE_SLA'];
                $aData['SLA_CONDITIONS'] = $_REQUEST['VAL_CONDITION_SLA'];
                if ($_REQUEST['VAL_PENALITY'] == 'true') {
                    $aData['SLA_PEN_ENABLED'] = '1';
                } else {
                    $aData['SLA_PEN_ENABLED'] = '0';
                }
                $aData['SLA_PEN_TIME'] = $_REQUEST['VAL_PENALITY_TIME_NUMBER_SLA'];
                $aData['SLA_PEN_VALUE'] = $_REQUEST['VAL_PENALITY_VALUE_NUMBER_SLA'];

                $aData['SLA_PEN_TIME_MODE'] = $_REQUEST['VAL_PENALITY_VALUE_TYPE_SLA'];
                $aData['SLA_PEN_VALUE_UNIT'] = $_REQUEST['VAL_PENALITY_UNIT_TYPE_SLA'];
                $aData['SLA_STATUS'] = $_REQUEST['VAL_STATUS_SLA'];

                $aProcess = $oSla->create($aData);
                echo G::json_encode(array('success' => true));
            } else {
                echo G::json_encode(array('success' => false, 'msg' => 'SLA Name Exist'));
            }
        } else {
            echo G::json_encode(array('success' => false, 'msg' => 'SLA Name Empty'));
        }
        break;
    case 'existSla':
        $slaUid = $_REQUEST['SLA_UID'];
        $oSla = new sla();
        if ($oSla->slaExists($slaUid)) {
            echo "true";
        } else {
            echo "false";
        }

        break;
    case 'updateSla':
        $oSla = new sla();

        $aData['SLA_UID'] = $_REQUEST['VAL_ID_SLA'];
        $aData['PRO_UID'] = $_REQUEST['PRO_UID'];
        $aData['SLA_NAME'] = $_REQUEST['VAL_NAME_SLA'];
        $aData['SLA_DESCRIPTION'] = $_REQUEST['VAL_DESCRIPTION_SLA'];
        $aData['SLA_TYPE'] = $_REQUEST['VAL_TYPE_SLA_ID'];
        $aData['SLA_TAS_START'] = $_REQUEST['VAL_TASK_START_SLA_ID'];
        if ($_REQUEST['VAL_TYPE_SLA_ID'] == 'RANGE') {
            $aData['SLA_TAS_END'] = $_REQUEST['VAL_TASK_END_SLA_ID'];
        }
        $aData['SLA_TIME_DURATION'] = $_REQUEST['VAL_DURATION_NUMBER_SLA'];
        $aData['SLA_TIME_DURATION_MODE'] = $_REQUEST['VAL_DURATION_TYPE_SLA'];
        $aData['SLA_CONDITIONS'] = $_REQUEST['VAL_CONDITION_SLA'];
        if ($_REQUEST['VAL_PENALITY'] == 'true') {
            $aData['SLA_PEN_ENABLED'] = '1';
        } else {
            $aData['SLA_PEN_ENABLED'] = '0';
        }
        $aData['SLA_PEN_TIME'] = $_REQUEST['VAL_PENALITY_TIME_NUMBER_SLA'];
        $aData['SLA_PEN_VALUE'] = $_REQUEST['VAL_PENALITY_VALUE_NUMBER_SLA'];

        $aData['SLA_PEN_TIME_MODE'] = $_REQUEST['VAL_PENALITY_VALUE_TYPE_SLA'];
        $aData['SLA_PEN_VALUE_UNIT'] = $_REQUEST['VAL_PENALITY_UNIT_TYPE_SLA'];
        $aData['SLA_STATUS'] = $_REQUEST['VAL_STATUS_SLA'];

        $aProcess = $oSla->update($aData);

        if (isset($_REQUEST['VAL_RELOAD_SLA']) && $_REQUEST['VAL_RELOAD_SLA'] == 'on') {
            $con = Propel::getConnection('workflow');
            $stmt = $con->createStatement();
            $query = 'DELETE FROM APP_SLA WHERE SLA_UID="' . $aData['SLA_UID'] . '"';
            $rs = $stmt->executeQuery($query, ResultSet::FETCHMODE_NUM);
        }

        echo G::json_encode(array('success' => true));
        break;
    case 'deleteSla':    
        $slaUid = $_REQUEST['SLA_UID'];
        $oSla = new sla();        
        if ($oSla->remove($slaUid)) {
            echo "true";
        } else {
            echo "false";
        }
        $con = Propel::getConnection('workflow');
        $stmt = $con->createStatement();
        $query = 'DELETE FROM APP_SLA WHERE SLA_UID="' . $slaUid . '"';
        $rs = $stmt->executeQuery($query, ResultSet::FETCHMODE_NUM);
        break;
    case 'loadSla':
        $slaUid = $_REQUEST['SLA_UID'];
        $oSla = new sla();
        $aData = $oSla->loadDetails($slaUid);

        $oTask = new task();
        $aTaskStart = '';
        $aTaskEnd = '';
        if ($aData['SLA_TAS_START'] != '') {
            $oTask->setTasUid($aData['SLA_TAS_START']);
            $aTaskStart = $oTask->getTasTitle();
        }
        if ($aData['SLA_TAS_END'] != '') {
            $oTask->setTasUid($aData['SLA_TAS_END']);
            $aTaskEnd = $oTask->getTasTitle();
        }
        $aData['SLA_TAS_START_TXT'] = $aTaskStart;
        $aData['SLA_TAS_END_TXT'] = $aTaskEnd;

        $resp = array('success' => true, 'data' => $aData);
        echo G::json_encode($resp);
        break;
    case 'listSlaName':
        $oSla = new sla();
        $aSla = $oSla->getListSlaName(array('SLA_UID' => 'ALL', 'SLA_NAME' => '- All -'));
        echo G::json_encode(array('data' => $aSla, 'total' => count($aSla)));
        break;
    case 'reportSla':
        $oAppSla = new AppSla();
        $slaUid = "";
        $sDateSTart = "";
        $sDateEnd = "";
        $sStatus = "";
        $sTypeExceeded = "";

        if ($_REQUEST['SLA_UID'] != '- All -' && $_REQUEST['SLA_UID'] != 'ALL') {
            $slaUid = $_REQUEST['SLA_UID'];
        }
        if ($_REQUEST['DATE_START'] != "") {
            $sDateSTart = $_REQUEST['DATE_START'];
        }
        if ($_REQUEST['DATE_END'] != "") {
            $sDateEnd = $_REQUEST['DATE_END'];
        }
        if ($_REQUEST['EXC_STATUS'] != "ALL") {
            $sStatus = $_REQUEST['EXC_STATUS'];
        }

        $aAppSla = $oAppSla->getReportAppSla($slaUid, $sDateSTart, $sDateEnd, $sStatus, $sTypeExceeded);

        echo G::json_encode(array('data' => $aAppSla, 'total' => count($aAppSla)));

        break;
    case    'reportSlaFirstLevel':
        $oAppSla = new AppSla();

        if ($_REQUEST['SLA_UID'] != '- All -' && $_REQUEST['SLA_UID'] != 'ALL') {
            $oAppSla->setSlaUidRep($_REQUEST['SLA_UID']);
        }
        if ($_REQUEST['TYPE_DATE'] != '- All -') {
            $oAppSla->setTypeDate($_REQUEST['TYPE_DATE']);
        }
        if ($_REQUEST['DATE_START'] != "") {
            $oAppSla->setDateStart($_REQUEST['DATE_START']);
        }
        if ($_REQUEST['DATE_END'] != "") {
            $oAppSla->setDateEnd($_REQUEST['DATE_END']);
        }
        if ($_REQUEST['TYPE_EXCEEDED'] != "") {
            $oAppSla->setTypeExceeded($_REQUEST['TYPE_EXCEEDED']);
        }
        if ($_REQUEST['EXC_DURATION_TYPE'] != "") {
            $oAppSla->setExecDurationType($_REQUEST['EXC_DURATION_TYPE']);
        }
        if ($_REQUEST['EXC_NUMBER'] != 0) {
            $oAppSla->setExecNumber($_REQUEST['EXC_NUMBER']);
        }
        if ($_REQUEST['EXC_STATUS'] != "ALL") {
            $oAppSla->setStatus($_REQUEST['EXC_STATUS']);
        }
        if (isset($_REQUEST['start']) && $_REQUEST['start'] != 0) {
            $oAppSla->setStart($_REQUEST['start']);
        }
        if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != 0) {
            $oAppSla->setLimit($_REQUEST['limit']);
        }
        $aAppSla = $oAppSla->getReportFirstLevel();
        if ($oAppSla->getTotalRows() != 0 ) {
            $totalRows = $oAppSla->getTotalRows();
        } else {
            $totalRows = count($aAppSla);
        }
        echo G::json_encode(array('data' => $aAppSla, 'total' => count($aAppSla)));

        break;
    case 'reportCaseSecondLevel':

        $oAppSla = new AppSla();

        if (isset($_REQUEST['SLA_UID'])) {
            $oAppSla->setSlaUidRep($_REQUEST['SLA_UID']);
        }
        if ($_REQUEST['TYPE_DATE'] != '- All -') {
            $oAppSla->setTypeDate($_REQUEST['TYPE_DATE']);
        }
        if ($_REQUEST['DATE_START'] != "") {
            $oAppSla->setDateStart($_REQUEST['DATE_START']);
        }
        if ($_REQUEST['DATE_END'] != "") {
            $oAppSla->setDateEnd($_REQUEST['DATE_END']);
        }
        if ($_REQUEST['TYPE_EXCEEDED'] != "") {
            $oAppSla->setTypeExceeded($_REQUEST['TYPE_EXCEEDED']);
        }
        if ($_REQUEST['EXC_DURATION_TYPE'] != "") {
            $oAppSla->setExecDurationType($_REQUEST['EXC_DURATION_TYPE']);
        }
        if ($_REQUEST['EXC_NUMBER'] != 0) {
            $oAppSla->setExecNumber($_REQUEST['EXC_NUMBER']);
        }
        if ($_REQUEST['EXC_STATUS'] != "ALL") {
            $oAppSla->setStatus($_REQUEST['EXC_STATUS']);
        }
        if (isset($_REQUEST['start']) && $_REQUEST['start'] != 0) {
            $oAppSla->setStart($_REQUEST['start']);
        }
        if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != 0) {
            $oAppSla->setLimit($_REQUEST['limit']);
        }

        if (isset($_REQUEST['sort'])) {
            $oAppSla->setSort($_REQUEST['sort']);
        }
        if (isset($_REQUEST['dir'])) {
            $oAppSla->setDir($_REQUEST['dir']);
        }

        $aAppSla = $oAppSla->getReportAppSla();
        if ($oAppSla->getTotalRows() != 0 ) {
            $totalRows = $oAppSla->getTotalRows();
        } else {
            $totalRows = count($aAppSla);
        }
        echo G::json_encode(array('data' => $aAppSla, 'total' => $totalRows));
        break;
    case 'dashletSla':
        $oAppSla = new AppSla();
        $aAppSla = $oAppSla->loadDashlet();
        echo G::json_encode(array('data' => $aAppSla, 'total' => count($aAppSla)));
        break;
    case 'reportCase':
        $oAppSla = new AppSla();
        $slaUid = $_REQUEST['SLA_UID'];
        $nAppNumber = $_REQUEST['APP_NUMBER'];
        $aAppSla = $oAppSla->loadDetailReportSel($slaUid, $nAppNumber);
        echo G::json_encode(array('success' => true, 'data' => $aAppSla));
        break;
    case 'selectSlaUID':
        // SubReport Top Information
        $oSla = new Sla();
        $slaUid = $_REQUEST['SLA_UID'];
        $nAppNumber = $_REQUEST['APP_NUMBER'];
        $aSla = $oSla->getSelectSlaUid($slaUid, $nAppNumber);
        echo G::json_encode(array('success' => true, 'data' => $aSla));
        break;
    default:
        echo G::json_encode(array('success' => true));
        break;
}

