<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.chartLibrary.php");





function caseDataList($r, $i, $status, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
{
    $data = CaseLibrary::caseData(1, null, $status, $status, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

    return array(count($data), array_slice($data, $i, $r));
}





$option = $_POST["option"];

$response = array();

switch ($option) {
    case "GRIDDATA":
        $appStatus = $_POST["appStatus"];
        $dateIni = (!empty($_POST["dateIni"]))? $_POST["dateIni"] : null;
        $dateEnd = (!empty($_POST["dateEnd"]))? $_POST["dateEnd"] : null;
        $configData = (!empty($_POST["configData"]))? $_POST["configData"] : null;
        $processUid = (!empty($_POST["processUid"]))? $_POST["processUid"] : null;
        $taskUid = (!empty($_POST["taskUid"]))? $_POST["taskUid"] : null;
        $userUid = (!empty($_POST["userUid"]))? $_POST["userUid"] : null;
        $groupUid = (!empty($_POST["groupUid"]))? $_POST["groupUid"] : null;
        $departmentUid = (!empty($_POST["departmentUid"]))? $_POST["departmentUid"] : null;

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

        $configData = str_replace("@@doubleQuote", "\"", $configData);
        $configData = str_replace("@@singleQuote", "'",  $configData);
        $arrayConfigData = unserialize($configData);

        $status = 1;

        try {
            list($numRec, $caseData) = caseDataList((int)($_POST["iDisplayLength"]), (int)($_POST["iDisplayStart"]), $appStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

            //Response
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
        $chartType = $_POST["chartType"];
        $sequence = $_POST["sequence"];
        $index = (int)($_POST["index"]);

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
            $response["data"] = ChartLibrary::casesDrillDownData(null, $chartType, $sequence, $index, $appStatus, $appDelStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

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

