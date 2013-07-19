<?php
require_once 'classes/model/Process.php';
G::loadClass('dbConnections');

$process = new Process();
$ruleSet = new RuleSet();
$dbConnections = new dbConnections();
$globalFields = new GlobalFields();

$globalFieldTypes = array();
$processesList = array();
$globalsList = array();
$globalFieldTypesList = GlobalFields::getTypes();

$rules = $ruleSet->getAll();
$processes = $process->getAll();
$globals = $globalFields->getAll();
$dbServices = $dbConnections->getDbServicesAvailables();

foreach ($globalFieldTypesList as $key => $value) {
    $globalFieldTypes[] = array('id' => $key, 'label' => $value);
}

foreach ($processes as $proc) {
    $processesList[] = array('PRO_UID' => $proc['PRO_UID'], 'PRO_TITLE' => $proc['PRO_TITLE']);
}

foreach ($rules as $i => $rule) {
    $rules[$i]['RST_CURRENT_CHECKSUM'] = md5($rules[$i]['RST_SOURCE']);
    $rules[$i]['SHOW_OPEN_EDITOR'] = (int) ($rules[$i]['RST_CURRENT_CHECKSUM'] == $rules[$i]['RST_CHECKSUM']);
}

include "templates/main.phtml";
