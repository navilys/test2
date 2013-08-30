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

$actuelDatas = convergence_getAllAppData($_REQUEST['uid']);

$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';

$newFields['APP_DATA']['uidDemande'] = $_REQUEST['uid'];
$newFields['APP_DATA']['STATUT'] = $actuelDatas['STATUT'];
/* $newFields['APP_DATA']['typeVoie'] = $actuelDatas['typeVoie'];
  $newFields['APP_DATA']['nomVoie'] = $actuelDatas['nomVoie'];
  $newFields['APP_DATA']['autreVoie'] = $actuelDatas['autreVoie'];
  $newFields['APP_DATA']['codePostal'] = $actuelDatas['codePostal'];
  $newFields['APP_DATA']['codePostal_label'] = $actuelDatas['codePostal_label'];
  $newFields['APP_DATA']['ville'] = $actuelDatas['ville']; */


PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($data['APPLICATION'], $newFields);
	
$eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);

// Redirect to cases steps
$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../../cases/' . $nextStep['PAGE']);
//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
?>