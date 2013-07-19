<?php
$option = $_POST["option"];

$response = array();

switch ($option) {  
  case "DATA":
    $sql = $_POST["sql"];
    
    ///////
    $status = 1;
    
    try {
      $cnn = Propel::getConnection("workflow");
      $stmt = $cnn->createStatement();
      
      $result = array();
      
      $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
      if ($rsSQL->getRecordCount() > 0) {
        while ($rsSQL->next()) {
          $row = $rsSQL->getRow();
          
          $result[] = $row;
        }
      }
      
      $r = intval($_POST["iDisplayLength"]);
      $i = intval($_POST["iDisplayStart"]);
      
      $result = array_slice($result, $i, $r);
      
      ///////
      $response["sEcho"] = intval($_POST["sEcho"]);
      $response["iTotalRecords"] = $rsSQL->getRecordCount(); //total records
      $response["iTotalDisplayRecords"] = $rsSQL->getRecordCount(); //total records, but with filter
      $response["aaData"] = $result;
    
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