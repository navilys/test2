<?php
$G_MAIN_MENU            = 'processmaker';
$G_ID_MENU_SELECTED     = 'ID_MENUSETPERMISSIONBYFIELD';
$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'fieldcontrol/setPermissionByField', '', $_POST['form'], 'setPermissionByField.php');
if($_POST['form']['LIST_FORM'] != ''){
$FIELDS['SUMMARY_TITLE']="<table border='0'>
												<tr>
													<td>
														4444
													</td>
												</tr>
											</table>
											";


$G_PUBLISH->AddContent('xmlform', 'xmlform', 'fieldcontrol/listPermissionByField', '', $FIELDS, '');
}
G::RenderPage('publish');