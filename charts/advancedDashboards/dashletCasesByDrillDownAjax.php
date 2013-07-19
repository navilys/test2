<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");





function caseDataList($category, $status, $r, $i, $process_uid, $task_uid, $user_uid, $group_uid, $department_uid)
{  $data = CaseLibrary::caseData(1, $category, $status, $status, $process_uid, $task_uid, $user_uid, $group_uid, $department_uid);
   
   return (array(count($data), array_slice($data, $i, $r)));
}





$option = $_POST["option"];

$response = array();

switch ($option) {  
  case "DATA":
    $category = (!empty($_POST["category"]))? $_POST["category"] : null;
    $appStatus = $_POST["appStatus"];
    $process_uid = $_POST["process_uid"];
    $task_uid = $_POST["task_uid"];
    $user_uid = $_POST["user_uid"];
    $group_uid = $_POST["group_uid"];
    $department_uid = $_POST["department_uid"];
    
    ///////
    $status = 1;
    
    try {
      list($numRec, $caseData) = caseDataList($category, $appStatus, intval($_POST["iDisplayLength"]), intval($_POST["iDisplayStart"]), $process_uid, $task_uid, $user_uid, $group_uid, $department_uid);
      
      $response["sEcho"] = intval($_POST["sEcho"]);
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