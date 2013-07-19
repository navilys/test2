<?php
/*
* This class handle functionality related with the backuping and restauration of workspaces
* 
*/
class workspaceFunctions {
	private $sPathTrunk;
	public $sWorkspace;
	public $sTargetWorkspace;
	private $sOverwrite;
	public $sOutputBackup;
	public $sOutputRestore;
    public $sOutputDelete;
	public $sErrorMessage;
	/*
	* Constructor of class workspace
	* 
	*	@return void
	*/
	function __construct($sWorkspace = "", $sTargetWorkspace = ""){
		$this->sPathTrunk = PATH_TRUNK;
		$this->sOverwrite = "";
		$this->sWorkspace = $sWorkspace;
		$this->sTargetWorkspace = $sTargetWorkspace;
		$this->sOutputBackup = array();
		$this->sOutputRestore = array();
        $this->sOutputDelete = array();
		$this->sErrorMessage = "";
	}

	/*
	* Backups a given workspace using processmaker functionality
	* 
	* @param string $sWorkspace source to backup
	*	@return boolean, true if the operation was successful, otherwise returns false.
	*/
	public function backupWorkspace($sWorkspace = "", $sTag = "", $bIncludeTimestamp = true)
	{
		if($sWorkspace != ""){
			$this->sWorkspace = $sWorkspace;
		}
		$this->sErrorMessage = "";

        if ($bIncludeTimestamp) {
		    $iTimeStamp = strtotime("now");
        } else {
            $iTimeStamp = '';
        }

		if ($sTag !== "")
			$sTag = "_".str_replace("/","_",str_replace(" ", "_", $sTag));
		
		$sBasePath = $this->sPathTrunk."shared/backups/".$this->sWorkspace.".tar"; 
		$sFilePath = $this->sPathTrunk."shared/backups/".$this->sWorkspace.$iTimeStamp.$sTag.".tar";
		
		if(file_exists($sBasePath)){
			//clearstatcache();
			//$iFileStat = stat($sFilePath);
			//$iFiletime = $iFileStat['mtime'];
			if(!unlink($sBasePath)){
				$this->sOutputBackup = "Could not write on $sFilePath please check permissions";
				return false;
			}
		}
		//}else{
			//$iFiletime = "NaN";
		//}
		exec("cd ".$this->sPathTrunk."; ./processmaker workspace-backup ".$this->sWorkspace,$sOutput);
		$this->sOutputBackup = $sOutput;
		clearstatcache();
		
		// this validates that the file was created by checking the mtime attribute.
		if(file_exists($sBasePath)){
			//$iNewFileStat = stat($sFilePath);
			//$iNewFiletime = $iNewFileStat['mtime'];
			//if($iFiletime == $iNewFiletime){
			//	return false;
			//}
			//else if ($bIncludeTimestamp)
			rename($sBasePath, $sFilePath);
		}else{
			return false;
		}
		return true;
	}
	/*
	* Restores a given workspace using processmaker functionality
	* @param string $sWorkspace source to copy or restore
	* @param string $sTargetWorkspace workspace that will be created, overwritten or restored
	* @param string $sOverwrite overwrites target workspace if exists
	* @return boolean, true if the operation was successful, otherwise returns false.
	*/
	public function restoreWorkspace($sWorkspace = "", $sTargetWorkspace = "", $sOverwrite = "-o", $sFileName = ""){
		$this->sErrorMessage = "";
		$this->sOverwrite = $sOverwrite;
		if($sWorkspace != ""){
			$this->sWorkspace = $sWorkspace;
		}
		if(!isset($this->sWorkspace) || $this->sWorkspace==""){
			$this->sErrorMessage = "Workspace source not specified";
			return false;
		}
		if($sTargetWorkspace != ""){
			$this->sTargetWorkspace = $sTargetWorkspace;
		}else{
			$this->sTargetWorkspace = $this->sWorkspace;
		}
		
		if ($sFileName === "")
		{
			$sFileName = $this->sWorkspace.".tar";
		}
		
		if(!file_exists($this->sPathTrunk."shared/backups/".$sFileName)){
			$this->sErrorMessage = "Backup source not found";
			return false;
		}
		
		if(is_dir($this->sPathTrunk."shared/sites/".$this->sTargetWorkspace)){
			clearstatcache();
			$aFileStat = stat($this->sPathTrunk."shared/sites/".$this->sTargetWorkspace);
			$iFiletime = $aFileStat['mtime'];
		}else{
			$iFiletime = "NaN";
		}
		
		// because of a bug in processmaker 2.0.43, it is necessary to erase the file .server_info in
		// the workspace before restoring the workspace.
		$sServerInfoFile = $this->sPathTrunk."shared/sites/".$this->sTargetWorkspace."/.server_info";
		if (file_exists($sServerInfoFile))
			unlink($sServerInfoFile);
		
		// execute the workspace-restore command line from processmaker to perform restoration.
		exec("cd ".$this->sPathTrunk."; ./processmaker workspace-restore ".$this->sOverwrite." ".$sFileName." ".$this->sTargetWorkspace,$sOutput);
		$this->sOutputRestore = $sOutput;
		if(is_dir($this->sPathTrunk."shared/sites/".$this->sTargetWorkspace)){
			clearstatcache();
			$aNewFileStat = stat($this->sPathTrunk."shared/sites/".$this->sTargetWorkspace);
			$iNewFiletime = $aNewFileStat['mtime'];
			//G::pr($iFiletime);
			//G::pr($iNewFiletime);
			if($iFiletime == $iNewFiletime){
				return false;
			}
			
			// Verify there is no "error" entries in the restore output text.
			foreach ($sOutput as $sOutputLine) {
				if (strpos($sOutputLine, "Error executing 'workspace-restore'") !== false)
					return false;
			}
			
		}else{
			return false;
		}
		return true;
	}
	/*
	* Delete a workspace
	* @param string $sWorkspace source to copy or restore
	* @param string $sTargetWorkspace workspace that will be created, overwritten or restored
	* @param string $sOverwrite overwrites target workspace if exists
	* @return boolean, true if the operation was successful, otherwise returns false.
	*/
	public function deleteWorkspace($sWorkspace = ""){
		
		if($sWorkspace != ""){
			$this->sWorkspace = $sWorkspace;
		}
		
		$pathWorkspace = PATH_DB.$this->sWorkspace.PATH_SEP;
		
		$this->sErrorMessage = "";
		
		require_once(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'class.connectionManager.php');
		
		
		$cResult = G::decrypt(HASH_INSTALLATION, SYSTEM_HASH);
		$cResult = str_replace(SYSTEM_HASH, '-', $cResult);
		$cResult = explode("-",$cResult);
		
		/*main delete process:begin*/
		/*mysql connection as root user :begin*/
		$oConnection = new connectionManager();
		$oConnection->stablishNewConnection($cResult[0],$cResult[1],$cResult[2]);
		//$oConnection->selectDataBase("wf_formshare");//example choosing an specific database
		/*mysql connection as root user :end*/
		
		//delete rb database if exist
		$sSql="DROP DATABASE IF EXISTS `rb_".$this->sWorkspace."`";
		$oConnection->executeQuery($sSql);
		
		//delete rp database if exist
		$sSql="DROP DATABASE IF EXISTS `rp_".$this->sWorkspace."`";
		$oConnection->executeQuery($sSql);
		
		//delete wf database if exist
		$sSql="DROP DATABASE IF EXISTS `wf_".$this->sWorkspace."`";
		$oConnection->executeQuery($sSql);
		
			/*delete mysql database user:begin*/
			$this->deleteMysqlUser( 'rb_'.$this->sWorkspace, $oConnection);
			$this->deleteMysqlUser( 'rb_'.$this->sWorkspace, $oConnection, 'localhost');
			$this->deleteMysqlUser( 'rp_'.$this->sWorkspace, $oConnection);
			$this->deleteMysqlUser( 'rp_'.$this->sWorkspace, $oConnection, 'localhost');
			$this->deleteMysqlUser( 'wf_'.$this->sWorkspace, $oConnection);
			$this->deleteMysqlUser( 'wf_'.$this->sWorkspace, $oConnection, 'localhost');
			/*delete mysql database user:end*/
		/*main delete process:end*/

		function destroy($dir) {
		    $mydir = opendir($dir);
		    while(false !== ($file = readdir($mydir))) {
		        if($file != "." && $file != "..") {
		            chmod($dir.$file, 0777);
		            if(is_dir($dir.$file)) {
		                chdir('.');
		                destroy($dir.$file.'/');
		                rmdir($dir.$file) or DIE("couldn't delete $dir$file<br />");
		            }
		            else
		                unlink($dir.$file) or DIE("couldn't delete $dir$file<br />");
		        }
		    }
		    closedir($mydir);
		}
		destroy($pathWorkspace);
		rmdir($pathWorkspace);
		
		/*resume message about delete operation:begin*/
		if(is_dir($pathWorkspace)){
			$_SESSION['ERROR_MESSAGE'] = "Delete operation has failed \n";
            $this->sOutputDelete[] = "Delete operation has failed \n";

            return FALSE;
		}else{
			$_SESSION['MESSAGE'] = "Delete operation has been completed successfully \n";
            return TRUE;
		}
		/*resume message about delete operation:end*/
	}
	/**
	 * deletes mysql users
	 * @param string $sMysqlUser user to be deleted
	 */
	private function deleteMysqlUser($sMysqlUser, $oConnection, $sHost='%'){
		$sSql = "SELECT user
			FROM mysql.user
			WHERE user =  '".$sMysqlUser."' AND
			Host = '".$sHost."'";
		$rCount = count($oConnection->executeQuery($sSql));
		if($rCount != 0){
			$sSql="DROP USER ".$sMysqlUser."@`".$sHost."`";
			$oConnection->executeQuery($sSql);
		}
	}
	/*
	* Restores a given workspace using processmaker functionality
	* @param string $sWorkspace source to copy
	* @param string $sTargetWorkspace workspace that will be created or overwritten
	* @param string $sOverwrite overwrites target workspace if exists
	* @param boolean $bCreateNewBackup creates a new backup of the workspace that will be copied, otherwise the last backup created for that workspace will be used to perform to copy funtionality
	* @return boolean, true if the operation was successful, otherwise returns false.
	*/
	public function copyWorkspace($sWorkspace = "", $sTargetWorkspace = "", $sOverwrite = "-o", $bCreateNewBackup = true, $sFileName = ""){
		$this->sErrorMessage = "";
		if($bCreateNewBackup){
			$bBackupResult = $this->backupWorkspace($sWorkspace, '', false);
			if(!$bBackupResult){
				return false;
			}
			$sFileName = $sWorkspace.".tar";
		}
		if($sTargetWorkspace == ""){
			$this->sErrorMessage = "Invalid target workspace";
			return false;
		}
		if ($sFileName == "")
		{
			$this->sErrorMessage = "Invalid backup file";
			return false;
		}
		
		$bRestoreResult = $this->restoreWorkspace($sWorkspace, $sTargetWorkspace, $sOverwrite, $sFileName);
		if(!$bRestoreResult){
			print 1;
			return false;
		}
		return true;
	}
	
	/*
	* Retrieves the list of all backups for a given workspace
	*/
	public function retrieveBackupList($sWorkspace = "") 
	{
		$sBackupDir = $this->sPathTrunk."shared/backups/";
		$aBackupFiles = array();
		
		if ($dh = opendir($sBackupDir)) {
			while (($file = readdir($dh)) !== false)
			{
				if (preg_match("/^$sWorkspace([\d]{10})(_.*)?\.tar$/", $file, $matches))
				{
					$dateInfo = date("Y-m-d H:i:s", $matches[1]);
					if (count($matches) === 3)
						// ignore the "_" characer at the beginning of the comment
						$sComment = substr($matches[2],1);
					else
						$sComment = "";
					$aBackupFiles[] = array(file => $file, name => "$sWorkspace $dateInfo $sComment");
				}
			}
			closedir($dh);
		}
		usort($aBackupFiles, array("workspaceFunctions", "compareFilename"));
		
		return $aBackupFiles;
	}
	
	static function compareFilename($a, $b)
	{
		if ($a["file"] == $b["file"])
			return 0;
		
		if ($a["file"] > $b["file"])
			return -1;
		
		return 1;
	}

    // changes the state of a workspace from disabled to enabled and viceversa
    public function toggleWorkspaceStatus($sWorkspace = "") {

        if ($sWorkspace === "") {
            $sWorkspace = $this->sWorkspace;
        }
        G::LoadClass('serverConfiguration');
        $oServerConf =& serverConf::getSingleton();
        $oServerConf->changeStatusWS($sWorkspace);
    }

    // determines whether a workspace is currently disabled or not
    // @sWorkspace: name of the workspace to check
    // @return: true if the workspace is disabled
    public function isWorkspaceDisabled($sWorkspace = "") {
        if ($sWorkspace === "") {
            $sWorkspace = $this->sWorkspace;
        }
        G::LoadClass('serverConfiguration');
        $oServerConf =& serverConf::getSingleton();
        return $oServerConf->isWSDisabled($sWorkspace);
    }

    /*
	* Retrieves the list of all workspaces
	* @return array: list of all workspaces
	*/
    public function getWorkspaceList()
    {
        G::LoadClass('serverConfiguration');
	    $oServerConf =& serverConf::getSingleton();

        $wksList = array();

        // search for all directories that contain a db.php file inside.
        // those directories are considered to be workspaces
        $dir = PATH_DB;
        $filesArray = array ();
        if (file_exists ( $dir ) && ($handle = opendir ( $dir ))) {
            while ( false !== ($file = readdir ( $handle )) ) {
                if (($file != ".") && ($file != "..") && file_exists ( PATH_DB . $file . '/db.php' )) { 

                    $statusl = ( $oServerConf->isWSDisabled($file)) ? G::LoadTranslation ( 'ID_DISABLED' ) : G::LoadTranslation ( 'ID_ENABLED' );
            
                    $ws_flag='none';
                    if (strcmp ( SYS_SYS, $file ) != 0) {
            	        $ws_flag='';
                    }

                    $wksList [] = array ('WSP_ID' => $file, 'WSP_NAME' => $file, 'WSP_STATUS' => $statusl, 'WSP_FLAG' => $ws_flag);
                }
            }
            closedir ( $handle );
        }

        return $wksList;

    }
}