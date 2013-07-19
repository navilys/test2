<?php
require_once ("classes/model/Dynaform.php");

//require_once (PATH_PLUGINS."pmConsolidatedCL/classes/model/CaseConsolidated.php");
//require_once (PATH_PLUGINS."pmConsolidatedCL/classes/model/CaseConsolidatedPeer.php");
require_once ("classes/model/CaseConsolidatedPeer.php");
require_once ("classes/model/CaseConsolidated.php");
//$_SESSION["cDhTajE2T2lxSkhqMzZUTXVacWYyNcKwV3A4eWYybDdyb1p3"]["TAS_UID"] = "6395712624d42bd40106f20011047729"; //quitar
//$_SESSION["PROCESS"] = "6101674954d41ebecc01216083572251"; //quitar

global $G_PUBLISH;

$aFields = array();

$oCaseConsolidated = CaseConsolidatedPeer::retrieveByPK($_SESSION["cDhTajE2T2lxSkhqMzZUTXVacWYyNcKwV3A4eWYybDdyb1p3"]["TAS_UID"]);
if ((is_object($oCaseConsolidated)) && get_class($oCaseConsolidated) == "CaseConsolidated") {
  require_once ("classes/model/ReportTable.php");
  
  $aFields["CON_STATUS"]  = $oCaseConsolidated->getConStatus();
  $aFields["DYN_UID"]     = $oCaseConsolidated->getDynUid();
  $aFields["REP_TAB_UID"] = $oCaseConsolidated->getRepTabUid();
  
  $oReportTables = new ReportTable();
  $oReportTables = $oReportTables->load($aFields["REP_TAB_UID"]);
  if (count($oReportTables)>0) {
    if ($oReportTables['REP_TAB_STATUS'] == 'ACTIVE') {
      $aFields["TABLE_NAME"] = $oReportTables['REP_TAB_NAME'];
      $aFields["TITLE"] = $oReportTables['REP_TAB_TITLE'];
    }
  }
}

$aFields["PRO_UID"]  = $_SESSION["PROCESS"];
$aFields["TAS_UID"]  = $_SESSION["cDhTajE2T2lxSkhqMzZUTXVacWYyNcKwV3A4eWYybDdyb1p3"]["TAS_UID"];
$aFields["SYS_LANG"] = SYS_LANG;
$aFields['INDEX']    = 0;
$aFields["TABLE_NAME_DEFAULT"] = "__" . $aFields["TAS_UID"];

//$this->setAppUid (G::generateUniqueID());
/*
SELECT
  DISTINCT
  DYNAFORM.DYN_UID,
  CONTENT.CON_VALUE
FROM
  DYNAFORM AS DFRM
  LEFT JOIN CONTENT ON (DYNAFORM.DYN_UID = CONTENT.CON_ID AND CONTENT.CON_CATEGORY = 'DYN_TITLE' AND CONTENT.CON_LANG = 'en')
WHERE
  DYNAFORM.PRO_UID = @@PRO_UID AND
  DYNAFORM.DYN_TYPE = 'grid'
ORDER BY CONTENT.CON_VALUE
*/
$oCriteria = new Criteria("workflow");
$del = DBAdapter::getStringDelimiter();

//SELECT
$oCriteria->setDistinct();
$oCriteria->addSelectColumn(DynaformPeer::DYN_UID);
$oCriteria->addSelectColumn(ContentPeer::CON_VALUE);
//FROM
//$oCriteria->addMultipleJoin(array(array(DynaformPeer::DYN_UID, ContentPeer::CON_ID), //not ok
//                                  array(ContentPeer::CON_CATEGORY, "DYN_TITLE"), //not ok
//                                  array(ContentPeer::CON_LANG, "en")), //not ok
//                           Criteria::LEFT_JOIN); //not ok
//$oCriteria->addJoin(array(DynaformPeer::DYN_UID, ContentPeer::CON_ID),
//                    array(ContentPeer::CON_CATEGORY, "DYN_TITLE"),
//                    array(ContentPeer::CON_LANG, "en"),
//                    Criteria::LEFT_JOIN);
$aConditions   = array();
$aConditions[] = array(DynaformPeer::DYN_UID, ContentPeer::CON_ID);
$aConditions[] = array(ContentPeer::CON_CATEGORY, $del . "DYN_TITLE" . $del);
$aConditions[] = array(ContentPeer::CON_LANG, $del . "en" . $del);
$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
//WHERE
$oCriteria->add(DynaformPeer::PRO_UID, $_SESSION["PROCESS"]);
$oCriteria->add(DynaformPeer::DYN_TYPE, "grid");
//ORDER BY X ASC
$oCriteria->addAscendingOrderByColumn(ContentPeer::CON_VALUE);
    
//echo "<hr />" . $oCriteria->toString() . "<hr />";
    
//query
//doCount(Criteria $criteria, $distinct = false, $con = null)
$numRows = DynaformPeer::doCount($oCriteria);

if ($numRows > 0) {
  $G_PUBLISH->AddContent("xmlform", "xmlform", "pmConsolidatedCL/consAdmin", null, $aFields);
}
else {
  echo "<div style=\"margin:1em;\"><strong>Alert</strong><br />The process has no type template Dynaform grid, this Dynaform is required by the plugin</div>";
  exit(0);
}
?>