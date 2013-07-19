<?php
G::LoadClass('case');
$case = new Cases();
$caseData = $case->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
$_SESSION['APPLICATION']   = $caseData['APPLICATION'];
$_SESSION['INDEX']         = $caseData['INDEX'];
$_SESSION['PROCESS']       = $caseData['PROCESS'];
$_SESSION['TASK']          = $_REQUEST['task'];
$_SESSION['STEP_POSITION'] = 0;

$cases = new Cases();
$nextStep = $cases->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
G::header('Location: ../cases/' . $nextStep['PAGE']);
die;
?>