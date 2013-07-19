<?php
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
               APP_CACHE_VIEW.APP_STATUS = 'TO_DO' AND
               USR_UID = '{$usrUid}'";
$resultSQL = executeQuery($sql);
$numRows = ($activeNumRows > 0)? $resultSQL[1]["QTY"] : 0;

$response["conCount"] = $numRows;

echo G::json_encode($response);
?>