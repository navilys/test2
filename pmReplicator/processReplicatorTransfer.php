<?php
ini_set('max_execution_time', 0);
ini_set("memory_limit","-1");
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclFormReplicator.php');
require_once(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'class.logger.php');
if(isset($_POST['workspaces']))
	$aWorkspacesDestiny = $_POST['workspaces'];

/****************************************** PROCESSES **************************************************/

//start Proces replicator object
$oProcessRepliactor = new tclProcessManipulator();
print "<h2>TRANSFER RESULT</h2>";
if(isset($_POST['process'])){
	$aProcessesToReplicate = $_POST['process'];
	$aProcessesResult = array();
	if(count($aProcessesToReplicate)){
		foreach($aProcessesToReplicate as $sWorkspaceOrigin => $aProcesses){
			$aProcessErrorMessage=array();
			foreach($aProcesses as $aProcess){
				
				$sProcessUid = $aProcess[0];
				$sProcessLabel = $aProcess[1];
				$sExportedFileName=$oProcessRepliactor->exportProcess($sWorkspaceOrigin,$sProcessUid);
				foreach ($aWorkspacesDestiny as $sWorkspaceDestiny => $aArray){
					$sImportedMessage=$oProcessRepliactor->importProcess($sWorkspaceOrigin, $sWorkspaceDestiny, $sExportedFileName);
					$sMessageResult = "Done";
					if($sImportedMessage!="Done"){
						$sMessageResult = "Errors transfering $sProcessLabel from $sWorkspaceOrigin to $sWorkspaceDestiny";
						//$aProcessErrorMessage[] = $sMessageResult
					}
					$aProcessesResult[]=array(
							'element' => $sProcessLabel,
							'workspaceOrigin' => $sWorkspaceOrigin,
							'workspaceDestiny' => $sWorkspaceDestiny,
							'messageResult' => $sMessageResult,
							'warnings' => ''
					);
				}
			}
		}
	}
	printHtml($aProcessesResult, "Processes");
}
	/****************************************** PM TABLES **************************************************/
if(isset($_POST['tables'])){
	// procesing data transformation
	$aTablesResult = array();
	$aTablesToReplicate = $_POST['tables'];
	if(count($aTablesToReplicate)){
		$aTablesErrorMessage=array();
		foreach($aTablesToReplicate as $sWorkspaceOrigin => $aTables){
			foreach($aTables as $aTable){
				$sTable = $aTable[0];
				$aOptions = $aTable[1];
				foreach ($aWorkspacesDestiny as $sWorkspaceDestiny => $aArray){
					$oReplicator = new tclReplicator($sWorkspaceOrigin,array($sWorkspaceDestiny));
					//$oReplicator->setOriginWorkspace($sWorkspaceOrigin);
					$bTablesErrorMessage = false;
					if(isset($aOptions['structure']) && isset($aOptions['data'])){
						$bTablesErrorMessage=($oReplicator->copyTablesWithData(array($sTable))!="done");
					}
					if(isset($aOptions['structure']) && !isset($aOptions['data'])){
						$bTablesErrorMessage=($oReplicator->copyStructureOnly(array($sTable))!="done");
					}
					if(!isset($aOptions['structure']) && isset($aOptions['data'])){;
						$bTablesErrorMessage=($oReplicator->copyDataOnly(array($sTable))!="done");
					}
					$sMessageResult = "Done";
					if($bTablesErrorMessage){
						$sMessageResult = "Errors transfering $sTable from $sWorkspaceOrigin to $sWorkspaceDestiny";
						//$aTablesErrorMessage[] = "Errors transfering $sTable from $sWorkspaceOrigin to $sWorkspaceDestiny";
					}
					$aTablesResult[]=array(
							'element' => $sTable,
							'workspaceOrigin' => $sWorkspaceOrigin,
							'workspaceDestiny' => $sWorkspaceDestiny,
							'messageResult' => $sMessageResult,
							'warnings' => ''
					);
				}
			}
		}
	}
	printHtml($aTablesResult, "PM tables");
}

	/****************************************** DYNAFORMS **************************************************/
if(isset($_POST['dynaforms'])){
	$aDynaformResult = array();
	$aDynaformToReplicate = $_POST['dynaforms'];
	if(count($aDynaformToReplicate)){
		$aDynaformErrorMessage=array();
		foreach($aDynaformToReplicate as $sWorkspaceOrigin => $aDynaforms){
			foreach($aDynaforms as $aDynaform){
				$sDynaform = $aDynaform[0];
				foreach($aWorkspacesDestiny as $sWorkspaceDestiny => $aProcessesDestiny){
					foreach($aProcessesDestiny as $aProcessDestiny){
						$sProcessDestiny = $aProcessDestiny[0];
						$oFormReplicator = new tclFormReplicator($sWorkspaceOrigin, $sWorkspaceDestiny, $sDynaform, $sProcessDestiny);
						$oFormReplicator->sOriginWorkspace = $sWorkspaceOrigin;
						$sMessageResult = "Done";
						if(!$oFormReplicator->copyDynaform()){
							$sMessageResult = implode(", ",$oFormReplicator->aErrors);
						}
						//$sWarnings = implode(", ",$oFormReplicator->aWarnings);
						$bDynaformsErrorMessage = false;
						$aDynaformsResult[]=array(
								'element' => $oFormReplicator->getDynaformName($sDynaform, $sWorkspaceOrigin),
								'workspaceOrigin' => $sWorkspaceOrigin,
								'workspaceDestiny' => $sWorkspaceDestiny."</b> at process <b>".$oFormReplicator->getProcessName($sProcessDestiny, $sWorkspaceDestiny)."</b>",
								'messageResult' => $sMessageResult,
								'warnings' => $oFormReplicator->aWarnings
						);
					}
				}
			}
		}
	}
	printHtml($aDynaformsResult, "Dynaforms");
}

function printHtml($aElements, $sTitle){
		print "<h3>$sTitle</h3>";
		print "<ul>";
		foreach($aElements as $aRow){
			print "<li> Transfer <b>".$aRow['element']."</b> from workspace <b>".$aRow['workspaceOrigin']."</b> to workspace <b>".$aRow['workspaceDestiny']."</b>";
			$sAdditionalMessage = "";
			if($aRow['messageResult'] != ""){
				if($aRow['messageResult'] == "Done"){
					$sAdditionalMessage.=" <li><span style='color:green'><b>".$aRow['messageResult']."</b></span></li>";
				}else{
					$sAdditionalMessage.=" <li><span style='color:red'><b>Error: </b>".$aRow['messageResult']."</span></li>";
				}
			}
			if(count($aRow['warnings']) && is_array($aRow['warnings'])){
				foreach($aRow['warnings'] as $sWarning){
					$sAdditionalMessage.=" <li><span style='color:blue'><b>Warning: </b>".$sWarning."</span></li>";
				}
			}
			if($sAdditionalMessage!=""){
				print "<ul>".$sAdditionalMessage."</ul>";
			}
			print "</li>";
			//log to database
			$nl="
			";
			$sDescription = "Transfer ".$aRow['element']." from workspace ".$aRow['workspaceOrigin']." to workspace ".$aRow['workspaceDestiny'];
			$sWarnings = "";
			if($aRow['messageResult'] == "Done"){
				if(count($aRow['warnings']) && is_array($aRow['warnings'])){
					$sType = "warning";
					foreach($aRow['warnings'] as $sWarning){
						$sWarnings.=$sWarning.$nl;
					}
				}
				else
					$sType = "message";
			}else{
				$sType = "error";
			}
			$sAdditionalDetails = $aRow['messageResult'].$nl.$sWarnings;
			logger::register($sTitle." replication", $sDescription, $sType, $_SESSION['USER_LOGGED'], $sAdditionalDetails);
		}
		print "</ul>";
	}
?>