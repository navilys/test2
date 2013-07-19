<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");





function caseDataList($status, $r, $i, $process_uid, $task_uid, $user_uid)
{  $data = CaseLibrary::caseData(1, null, $status, $status, $process_uid, $task_uid, $user_uid, null, null);
   
   return (array(count($data), array_slice($data, $i, $r)));
}





$option = $_REQUEST["option"];

$response = array();

switch ($option) {  
  case "DATA":
    $appStatus = $_REQUEST["appStatus"];
    $process_uid = $_REQUEST["process_uid"];
    $task_uid = $_REQUEST["task_uid"];
    $user_uid = (!empty($_REQUEST["user_uid"]))? $_REQUEST["user_uid"] : null;
    
    //$aColumns = array("engine", "browser", "platform", "version", "grade");
    
    ////Paging
    //$sLimit = null;
    //if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    //  $sLimit = "LIMIT " . mysql_real_escape_string($_GET['iDisplayStart']) . ", " . mysql_real_escape_string($_GET['iDisplayLength']);
    //}
    
    ////Ordering
    //$sOrder = null;
    //if (isset($_GET['iSortCol_0'])) {
    //  $sOrder = "ORDER BY ";
    //  for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
    //    if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
    //      $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "" . mysql_real_escape_string($_GET['sSortDir_'.$i]) . ", ";
    //    }
    //  }
    //  $sOrder = substr_replace($sOrder, "", -1);
    //  if ($sOrder == "ORDER BY") {
    //    $sOrder = null;
    //  }
    //}
    
    ////Filtering
    //$sWhere = null;
    //if ($_GET['sSearch'] != "") {
    //  $sWhere = "WHERE (";
    //  for ($i = 0; $i < count($aColumns); $i++) {
    //    $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    //  }
    //  $sWhere = substr_replace($sWhere, "", -3);
    //  $sWhere .= ')';
    //}
    ////Individual column filtering
    //for ($i = 0; $i < count($aColumns); $i++) {
    //  if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
    //    if ($sWhere == "") {
    //      $sWhere = "WHERE ";
    //    }
    //    else {
    //      $sWhere .= " AND ";
    //    }
    //    $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    //  }
    //}
    
    $status = 1;
    
    try {
      list($numRec, $caseData) = caseDataList($appStatus, intval($_REQUEST["iDisplayLength"]), intval($_REQUEST["iDisplayStart"]), $process_uid, $task_uid, $user_uid);
      
      $response["sEcho"] = intval($_REQUEST["sEcho"]);
      $response["iTotalRecords"] = $numRec; //total records
      $response["iTotalDisplayRecords"] = $numRec; //total records, but with filter
      $response["aaData"] = array();
    
      for ($i = 0; $i <= count($caseData) - 1; $i++) {
        $application_uid = $caseData[$i][0];
        $appStatus  = $caseData[$i][1];
        $appNumber  = $caseData[$i][2];
        $app_process_uid = $caseData[$i][3];
        $appProName      = $caseData[$i][4];
        $appDelIndex  = $caseData[$i][5];
        $app_task_uid = $caseData[$i][6];
        $appTaskName  = $caseData[$i][7];
        $app_sentby_user_uid = $caseData[$i][8];
        $appSentbyUsrName    = $caseData[$i][9];
        $app_current_user_uid = $caseData[$i][10];
        $appCurrentUsrName    = $caseData[$i][11];
        $appDelTaskDueDate    = $caseData[$i][12];
        
        switch ($appStatus) {
          case "TO_DO": $appStatus = "To do"; break;
          case "DRAFT": $appStatus = "Draft"; break;
          case "COMPLETED": $appStatus = "Completed"; break;
        }
        
        $aux = explode(" ", $appDelTaskDueDate);
        $appDelTaskDueDate = $aux[0];
        
        $response["aaData"][] = array($application_uid, $appDelIndex, $appNumber, $appStatus, $appNumber, $appProName, $appTaskName, $appSentbyUsrName, $appCurrentUsrName, $appDelTaskDueDate);
      }
    
      ///////
      $response["status"] = "OK";
    }
    catch (Exception $e) {
      $response["message"] = $e->getMessage();
      $status = 0;
    }

    if ($status == 0) {
      $response["status"] = "ERROR";
    }
    break;
}

echo G::json_encode($response);
?>