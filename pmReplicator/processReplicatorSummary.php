<?php
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclProcessReplicator.php');
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclFormReplicator.php');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(isset($_POST['workspaces']))
	$aWorkspacesDestiny = $_POST['workspaces'];
print "<h2>SUMMARY</h2>";
if(isset($_POST['process'])){
	$aProcessesToReplicate = $_POST['process'];
	$aProcessesResult = array();
	if(count($aProcessesToReplicate)){
		foreach($aProcessesToReplicate as $sWorkspaceOrigin => $aProcesses){
			$aProcessErrorMessage=array();
			foreach($aProcesses as $aProcess){
				$sProcessUid = $aProcess[0];
				$sProcessLabel = $aProcess[1];
				foreach ($aWorkspacesDestiny as $sWorkspaceDestiny => $aArray){
					$aProcessesResult[]=array(
							'element' => $sProcessLabel,
							'workspaceOrigin' => $sWorkspaceOrigin,
							'workspaceDestiny' => $sWorkspaceDestiny,
							'messageResult' => '',
							'warnings' => ''
					);
				}
			}
		}
	}
	printHtml($aProcessesResult, "Processes");
}
if(isset($_POST['tables'])){
	$aTablesResult = array();
	$aTablesToReplicate = $_POST['tables'];
	if(count($aTablesToReplicate)){
		$aTablesErrorMessage=array();
		foreach($aTablesToReplicate as $sWorkspaceOrigin => $aTables){
			foreach($aTables as $aTable){
				$sTable = $aTable[0];
				$aOptions = $aTable[1];
				foreach ($aWorkspacesDestiny as $sWorkspaceDestiny => $aArray){
					$bTablesErrorMessage = false;
					$aTablesResult[]=array(
							'element' => $sTable,
							'workspaceOrigin' => $sWorkspaceOrigin,
							'workspaceDestiny' => $sWorkspaceDestiny,
							'messageResult' => '',
							'warnings' => ''
					);
				}
			}
		}
	}
	printHtml($aTablesResult, "PM tables");
}

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
						$sMessageResult = "";
						if(!$oFormReplicator->validateReplication()){
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
		}
		print "</ul>";
	}
?>
