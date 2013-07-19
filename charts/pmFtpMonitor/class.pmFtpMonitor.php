<?php

/**
 * class.pmFtpMonitor.php
 *
 */
class pmFtpMonitorClass extends PMPlugin {

    function getCurrentPort() {
        return (isset($_SERVER ['SERVER_PORT'])) && ($_SERVER ['SERVER_PORT'] != '80') ? ':' . $_SERVER ['SERVER_PORT'] : '';
    }

    function getDefaultEndpoint() {
        return 'http://' . SERVER_NAME . $this->getCurrentPort() . '/sys' . SYS_SYS . '/en/green/services/wsdl2';
    }

    function getDefaultUploadService() {
        return 'http://' . SERVER_NAME . $this->getCurrentPort() . '/sys' . SYS_SYS . '/en/green/services/upload';
    }

    function getSysLang() {
        return defined('SYS_LANG') ? SYS_LANG : 'en';
    }

// Constructor
    function __construct() {
        set_include_path(
        PATH_PLUGINS . 'pmFtpMonitor' . PATH_SEPARATOR .
        get_include_path()
        );
    }

    function setup() {
        
    }

    function getFtpMonitorSettingsList() {
        $rows = array();
        require_once ( 'classes/model/Users.php' );
        require_once (PATH_PLUGINS . 'pmFtpMonitor/classes/model/FtpMonitorSetting.php');
        $c = new Criteria("workflow");
        $c->clear();
        foreach (FtpMonitorSettingPeer::getFieldNames() as $f)
            $c->addSelectColumn(FtpMonitorSettingPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
        $c->addSelectColumn("cp.CON_VALUE AS PRO_TITLE");
        $c->addAlias("cp", "CONTENT");
        $aProCond = array();
        $aProCond[] = array(FtpMonitorSettingPeer::PRO_UID, "cp.CON_ID");
        $aProCond[] = array("cp.CON_CATEGORY", DBAdapter::getStringDelimiter() . "PRO_TITLE" . DBAdapter::getStringDelimiter());
        $aProCond[] = array("cp.CON_LANG", DBAdapter::getStringDelimiter() . pmFtpMonitorClass::getSysLang() . DBAdapter::getStringDelimiter());
        $c->addJoinMC($aProCond, Criteria::LEFT_JOIN);
        $c->addSelectColumn("ct.CON_VALUE AS TAS_TITLE");
        $c->addAlias("ct", "CONTENT");
        $aTasCond = array();
        $aTasCond[] = array(FtpMonitorSettingPeer::TAS_UID, "ct.CON_ID");
        $aTasCond[] = array("ct.CON_CATEGORY", DBAdapter::getStringDelimiter() . "TAS_TITLE" . DBAdapter::getStringDelimiter());
        $aProCond[] = array("ct.CON_LANG", DBAdapter::getStringDelimiter() . pmFtpMonitorClass::getSysLang() . DBAdapter::getStringDelimiter());
        $c->addJoinMC($aTasCond, Criteria::LEFT_JOIN);
        $c->addSelectColumn(UsersPeer::USR_USERNAME);
        $aUsrCond = array();
        $aUsrCond[] = array(FtpMonitorSettingPeer::DEL_USER_UID, UsersPeer::USR_UID);
        $c->addJoinMC($aUsrCond, Criteria::LEFT_JOIN);
        $c->add(FtpMonitorSettingPeer::FTP_UID, "xxx", Criteria::NOT_EQUAL);
        $oDataSet = FtpMonitorSettingPeer::DoSelectRs($c);
        $oDataSet->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($oDataSet->next()) {
            $rows[] = $oDataSet->getRow();
        }
        return $rows;
    }

    function getFtpMonitorSettings($uid) {
        $rows = array();
        if (!isset($uid) || strlen($uid) == 0)
            return array();
        require_once (PATH_PLUGINS . 'pmFtpMonitor/classes/model/FtpMonitorSetting.php');
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        foreach (FtpMonitorSettingPeer::getFieldNames() as $f)
            $c->addSelectColumn(FtpMonitorSettingPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
        $c->add(FtpMonitorSettingPeer::FTP_UID, $uid, Criteria::EQUAL);
        $oDataSet = FtpMonitorSettingPeer::DoSelectRs($c);
        $oDataSet->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($oDataSet->next()) {
            $rows[] = $oDataSet->getRow();
        }
        return isset($rows[0]) ? $rows[0] : array();
    }

    function saveSettings($data) {
        $res_msg = "Some error has occurred.";
        try {
            $id = isset($data->FTP_UID) ? $data->FTP_UID : '';
            require_once ('classes/model/FtpMonitorSetting.php');
            $tr = FtpMonitorSettingPeer::retrieveByPK($id);
            if (!( is_object($tr) && get_class($tr) == 'FtpMonitorSetting' )) {
                $tr = new FtpMonitorSetting();
            }
            foreach (FtpMonitorSettingPeer::getFieldNames() as $f) {
                $fn = ltrim(ltrim(FtpMonitorSettingPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME), FtpMonitorSettingPeer::TABLE_NAME), '.');
                if ($fn === "XML_SEARCH" && (!isset($data->$fn) || strtoupper($data->$fn) != "TRUE"))
                    $tr->setXmlSearch("FALSE");
                else if ($fn === "FTP_STATUS" && (!isset($data->$fn) || strtoupper($data->$fn) != "ACTIVE"))
                    $tr->setFtpStatus("INACTIVE");
                else
                    $tr->setByName($f, isset($data->$fn) ? $data->$fn : '');
            }
            $id = $tr->getFtpUid();
            if (!isset($id) || strlen($id) == 0)
                $tr->setFtpUid(G::generateUniqueID());
            if ($tr->validate()) {
                try {
                    if ($tr->save())
                        $res_msg = "The record has been saved successfully.";
                    else
                        $res_msg = "The are no changes to the record to be saved.";
                } catch (PropelException $ex) {
                    $res_msg = $ex->getMessage();
                }
            } else
                $res_msg = implode($tr->getValidationFailures(), ";");
        } catch (Exception $ee) {
            $res_msg = $ee;
        }
        return $res_msg;
    }

    function switchSettingsStatus($uid) {
        $res_msg = "Some error has occured.";
        require_once (PATH_PLUGINS . 'pmFtpMonitor/classes/model/FtpMonitorSetting.php');
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $tr = FtpMonitorSettingPeer::retrieveByPK($uid);
        if (is_object($tr) && get_class($tr) === "FtpMonitorSetting") {
            try {
                $status = $tr->getFtpStatus();
                if ($status === "ACTIVE")
                    $tr->setFtpStatus("INACTIVE");
                else
                    $tr->setFtpStatus("ACTIVE");
                try {
                    if ($tr->save())
                        $res_msg = "The status of the record has been switched successfully.";
                } catch (PropelException $ex) {
                    $res_msg = $ex->getMessage();
                }
            } catch (PropelExaption $ex) {
                $res_msg = $ex->getMessage();
            }
        } else
            $res_msg = "The record hasn't been found.";
        return $res_msg;
    }

    function executeSchedulerJob() {
        eprintln('* Executing pmFtpMonitor plugin', 'white');
        require_once ( "classes/model/FtpMonitorSetting.php" );
        $c = new Criteria('workflow');
        $c->clear();
        foreach (FtpMonitorSettingPeer::getFieldNames() as $f)
            $c->addSelectColumn(FtpMonitorSettingPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
        $c->add(FtpMonitorSettingPeer::FTP_STATUS, 'ACTIVE');
        $oDataSet = FtpMonitorSettingPeer::DoSelectRs($c);
        $oDataSet->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($oDataSet->next()) {
            $settings = $oDataSet->getRow();
            if (!isset($settings) || !is_array($settings))
                return null;
            $connection_type = isset($settings['CONNECTION_TYPE']) ? $settings['CONNECTION_TYPE'] : '';
            $files = array();
            switch ($connection_type) {
                case "FTP":
                    eprintln(" ++ Connection type: FTP", "white");
                    $conn = $this->getFtpConnection($settings);
                    if (isset($conn) && !is_null($conn)) {
                        $files = $this->getFileListFromFtp($settings, $conn);
                        if (isset($files) && is_array($files)) {
                            eprintln(" ---- Files found on the server: " . count($files), "white");
                            if (count($files) > 0) {
                                $files = $this->removeLoggedFiles($settings['FTP_UID'], $files);
                                eprintln(" ---- Files matching the pattern and not existing in the log: " . count($files), "white");
                                if (count($files) > 0) {
                                    $tempFolder = PATH_DATA_SITE . "tempFtpMonitor/" . $settings['FTP_UID'];
                                    if (!@is_dir($tempFolder))
                                        if (!$this->makedir($tempFolder)) {
                                            eprintln("[ERROR]: Cannot create directory $tempFolder.", "red");
                                            return null;
                                        }
                                    if (!@opendir($tempFolder)) {
                                        eprintln("[ERROR]: There's no access rights to the $tempFolder", "red");
                                        return null;
                                    }
                                    if (!is_writable($tempFolder))
                                        if (!@chmod($tempFolder, 0777)) {
                                            eprintln("[ERROR]: There's no access rights to write in the $tempFolder folder", "red");
                                            return null;
                                        }
                                    eprintln(" ---- Processing files", "white");
                                    $this->processFilesFromFtp($files, $conn, $settings, $tempFolder);
                                }
                            }
                        }
                        ftp_close($conn);
                    }
                    break;
                case 'SHARED':
                    eprintln(" ++ Connection type: SHARED", "white");
                    $files = $this->getFileListFromSharedFolder($settings);
                    if (isset($files) && is_array($files)) {
                        eprintln(" ---- Files found on the server: " . count($files), "white");
                        if (count($files) > 0) {
                            $files = $this->removeLoggedFiles($settings['FTP_UID'], $files);
                            eprintln(" ---- Files matching the pattern and not existing in the log: " . count($files), "white");
                            if (count($files) > 0) {
                                eprintln(" ---- Processing files", "white");
                                $this->processFilesFromSharedFolder($files, $settings, $tempFolder);
                            }
                        }
                    }
                    break;
                case 'SFTP':
                    eprintln("Connection type: SFTP", "white");
                    eprintln("This pmFtpMonitor version doesn't support SFTP connection.", "red");
//                    $conn = $this->getSftpConnection($settings);
//                    $files = $this->getFileListFromSftp($settings, $conn);
            }
        }
        eprint("* Executing pmFtpmonitor plugin...............................", "white");
        eprintln("[DONE]", "green");
    }

    function getFtpConnection($sett) {
        if (!(isset($sett) && is_array($sett)))
            return null;
        $host = isset($sett['HOST']) ? $sett['HOST'] : '';
        $user = isset($sett['USER']) && strlen($sett['USER']) > 0 ? $sett['USER'] : 'anonymous';
        $pass = isset($sett['PASS']) ? $sett['PASS'] : '';
        $port = isset($sett['PORT']) ? $sett['PORT'] : 21;
        $ftp_path = isset($sett['FTP_PATH']) ? $sett['FTP_PATH'] : '';
        eprintln(" ---- Connecting to host ($host) with port($port)", "white");
        $conn = ftp_connect($host, intval($port));
        if (!$conn) {
            eprintln("Connection cannot be established", "red");
            return null;
        }
        eprintln(" ---- Login to host as $user", "white");
        $ftpLogin = ftp_login($conn, $user, $pass);
        if (!$ftpLogin) {
            eprintln("The credentials provided for the FTP connection are invalid", "red");
            return null;
        }

        // setting passive mode in case of the client is behind the firewall
        if (is_object($conn))
            ftp_pasv($conn, true);

        if (!@opendir("ftp://$user:$pass@$host:$port/$ftp_path/")) {
            eprintln("The user $user doesn't have permissions to access the $ftp_path folder at the $host throught the $port port");
            return null;
        }

        return $conn;
    }

//    function getSftpConnection($sett) {
//        if (!(isset($sett) && is_array($sett)))
//            return null;
//        $host = isset($sett['HOST']) ? $sett['HOST'] : '';
//        $user = isset($sett['USER']) && strlen($sett['USER']) > 0 ? $sett['USER'] : 'anonymous';
//        $pass = isset($sett['PASS']) ? $sett['PASS'] : '';
//        $port = isset($sett['PORT']) && strlen($sett['PORT']) > 0 ? $sett['PORT'] : '22';
//        eprint(" -- Connecting to host ($host) .... ", "green");
//        $conn = ssh2_connect($host, $port);
//        if (ssh2_auth_password($conn, $user, $pass))
//            eprintln("[OK]", "green");
//        else {
//            eprintln("[ERROR]", "red");
//            return null;
//        }
//        return $conn;
//    }

    function getFileListFromFtp($sett, $conn) {
        if (!(isset($sett) && is_array($sett)))
            return null;
        $ftp_path = isset($sett['FTP_PATH']) ? $sett['FTP_PATH'] . '/' : '/';
        $search_pattern = isset($sett['SEARCH_PATTERN']) ? $sett['SEARCH_PATTERN'] : '';
        $xml_search = $sett['XML_SEARCH'];
        $aguja = '/^' . strtr(addcslashes($search_pattern, '\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')) . '$/i';
        if (strlen($sett['SEARCH_PATTERN']) == 0)
            $aguja = '/\./';
        return $this->getFileListRecursiveFromFtp($conn, $ftp_path, $aguja, $xml_search);
    }

    function getFileListRecursiveFromFtp($cnn, $path, $pattern, $have_xml) {
        $fls = array();
        if (isset($cnn) && !is_null($cnn)) {
            $cur_level = ftp_nlist($cnn, $path);
            if (isset($cur_level) && is_array($cur_level))
                foreach ($cur_level as $item) {
                    if (ftp_size($cnn, $item) == -1) { // directory
                        $next_level = $this->getFileListRecursiveFromFtp($cnn, $item . '/', $pattern, $have_xml);
                        if (is_array($next_level) && count($next_level) > 0)
                            $fls = array_merge($fls, $next_level);
                    } else { // file
                        $fn = $this->getFileNameFromPath($item);
                        if (preg_match($pattern, $fn)) { // check if file name matches the pattern
                            $fls[$item] = '';
                            if (strtolower($have_xml) === 'true') {
                                $xml_path = strrpos($item, '.') > -1 ? substr($item, 0, strrpos($item, '.')) . '.xml' : $item . '.xml';
                                if (ftp_size($cnn, $xml_path) > 0)
                                    $fls[$item] = $xml_path;
                            }
                        }
                    }
                }
        } else
            eprintln("FTP connection has failed", "red");
        return $fls;
    }

//    function getFileListFromSftp($sett, $conn) {
//        if (!(isset($sett) && is_array($sett)))
//            return null;
//        $ftp_path = isset($sett['FTP_PATH']) ? $sett['FTP_PATH'] . '/' : '/';
//        $search_pattern = isset($sett['SEARCH_PATTERN']) ? $sett['SEARCH_PATTERN'] : '';
//        $xml_search = $sett['XML_SEARCH'];
//        $aguja = '/^' . strtr(addcslashes($search_pattern, '\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')) . '$/i';
//        if (strlen($sett['SEARCH_PATTERN']) == 0)
//            $aguja = '/\./';
//        return $this->getFileListRecursiveFromSftp($conn, $ftp_path, $aguja, $xml_search);
//    }
//
//    function getFileListRecursiveFromSftp($cnn, $path, $pattern, $have_xml) {
//        $fls = array();
//        $stream = ssh2_exec($cnn, "ls $path");
//        stream_set_blocking($stream, true);
//        $cmd = fread($stream, 4096);
//        $arr = explode("\n", $cmd);
//        echo sizeof($arr);
//        die;
//    }

    function getFileListFromSharedFolder($sett) {
        if (isset($sett) && is_array($sett)) {
            $dir = $sett['FTP_PATH'];
            $search_pattern = isset($sett['SEARCH_PATTERN']) ? $sett['SEARCH_PATTERN'] : '';
            $xml_search = $sett['XML_SEARCH'];
            $aguja = '/^' . strtr(addcslashes($search_pattern, '\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')) . '$/i';
            if (strlen($sett['SEARCH_PATTERN']) == 0)
                $aguja = '/\./';
            return $this->getFileListRecursiveFromSharedFolder($dir, '', $aguja, $xml_search);
        } else
            eprintln("[ERROR] The settings object is not found.", "red");
    }

    function getFileListRecursiveFromSharedFolder($path, $relative_path, $pattern, $have_xml) {
        $fls = array();
        $cur_path = $this->joinUrl($path, $relative_path);
        if (@is_dir($cur_path)) {
            $cur_level = scandir($cur_path);
            foreach ($cur_level as $item) {
                $p = $this->joinUrl($cur_path, $item);
                if ($item !== "." && $item !== "..") {
                    $next_level = array();
                    if (@is_dir($p)) {
                        $next_level = $this->getFileListRecursiveFromSharedFolder($path, $this->joinUrl($relative_path, $item), $pattern, $have_xml);
                        if (is_array($next_level) && count($next_level) > 0)
                            $fls = array_merge($fls, $next_level);
                    } else if (is_file($p)) {
                        if (preg_match($pattern, $item)) { // check if file name matches the pattern
                            $cur_rel_path = $this->joinUrl($relative_path, $item);
                            $fls[$cur_rel_path] = '';
                            if (strtolower($have_xml) === 'true') {
                                $xml_path = $this->joinUrl($relative_path, $this->getXmlFilePath($item));
                                if (is_file($this->joinUrl($path, $xml_path)) > 0)
                                    $fls[$cur_rel_path] = $this->joinUrl($relative_path, $xml_path);
                            }
                        }
                    }
                }
            }
        } else {
            eprintln("[ERROR] The path '$cur_path' is not a directory", "red");
            $fls = null;
        }
        return $fls;
    }

    function getXmlFilePath($path) {
        return strrpos($path, '.') > -1 ? substr($path, 0, strrpos($path, '.')) . '.xml' : $path . '.xml';
    }

    function getFileNameFromPath($path) {
        $arFN = explode('/', $path);
        return is_array($arFN) && count($arFN) > 1 ? $arFN[count($arFN) - 1] : $path;
    }

    function joinUrl($url1, $url2) {
        $res = strlen(trim($url1, '/ \\')) > 0 ? $url1 : '';
        $res .= strlen($res) > 0 ? '/' : '';
        $res .= trim($url2, '/ \\');
        return $res;
    }

    function removeLoggedFiles($ftpuid, $fls) {
        if (!(isset($ftpuid) && strlen($ftpuid) > 0 && isset($fls) && is_array($fls) && count($fls) > 0))
            return $fls; // Leave if no info found about the server or if there's nothing to remove
// Get all records from the LOGS_DETAILS table with the same FULL_PATH as $files
        require_once ( 'classes/model/FtpMonitorLogs.php' );
        require_once ( 'classes/model/FtpMonitorLogsDetails.php' );
        $c = new Criteria('workflow');
        $c->clear();
        $c->addSelectColumn(FtpMonitorLogsDetailsPeer::FULL_PATH);
        $c->add(FtpMonitorLogsDetailsPeer::STATUS, 'ERROR', Criteria::NOT_EQUAL);
        $c->add(FtpMonitorLogsDetailsPeer::FULL_PATH, array_keys($fls), Criteria::IN);
        $cond = array();
        $cond[] = array(FtpMonitorLogsDetailsPeer::FTP_LOG_UID, FtpMonitorLogsPeer::FTP_LOG_UID);
        $cond[] = array(FtpMonitorLogsPeer::FTP_UID, DBAdapter::getStringDelimiter() . $ftpuid . DBAdapter::getStringDelimiter());
        $c->addJoinMC($cond, Criteria::LEFT_JOIN);
        $oDataSet = FtpMonitorLogsDetailsPeer::DoSelectRs($c);
        $oDataSet->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($oDataSet->next()) {
            $row = $oDataSet->getRow();
            $full_path = $row['FULL_PATH'];
            unset($fls[$full_path]);
        }
        return $fls;
    }

    function processFilesFromFtp($fls, $cnn, $sett, $tempFolder) {
        if (isset($sett) && is_array($sett)) {
            $host = isset($sett['HOST']) ? $sett['HOST'] : '';
            $user_id = isset($sett['DEL_USER_UID']) ? $sett['DEL_USER_UID'] : '';
            $processId = isset($sett['PRO_UID']) ? $sett['PRO_UID'] : '';
            $taskId = isset($sett['TAS_UID']) ? $sett['TAS_UID'] : '';
            $input_document_uid = isset($sett['INPUT_DOCUMENT_UID']) ? $sett['INPUT_DOCUMENT_UID'] : '';
            require_once ( 'classes/model/Users.php' );
            $user = UsersPeer::retrieveByPK($user_id);
            if (isset($user) && is_object($user)) {
                eprint(" ---- Logging to " . $this->getDefaultEndpoint() . " as user " . $user->getUsrUsername(), "white");
                $client = new SoapClient($this->getDefaultEndpoint());
                $result = $client->__SoapCall('login', array(array('userid' => $user->getUsrUsername(), 'password' => 'md5:' . $user->getUsrPassword())));
                if ($result->status_code == 0) {
                    $sessionId = $result->message;
                    $log_id = $this->saveFtpMonitorLogs($fls, $sett['FTP_UID']);
                    if (isset($log_id) && !is_null($log_id))
                        foreach ($fls as $path => $xml) {
                            $description = $this->copyFileFtp($cnn, $path, $host, $tempFolder);
                            $variables = array();
                            $app_uid = "";
                            $status = "ERROR";
                            if (strlen($description) == 0) {
// getting variables from the xml
                                if ($xml) {
                                    $xml_fn = $this->getFileNameFromPath($xml);
                                    if (ftp_get($cnn, $tempFolder . "/" . $xml_fn, "/" . $xml, FTP_BINARY))
                                        $variables = $this->getVariablesFromXml($tempFolder . "/" . $xml_fn);
                                    else
                                        eprint("Error uploading XML file.", "red");
                                }
// creating a case
                                eprint(" --> Creating the new case.............", "white");
                                $nc_res = $client->__SoapCall('NewCase', array(array('sessionId' => $sessionId, 'processId' => $processId, 'taskId' => $taskId, 'variables' => $variables)));
                                $status = "SUCCESS";
                                $description = "";
                                $app_uid = "";
                                if ($nc_res->status_code == 0) {
                                    eprintln("OK+ CASE #{$nc_res->caseNumber} was created!", 'green');
                                    $app_uid = $nc_res->caseId;
                                    eprint("Linking Input Document to the case #$nc_res->caseNumber.................", "white");
                                    $fn = $this->getFileNameFromPath($path);
                                    $this->linkInputDocument($nc_res->caseId, $input_document_uid, $tempFolder . "/" . $fn);
                                    eprint(" --> Routing the case #$nc_res->caseNumber..............", "white");
                                    $rc_res = $client->__SoapCall('RouteCase', array(array('sessionId' => $sessionId, 'caseId' => $nc_res->caseId, 'delIndex' => "1")));
                                    $msg_ar = explode(" --- ", $rc_res->message);

                                    if ($rc_res->status_code == 0)
                                        eprintln("OK+ $msg_ar[0]", 'green');
                                    else {
                                        $status = "OK";
                                        $description = "Error #$rc_res->status_code routing the case: $msg_ar[0]";
                                        eprintln($description, 'red');
                                    }
                                } else {
                                    $status = "ERROR";
                                    $description = "Error #$result->status_code creating the case: $nc_res->message";
                                    eprintln($description, "red");
                                }
                            }
                            if ($description != "") {
                                eprintln("[ERROR: $description", "red");
                            }
                            $det_id = $this->saveFtpMonitorLogsDetails($log_id, $app_uid, $path, $xml, json_encode($variables), $status, $description);
                        }
                } else
                    eprintln("[ERROR]: " . $result->message, "red");
            }
        }
    }

    function copyFileFtp($cnn, $fl, $ftpServerPath, $tempFolder) {
        $value = "";
        $fn = $this->getFileNameFromPath($fl);
        eprintln(" ---- Copying $fl from $ftpServerPath to $tempFolder/$fn with $cnn", "white");
        try {
            if (!ftp_get($cnn, $tempFolder . "/" . $fn, "/" . $fl, FTP_BINARY))
                return "Some error has occured while copying the file.";
        } catch (Exception $ex) {
            return $ex;
        }
        return $value;
    }

    function processFilesFromSharedFolder($fls, $sett, $tempFolder) {
        if (!@is_dir($tempFolder))
            if (!$this->makedir($tempFolder)) {
                eprintln("[ERROR]: Cannot create directory $tempFolder.", "red");
                return null;
            }
        if (isset($sett) && is_array($sett)) {
            $user_id = isset($sett['DEL_USER_UID']) ? $sett['DEL_USER_UID'] : '';
            $processId = isset($sett['PRO_UID']) ? $sett['PRO_UID'] : '';
            $taskId = isset($sett['TAS_UID']) ? $sett['TAS_UID'] : '';
            $input_document_uid = isset($sett['INPUT_DOCUMENT_UID']) ? $sett['INPUT_DOCUMENT_UID'] : '';
            require_once ( 'classes/model/Users.php' );
            $user = UsersPeer::retrieveByPK($user_id);
            if (isset($user) && is_object($user)) {
                eprint(" - Logging to " . $this->getDefaultEndpoint() . " as user " . $user->getUsrUsername() . ".............", "white");
                $client = new SoapClient($this->getDefaultEndpoint());
                $result = $client->__SoapCall('login', array(array('userid' => $user->getUsrUsername(), 'password' => 'md5:' . $user->getUsrPassword())));
                if ($result->status_code == 0) {
                    eprint("[OK]", "green");
                    $sessionId = $result->message;
                    $log_id = $this->saveFtpMonitorLogs($fls, $sett['FTP_UID']);
                    if (isset($log_id) && !is_null($log_id))
                        foreach ($fls as $path => $xml) {
                            $p = $this->joinUrl($tempFolder, $path);
                            $description = $this->copyFileSharedFolder($this->joinUrl($sett['FTP_PATH'], $path), $tempFolder, $path);
                            $variables = array();
                            $status = "ERROR";
                            $app_uid = "";
                            if (strlen($description) == 0) {
                                // getting variables from the xml
                                if ($xml) {
                                    $xml_fn = $this->getFileNameFromPath($xml);
                                    $xml_path = $this->joinUrl($tempFolder, $xml_fn);
                                    if (copy($this->getXmlFilePath($this->joinUrl($sett['FTP_PATH'], $path)), $xml_path))
                                        $variables = $this->getVariablesFromXml($xml_path);
                                    else
                                        eprint("Error uploading XML file.", "red");
                                }
                                eprint(" --> Creating the new case.............", "white"); // creating a case
                                $nc_res = $client->__SoapCall('NewCase', array(array('sessionId' => $sessionId, 'processId' => $processId, 'taskId' => $taskId, 'variables' => $variables)));
                                $status = "SUCCESS";
                                $description = "";
                                $app_uid = "";
                                if ($nc_res->status_code == 0) {
                                    eprintln("OK+ CASE #{$nc_res->caseNumber} was created!", 'green');
                                    $app_id = $nc_res->caseId;
                                    eprint("Linking Input Document to the case #$nc_res->caseNumber.................", "white");
                                    $fn = $this->getFileNameFromPath($path);
                                    $this->linkInputDocument($nc_res->caseId, $input_document_uid, $tempFolder . "/" . $fn);
                                    eprint(" --> Routing the case #$nc_res->caseNumber..............", "white");
                                    $rc_res = $client->__SoapCall('RouteCase', array(array('sessionId' => $sessionId, 'caseId' => $nc_res->caseId, 'delIndex' => "1")));
                                    $msg_ar = explode(" --- ", $rc_res->message);

                                    if ($rc_res->status_code == 0)
                                        eprintln("OK+ $msg_ar[0]", 'green');
                                    else {
                                        $status = "OK";
                                        $description = "Error #$rc_res->status_code while routing the case: $msg_ar[0]";
                                        eprintln($description, 'red');
                                    }
                                } else {
                                    $status = "ERROR";
                                    $description = "Error #$nc_res->status_code while creating the case: $nc_res->message";
                                    eprintln($description, "red");
                                }
                                $det_id = $this->saveFtpMonitorLogsDetails($log_id, $app_uid, $path, $xml, json_encode($variables), $status, $description);
                            }
                        }
                } else
                    eprintln("[ERROR]: " . $result->message, "red");
            }
        }
    }

    function copyFileSharedFolder($path, $tempFolder, $relativePath) {
        $value = "";
        if (!@is_dir($tempFolder))
            if (!$this->makedir($tempFolder)) {
                eprintln("[ERROR]: Cannot create directory $tempFolder.", "red");
                return "[ERROR]: Cannot create directory $tempFolder.";
            }
        $fn = $this->getFileNameFromPath($path);
        $p = $tempFolder . "/" . $fn;
        eprint(" -- Copying $fn from $path to $p", "white");
        try {
            if (copy($path, $p))
                eprintln("[OK]", "green");
            else {
                eprintln("[ERROR]", "red");
                $value = "Some error has occured while copying the file.";
            }
        } catch (Exception $ex) {
            $value = $ex;
        }
        return $value;
    }

    function linkInputDocument($caseId, $input_document_uid, $local_temp_file) {
        $params = array(
        'APPLICATION' => $caseId,
        'INDEX' => "1",
        'USR_UID' => "-1",
        'DOC_UID' => $input_document_uid,
        'APP_DOC_TYPE' => "INPUT",
        'APP_DOC_COMMENT' => "Uploaded from FTP Monitor",
        'ATTACH_FILE' => "@$local_temp_file"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getDefaultUploadService());
//curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch);
        curl_close($ch);
        eprintln(" -->>" . $response, "white");
        unlink($local_temp_file);
    }

    function saveFtpMonitorLogs($files, $ftp_uid) {
        $todayDate = date("y.m.d");
        $todayTime = date("H:i:s");
        $serializeResult = serialize($files);
        $uniqueFtpLogUid = rand();

        require_once ( "classes/model/FtpMonitorLogs.php" );

//if exists the row in the database propel will update it, otherwise will insert.
        $tr = FtpMonitorLogsPeer::retrieveByPK($uniqueFtpLogUid);
        if (!( is_object($tr) && get_class($tr) == 'FtpMonitorLogs' )) {
            $tr = new FtpMonitorLogs();
        }
        $tr->setFtpLogUid($uniqueFtpLogUid);
        $tr->setFtpUid($ftp_uid);
        $tr->setExecutionDate($todayDate);
        $tr->setExecutionTime($todayTime);
        $tr->setExecutionDatetime($todayDate . " " . $todayTime);
        $tr->setResult($serializeResult);

        if ($tr->validate()) {
            try {
                $res = $tr->save();
            } catch (PropelException $ex) {
                $res = $ex->getMessage();
            }
            return $uniqueFtpLogUid;
        }
        return null;
    }

    function getVariablesFromXml($xml_file) {
        $variable = array();
        if (file_exists($xml_file)) {
            $xml = simplexml_load_file($xml_file);
            eprint("Passing variable(s): ", "white");
            foreach ($xml->children() as $child) {
                $objName = new variableStruct();
                $objName->name = $child->getName();
                $objName->value = $child;
                $variable[] = $objName;
                eprint("@@$objName->name = '$objName->value'", "white");
            }
            unlink($xml_file);
        }
        return $variable;
    }

    function saveFtpMonitorLogsDetails($logs_id, $app_uid, $path, $have_xml, $variables, $status, $description) {
        $todaydatetime = date("y.m.d H:i:s");
        $det_id = rand();

        require_once ( "classes/model/FtpMonitorLogsDetails.php" );
        $tr = FtpMonitorLogsDetailsPeer::retrieveByPK($det_id);
        if (!( is_object($tr) && get_class($tr) == 'FtpMonitorLogsDetails' )) {
            $tr = new FtpMonitorLogsDetails();
        }
        $tr->setFtpLogDetUid($det_id);
        $tr->setFtpLogUid($logs_id);
        $tr->setAppUid($app_uid);
        $tr->setExecutionDatetime($todaydatetime);
        $tr->setFullPath($path);
        $tr->setHaveXml(strlen($have_xml) > 0 ? "TRUE" : "FALSE");
        $tr->setVariables($variables);
        $tr->setStatus($status);
        $tr->setDescription($description);
        if ($tr->validate()) {
            $res = "";
            try {
                $res = $tr->save();
            } catch (PropelException $ex) {
                $res = $ex->getMessage();
            }
// Update FTP_MONITOR_LOGS counters of succeeede, failed, and processed couners
            require_once ( 'classes/model/FtpMonitorLogs.php' );
            $logs = FtpMonitorLogsPeer::retrieveByPK($logs_id);
            if (isset($logs) && is_object($logs)) {
                $logs->setProcessed($logs->getProcessed() + 1);
                if ($status === "SUCCESS" || $status === "OK")
                    $logs->setSucceeded($logs->getSucceeded() + 1);
                else
                    $logs->setFailed($logs->getFailed() + 1);
            }
            try {
                $logs->save();
            } catch (PropelException $ex) {
                $res = $ex->getMessage();
            }
            return $det_id;
        }
        return null;
    }

    function makedir($struct) {
        $ar = explode("/", $struct);
        $path = "";
        if (isset($ar) && is_array($ar))
            foreach ($ar as $v)
                if (strlen($v) > 0) {
                    $res = strpos(strtolower(PHP_OS), "win"); // in Windows results in 0, in Unix-like results in false
                    if ($res === 0) // for Windows
                        $path .= (strlen($path) > 0) ? PATH_SEP . $v : $v;
                    else if (!$res) // for Unix-like
                        $path .= PATH_SEP . $v;
                    if (!@is_dir($path))
                        if (!@mkdir($path, 0777, true))
                            return false;
                }
        return true;
    }

}

class variableStruct {

    public $name;
    public $value;

}

