<?php
	
    require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoCacheManager.php');
    require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceFunctions.php');

    // retrieve workspace information    
    $oWksFunc = new workspaceFunctions();
    $aWksList = $oWksFunc->getWorkspaceList();
    $aCachedInfo = workspaceInfoCacheManager::LoadAllInfo();

    $iTotal = count($aWksList);
    $iFilteredTotal = $iTotal;

	// Prepare the output array
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

    // retrieve the filter that is being used.
    $searchString = '';
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
        $searchString = $_GET['sSearch'];

        $aFilteredList = array();
        foreach($aWksList as $wks) {
            if (preg_match('/'.preg_quote($searchString).'/', $wks['WSP_NAME']))
                $aFilteredList[] = $wks;
        }

        $aWksList = $aFilteredList;
        $output['iTotalDisplayRecords'] = count($aWksList);
    }

    // Apply sorting. Only the workspace can be sorted, so it is automatically used.
    if (isset($_GET['iSortCol_0']) && $_GET[ 'bSortable_'.intval($_GET['iSortCol_0']) ] == "true") {
        uasort($aWksList, $_GET['sSortDir_0'] === 'asc' ? "workspaceCompareAsc" : "workspaceCompareDesc");
    }

    // filter the array according to the paging
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
        $startIndex = intval($_GET['iDisplayStart']);
        $pageSize = intval($_GET['iDisplayLength']);

		$wksArrayPaged = array();
        $wksIndex = 0;
        foreach ($aWksList as $wks) {

            if ($wksIndex >= $startIndex && $wksIndex < $startIndex + $pageSize)
                $wksArrayPaged[] = $wks;
            $wksIndex++;
            if ($wksIndex >= $startIndex + $pageSize)
                break;

        }

        $aWksList = $wksArrayPaged;
	}

    // build a row for every workspace
    foreach ($aWksList as $wks) {
        
        $workspaceRow = array();
        

        // if there is cached statistics about the workspace, display that information
        // otherwise just display n/a
        if (array_key_exists($wks['WSP_NAME'], $aCachedInfo)) {
            $cacheInfo = $aCachedInfo[$wks['WSP_NAME']];
             // $workspaceRow[] = applyTemplate("WorkspaceLogo.html", array('logoPath' => str_replace('{WORKSPACE}', SYS_SYS, $cacheInfo->logo)));
            $workspaceRow[] = applyTemplate("WorkspaceNameInfo.html",array('wksName'=>$wks['WSP_NAME'])) ;
            $workspaceRow[] = applyTemplate("TotalCasesInfo.html", $cacheInfo->totalCases);
            $workspaceRow[] = applyTemplate("DiskUsageInfo.html", array('wks' => round($cacheInfo->fileDiskUsage,2), 'db' => round($cacheInfo->dataBaseDiskUsage,2)));

            $tableInformation = '';
            foreach ( $cacheInfo->totalTables as $tableType => $tableTotal ) {
                $tableInformation .= applyTemplate("TableInfo.html", array('tableType' => $tableType, 'tableCount' => $tableTotal));
            }
            $workspaceRow[] = $tableInformation;
            $workspaceRow[] = applyTemplate("WorkspaceStatInfo.html", array('numProcesses' => $cacheInfo->totalActiveProcesses, 'numUsers' => $cacheInfo->numberOfUsers));

        } else {
            $workspaceRow[] = applyTemplate("WorkspaceLogo.html", array('logoPath' => '/images/processmaker.logo.jpg'));
            $workspaceRow[] = applyTemplate("WorkspaceNameInfo.html",array('wksName'=>$wks['WSP_NAME']));
            $workspaceRow[] = "Information not yet available.";
            $workspaceRow[] = "Information not yet available.";
            $workspaceRow[] = "Information not yet available.";
            $workspaceRow[] = "Information not yet available.";
        }

        $workspaceRow[] = applyTemplate("StatusInfo.html", array('wksStatus' => $wks['WSP_STATUS'], 'wksStatusValue' => $wks['WSP_STATUS'] === 'Enabled' ? TRUE : FALSE));

        $output['aaData'][] = $workspaceRow;

    }
	
	echo json_encode( $output );
    

    // Auxiliar method for sorting workspaces by name case-insensitively
    function workspaceCompareAsc($wksA, $wksB) {
        return strcasecmp($wksA['WSP_NAME'], $wksB['WSP_NAME']);
    }

    // Auxiliar method for sorting workspaces by name case-insensitively
    function workspaceCompareDesc($wksA, $wksB) {
        return -1 * strcasecmp($wksA['WSP_NAME'], $wksB['WSP_NAME']);
    }

    // Applies an html template using the field values provided.
    function applyTemplate($templateName, $fieldValues) {

        // apply brackets to the field names
        $bracketedArray = array();
        $keyArray = array_keys($fieldValues);
        foreach($keyArray as $key) {
            $bracketedArray["{".$key."}"] = $fieldValues[$key];
        }

        ob_start();
        include(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'templates'.PATH_SEP.$templateName);
        return str_replace(array_keys($bracketedArray), $bracketedArray, ob_get_clean());
    }

?>