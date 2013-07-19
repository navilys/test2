<?php
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "class.pmLicenseManager.php");
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AddonsStore.php");
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "class.enterpriseUtils.php");





AddonsStore::checkLicenseStore();

$translations = G::getTranslations(
    array(
        "ID_PM_ENV_SETTINGS_TITLE",
        "ID_PM_ENV_SETTINGS_USERFIELDSET_TITLE",
        "IS_USER_NAME_DISPLAY_FORMAT",
        "ID_SAVE_SETTINGS",
        "ID_LAN_UPDATE_DATE",
        "ID_SAVING_ENVIRONMENT_SETTINGS",
        "ID_ENVIRONMENT_SETTINGS_MSG_1",
        "ID_PM_ENV_SETTINGS_REGIONFIELDSET_TITLE",
        "ID_GLOBAL_DATE_FORMAT",
        "ID_PM_ENV_SETTINGS_CASESLIST_TITLE",
        "ID_CASES_ROW_NUMBER",
        "ID_CASES_DATE_MASK"
    )
);

$licenseManager = &pmLicenseManager::getSingleton();
$oHeadPublisher = &headPublisher::getSingleton();

if (isset($licenseManager->result) && $licenseManager->result == "OK") {
    $oHeadPublisher->assign("license_start_date", date("Y-m-d", $licenseManager->date["START"]));
    $oHeadPublisher->assign("license_end_date", $licenseManager->expireIn!="NEVER" ? date("Y-m-d",$licenseManager->date["END"]):"NA" );
    $oHeadPublisher->assign("license_user",     $licenseManager->info["FIRST_NAME"] . " " . $licenseManager->info["LAST_NAME"] . " (" . $licenseManager->info["DOMAIN_WORKSPACE"] . ")");
    $oHeadPublisher->assign("license_span",     $licenseManager->expireIn != "NEVER" ? ceil($licenseManager->date["SPAN"]/60/60/24) : "~");
    $oHeadPublisher->assign("license_name",     $licenseManager->type);
    $oHeadPublisher->assign("license_server",   $licenseManager->server);
    $oHeadPublisher->assign("license_expires",  $licenseManager->expireIn);
    $oHeadPublisher->assign("license_message",  $licenseManager->status["message"]);
    $oHeadPublisher->assign("licensed", true);
}
elseif (isset($licenseManager->info)) {
    $oHeadPublisher->assign("license_start_date", date("Y-m-d",$licenseManager->date["START"]));
    $oHeadPublisher->assign("license_end_date", date("Y-m-d",$licenseManager->date["END"]));
    $oHeadPublisher->assign("license_span", $licenseManager->expireIn != "NEVER" ? ceil($licenseManager->date["SPAN"]/60/60/24) : "~");
    $oHeadPublisher->assign("license_user", $licenseManager->info["FIRST_NAME"] . " " . $licenseManager->info["LAST_NAME"] . " (" . $licenseManager->info["DOMAIN_WORKSPACE"] . ")");
    $oHeadPublisher->assign("license_name", $licenseManager->type);
    $oHeadPublisher->assign("license_server",  $licenseManager->server);
    $oHeadPublisher->assign("license_expires", $licenseManager->expireIn);
    $oHeadPublisher->assign("license_message", $licenseManager->status["message"]);
    $oHeadPublisher->assign("licensed", false);
} else {
    $oHeadPublisher->assign("license_user", "");
    $oHeadPublisher->assign("license_name", "<b>Unlicensed</b>");
    $oHeadPublisher->assign("license_server",  "<b>no server</b>");
    $oHeadPublisher->assign("license_expires", "");

    $currentLicenseStatus = $licenseManager->getCurrentLicenseStatus();

    $oHeadPublisher->assign("license_message", $currentLicenseStatus["message"]);
    $oHeadPublisher->assign("license_start_date", "");
    $oHeadPublisher->assign("license_end_date", "");
    $oHeadPublisher->assign("license_span", "");
    $oHeadPublisher->assign("licensed", false);
}

G::LoadClass("system");

$oHeadPublisher->assign("PROCESSMAKER_VERSION", System::getVersion());
$oHeadPublisher->assign("PROCESSMAKER_URL", "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN );
$oHeadPublisher->assign("SYS_SKIN", SYS_SKIN);
$oHeadPublisher->assign("URL_PART_LOGIN", ((substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs")? "main/login" : "login/login"));
$oHeadPublisher->assign("URL_PART_SETUP", EnterpriseUtils::getUrlPartSetup());
$oHeadPublisher->assign("PATH_PLUGINS_WRITABLE", ((is_writable(PATH_PLUGINS))? 1 : 0));
$oHeadPublisher->assign("PATH_PLUGINS_WRITABLE_MESSAGE", "The directory " . PATH_PLUGINS . " have not writable.");
$oHeadPublisher->assign("TRANSLATIONS", $translations);
$oHeadPublisher->assign("SKIN_IS_UX", EnterpriseUtils::skinIsUx());
$oHeadPublisher->assign("INTERNET_CONNECTION", EnterpriseUtils::getInternetConnection());

$oHeadPublisher->addExtJsScript("enterprise/addonsStore", true);
G::RenderPage("publish", "extJs");

