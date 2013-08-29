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

$newFields = $caseInstance->loadCase ($data['APPLICATION']);
$selectAppNumber = "SELECT APP_NUMBER FROM APPLICATION WHERE APP_UID = '".$data['APPLICATION']."' ";
$dataAppNumber = executeQuery($selectAppNumber);
$numDossier = $dataAppNumber[1]['APP_NUMBER'];
$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
$newFields['APP_DATA']['FLAGTYPO3'] = 'Off';
//$newFields['APP_DATA']['FLAG_USER_ROLE'] = convergence_getUserRole($_SESSION['USER_LOGGED']);
$newFields['APP_DATA']['NUM_DOSSIER'] = $numDossier;
PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($data['APPLICATION'], $newFields);


// Redirect to cases steps
$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../../cases/' . $nextStep['PAGE']);
//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
?>