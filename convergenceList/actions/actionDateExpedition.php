<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
// Execute events
require_once 'classes/model/Event.php';

$caseInstance = new Cases ();
$eventInstance = new Event();

$data = $caseInstance->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
$_SESSION['APPLICATION'] = $data['APPLICATION'];
$_SESSION['INDEX'] = $data['INDEX'];
$_SESSION['PROCESS'] = $data['PROCESS'];
$_SESSION['TASK'] = $_REQUEST['task'];
$_SESSION['STEP_POSITION'] = 0;

$newFields = $caseInstance->loadCase($data['APPLICATION']);

$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
$newFields['APP_DATA']['idListeProd'] = $_REQUEST['uid'];

PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($data['APPLICATION'], $newFields);

$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

// Redirect to cases steps
$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../../cases/' . $nextStep['PAGE']);
//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
?>