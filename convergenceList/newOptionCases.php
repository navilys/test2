<?php
G::LoadClass ( 'pmFunctions' );
require_once ("classes/model/Users.php");
global $G_TMP_MENU;
global $RBAC;
$users = $_SESSION ['USER_LOGGED'];
$Us = new Users ( );
$Roles = $Us->load ( $users );
$rolesAdmin = $Roles ['USR_ROLE'];

$queryInbox = " SELECT PI.ID_INBOX, I.DESCRIPTION FROM PMT_INBOX_ROLES PI
					 INNER JOIN PMT_INBOX I ON ( I.INBOX = PI.ID_INBOX )
					 WHERE ROL_CODE = '" . $rolesAdmin . "'	 ORDER BY  PI.POSITION
			  ";
$inbox = executeQuery ( $queryInbox );
$total = sizeof ( $inbox );
if (sizeof($inbox))  
{
	$G_TMP_MENU->AddIdRawOption ( 'NEW_HOME', '', 'Gestion du dispositif', '', '', 'blockHeader' );
	foreach ( $inbox as $index ) 
	{
		$G_TMP_MENU->AddIdRawOption ( "NEW_OPTION_" . $index ['ID_INBOX'], "../convergenceList/inboxDinamic.php?idInbox=" . $index ['ID_INBOX'], $index ['DESCRIPTION'], "" );
	}
}

?>
