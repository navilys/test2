<?php
G::LoadClass("plugin");

require_once ("class.knowledgeTree.php");

$pluginFile = "knowledgeTree.php";

$externalSetup= null;
if (!(isset($details->sPluginFolder))) {
    $oPluginRegistry = &PMPluginRegistry::getSingleton();
    $details = $oPluginRegistry->getPluginDetails($pluginFile);
    $externalSetup = "../setup/";
}

$xmlform = isset($details->sPluginFolder)? $details->sPluginFolder . "/" . "setupPage" : "knowledgeTree/setupPage";

$knowledgeTreeClass = new KnowledgeTreeClass();

$swCnn = $knowledgeTreeClass->validateWSDLConnectivity();

///////
$headPublisher = &headPublisher::getSingleton();

//$headPublisher->addScriptFile("/jscore/cases/core/cases_Step.js");
$location = (substr(SYS_SKIN, 0, 2) == 'ux') ? "/main?s=KTDMS":"/setup/main?s=KTDMS";
$js = (isset($_GET["id"]))? "(function () {  parent.parent.window.location.href = 'http://" . $_SERVER["SERVER_NAME"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . $location."';  })();" : null;

$headPublisher->addScriptCode($js);
$fields = $oPluginRegistry->getFieldsForPageSetup($details->sNamespace);
G::LoadClass( 'configuration' );
$conf = new Configurations();
try {
    $preferencesKt = $conf->getConfiguration( 'KT_PREFERENCES', '' );
} catch (Exception $e) {
    $preferencesKt = array ();
}
if (isset($preferencesKt['KT_WIN'])) {
    $fields['KT_WIN'] = ($preferencesKt['KT_WIN'] == 'on') ? true : false;
}
///////
$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent("xmlform", "xmlform", $xmlform, "", $fields, $externalSetup . "pluginsSetupSave?id=" . $pluginFile);

///////
G::RenderPage("publishBlank", "blank");

