<?php
$G_MAIN_MENU            = 'processmaker';
$G_ID_MENU_SELECTED     = 'ID_MENUSETPERMISSIONBYFIELD';
$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'fieldcontrol/setPermissionByField', '', $_POST['form'], 'setPermissionByField.php');
if($_POST['form']['LIST_FORM'] != ''){
	$G_PUBLISH->AddContent('xmlform', 'xmlform', 'fieldcontrol/listPermissionByField', '', $_POST['form'], '');
}
G::RenderPage('publish');