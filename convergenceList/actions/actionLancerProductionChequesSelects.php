<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
// Execute events
require_once 'classes/model/Event.php';

$caseInstance = new Cases ();
$eventInstance = new Event();

	// Create the case in the process demand
    $array=array();
    $array = $_REQUEST['array'];
    $items = json_decode($array,true);
	$data = $caseInstance->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
	$_SESSION['APPLICATION'] = $data['APPLICATION'];
	$_SESSION['INDEX'] = $data['INDEX'];
	$_SESSION['PROCESS'] = $data['PROCESS'];
	$_SESSION['TASK'] = $_REQUEST['task'];
	$_SESSION['STEP_POSITION'] = 0;

	// End Create the case in the process demand

	$newFields = $caseInstance->loadCase ($data['APPLICATION']);
	//$newFields['APP_DATA']['FLAG_ACTION'] = 'Production'; // Flag to hide the first step
	$newFields['APP_DATA']['demandeID'] = $_REQUEST['demandID']; // demandID will be the appuid from Demandes process
	$newFields['APP_DATA']['liste'] = $items; // list of demandes to product
	$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';

	PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);	 // update the data	    
	$caseInstance->updateCase($data['APPLICATION'], $newFields); // update the data

	$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

	// Redirect to cases steps
	$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
	//G::header('Location: ../../cases/' . $nextStep['PAGE']);
	//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
?>