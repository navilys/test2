<?php
require_once ("classes/model/Users.php");
G::LoadClass('pmFunctions');
G::LoadClass('configuration');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', True);
$users=$_SESSION['USER_LOGGED'];
$Us = new Users();
$Roles=$Us->load($users);
$rolesAdmin=$Roles['USR_ROLE'];
$oConf = new Configurations();
$queryInbox = " SELECT PI.ID_INBOX, I.DESCRIPTION FROM PMT_INBOX_ROLES PI
		INNER JOIN PMT_INBOX I ON ( I.INBOX = PI.ID_INBOX )
		WHERE ROL_CODE =  '" . $rolesAdmin . "'	 ORDER BY  PI.POSITION ";
$inbox = executeQuery ( $queryInbox );
if(count($inbox))	
{		
    $submenuInbox = "NEW_OPTION_" . $inbox [1]['ID_INBOX'];
	$oConf->loadConfig($x, 'USER_PREFERENCES','','',$_SESSION['USER_LOGGED'],'');
	$fatherMenu = $oConf->aConfig['DEFAULT_MENU'];
	$Inbox1 = $oConf->aConfig['DEFAULT_CASES_MENU'];
	if($fatherMenu != 'PM_CASES' || $Inbox1 != $submenuInbox){
	    $def_lang = 'en';
		$def_menu = 'PM_CASES';
		$def_cases_menu = $submenuInbox;	
		$aConf = Array(
				'DEFAULT_LANG'=>$def_lang,
				'DEFAULT_MENU'=>$def_menu,
				'DEFAULT_CASES_MENU'=>$def_cases_menu
		);
		$oConf->aConfig = $aConf;
		$oConf->saveConfig('USER_PREFERENCES', '', '',$_SESSION['USER_LOGGED']);
    }
}
header('location: ../cases/main');
?>
