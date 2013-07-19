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

$js = (isset($_GET["id"]))? "(function () {  parent.parent.window.location.href = 'http://" . $_SERVER["SERVER_NAME"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main?s=KTDMS';  })();" : null;

$headPublisher->addScriptCode($js);

///////
$G_PUBLISH = new Publisher;

$fields = $oPluginRegistry->getFieldsForPageSetup($details->sNamespace);
$G_PUBLISH->AddContent("xmlform", "xmlform", $xmlform, "", $fields, $externalSetup . "pluginsSetupSave?id=" . $pluginFile);

///////
G::RenderPage("publishBlank", "blank");
?>