<?php

$G_PUBLISH = new Publisher();

try {
  // Include dependences
  set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
  require_once 'classes/model/ErConfiguration.php';
  require_once 'classes/model/ErRequests.php';
  require_once 'classes/class.ExternalRegistrationUtils.php';

  // Load request
  $erRequestsInstance = new ErRequests();
  $request = $erRequestsInstance->load($_REQUEST['ER_REQ_UID']);
  if (!$request) {
    throw new Exception('Parameter "ER_REQ_UID" is invalid.');
  }

  // Load configuration
  $erConfigurationInstance = new ErConfiguration();
  $configuration = $erConfigurationInstance->load($request['ER_UID']);
  if (!$configuration) {
    throw new Exception('Parameter "ER_UID" is invalid.');
  }

  // Set vars
  $data = $request['ER_REQ_DATA'];
  //if ($data['__EMAIL_AS_USERNAME__'] == '1') {
   // $data['__USR_USERNAME__'] = $data['__USR_EMAIL__'];
  //}

  // Render form
  if ($configuration['DYN_UID'] != '') {
    $dynaform = new Form($configuration['PRO_UID'] . PATH_SEP . $configuration['DYN_UID'], PATH_DYNAFORM, SYS_LANG, false);
    $dynaform->mode = 'view';
    $dynaform->values = $data;
    foreach ($dynaform->fields as $fieldName => $field) {
      if ($field->type == 'submit') {
        unset($dynaform->fields[$fieldName]);
      }
    }
    $data['__ADDITIONAL_FORM__'] = $dynaform->render(PATH_PLUGINS . 'externalRegistration/xmlform.html', $scriptCode = '');
  }
  else {
    $data['__ADDITIONAL_FORM__'] = '&nbsp;';
  }

  // Add forms
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/viewPersonalInformation', '', $data);
}
catch (Exception $error) {
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', array('MESSAGE' => $error->getMessage()));
}

G::RenderPage('publish', 'blank');