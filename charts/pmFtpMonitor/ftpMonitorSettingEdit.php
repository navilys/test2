<?php

if (($RBAC_Response = $RBAC->userCanAccess("PM_LOGIN")) != 1)
    return $RBAC_Response;
global $RBAC;

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
try {
    $oHeadPublisher = &headPublisher::getSingleton();
    //$oHeadPublisher->addContent("pmFtpMonitor/ftpMonitorSettingEdit"); //Adding a html file .html.
    $oHeadPublisher->addExtJsScript("pmFtpMonitor/ftpMonitorSettingEdit", false); //Adding a javascript file .js

    require_once( PATH_PLUGINS . 'pmFtpMonitor/class.pmFtpMonitor.php');
    require_once (PATH_PLUGINS . 'pmFtpMonitor/classes/model/FtpMonitorSetting.php');
    $rows = pmFtpMonitorClass::getFtpMonitorSettings($uid);
    foreach (FtpMonitorSettingPeer::getFieldNames() as $f) {
        $fn = ltrim(ltrim(FtpMonitorSettingPeer::translateFieldName($f, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME), FtpMonitorSettingPeer::TABLE_NAME), '.');
        $oHeadPublisher->assign($fn, isset($rows[$fn]) ? $rows[$fn] : '');
    }
    require_once ( 'classes/model/Users.php' );
    $user = UsersPeer::retrieveByPK(isset($rows["DEL_USER_UID"]) ? $rows["DEL_USER_UID"] : 0);
    $oHeadPublisher->assign("DEL_USER_LOGIN", (isset($user) && is_object($user)) ? $user->getUsrUsername() : '');
    G::RenderPage("publish", "extJs");
} catch (Exception $e) {
    $G_PUBLISH = new Publisher;

    $aMessage["MESSAGE"] = $e->getMessage();
    $G_PUBLISH->AddContent("xmlform", "xmlform", "pmFtpMonitor/messageShow", "", $aMessage);
    G::RenderPage("publish", "blank");
}