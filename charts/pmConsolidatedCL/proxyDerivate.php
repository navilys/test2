<?php
G::LoadClass('wsBase');
G::LoadClass('case');

$userUid = $_SESSION ['USER_LOGGED'];

//$appUid   = '5600280764d40f2a16e31d3006742700';
$appUid    = $_POST['appUid'];
$appNumber = $_POST['appNumber'];
$delIndex  = $_POST['delIndex'];

$response = array();
$ws = new wsBase();
$oCase = new Cases();

if (!isset($Fields['DEL_INIT_DATE'])) {
  $oCase->setDelInitDate($appUid, $delIndex);
  $aFields = $oCase->loadCase($appUid, $delIndex);
}

//@$res = $ws->derivateCase($userUid, $appUid, $delIndex, true);
$res = $ws->derivateCase($userUid, $appUid, $delIndex, true);
$messageDerivateCase = '';
if (is_array($res)) {
  $messageDerivateCase = '<ul type="square">';
  if (count($res['routing']) > 0) {
    foreach ($res['routing'] as $k => $field) {
      $messageDerivateCase .= "<li>".$res['routing'][$k]->taskName ." - ".$res['routing'][$k]->userName ;
    }
  }
  else {
    $messageDerivateCase = explode('-',$res['message']);
    $messageDerivateCase = "<li>".$messageDerivateCase[0];
  }
  $messageDerivateCase .= "</ul>";
}

G::loadClass("pmFunctions");

$oCriteria = new Criteria("workflow");
$oCriteria->addSelectColumn(CaseConsolidatedPeer::CON_STATUS);
$oCriteria->add(CaseConsolidatedPeer::CON_STATUS, "ACTIVE");
$activeNumRows = CaseConsolidatedPeer::doCount($oCriteria);

$usrUid = $_SESSION["USER_LOGGED"];

$sql = "SELECT COUNT(APP_CACHE_VIEW.TAS_UID) AS QTY
        FROM   CASE_CONSOLIDATED
               LEFT JOIN CONTENT ON (CASE_CONSOLIDATED.TAS_UID = CONTENT.CON_ID)
               LEFT JOIN APP_CACHE_VIEW ON (CASE_CONSOLIDATED.TAS_UID = APP_CACHE_VIEW.TAS_UID)
               LEFT JOIN TASK ON (CASE_CONSOLIDATED.TAS_UID = TASK.TAS_UID)
        WHERE  CONTENT.CON_CATEGORY = 'TAS_TITLE' AND
               CONTENT.CON_LANG = 'en' AND
               APP_CACHE_VIEW.DEL_THREAD_STATUS = 'OPEN' AND
               USR_UID = '{$usrUid}'";

$resultSQL = executeQuery($sql);

$totalCount = 0;

foreach ($resultSQL as $item) {
  $totalCount = $totalCount + $item["QTY"];
}

$numRows = ($activeNumRows > 0)? $totalCount : 0;

$response["conCount"] = $numRows;

if (is_array($res)) {
  $response ["message"] = "<b>".G::LoadTranslation("ID_CASE") . " " . $appNumber . "</b>   Summary of Derivations: <br> " . $messageDerivateCase;
} else {
  $response ["message"] = G::LoadTranslation("ID_CASE") . " " . $appNumber . " " . $res->message;
}

echo G::json_encode($response);
?>