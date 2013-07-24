<?php
G::LoadClass ( 'pmFunctions' );
require_once ("classes/model/Users.php");
global $G_TMP_MENU;
global $RBAC;
//G::pr("odfoo");die;
$users = $_SESSION ['USER_LOGGED'];
$Us = new Users ( );
$Roles = $Us->load ( $users );
$rolesAdmin = $Roles ['USR_ROLE'];
$swInbox = 1;
$queryPMInbox = "SELECT PM_ID,PM_SW_INBOX FROM PMT_PM_INBOX_ROLES WHERE PM_ROL_CODE = '". $rolesAdmin ."'";
$queryPMInbox1 = executeQuery($queryPMInbox);
if(count($queryPMInbox1))
	$swInbox = $queryPMInbox1[1]['PM_SW_INBOX'];
if($swInbox == 0)
{
	$G_TMP_MENU->DisableOptionId("CASES_INBOX");
	$G_TMP_MENU->DisableOptionId("CASES_DRAFT");
	$G_TMP_MENU->DisableOptionId("CASES_PAUSED");
	$G_TMP_MENU->DisableOptionId("CASES_SELFSERVICE");
	$G_TMP_MENU->DisableOptionId("CASES_SENT");
	$G_TMP_MENU->DisableOptionId("CASES_START_CASE");
	$G_TMP_MENU->DisableOptionId("CASES_TO_REVISE");
	$G_TMP_MENU->DisableOptionId("CASES_TO_REASSIGN");
	$G_TMP_MENU->DisableOptionId("CASES_SEARCH");
	$G_TMP_MENU->DisableOptionId("CASES_FOLDERS");
}
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
