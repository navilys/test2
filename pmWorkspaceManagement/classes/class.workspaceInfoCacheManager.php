<?php

G::loadClass('pmFunctions');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfo.php');


/*
    * Class that manages the cache file with the statistics about the workspaces
    */
class workspaceInfoCacheManager {
        
    /*
        * Loads the cached information about a given workspace
        * @param string $workspaceName - name of the workspace to load the information of.
        */
    public static function LoadInfo($workspaceName) {
            
        $workspaceCache = simplexml_load_file(self::GetCacheFilePath());
        // G::pr(self::GetCacheFilePath());
        // G::pr($workspaceCache);
        $found = false;

        foreach ($workspaceCache->workspace as $wks) {
            if (strtolower($wks['name']) == strtolower($workspaceName)) {
                $wksInfo = self::BuildWorkspaceInfoFromSimpleXML($wks);
                $found = true;
                break;
            }
        }

        if ($found == false) {
            throw new Exception('No cache info data found for workspace: '.$workspaceName);
        }

        return $wksInfo;

    }

    /*
        * Loads the cached information about all workspaces
        * Returns an array using the names of the workspaces as keys
        */
    public static function LoadAllInfo() {
            
        $workspaceCache = simplexml_load_file(self::GetCacheFilePath());
        $workspacesInfo = array();

        foreach ($workspaceCache->workspace as $wks) {
            $wksInfo = self::BuildWorkspaceInfoFromSimpleXML($wks);
            $workspacesInfo[$wksInfo->workspaceName] = $wksInfo;
        }

        return $workspacesInfo;

    }

    /*
        * Updates the information about a given workspace
        * @param workspaceInfo $workspaceInfo - new information for a given workspace.
        */
    public static function UpdateInfo($workspaceInfo) {

        $baseXML = new DOMDocument();

        if (!file_exists(self::GetCacheFilePath())) {
                
            // create the caché file
            $xmlContent = '<?xml version="1.0" encoding="utf-8" ?><workspaceInfoCache></workspaceInfoCache>';
            $baseXML->loadXML($xmlContent);

        } else {
            $baseXML->load(self::GetCacheFilePath());
        }
        
        $xmlCacheInfo = $baseXML->getElementsByTagName('workspaceInfoCache')->item(0);
        $workspaces = $xmlCacheInfo->getElementsByTagName('workspace');

        // remove the workspace element and add it as a new element with the updated values.
        foreach ($workspaces as $wks) {
            if (strtolower($wks->getAttribute('name')) == strtolower($workspaceInfo->workspaceName)) {

                $xmlCacheInfo->removeChild($wks);
                break;
            }
        }

        $xmlCacheInfo->appendChild(self::BuildXMLFromWorkspaceInfo($baseXML, $workspaceInfo));

        $baseXML->save(self::GetCacheFilePath());
            
    }

    /*
     * Updates the information about a set of workspaces
     * @param array[workspaceInfo] $batchWorkspaceInfo - new information for the xml file
     */
    public static function UpdateBatchInfo($batchWorkspaceInfo) {

        $baseXML = new DOMDocument();

        if (!file_exists(self::GetCacheFilePath())) {
                
            // create the caché file
            $xmlContent = '<?xml version="1.0" encoding="utf-8" ?><workspaceInfoCache></workspaceInfoCache>';
            $baseXML->loadXML($xmlContent);

        } else {
            $baseXML->load(self::GetCacheFilePath());
        }
        
        $xmlCacheInfo = $baseXML->getElementsByTagName('workspaceInfoCache')->item(0);
        $workspaces = $xmlCacheInfo->getElementsByTagName('workspace');

        $nodesToDelete = array();
        // remove the workspace element and add it as a new element with the updated values.
        foreach ($workspaces as $wks) {
            foreach ($batchWorkspaceInfo as $workspaceInfo) {
                if (strtolower($wks->getAttribute('name')) == strtolower($workspaceInfo->workspaceName)) {
                    $nodesToDelete[] = $wks;                    
                    break;
                }
            }
        }

        foreach ($nodesToDelete as $wks) {
            $xmlCacheInfo->removeChild($wks);
        }

        foreach ($batchWorkspaceInfo as $workspaceInfo) {
            $xmlCacheInfo->appendChild(self::BuildXMLFromWorkspaceInfo($baseXML, $workspaceInfo));
        }

        // G::pr($baseXML->saveXML());
        if ($baseXML->save(self::GetCacheFilePath()) == false) {
            throw new Exception('Error when attempting to overwrite the workspace info cache file.');
        }

    }

    /*
        * Creates a workspaceInfo object using an simple XML object pointing to a <workspace> element
        * @param simpleXMLElement $xmlWks - <workspace> element
        */
    private static function BuildWorkspaceInfoFromSimpleXML($xmlWks) {
        $wksInfo = new workspaceInfo();
        $wksInfo->workspaceName = (string)$xmlWks['name'];
                    
        $wksInfo->totalCases = array();
        $wksInfo->totalCases['todo'] = (int)$xmlWks->totalCases->todo;
        $wksInfo->totalCases['cancelled'] = (int)$xmlWks->totalCases->cancelled;
        $wksInfo->totalCases['paused'] = (int)$xmlWks->totalCases->paused;
        $wksInfo->totalCases['completed'] = (int)$xmlWks->totalCases->completed;
        $wksInfo->totalCases['draft'] = (int)$xmlWks->totalCases->draft;

        $wksInfo->fileDiskUsage = (float)$xmlWks->fileDiskUsage;
        $wksInfo->dataBaseDiskUsage = (float)$xmlWks->dataBaseDiskUsage;
        $wksInfo->totalActiveProcesses = (int)$xmlWks->totalActiveProcesses;
        $wksInfo->numberOfUsers = (int)$xmlWks->numberOfUsers;
        $wksInfo->logo = (string)$xmlWks->logo;

        $wksInfo->totalTables = array();
        foreach ($xmlWks->totalTables->children() as $xmlTableType) {
            $wksInfo->totalTables[$xmlTableType->getName()] = (int)$xmlTableType;
        }

        return $wksInfo;
    }

    /*
     * Creates a new <workspace> element from a workspace info object
     * @param DOMDocument $domDocument - base document
     * @param workspaceInfo $wksInfo - workspace information
     */
    private static function BuildXMLFromWorkspaceInfo($domDocument, $wksInfo) {

        $xmlWks = $domDocument->createElement('workspace');
        $xmlWks->setAttribute('name',$wksInfo->workspaceName);


        $xmlTotals = $domDocument->createElement('totalCases');
        $xmlTotals->appendChild($domDocument->createElement('todo',$wksInfo->totalCases['todo']));
        $xmlTotals->appendChild($domDocument->createElement('cancelled',$wksInfo->totalCases['cancelled']));
        $xmlTotals->appendChild($domDocument->createElement('paused',$wksInfo->totalCases['paused']));
        $xmlTotals->appendChild($domDocument->createElement('completed',$wksInfo->totalCases['completed']));
        $xmlTotals->appendChild($domDocument->createElement('draft',$wksInfo->totalCases['draft']));
        $xmlWks->appendChild($xmlTotals);

        $xmlWks->appendChild($domDocument->createElement('fileDiskUsage',$wksInfo->fileDiskUsage));
        $xmlWks->appendChild($domDocument->createElement('dataBaseDiskUsage',$wksInfo->dataBaseDiskUsage));
        $xmlWks->appendChild($domDocument->createElement('totalActiveProcesses',$wksInfo->totalActiveProcesses));
        $xmlWks->appendChild($domDocument->createElement('numberOfUsers',$wksInfo->numberOfUsers));
        $xmlWks->appendChild($domDocument->createElement('logo',$wksInfo->logo));

        $xmlTableInfo = $domDocument->createElement('totalTables');
        foreach($wksInfo->totalTables as $tableType => $tableTypeTotal) {
            $xmlTableInfo->appendChild($domDocument->createElement($tableType, $tableTypeTotal));
        }
        $xmlWks->appendChild($xmlTableInfo);

        return $xmlWks;

    }

    /*
     * Gets the path to the cache file
     */
    private static function GetCacheFilePath() {
        return PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'workspaceInfoCache.xml';
    }

}

?>