<?php
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceConnectionManager.php');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfo.php');
G::loadClass('processes');
G::loadClass('users');
G::loadClass('applications');
    /**
     * Loads general values per workspace
     * @author pm
     *
     */
    class workspaceInfoManager {
        
        private $aWorkspaces;//list of workspaces, it could be defined, otherwise the script will read the shared folder looking for workspaces
        private $oWSConnectionManager;//manages connections with the databases 
        private $sWorkspace;//auxiliary variable that stores a workspace name
        
        /**
         * Constructor will handle the list of workspaces and the processmaker mysql admin connection
         * @param array $aWorkspace
         */
        function __construct($aWorkspaces = array()){
        	$this->oWSConnectionManager = new workspaceConnectionManager("PROPEL");
        	$this->aWorkspaces = $aWorkspaces;
        	$this->workspacesList();
        }
        /**
         * Return a list of workspaces specified by the constructor or reads the file structure to return all workspaces it founds.
         * @return array of workspaces
         */
        public function workspacesList(){
        	if(count($this->aWorkspaces)){
        		return $this->aWorkspaces;
        	}
        	$oServerConf =& serverConf::getSingleton();
        	$dir = PATH_DB;
        	$filesArray = array ();
        	if (file_exists ( $dir )) {
        		$handle = opendir ( $dir);
        		if ($handle) {
        			while ( false !== ($file = readdir ( $handle )) ) {
        				if (($file != ".") && ($file != "..")) {
        					if (file_exists ( $dir . $file . '/db.php' )) {
        						$statusl = ( $oServerConf->isWSDisabled($file)) ? G::LoadTranslation ( 'ID_DISABLED' ) : G::LoadTranslation ( 'ID_ENABLED' );
        						$this->aWorkspaces [] = $file;
        					}
        				}
        			}
        			closedir ( $handle );
        		}
        	}
        	return $this->aWorkspaces;
        }
        /**
         * fills and array with general data of all workspaces in the list
         * @return multitype:workspaceInfo
         */
        public function fillWorkspacesData(){
        	$aWorkspacesData = array();
        	foreach($this->aWorkspaces as $this->sWorkspace){
        		$oWorkspaceInfo = new workspaceInfo(); 
        		$this->oWSConnectionManager->createConnectionObjects($this->sWorkspace);
        		$oWorkspaceInfo->workspaceName = $this->sWorkspace;
        		$oWorkspaceInfo->totalActiveProcesses = $this->processesByWorkspace();
        		$oWorkspaceInfo->totalCases = $this->casesByWorkspace();
        		$oWorkspaceInfo->numberOfUsers = $this->usersByWorkspace();
        		$oWorkspaceInfo->dataBaseDiskUsage = $this->workspaceDatabaseDiskUsage();
        		$oWorkspaceInfo->fileDiskUsage = $this->workspaceFileDiskUsage();
        		$oWorkspaceInfo->totalTables = $this->workspaceTablesByEngine();
        		$oWorkspaceInfo->logo = $this->getLogo();
        		$aWorkspacesData[]=$oWorkspaceInfo;
        	}
        	return $aWorkspacesData;
        }
        /**
         * Run a query in a workspace to return the quantity of active processes in that workspace
         * @return integer
         */
        private function processesByWorkspace(){
        	$oCriteria = new Criteria();
					$oCriteria->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        	$iResult = ProcessPeer::doCount($oCriteria);
        	return $iResult;
        }
        /**
         * Query a workflow database to obtain the number of cases per status
         * @return array
         */
        private function casesByWorkspace(){
        	$aResultCases['todo'] = $this->countCasesByStatus('TO_DO');
        	$aResultCases['draft'] = $this->countCasesByStatus('DRAFT');
        	$aResultCases['cancelled'] = $this->countCasesByStatus('CANCELLED');
        	$aResultCases['paused'] = $this->countCasesByStatus('PAUSED');
        	$aResultCases['completed'] = $this->countCasesByStatus('COMPLETED');
        	return $aResultCases;
        }
        /**
         * Runs a query to get the count of cases for a given status
         * @param string $sStatus
         * @return integer
         */
        function countCasesByStatus($sStatus){
        	$oCriteria = new Criteria();
        	$oCriteria->add(ApplicationPeer::APP_STATUS, $sStatus);
        	return ApplicationPeer::doCount($oCriteria);
        }
        /**
         * Run a query in a workspace to return the quantity of active users in that workspace
         * @return integer
         */
        private function usersByWorkspace(){
        	$aActiveStatus = array('ACTIVE','1');
        	$oCriteria = new Criteria();
        	$oCriteria->add(UsersPeer::USR_STATUS, $aActiveStatus, CRITERIA::IN);
        	$iResult = UsersPeer::doCount($oCriteria);
        	return $iResult;
        }
        /**
         * Calculates the disk usage of the three processmaker databases used by a workspace
         * @return float
         */
        private function workspaceDatabaseDiskUsage(){
        	$fResultWF = $this->databaseDiskUsage($this->oWSConnectionManager->aDataSources['workflow'],'workflow');
					$fResultRB = $this->databaseDiskUsage($this->oWSConnectionManager->aDataSources['rbac'],'rbac');
					$fResultRP = $this->databaseDiskUsage($this->oWSConnectionManager->aDataSources['report'],'report');
					return $fResultWF+$fResultRB+$fResultRP;
        }
        /**
         * Calculates the disk usage of the database
         * @param array $aDataSource, array that contains the information of database adapter and name
         * @return float
         */
        private function databaseDiskUsage($aDataSource, $sCon){
        	//$con = Propel::getConnection('workflowCon');
        	switch($aDataSource['sAdapter']){
        		case "mysql":
		        	$sQuery="
		        		SELECT
		        			sum( data_length + index_length ) / 1024 / 1024 'SIZE'
		        		FROM
		        			information_schema.TABLES
		        		WHERE
		        			table_schema = '".$aDataSource['sName']."'
		        	";
		        	$aResult = executeQuery($sQuery, $sCon);
		        	$fSize = ($aResult[1]['SIZE']!==null)? $aResult[1]['SIZE']:0;
		        	return $fSize;
		        break;
		        default:
		        	return 0;
        	}
        }
        private function workspaceTablesByEngine(){
        	$aTablesByEngine = array();
        	$aResultWF = $this->tablesByEngine($this->oWSConnectionManager->aDataSources['workflow'],'workflow');
					$aResultRB = $this->tablesByEngine($this->oWSConnectionManager->aDataSources['rbac'],'rbac');
					$aResultRP = $this->tablesByEngine($this->oWSConnectionManager->aDataSources['report'],'report');
					$aTablesByEngine = array_merge($aResultWF, $aResultRB, $aResultRP);
					foreach($aTablesByEngine as $sKey => &$value){
						$value = 0;
						$value += (isset($aResultWF[$sKey]))? $aResultWF[$sKey] : 0;
						$value += (isset($aResultRB[$sKey]))? $aResultRB[$sKey] : 0;
						$value += (isset($aResultRP[$sKey]))? $aResultRP[$sKey] : 0;
					}
					return $aTablesByEngine;
        }
        /**
         * Returns a count of the tables grouped by engine
         * @param array $aDataSource, array that contains the information of database adapter and name
         * @return array
         */
        private function tablesByEngine($aDataSource, $sCon){
        	$aTablesByEngine = array('undefined' => 0);
        	switch($aDataSource['sAdapter']){
        		case "mysql":
        			$sQuery="
			        	SELECT 
			        		ENGINE , 
			        		COUNT( * ) AS TABLES_COUNT
								FROM 
									information_schema.TABLES
								WHERE 
									TABLE_SCHEMA = '".$aDataSource['sName']."' AND 
									TABLE_TYPE = 'BASE TABLE'
								GROUP BY
									ENGINE";
							$aResult = executeQuery($sQuery, $sCon);
							$aTablesByEngine = array();
							if(count($aResult)){
								foreach($aResult as $aRow){
									$aTablesByEngine[$aRow['ENGINE']] = $aRow['TABLES_COUNT'];
								}
		        	}
						break;
					}
					return $aTablesByEngine;
        }
        /**
         * Calculates a workspace directory size
         * @return float
         */
        private function workspaceFileDiskUsage(){
        	$sPath = PATH_DB.$this->sWorkspace;
        	if(is_dir($sPath)){
        		clearstatcache();
        		$iSize = $this->getDirectorySize($sPath);
        		$fSizeMB = $iSize/(1024*1024);
        		return $fSizeMB;
        	}else{
        		return 0;
        	}
        }
        /**
         * Calculates a directory size
         * @param string $path
         * @return float
         */
        function getDirectorySize($path){
        	$totalsize = 0;
        	$handle = opendir($path);
        	if($handle){
        		while (false !== ($file = readdir($handle))){
        			$nextpath = $path .PATH_SEP. $file;
        			if ($file != '.' && $file != '..' && !is_link ($nextpath)){
        				if (is_dir ($nextpath)){
        					$totalsize += $this->getDirectorySize($nextpath);
        				}
        				elseif (is_file ($nextpath)){
        					$totalsize += filesize ($nextpath);
        				}
        			}
        		}
        	}
        	closedir($handle);
        	return $totalsize;
        }
        /**
         * Looks for the customized logo in each workspace or returns the default processmaker logo
         * This method doesn't consider logos registered in  plugins
         * @return string
         */
        function getLogo(){
        	$this->sWorkspace;
        	G::LoadClass( 'replacementLogo' );
		      $oLogoR = new replacementLogo();
        	$aFotoSelect = $oLogoR->getNameLogo('none');
	        if (is_array($aFotoSelect)) {
	          $sFotoSelect   = trim($aFotoSelect['DEFAULT_LOGO_NAME']);
	          $sWspaceSelect = trim($aFotoSelect['WORKSPACE_LOGO_NAME']);
	        }
					if ( isset($sFotoSelect) && $sFotoSelect!='' && !(strcmp($sWspaceSelect, $this->sWorkspace)) ){
		      	$sCompanyLogo = "/sys"."{WORKSPACE}"."/".SYS_LANG."/".SYS_SKIN."/setup/showLogoFile.php?id=".base64_encode($sFotoSelect);
		      }else {
		        $sCompanyLogo = '/images/processmaker.logo.jpg';
		      }
		      return $sCompanyLogo;
        }
        /**
         * Looks for the customized logo in each workspace or returns the default processmaker logo 
         * This method usage is not recommended due to image files registered in plugins can be placed in folders that are inaccessible
         * @return string
         */
        function getLogoWithPlugin(){
        	$this->sWorkspace;
        	G::LoadClass( 'replacementLogo' );
		      $oLogoR = new replacementLogo();
        	$aFotoSelect = $oLogoR->getNameLogo('none');

	        if (is_array($aFotoSelect)) {
	          $sFotoSelect   = trim($aFotoSelect['DEFAULT_LOGO_NAME']);
	          $sWspaceSelect = trim($aFotoSelect['WORKSPACE_LOGO_NAME']);
	        }
					if (class_exists('PMPluginRegistry')) {
		        $oPluginRegistry = &PMPluginRegistry::getSingleton();
		        if ( isset($sFotoSelect) && $sFotoSelect!='' && !(strcmp($sWspaceSelect, $this->sWorkspace)) ){
		          $sCompanyLogo = $oPluginRegistry->getCompanyLogo($sFotoSelect);
		          $sCompanyLogo = "/sys"."{WORKSPACE}"."/".SYS_LANG."/".SYS_SKIN."/setup/showLogoFile.php?id=".base64_encode($sCompanyLogo);
		        }
		        else {
		          $sCompanyLogo = $oPluginRegistry->getCompanyLogo('/images/processmaker.logo.jpg');
		        }
		      }
		      else {
		        $sCompanyLogo = '/images/processmaker.logo.jpg';
		      }
		      return $sCompanyLogo;
        }

    }

?>