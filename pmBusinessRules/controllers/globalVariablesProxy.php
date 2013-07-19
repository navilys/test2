<?php
G::LoadClass('pmFunctions');


if (!defined('PATH_PM_BUSINESS_RULES')) {
    define('PATH_PM_BUSINESS_RULES', PATH_CORE . 'plugins' . PATH_SEP . 'pmBusinessRules' . PATH_SEP );
}

require_once PATH_PM_BUSINESS_RULES . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'GlobalFields.php';

$functionExec = '';
if (isset($_REQUEST['functionExecute'])) {
    $functionExec = $_REQUEST['functionExecute'];
}

switch ($functionExec) {
    case 'listGlobalVariables':
        $oGlobalFields = new GlobalFields();
        $data = $oGlobalFields->getAll();

        echo G::json_encode(array('total' => count($data), 'data' => $data));
        break;
    case 'saveGlobalVariables':
        $oGlobalFields = new GlobalFields();
        $_POST['GLOBAL_FORMAT'] = ($_POST['GLOBAL_TYPE'] == 'Date') ? $_POST['GLOBAL_FORMAT'] : '';
        if ($_POST['ACCION'] == 'new') {
            $oGlobalFields->create($_POST);
        } else {
            if ($_POST['ACCION'] == $_POST['GLOBAL_UID']) {
                $oGlobalFields->update($_POST);
            } else {
                $oGlobalFields->remove($_POST['ACCION']);
                $oGlobalFields = new GlobalFields();
                $oGlobalFields->create($_POST);
            }
        }

        echo G::json_encode(array('success' => true));
        break;
    case 'deleteGlobalVariables':
        $oGlobalFields = new GlobalFields();
        $oGlobalFields->remove($_POST['GLOBAL_UID']);

        echo G::json_encode(array('success' => true));
        break;
    default:
        echo G::json_encode(array('success' => true));
        break;
}

