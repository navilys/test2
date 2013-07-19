<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.chartLibrary.php");





function caseDataList($r, $i, $category, $status, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
{
    $data = CaseLibrary::caseData(1, $category, $status, $status, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

    return array(count($data), array_slice($data, $i, $r));
}





$option = $_POST["option"];

$response = array();

switch ($option) {
    case "GRIDDATA":
        $category = (!empty($_POST["category"]))? $_POST["category"] : null;
        $appStatus = $_POST["appStatus"];
        $dateIni = (!empty($_POST["dateIni"]))? $_POST["dateIni"] : null;
        $dateEnd = (!empty($_POST["dateEnd"]))? $_POST["dateEnd"] : null;
        $configData = (!empty($_POST["configData"]))? $_POST["configData"] : null;
        $processUid = (!empty($_POST["processUid"]))? $_POST["processUid"] : null;
        $taskUid = (!empty($_POST["taskUid"]))? $_POST["taskUid"] : null;
        $userUid = (!empty($_POST["userUid"]))? $_POST["userUid"] : null;
        $groupUid = (!empty($_POST["groupUid"]))? $_POST["groupUid"] : null;
        $departmentUid = (!empty($_POST["departmentUid"]))? $_POST["departmentUid"] : null;

        $configData = str_replace("@@doubleQuote", "\"", $configData);
        $configData = str_replace("@@singleQuote", "'",  $configData);
        $arrayConfigData = unserialize($configData);

        $status = 1;

        try {
            list($numRec, $caseData) = caseDataList((int)($_POST["iDisplayLength"]), (int)($_POST["iDisplayStart"]), $category, $appStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

            $response["sEcho"] = (int)($_POST["sEcho"]);
            $response["iTotalRecords"] = $numRec; //Total records
            $response["iTotalDisplayRecords"] = $numRec; //Total records, but with filter
            $response["aaData"] = array();

            for ($i = 0; $i <= count($caseData) - 1; $i++) {
                $applicationUid = $caseData[$i][0];
                $appStatus = $caseData[$i][1];
                $appNumber = $caseData[$i][2];
                $appProcessUid = $caseData[$i][3];
                $appProName  = $caseData[$i][4];
                $appDelIndex = $caseData[$i][5];
                $appTaskUid  = $caseData[$i][6];
                $appTaskName = $caseData[$i][7];
                $appSentByUserUid = $caseData[$i][8];
                $appSentByUsrName = $caseData[$i][9];
                $appCurrentUserUid = $caseData[$i][10];
                $appCurrentUsrName = $caseData[$i][11];
                $appDelTaskDueDate = $caseData[$i][12];

                switch ($appStatus) {
                    case "TO_DO":
                        $appStatus = G::LoadTranslation("ID_TO_DO");
                        break;
                    case "DRAFT":
                        $appStatus = G::LoadTranslation("ID_DRAFT");
                        break;
                    case "COMPLETED":
                        $appStatus = G::LoadTranslation("ID_COMPLETED");
                        break;
                }

                $aux = explode(" ", $appDelTaskDueDate);
                $appDelTaskDueDate = $aux[0];

                $response["aaData"][] = array($applicationUid, $appDelIndex, $appNumber, $appStatus, $appNumber, $appProName, $appTaskName, $appSentByUsrName, $appCurrentUsrName, $appDelTaskDueDate);
            }

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response["status"] = "ERROR";
        }
        break;
    case "CHARTDATA":
        $node = $_POST["node"];
        $index = (int)($_POST["index"]);
        $chartType = $_POST["chartType"];
        $category = $_POST["category"];

        $appStatus = $_POST["appStatus"];
        $appDelStatus = $_POST["appDelStatus"];
        $dateIni = (!empty($_POST["dateIni"]))? $_POST["dateIni"] : null;
        $dateEnd = (!empty($_POST["dateEnd"]))? $_POST["dateEnd"] : null;
        $configData = (!empty($_POST["configData"]))? $_POST["configData"] : null;
        $processUid = (!empty($_POST["processUid"]))? $_POST["processUid"] : null;
        $taskUid = (!empty($_POST["taskUid"]))? $_POST["taskUid"] : null;
        $userUid = (!empty($_POST["userUid"]))? $_POST["userUid"] : null;
        $groupUid = (!empty($_POST["groupUid"]))? $_POST["groupUid"] : null;
        $departmentUid = (!empty($_POST["departmentUid"]))? $_POST["departmentUid"] : null;

        $configData = str_replace("@@doubleQuote", "\"", $configData);
        $configData = str_replace("@@singleQuote", "'",  $configData);
        $arrayConfigData = unserialize($configData);

        $status = 1;

        try {
            //Response
            if ($index > 0) {
                $response["data"] = ChartLibrary::casesByDrillDownData2($node, $index, $chartType, $category, $appStatus, $appDelStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);
            } else {
                $response["data"] = ChartLibrary::casesByDrillDownData($node, $index, $chartType, $category, $appStatus, $appDelStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);
            }

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response["status"] = "ERROR";
        }
        break;
}

echo G::json_encode($response);

