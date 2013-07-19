<?php
	ini_set('max_execution_time', 0);
	ini_set("memory_limit","-1");
	require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
	$aWorkspacesDestiny = $_POST['workspaces'];
	
	/****************************************** PROCESSES **************************************************/
	
	//start Proces replicator object
	$oProcessRepliactor = new tclProcessManipulator();
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
							'messageResult' => $sMessageResult
					);
				}
			}
		}
	}
	/****************************************** PM TABLES **************************************************/
	
	// procesing data transformation
	$aTablesResult = array();
	$aTablesToReplicate = $_POST['tables'];
	$oReplicator = new tclReplicator('workflow',$aWorkspacesDestiny);
	if(count($aTablesToReplicate)){
		$aTablesErrorMessage=array();
		foreach($aTablesToReplicate as $sWorkspaceOrigin => $aTables){
			foreach($aTables as $aTable){
				$sTable = $aTable[0];
				$aOptions = $aTable[1];
				$oReplicator->setOriginWorkspace($sWorkspaceOrigin);
				foreach ($aWorkspacesDestiny as $sWorkspaceDestiny => $aArray){
					$bTablesErrorMessage = false;
					if(isset($aOptions['structure']) && isset($aOptions['data'])){
						$bTablesErrorMessage=($oReplicator->copyTablesWithData(array($sTable))!="done");
					}
					if(isset($aOptions['structure']) && !isset($aOptions['data'])){
						$bTablesErrorMessage=($oReplicator->copyStructureOnly(array($sTable))!="done");
					}
					if(!isset($aOptions['structure']) && isset($aOptions['data'])){
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
							'messageResult' => $sMessageResult
					);
				}
			}
		}
	}
	printHtml($aProcessesResult, "Processes");
	printHtml($aTablesResult, "PM tables");
	function printHtml($aElements, $sTitle){
		print "<h3>$sTitle</h3>";
		foreach($aElements as $aRow){
			print "<li> Transfer ".$aRow['element']." from workspace ".$aRow['workspaceOrigin']." to workspace ".$aRow['workspaceDestiny'];
			if($aRow['messageResult'] != ""){
				if($aRow['messageResult'] == "Done"){
					print "<span style='color:green'></span>";
				}else{
					print "<span style='color:red'></span>";
				}
			}
			print "</li>";
		}
	}
?>