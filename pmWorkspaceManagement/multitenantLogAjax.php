<?php

    // file that handles the ajax calls made by the grid in the multitenant log viewer

    // requiers the logger class
    require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.logger.php');

    // retrieve the values of the filters, if they are received.
    $dDateFrom = getValueAsDateIfExists("dateFrom");
    $dDateTo = getValueAsDateIfExists("dateTo");
    $sActionName = getValueIfExistsAndNotEmpty("action");
    $sTypeName = getValueIfExistsAndNotEmpty("type");
    $sIpAddress = getValueIfExistsAndNotEmpty("ipaddress");
    $sContent = getValueIfExistsAndNotEmpty("content");

    $result = array();
    $result["sEcho"] = $_REQUEST["sEcho"];

    // find the column we need to sort by
    $sortByCol = array_key_exists("iSortCol_0", $_REQUEST) ? (intval($_REQUEST["iSortCol_0"]) + 1) : 1;
    $sortDirection = array_key_exists("sSortDir_0", $_REQUEST) ? $_REQUEST["sSortDir_0"] : "asc";

    // request a logger object to get the total count given the filters
    $oLogger = new logger();
    $totalRows = $oLogger->getFilteredLogListCount($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent);
    $displayRecords = intval($_REQUEST["iDisplayLength"]);

    $result["iTotalRecords"] = $totalRows;

    // retrieve the row logs in the appropriate page and with the given filters.
    $logRows = $oLogger->filterLogList($dDateFrom, $dDateTo, $sIpAddress, $sActionName, $sTypeName, $sContent, $displayRecords, $_REQUEST["iDisplayStart"], $sortByCol, $sortDirection);

    // convert the rows from Propel into plain index-based arrays, so that DataTables can understand them
    // also control any HTML tags that may have ended inside the database to avoid XSS.
    $logArray = array();
    while($logRows->next()) {
        $logArray[] = array_map("htmlentities", array_values($logRows->getRow()));
    }

    $result["iTotalDisplayRecords"] = count($logArray);
    $result["aaData"] = $logArray;

    echo json_encode($result);

    // private function to process the filter parameters
    function getValueIfExistsAndNotEmpty($sKeyName) {
        if (!array_key_exists($sKeyName, $_REQUEST))
            return NULL;

        $sValue = $_REQUEST[$sKeyName];
        if (strlen($sValue) === 0)
            return NULL;

        return $sValue;
    }

    // retrieves a filter parameter and converts it into a datetime object if not empty
    function getValueAsDateIfExists($sKeyName) {
        $sValueAsStr = getValueIfExistsAndNotEmpty($sKeyName);
        if ($sValueAsStr != NULL) {
            // split the dd/mm/YYYY string
            $datePieces = split("/", $sValueAsStr);
            // for some freaking reason PHP creates dates by using Hour, minute, second, month, day, year.
            return new DateTime(date('Y-m-d',mktime(0,0,0, $datePieces[1], $datePieces[0], $datePieces[2])));
        }

        return NULL;
    }

?>