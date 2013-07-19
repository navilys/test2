<?php
$oCriteria = new Criteria("workflow");

//SELECT
$oCriteria->addSelectColumn(CaseConsolidatedPeer::CON_STATUS);
//FROM
//WHERE
$oCriteria->add(CaseConsolidatedPeer::CON_STATUS, "ACTIVE");

$activeNumRows = CaseConsolidatedPeer::doCount($oCriteria);


global $G_TMP_MENU;

G::loadClass("pmFunctions");

$usrUid = $_SESSION["USER_LOGGED"];

$sql = "SELECT COUNT(APP_CACHE_VIEW.TAS_UID) AS QTY
        FROM   CASE_CONSOLIDATED
               LEFT JOIN CONTENT ON (CASE_CONSOLIDATED.TAS_UID = CONTENT.CON_ID)
               LEFT JOIN APP_CACHE_VIEW ON (CASE_CONSOLIDATED.TAS_UID = APP_CACHE_VIEW.TAS_UID)
               LEFT JOIN TASK ON (CASE_CONSOLIDATED.TAS_UID = TASK.TAS_UID)
        WHERE  CONTENT.CON_CATEGORY = 'TAS_TITLE' AND
               CONTENT.CON_LANG = 'en' AND
               APP_CACHE_VIEW.DEL_THREAD_STATUS = 'OPEN' AND
               APP_CACHE_VIEW.APP_STATUS = 'TO_DO' AND
               USR_UID = '{$usrUid}'";
$resultSQL = executeQuery($sql);
$numRows = ($activeNumRows > 0)? $resultSQL[1]["QTY"] : 0;
    
$tmpId      = array();
$tmpTypes   = array();
$tmpEnabled = array();
$tmpOptions = array(); //link
$tmpLabels  = array();
$tmpJS      = array();
$tmpIcons   = array();
$tmpEClass  = array();

$i = 0;
$k = 0;

foreach ($G_TMP_MENU->Id as $index => $value) {
  if ($index == 3) { //Option CASES_INBOX
    $tmpId[$index + $k]      = "CASE_CONSOLIDATED";
    $tmpTypes[$index + $k]   = "plugins";
    $tmpEnabled[$index + $k] = 1;
    $tmpOptions[$index + $k] = '../../plugins/pmConsolidatedCL/consolidatedList?action=consolidated'; //link
    $tmpLabels[$index + $k]  = htmlentities("Batch Routing (<span id=\"conTotalCount\">" . $numRows . "</span>)");
    $tmpJS[$index + $k]      = null;
    $tmpIcons[$index + $k]   = null;
    $tmpEClass[$index + $k]  = null;
   
    $k = 1;
  }

  $tmpId[$index + $k]      = $G_TMP_MENU->Id[$index];
  $tmpTypes[$index + $k]   = $G_TMP_MENU->Types[$index];
  $tmpEnabled[$index + $k] = $G_TMP_MENU->Enabled[$index];
  $tmpOptions[$index + $k] = $G_TMP_MENU->Options[$index]; //link
  $tmpLabels[$index + $k]  = $G_TMP_MENU->Labels[$index];
  $tmpJS[$index + $k]      = $G_TMP_MENU->JS[$index];
  $tmpIcons[$index + $k]   = $G_TMP_MENU->Icons[$index];
  $tmpEClass[$index + $k]  = $G_TMP_MENU->ElementClass[$index];
  
  $i = $index + $k;
}

//$i = $i + 1;

$G_TMP_MENU->Id      = $tmpId;
$G_TMP_MENU->Types   = $tmpTypes;
$G_TMP_MENU->Enabled = $tmpEnabled;
$G_TMP_MENU->Options = $tmpOptions; //link
$G_TMP_MENU->Labels  = $tmpLabels;
$G_TMP_MENU->JS      = $tmpJS;
$G_TMP_MENU->Icons   = $tmpIcons;
$G_TMP_MENU->ElementClass = $tmpEClass;
?>