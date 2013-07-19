<?php

$G_PUBLISH = new Publisher();

try {
  // Validate parameters
  if (!isset($_REQUEST['ER_UID'])) {
    $_REQUEST['ER_UID'] = '';
  }
  if ($_REQUEST['ER_UID'] == '') {
    throw new Exception('The parameter "ER_UID" is empty.');
  }

  // Set vars
  if (isset($_SESSION['__EXTERNAL_REGISTRATION_DATA__'])) {
    $data = $_SESSION['__EXTERNAL_REGISTRATION_DATA__'];
    unset($_SESSION['__EXTERNAL_REGISTRATION_DATA__']);
  }
  else {
    $data = array();
  }

  // Decrypt ER_UID
  $erUid = G::decrypt($_REQUEST['ER_UID'], URL_KEY);

  // Include dependences
  set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
  require_once 'classes/model/ErConfiguration.php';

  // Get configuration
  $erConfigurationInstance = new ErConfiguration();
  $configuration = $erConfigurationInstance->load($erUid);

  // Add forms
  if ($configuration['DYN_UID'] != '') {
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/personalInformation', '', $data, 'registrationFormPost?ER_UID=' . $_REQUEST['ER_UID']);
    $G_PUBLISH->AddContent('dynaform', 'xmlform', $configuration['PRO_UID'] . PATH_SEP . $configuration['DYN_UID'], '', $data, 'registrationFormPost?ER_UID=' . $_REQUEST['ER_UID'], '../../gulliver/defaultAjaxDynaform');
  }
  else {
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/personalInformationWithButton', '', $data, 'registrationFormPost?ER_UID=' . $_REQUEST['ER_UID']);
  }
}
catch (Exception $error) {
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', array('MESSAGE' => $error->getMessage()));
}

G::RenderPage('publish', 'blank');