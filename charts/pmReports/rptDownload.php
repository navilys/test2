<?php
require_once ("classes/model/AdditionalTables.php");

$addtabUID = $_GET["sPmrUid"];

$oCriteria = new Criteria("workflow");

//SELECT
$oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
//FROM
//WHERE
$oCriteria->add(AdditionalTablesPeer::ADD_TAB_UID, $addtabUID);
   
//query
$oDataset = AdditionalTablesPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

$addtabName = null;

while ($oDataset->next()) {
  $row = $oDataset->getRow();
  
  $addtabName = $row["ADD_TAB_NAME"];
}

$xlsDir  = PATH_PLUGINS . "pmReports" . PATH_SEP . "public_html" . PATH_SEP . "generatedReports" . PATH_SEP;
$xlsName = $addtabUID . ".xls";

G::streamFile($xlsDir . $xlsName, true, $addtabName . "_" . date("Y-m-d") . ".xls");
?>