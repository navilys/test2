<?php

require_once( PATH_PLUGINS . 'pmFtpMonitor/class.pmFtpMonitor.php');
try {
    $action = isset($_POST["action"]) ? $_POST["action"] : "";
    $uid = isset($_POST["uid"]) && strlen($_POST["uid"]) > 0 ? $_POST["uid"] : 0;
    $proUid = isset($_POST["proUid"]) ? $_POST["proUid"] : '';
    $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
    switch ($action) {
        case "showSettingsList":
            $rows = pmFtpMonitorClass::getFtpMonitorSettingsList();
            echo json_encode(array("success" => true, "resultTotal" => count($rows), "resultRoot" => $rows));
            break;
        case "editSettings":
            $formData = isset($_POST["formData"]) ? $_POST["formData"] : null;
            $m = "Error";
            if (!is_null($formData))
                $m = pmFtpMonitorClass::saveSettings(json_decode($formData));
            echo json_encode(array("message" => $m));
            break;
        case "switchSettingsStatus":
            $m = pmFtpMonitorClass::switchSettingsStatus($uid);
            echo json_encode(array("message" => $m));
            break;
        case "showProcessList":
            require_once( PATH_HOME . 'engine/classes/class.processMap.php');
            $rows = processMap::getAllProcesses();
            echo $rows;
            break;
        case "showTaskList":
            $rows = Array();
            if (strlen($proUid) > 0) {
                require_once( PATH_HOME . 'engine/classes/model/Task.php');
                require_once( PATH_HOME . 'engine/classes/model/Route.php');
                require_once( PATH_HOME . 'engine/classes/model/Content.php');
                $c = new Criteria('workflow');
                $c->clear();
                $c->addSelectColumn(TaskPeer::TAS_UID);
                $c->addSelectColumn(ContentPeer::CON_VALUE . " AS TAS_TITLE ");
                $aRouteCond[] = array(TaskPeer::TAS_UID, RoutePeer::TAS_UID);
                $c->addJoinMC($aRouteCond, Criteria::INNER_JOIN);
                $aContentCond = array();
                $aContentCond[] = array(TaskPeer::TAS_UID, ContentPeer::CON_ID);
                $aContentCond[] = array(ContentPeer::CON_CATEGORY, DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter());
                $aContentCond[] = array(ContentPeer::CON_LANG, DBAdapter::getStringDelimiter() . $lang . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aContentCond, Criteria::INNER_JOIN);
                $c->add(TaskPeer::TAS_START, 'TRUE');
                $c->add(TaskPeer::PRO_UID, $proUid);
                $c->addGroupByColumn(TaskPeer::TAS_UID);
                $c->addGroupByColumn(ContentPeer::CON_VALUE);
                $oDataset = TaskPeer::doSelectRS($c);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($oDataset->next())
                    $rows[] = $oDataset->getRow();
            }
            echo json_encode($rows);
            break;
        case "showCaseUserList":
            $tasUid = isset($_POST["tasUid"]) ? $_POST["tasUid"] : '';
            $rows = Array();
            if (strlen($tasUid) > 0) {
                require_once( PATH_HOME . 'engine/classes/model/TaskUser.php');
                require_once( PATH_HOME . 'engine/classes/model/Users.php');
                require_once( PATH_HOME . 'engine/classes/model/Groupwf.php');
                require_once( PATH_HOME . 'engine/classes/model/GroupUser.php');
                $c = new Criteria('workflow');
                $c->clear();
                $c->addSelectColumn("u.USR_UID AS USR_UID");
                $c->addSelectColumn("u.USR_USERNAME AS USR_USERNAME");
                $c->addSelectColumn("ug.USR_UID AS USR_UID1");
                $c->addSelectColumn("ug.USR_USERNAME AS USR_USERNAME1");
                $c->addAlias('u', 'USERS');
                $aUserCond = array();
                $aUserCond[] = array(TaskUserPeer::USR_UID, "u.USR_UID");
                $aUserCond[] = array("u.USR_STATUS", DBAdapter::getStringDelimiter() . 'ACTIVE' . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aUserCond, Criteria::LEFT_JOIN);
                $aGroupCond = array();
                $aGroupCond[] = array(TaskUserPeer::USR_UID, GroupwfPeer::GRP_UID);
                $aGroupCond[] = array(GroupwfPeer::GRP_STATUS, DBAdapter::getStringDelimiter() . 'ACTIVE' . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aGroupCond, Criteria::LEFT_JOIN);
                $c->add(TaskUserPeer::TAS_UID, $tasUid);
                $aGroupUserCond = array();
                $aGroupUserCond[] = array(GroupwfPeer::GRP_UID, GroupUserPeer::GRP_UID);
                $c->addJoinMC($aGroupUserCond, Criteria::LEFT_JOIN);
                $c->addAlias('ug', 'USERS');
                $aUser1Cond = array();
                $aUser1Cond[] = array(GroupUserPeer::USR_UID, "ug.USR_UID");
                $aUser1Cond[] = array("ug.USR_STATUS", DBAdapter::getStringDelimiter() . 'ACTIVE' . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aUser1Cond, Criteria::LEFT_JOIN);
                $oDataset = TaskUserPeer::doSelectRS($c);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($oDataset->next()) {
                    $rows1 = array();
                    $rows1[] = $oDataset->getRow();
                    if (isset($rows1[0]))
                        if (isset($rows1[0]["USR_UID"]) && isset($rows1[0]["USR_USERNAME"]))
                            $rows = array_merge($rows, array(count($rows) => array("USR_UID" => $rows1[0]["USR_UID"], "USR_USERNAME" => $rows1[0]["USR_USERNAME"])));
                        else if (isset($rows1[0]["USR_UID1"]) && isset($rows1[0]["USR_USERNAME1"]))
                            $rows = array_merge($rows, array(count($rows) => array("USR_UID" => $rows1[0]["USR_UID1"], "USR_USERNAME" => $rows1[0]["USR_USERNAME1"])));
                }
                require_once( PATH_RBAC_HOME . 'engine/classes/model/RbacUsers.php');
                $crbac = new Criteria('rbac');
                $crbac->clear();
                $crbac->addSelectColumn(RbacUsersPeer::USR_UID);
                $oDatasetRB = TaskUserPeer::doSelectRS($crbac);
                $oDatasetRB->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $aRB = array();
                while ($oDatasetRB->next()) {
                    $aRB[] = $oDatasetRB->getRow();
                }
                $sRB = json_encode($aRB);
                foreach ($rows as $k => $v)
                    if (!strpos($sRB, "\"" . $v["USR_UID"] . "\""))
                        unset($rows[$k]);
                echo json_encode($rows);
            }
            break;
        case "showInputDocumentList":
            $rows = Array();
            if (strlen($proUid) > 0) {
                require_once( PATH_HOME . 'engine/classes/model/InputDocument.php');
                require_once( PATH_HOME . 'engine/classes/model/Content.php');
                $c = new Criteria('workflow');
                $c->clear();
                $c->addSelectColumn(InputDocumentPeer::INP_DOC_UID);
                $c->addSelectColumn(ContentPeer::CON_VALUE);
                $aContentCond = array();
                $aContentCond[] = array(InputDocumentPeer::INP_DOC_UID, ContentPeer::CON_ID);
                $aContentCond[] = array(ContentPeer::CON_CATEGORY, DBAdapter::getStringDelimiter() . 'INP_DOC_TITLE' . DBAdapter::getStringDelimiter());
                $aContentCond[] = array(ContentPeer::CON_LANG, DBAdapter::getStringDelimiter() . $lang . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aContentCond, Criteria::INNER_JOIN);
                $c->add(InputDocumentPeer::PRO_UID, $proUid);
                $oDataset = InputDocumentPeer::doSelectRS($c);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($oDataset->next())
                    $rows[] = $oDataset->getRow();
            }
            echo json_encode($rows);
            break;
        case "isCnn":
            $rows = array("success" => "true", "message" => "", "field" => "");
            try {
                $formData = isset($_POST["formData"]) ? json_decode($_POST["formData"]) : null;
                if (isset($formData) && !is_null($formData) && is_object($formData)) {
                    $cnnType = isset($formData->CONNECTION_TYPE) ? $formData->CONNECTION_TYPE : '';
                    $host = isset($formData->HOST) ? $formData->HOST : '';
                    $port = isset($formData->PORT) && strlen($formData->PORT) > 0 ? $formData->PORT : 21;
                    $u = isset($formData->USER) && strlen($formData->USER) > 0 ? $formData->USER : 'anonymous';
                    $p = isset($formData->PASS) ? $formData->PASS : '';
                    $path = isset($formData->FTP_PATH) ? $formData->FTP_PATH : '';
                    $rows = array("success" => "true", "message" => "", "field" => "");
                    switch ($cnnType) {
                        case "SHARED":
                            $rows = array("success" => "false", "message" => "The path '" . $path . "' is not found.", "field" => "txtFTP_PATH");
                            try {
                                if (@is_dir($path))
                                    $rows = array("success" => "true", "message" => "", "field" => "");
                            } catch (Exception $ex) {
                                $rows = array("success" => "false", "message" => $ex, "field" => "txtFTP_PATH");
                            }
                            break;
                        case "FTP":
                            $rows = array("success" => "false", "message" => "The host '" . $host . "' is not found or the port '" . $port . "' is not correct.", "field" => "txtHOST");
                            $conn = @ftp_connect($host, $port);
                            if ($conn) {
                                $rows = array("success" => "false", "message" => "The user '" . $u . "' doesn't have the access to the host '" . $host . "' or its password is not valid.", "field" => "txtUSER");
                                $ftpLogin = @ftp_login($conn, $u, $p);
                                if ($ftpLogin) {
                                    $rows = array("success" => "false", "message" => "The path '" . $path . "' is not found on the host '" . $host . "'.", "field" => "txtFTP_PATH");
                                    $nlist = ftp_nlist($conn, $path);
                                    if ($nlist)
                                        $rows = array("success" => "true", "message" => "", "field" => "");
                                }
                            }
                            break;
                    }
                }
            } catch (Exception $ex) {
                $rows = array("success" => "false", "message" => $ex);
            }
            echo json_encode($rows);
            break;
        case "showLogList":
            require_once ( 'classes/model/FtpMonitorLogs.php' );
            require_once ( 'classes/model/FtpMonitorSetting.php' );
            $rows = array();
            $c = new Criteria('workflow');
            $c->clear();
            foreach (FtpMonitorLogsPeer::getFieldNames() as $f)
                if ($f != "Result")
                    $c->addSelectColumn(FtpMonitorLogsPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
            $c->addSelectColumn(FtpMonitorSettingPeer::CONNECTION_TYPE);
            $c->addSelectColumn(FtpMonitorSettingPeer::HOST);
            $c->addSelectColumn(FtpMonitorSettingPeer::FTP_PATH);
            $aProCond = array();
            $aProCond[] = array(FtpMonitorLogsPeer::FTP_UID, FtpMonitorSettingPeer::FTP_UID);
            $c->addJoinMC($aProCond, Criteria::INNER_JOIN);
            $c->add(FtpMonitorLogsPeer::FTP_LOG_UID, "xxx", Criteria::NOT_EQUAL);
            $oDataset = FtpMonitorLogsPeer::doSelectRS($c);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next())
                $rows[] = $oDataset->getRow();
            echo json_encode(array("success" => true, "resultTotal" => count($rows), "resultRoot" => $rows));
            break;
        case "showLogDetails":
            require_once ( 'classes/model/FtpMonitorLogsDetails.php' );
            require_once( PATH_HOME . 'engine/classes/model/Content.php');
            $rows = array();
            if ($uid > 0) {
                $c = new Criteria('workflow');
                $c->clear();
                foreach (FtpMonitorLogsDetailsPeer::getFieldNames() as $f)
                    $c->addSelectColumn(FtpMonitorLogsDetailsPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
                $c->addSelectColumn(ContentPeer::CON_VALUE . " AS " . DBAdapter::getStringDelimiter() . "CASE" . DBAdapter::getStringDelimiter());
                $aCaseCond = array();
                $aCaseCond[] = array(FtpMonitorLogsDetailsPeer::APP_UID, ContentPeer::CON_ID);
                $aCaseCond[] = array(ContentPeer::CON_CATEGORY, DBAdapter::getStringDelimiter() . 'APP_TITLE' . DBAdapter::getStringDelimiter());
                $aCaseCond[] = array(ContentPeer::CON_LANG, DBAdapter::getStringDelimiter() . $lang . DBAdapter::getStringDelimiter());
                $c->addJoinMC($aCaseCond, Criteria::LEFT_JOIN);
                $c->add(FtpMonitorLogsDetailsPeer::FTP_LOG_UID, $uid, Criteria::EQUAL);
                $oDataset = FtpMonitorLogsDetailsPeer::doSelectRS($c);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($oDataset->next()) {
                    $rows[] = $oDataset->getRow();
                }
            }
            echo json_encode(array("success" => true, "resultTotal" => count($rows), "resultRoot" => $rows));
            break;
    }
} catch (Exception $e) {
    echo null;
}