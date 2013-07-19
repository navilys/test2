<?php

if (($RBAC_Response = $RBAC->userCanAccess("PM_LOGIN")) != 1)
    return $RBAC_Response;
global $RBAC;

$G_MAIN_MENU = 'workflow';
$G_SUB_MENU = 'ftpMonitorLogsList';
$G_ID_MENU_SELECTED = '';
$G_ID_SUB_MENU_SELECTED = '';

try {
    $oHeadPublisher = &headPublisher::getSingleton();
    $oHeadPublisher->addContent("pmFtpMonitor/page"); //Adding a html file .html.
    $uid = isset($_GET["uid"]) ? $_GET["uid"] : 0;
    $page = ($uid === 0) ? "List" : "Details";
    $oHeadPublisher->addExtJsScript("pmFtpMonitor/ftpMonitorLogs$page", false); //Adding a javascript file .js
    $config = array();
    $config["pageSize"] = 15;
    $oHeadPublisher->assign("CONFIG", $config);
    $oHeadPublisher->assign("FTP_LOG_UID", $uid);
    G::RenderPage("publish", "extJs");
} catch (Exception $e) {
    $G_PUBLISH = new Publisher;
    $aMessage["MESSAGE"] = $e->getMessage();
    $G_PUBLISH->AddContent("xmlform", "xmlform", "pmFtpMonitor/messageShow", "", $aMessage);
    G::RenderPage("publish", "blank");
}