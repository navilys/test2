<?php

require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceFunctions.php');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoManager.php');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoCacheManager.php');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.logger.php');

switch($_POST['operation']) {
    case 'backup':
        BackupWorkspace($_POST['wksName'], $_POST['backupName']);
        break;
    case 'list-backups':
        ListBackupsOf($_POST['wksName']);
        break;
    case 'restore':
        RestoreWorkspace($_POST['wksName'], $_POST['backupFile']);
        break;
    case 'list-workspaces':
        ListWorkspaces();
        break;
    case 'clone':
        CloneWorkspace($_POST['wksName'], $_POST['backupFile'], $_POST['targetWks'], $_POST['newWksName']);
        break;
    case 'delete':
        DeleteWorkspace($_POST['wksName']);
        break;
    case 'disable':
        ToggleStatusWorkspace($_POST['wksName']);
        break;
    case 'refresh':
        RefreshInformation();
        break;
}

function BackupWorkspace($wksName, $backupName) {
    
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    $oWksManager = new workspaceFunctions($wksName);
    $success = $oWksManager->backupWorkspace($wksName, $backupName, true);
    DoLogging('Backup Workspace', "Creating backup of workspace $wksName", $success, $oWksManager->sOutputBackup);
    $result = ob_get_clean();

    echo json_encode(array('result'=>$success, 'content'=>$result, 'log'=>$oWksManager->sOutputBackup));
}

function ListBackupsOf($wksName) {
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    $oWksManager = new workspaceFunctions($wksName);
    $aBackupList = $oWksManager->retrieveBackupList($wksName);
    $result = ob_get_clean();

    echo json_encode($aBackupList);
}

function RestoreWorkspace($wksName, $backupFile) {
    
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    $oWksManager = new workspaceFunctions($wksName);
    $success = $oWksManager->restoreWorkspace($wksName, $wksName, "-o", $backupFile);
    DoLogging('Restore Workspace', "Restoring $wksName from backup $backupFile", $success, $oWksManager->sOutputRestore);
    $result = ob_get_clean();

    if ($success == TRUE) {
        RefreshInfoForAWorkspace($wksName);
    }

    echo json_encode(array('result'=>$success, 'content'=>$result, 'log'=>$oWksManager->sOutputRestore));
}

// retrieve the list of workspaces and return it as a JSON
function ListWorkspaces() {
    
    $oWksManager = new workspaceFunctions();
    $aWksList = $oWksManager->getWorkspaceList();
    echo json_encode($aWksList);
}

function CloneWorkspace($wksName, $backupFile, $targetWks, $newWksName) {
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    
    $oWksManager = new workspaceFunctions($wksName);
    if ($backupFile === 'CURRENT_VERSION') {
        $bUseNewBackup = true;
        $backupFile = '';
    } else {
        $bUseNewBackup = false;
    }
    
    if ($targetWks === '') {
        $targetWks = $newWksName;
    }
    $success = $oWksManager->copyWorkspace($wksName, $targetWks, "-o", $bUseNewBackup, $backupFile);
    DoLogging('Cloning Workspace', "Cloning workspace $wksName from ".($bUseNewBackup ? "current version" : "backup $backupFile")." into workspace $targetWks", $success, array_merge($oWksManager->sOutputBackup,$oWksManager->sOutputRestore));
    $result = ob_get_clean();
    $result .= ";".$oWksManager->sErrorMessage;

    // if cloned successful regenerate information for the target workspace
    if ($success === TRUE) {
        RefreshInfoForAWorkspace($targetWks);
    }

    echo json_encode(array('result'=>$success, 'content'=>$result, 'log'=>$oWksManager->sOutputRestore));
}

// delete a workspace. A backup is created first.
function DeleteWorkspace($wksName) {
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    
    $oWksManager = new workspaceFunctions($wksName);
    $success = $oWksManager->deleteWorkspace($wksName);
    DoLogging('Delete Workspace', "Deleting workspace ".$wksName, $success, $oWksManager->sOutputDelete);
    $result = ob_get_clean();

    echo json_encode(array('result'=>$success, 'content'=>$result, 'log'=>$oWksManager->sOutputDelete));
}

function ToggleStatusWorkspace($wksName) {
    
    // save the resulting from the execution of the operation in a text variable
    ob_start();
    
    $oWksManager = new workspaceFunctions($wksName);
    $oWksManager->toggleWorkspaceStatus($wksName);
    $bIsDisabled = $oWksManager->isWorkspaceDisabled($wksName);
    DoLogging($bIsDisabled ? 'Disable Workspace' : 'Enable Workspace', $bIsDisabled ? "Disabling workspace $wksName" : "Enabling Workspace $wksName", TRUE, '');
    ob_get_clean();

}

function RefreshInformation() {
	
	// call the class that will retrieve the information.
	$oInfoMgr = new workspaceInfoManager();
	$aWorkspaceInfo = $oInfoMgr->fillWorkspacesData();
	workspaceInfoCacheManager::UpdateBatchInfo($aWorkspaceInfo);
	
}

function RefreshInfoForAWorkspace($wksName) {
    $oInfoMgr = new workspaceInfoManager(array($wksName));
    $aWorkspaceInfo = $oInfoMgr->fillWorkspacesData();
    workspaceInfoCacheManager::UpdateBatchInfo($aWorkspaceInfo);
}

// log the result of executing an operation
function DoLogging($actionName, $description, $wasSuccessful, $message) {

    // If we get a log as an array of string, implode them into a single string
    if (is_array($message)) {
        $sGluedMessage = implode("\r\n",$message);
        $message = $sGluedMessage;
    }
    logger::register($actionName, $description, $wasSuccessful == TRUE ? "message" : "error" ,$_SESSION['USER_LOGGED'], $message);
}

?>