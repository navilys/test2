<?php
require_once ("classes/model/Step.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/Content.php");

require_once (PATH_PLUGINS . "sigplus" . PATH_SEP . "class.sigplus.php");

$pluginObj = new sigplusClass();

//Get the step row
$oCriteria = new Criteria("workflow");
$oCriteria->add(StepPeer::STEP_UID, $_GET["STP_UID"]);
$oDataset = StepPeer::doSelectRS ($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aRow = $oDataset->getRow();
  
$oCriteria = new Criteria("workflow");

$oCriteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_UID);
$oCriteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_GENERATE);
$oCriteria->addSelectColumn(ContentPeer::CON_VALUE);
$oCriteria->addJoin(OutputDocumentPeer::OUT_DOC_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN);
$oCriteria->add(ContentPeer::CON_CATEGORY, "OUT_DOC_TITLE", Criteria::EQUAL);
  
$oDataset = OutputDocumentPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
  
$aDoc = array(); 
$aDoc[] = array("id" => "char", "name" => "char");

while ($oRow = $oDataset->getRow()) {
  $aDoc[] = array("id" => $oRow["OUT_DOC_UID"], "name" => $oRow["CON_VALUE"]);
  $oDataset->next();
}

$contentType   = null;
$contentLayout = null;
$contentName   = null;
$contentData   = array();
$contentTarget = null;

if (count($aDoc) > 1) {
  require_once ("classes/model/SigplusSigners.php");
  G::LoadClass("ArrayPeer");
  global $_DBArray;

  $_DBArray["docList"]  = $aDoc;
  $_SESSION["_DBArray"] = $_DBArray;

  $oCriteria = new Criteria("dbarray");
  $oCriteria->setDBArrayTable("docList");

  //If exists the row in the database propel will update it, otherwise will insert.
  $pk = $aRow["STEP_UID"];
  $tr = SigplusSignersPeer::retrieveByPK($pk);
  
  if ((is_object($tr) && get_class($tr) == "SigplusSigners")) {
    $tarray = array();
    $tarray = unserialize($tr->getSigSigners());
    $contentData["SIGNERS_GRID"] = unserialize($tr->getSigSigners());
    $contentData["PRO_UID"] = $tr->getProUid();
    $contentData["DOC_UID"] = $tr->getDocUid();
    $contentData["STP_UID"] = $pk;
  }
  else {
    $contentData["PRO_UID"] = $aRow["PRO_UID"];
    $contentData["TAS_UID"] = $aRow["TAS_UID"];
    $contentData["STP_UID"] = $_GET["STP_UID"];
  }
  
  $contentType   = "xmlform";
  $contentLayout = "xmlform";
  $contentName   = "sigplus/sigplusSignersEdit";
  $contentTarget = "../sigplus/sigplusSigners/sigplusSignersSave";
}
else {
  $template = new TemplatePower(PATH_PLUGINS . "sigplus" . PATH_SEP . "messageShow.html");
  $template->prepare();
  $template->assign("TITLE",   "Message");
  $template->assign("MESSAGE", "There is no Output Document.<br />You must create at least one Output Document, which will be used by the plugin.");
  
  $contentType   = "template";
  $contentLayout = null;
  $contentName   = null;
  $contentData   = $template;
  $contentTarget = null;
}

$G_MAIN_MENU = "workflow";
$G_SUB_MENU  = "sigplusSigners";
$G_ID_MENU_SELECTED     = null;
$G_ID_SUB_MENU_SELECTED = null;

$G_PUBLISH = new Publisher;

$G_PUBLISH->AddContent($contentType, $contentLayout, $contentName, null, $contentData, $contentTarget);
G::RenderPage("publish", "raw");
?>