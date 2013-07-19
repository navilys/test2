<?php 
G::LoadClass( "plugin");

require_once (PATH_PLUGINS . "sigplus" . PATH_SEP . "class.sigplus.php");
$pluginObj = new sigplusClass();

require_once ("classes/model/SigplusSigners.php");
require_once ("classes/model/Step.php");
require_once ("classes/model/OutputDocument.php");

//Get the sigplus step
$stepUidObj = $_GET["sigid"];
$stepUid = $_GET["stepid"];
$appUid  = $_SESSION["APPLICATION"];

$pluginObj->generateHtmlPdf($stepUidObj, $stepUid, $appUid);

exit(0);
?>