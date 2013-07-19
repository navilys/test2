<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.chartLibrary.php");





$option = $_POST["option"];

$response = array();

switch ($option) {
    case "GRIDDATA":
        $sql = $_POST["sql"];

        $status = 1;

        try {
            $cnn = Propel::getConnection("workflow");
            $stmt = $cnn->createStatement();

            $result = array();

            $rsSql = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);

            while ($rsSql->next()) {
                $row = $rsSql->getRow();

                $result[] = $row;
            }

            $r = intval($_POST["iDisplayLength"]);
            $i = intval($_POST["iDisplayStart"]);

            $result = array_slice($result, $i, $r);

            //Response
            $response["sEcho"] = intval($_POST["sEcho"]);
            $response["iTotalRecords"] = $rsSql->getRecordCount(); //total records
            $response["iTotalDisplayRecords"] = $rsSql->getRecordCount(); //total records, but with filter
            $response["aaData"] = $result;

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
        $chartType = $_POST["chartType"];

        $swList = intval($_POST["swList"]);
        $variableValue = $_POST["variableValue"];
        $sql = $_POST["sql"];
        $n = intval($_POST["n"]);
        $index = intval($_POST["index"]);

        $status = 1;

        try {
            $cnn = Propel::getConnection("workflow");
            $stmt = $cnn->createStatement();

            $data = null;

            $answer = ChartLibrary::customDrillDownData($node, $chartType, $swList, $variableValue, $sql, $n, $index, $stmt);

            if ($answer[0] == 1) {
                throw (new Exception($answer[1]));
            }

            $data = $answer[2];

            //Response
            $response["data"] = $data;

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

