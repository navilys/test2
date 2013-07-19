<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
// Execute events
require_once 'classes/model/Event.php';

$caseInstance = new Cases ();
$eventInstance = new Event();
$noMergeDatas = array('SYS_LANG', 'SYS_SKIN', 'SYS_SYS', 'APPLICATION', 'PROCESS', 'TASK', 'INDEX', 'USER_LOGGED', 'USER_USERNAME', 'PIN', 'FLAG_ACTION', 'APP_NUMBER');
$data = $caseInstance->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
$_SESSION['APPLICATION'] = $data['APPLICATION'];
$_SESSION['INDEX'] = $data['INDEX'];
$_SESSION['PROCESS'] = $data['PROCESS'];
$_SESSION['TASK'] = $_REQUEST['task'];
$_SESSION['STEP_POSITION'] = 0;

$newFields = $caseInstance->loadCase($data['APPLICATION']);

$actuelDatas = convergence_getAllAppData($_REQUEST['uid']);

$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
$newFields['APP_DATA']['uidDemande'] = $_REQUEST['uid'];
foreach ($actuelDatas as $key => $value)
{
    if (!in_array($key, $noMergeDatas))
        $newFields['APP_DATA'][$key] = $value;
}

PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($data['APPLICATION'], $newFields);
	
$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

// Redirect to cases steps
$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../../cases/' . $nextStep['PAGE']);
?>