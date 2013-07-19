<?php

G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$adatos= array();
$query = "SELECT * FROM PMT_FIELDS_INBOX WHERE ROL_UID = '".$_GET['rolID']."'";
$newOptions = executeQuery($query);
$filter = (isset($_REQUEST['textFilter']))? $_REQUEST['textFilter'] : '';
$ROL_UID = $_GET['rolID'];
$dataPermissions = $RBAC->getRolePermissions($ROL_UID, $filter, 1); 
$i =1;
foreach($newOptions as $perm)
{
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['ID'] = $perm['ID'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['ROL_UID'] = $perm['ROL_UID'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['GROUP_UID'] = $perm['GROUP_UID'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['ROL_CODE'] = $perm['ROL_CODE'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['DESCRIPTION'] = $perm['DESCRIPTION'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['INCLUDE_OPTION'] = $perm['INCLUDE_OPTION'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['FIELD_NAME'] = $perm['FIELD_NAME'];
	$adatos['GRID_SELECTOR_NEW_OPTIONS'][$i]['POSITION'] = $perm['POSITION'];
	$i++;
}

$G_PUBLISH = new Publisher ();
$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', SYS_COLLECTION . '/permissionsRol', '', $adatos , 'permissionsRol_Save');
G::RenderPage ( 'publish', 'blank');

?>
