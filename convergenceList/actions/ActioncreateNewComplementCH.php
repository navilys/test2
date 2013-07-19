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
$oDatas = $caseInstance->loadCase($_REQUEST['uid']);

$noMergeDatas = array('SYS_LANG','SYS_SKIN','SYS_SYS','APPLICATION','PROCESS','TASK','INDEX','USER_LOGGED','USER_USERNAME','PIN','FLAG_ACTION','APP_NUMBER');
foreach ($oDatas['APP_DATA'] as $key => $value) {                
    if(!in_array($key, $noMergeDatas))            
        $newFields['APP_DATA'][$key] = $value;           
}
$selectAppNumber = "SELECT APP_NUMBER FROM APPLICATION WHERE APP_UID = '".$data['APPLICATION']."' ";
$dataAppNumber = executeQuery($selectAppNumber);
$numDossier = $dataAppNumber[1]['APP_NUMBER'];

$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
$newFields['APP_DATA']['FLAGTYPO3'] = 'Off';
$newFields['APP_DATA']['NUM_DOSSIER'] = $numDossier; //contient le NUM_DOSSIER de la demande de complement
$newFields['APP_DATA']['NUM_DOSSIER_COMPLEMENT'] = $oDatas['APP_DATA']['NUM_DOSSIER']; // NUM_DOSSIER de la demande d'origine afin de garder des informations cohérente lors de la production
$newFields['APP_DATA']['COMPLEMENT_CHQ'] = 1;
$newFields['APP_DATA']['OLD_APP'] = $_REQUEST['uid'];
$newFields['APP_DATA']['REPRODUCTION_CHQ'] = 'N';
$newFields['APP_DATA']['STATUT'] = '0';
PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($data['APPLICATION'], $newFields);


// Redirect to cases steps
$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../../cases/' . $nextStep['PAGE']);
//G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
?>