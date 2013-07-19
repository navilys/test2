<?php
function consolidatedUpdate($dynaformUid, $dataUpdate)
{  G::LoadClass("case");
   G::LoadClass("pmFunctions");

   $delIndex = 1;
   $oCase = new Cases();

   $array = array();
   $array["form"] = (array)(G::json_decode($dataUpdate));

   $appUid = $array["form"]["APP_UID"];

   $fields = $oCase->loadCase($appUid);

   if (!isset($fields["DEL_INIT_DATE"])) {
     $oCase->setDelInitDate($appUid, $delIndex);
     //$aFields = $oCase->loadCase($appUid, $delIndex);
     $fields = $oCase->loadCase($appUid, $delIndex);
   }

   $auxAppDataApplication = $fields["APP_DATA"]["APPLICATION"];
   $auxAppDataProcess     = $fields["APP_DATA"]["PROCESS"];
   $auxAppDataTask        = $fields["APP_DATA"]["TASK"];
   $auxAppDataIndex       = $fields["APP_DATA"]["INDEX"];

   $fields["APP_DATA"] = array_merge($fields["APP_DATA"], G::getSystemConstants());
   $fields["APP_DATA"] = array_merge($fields["APP_DATA"], $array["form"]);

   $fields["APP_DATA"]["APPLICATION"] = $auxAppDataApplication;
   $fields["APP_DATA"]["PROCESS"]     = $auxAppDataProcess;
   $fields["APP_DATA"]["TASK"]        = $auxAppDataTask;
   $fields["APP_DATA"]["INDEX"]       = $auxAppDataIndex;

   $aData = array();
   $aData["APP_NUMBER"]       = $fields["APP_NUMBER"];
   $aData["APP_PROC_STATUS"]  = $fields["APP_PROC_STATUS"];
   $aData["APP_DATA"]         = $fields["APP_DATA"];
   $aData["DEL_INDEX"]        = $delIndex;
   $aData["TAS_UID"]          = $fields["APP_DATA"]["TASK"];
   $aData["CURRENT_DYNAFORM"] = $dynaformUid;
   $aData["USER_UID"]         = $_SESSION["USER_LOGGED"];
   $aData["APP_STATUS"]       = $fields["APP_STATUS"];
   $aData["PRO_UID"]          = $fields["APP_DATA"]["PROCESS"];

   $oCase->updateCase($appUid, $aData);
}





$option = (isset($_POST["option"]))? $_POST["option"] : null;

$response = array();

switch ($option) {
  case "ALL":
    $dynaformUid = $_POST["dynaformUid"];
    $dataUpdate = $_POST["dataUpdate"];

    $status = 1;

    try {
      $array = explode("(sep1 /)", $dataUpdate);

      for ($i = 0; $i <= count($array) - 1; $i++) {
        $arrayAux = explode("(sep2 /)", $array[$i]);

        $data = "{\"APP_UID\":\"". $arrayAux[0] ."\",\"" . $arrayAux[1] . "\":\"" . $arrayAux[2] . "\"}";

        consolidatedUpdate($dynaformUid, $data);
      }

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

  default:
    $dynUid = $_POST["dynUid"];
    $data = $_POST["data"];

    $status = 1;

    try {
      consolidatedUpdate($dynUid, $data);

      $response["status"] = "OK";
      $response["success"] = true;
    }
    catch (Exception $e) {
      $response["message"] = $e->getMessage();
      $status = 0;
    }

    if ($status == 0) {
      $response["status"] = "ERROR";
      $response["success"] = false;
    }
    break;
}

echo G::json_encode($response);
?>