<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
// Execute events
require_once 'classes/model/Event.php';

$caseInstance = new Cases ();
$eventInstance = new Event();

if(isset($_REQUEST['appid'])){

	// Create the case in the process demand
	$data = $caseInstance->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
	$_SESSION['APPLICATION'] = $data['APPLICATION'];
	$_SESSION['INDEX'] = $data['INDEX'];
	$_SESSION['PROCESS'] = $data['PROCESS'];
	$_SESSION['TASK'] = $_REQUEST['task'];
	$_SESSION['STEP_POSITION'] = 0;

	// End Create the case in the process demand
	$actuelDatas = convergence_getAllAppData($_REQUEST['appid']);

	$newFields = $caseInstance->loadCase ($data['APPLICATION']);

	$newFields['APP_DATA']['CODE_OPER'] = $actuelDatas['CODE_OPER'];
	$newFields['APP_DATA']['DATE_PROD'] = $actuelDatas['DATE_PROD'];
	$newFields['APP_DATA']['NUM_DOSSIER'] = $actuelDatas['NUM_DOSSIER'];
	$newFields['APP_DATA']['DATE_LANCEMENT'] = $actuelDatas['DATE_LANCEMENT'];
	$newFields['APP_DATA']['NB_DOSSIER'] = $actuelDatas['NB_DOSSIER'];

	PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);	 // update the data	    
	$caseInstance->updateCase($data['APPLICATION'], $newFields); // update the data

	$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

	// Redirect to cases steps
	$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
	G::header('Location: ../../cases/' . $nextStep['PAGE']);
	//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
}
?>
