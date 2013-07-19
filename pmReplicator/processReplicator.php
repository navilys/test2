<?php
$_SESSION['PROCESS'] = '4977961284d9b3af5620e52034800537';
G::loadClass('pmFunctions');
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
$G_PUBLISH = new Publisher();
$aFields = array();
$aFields["MESSAGE_DISPLAY"]="<span></span>";
if (isset($_SESSION["PROCESS_TRANSFER"]) || isset($_SESSION["TABLES_TRANSFER"])){
	$oTemplate = new TemplatePower(PATH_PLUGINS . 'pmReplicator' . PATH_SEP .'templates'.PATH_SEP .'errorMessage.html');
	$oTemplate->prepare();
	$bkSize= 58;
	$wrpSize=41;
	if (isset($_SESSION["PROCESS_TRANSFER"])){
		$iErrors=count($_SESSION["PROCESS_TRANSFER"]["ERROR"])==0? 1: count($_SESSION["PROCESS_TRANSFER"]["ERROR"]);
		$bkSize+=24*$iErrors;
		$wrpSize+=24*$iErrors;
	}
	if (isset($_SESSION["TABLES_TRANSFER"])){
		$bkSize+=24;
		$wrpSize+=24;
	}
	$sMessageType="tclBackMessageSuccessColor";
	$sMessageTitle="PROCESS COMPLETE";
	if (isset($_SESSION["PROCESS_TRANSFER"]["ERROR"]) || isset($_SESSION["TABLES_TRANSFER"]["ERROR"])){
		$sMessageType="tclBackMessageErrorColor";
		$sMessageTitle="ERRORS WHERE ENCOUNTERED";
	}
  	$oTemplate->assign("messageType",$sMessageType);
	$oTemplate->assign("headerMessage",$sMessageTitle);
  	$oTemplate->assign("bkSize",$bkSize);
  	$oTemplate->assign("msgWrappSize",$wrpSize);
  	if (isset($_SESSION["PROCESS_TRANSFER"]["ERROR"])){
  		$oTemplate->assign("optionalData","style='width:658px'");
  		foreach ($_SESSION["PROCESS_TRANSFER"]["ERROR"] as $error){
  			$oTemplate->newBlock("messages");
  			$oTemplate->assign("messageDisplay",$error);
  		}	
  	}else{
  		$oTemplate->newBlock("messages");
  		$oTemplate->assign("messageDisplay",$_SESSION["PROCESS_TRANSFER"]["DONE"]);
  	}
  	if (isset($_SESSION["TABLES_TRANSFER"]["ERROR"])){
  		$oTemplate->newBlock("messages");
  		$oTemplate->assign("messageDisplay",$_SESSION["TABLES_TRANSFER"]["ERROR"]);
  	}else{
  		$oTemplate->newBlock("messages");
  		$oTemplate->assign("messageDisplay",$_SESSION["TABLES_TRANSFER"]["DONE"]);	
  	}
  	$oPublish	= new Publisher();
  	$oPublish->AddContent ( 'template', '', '', '', $oTemplate );
  	$aFields["MESSAGE_DISPLAY"]=$oPublish->Parts[0]["RenderedContent"];
  	unset($_SESSION["PROCESS_TRANSFER"]);
  	unset($_SESSION["TABLES_TRANSFER"]);
}
$oTestPublish = new tclManageWorkSpaces();
$aWorkSpaces =$oTestPublish->getWorkSpaces();
 global $_DBArray;
 $_DBArray['workspaces'] = $aWorkSpaces;
 $_SESSION['_DBArray'] = $_DBArray;

$G_PUBLISH->AddContent('xmlform','xmlform', 'pmReplicator/processReplicator.xml', '', $aFields, 'processReplicatorSave');
G::RenderPage('publish','blank');
?>