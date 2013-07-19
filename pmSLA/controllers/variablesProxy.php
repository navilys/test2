<?php

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP);
}

$option = '';
if (isset($_REQUEST['option'])) {
    $option = $_REQUEST['option'];
}

$pro_uid = '';
if (isset($_REQUEST['PRO_UID'])) {
    $pro_uid = $_REQUEST['PRO_UID'];
}

G::LoadClass('xmlfield_InputPM');
$aVars = getDynaformsVars($pro_uid);

$aVarSelected = Array();
foreach ($aVars as $aVar) {
    switch ($option) {
        case "system":
            if ($aVar['sType'] == "system") {
                $aVarSelected[] = $aVar;
            }
            break;
        case "process":
            if ($aVar['sType'] != "system") {
                $aVarSelected[] = $aVar;
            }
            break;
        case "allVariables":
            $aVarSelected[] = $aVar;
            break;
        default:
            echo G::json_encode(array('success' => true));
            break;
    }
}

echo G::json_encode(array('data' => $aVarSelected, 'total' => count($aVarSelected)));

