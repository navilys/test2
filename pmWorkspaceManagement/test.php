<?php

G::loadclass('pmFunctions');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoCacheManager.php');
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.workspaceInfoManager.php');

/*

$mgr = workspaceInfoCacheManager::LoadInfo("formshare");
G::pr($mgr);

$info = workspaceInfoCacheManager::LoadAllInfo();
G::pr($info);

$info['formshare']->fileDiskUsage *= 1.15;
$info['trinity college']->fileDiskUsage *= 1.15;
// G::pr($info);

workspaceInfoCacheManager::UpdateBatchInfo($info);

$info = workspaceInfoCacheManager::LoadAllInfo();
G::pr($info);

*/

$oWorkspaceInfoManager = new workspaceInfoManager(array('formshare', 'requisition', 'template'));
$workspaceData = $oWorkspaceInfoManager->fillWorkspacesData();
workspaceInfoCacheManager::UpdateBatchInfo($workspaceData);
$wksInfo = workspaceInfoCacheManager::LoadInfo("formshare");

G::pr($wksInfo);
// G::pr($workspaceData);

/*
$newWks = new workspaceInfo();
$newWks->workspaceName = 'trinity college'; // name of the workspace
$newWks->totalCases = array('todo' => 5, 'cancelled' => 18, 'completed' => 22, 'draft' => 4, 'paused' => 0);
$newWks->fileDiskUsage = 12.25; // space used by the workspace files
$newWks->dataBaseDiskUsage = 5.22; // space used by the database files of the workspace
$newWks->totalActiveProcesses = 5; // number of active processes
$newWks->numberOfUsers = 14; // number of users
workspaceInfoCacheManager::UpdateInfo($newWks);

$mgr->numberOfUsers = 665;
workspaceInfoCacheManager::UpdateInfo($mgr);
*/




?>