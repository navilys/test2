<?php

$G_PUBLISH = new Publisher();

try {

  // Initialize vars
  $type = 'accountActivateError';

  // Include dependences
  set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
  require_once 'classes/model/ErConfiguration.php';
  require_once 'classes/model/ErRequests.php';
  require_once 'classes/class.ExternalRegistrationUtils.php';

  // Decrypt ER_UID
  $erReqUid = G::decrypt($_REQUEST['ER_REQ_UID'], URL_KEY);

  // Load request
  $erRequestsInstance = new ErRequests();
  $request = $erRequestsInstance->load($erReqUid);
  if (!$request) {
    $type = 'accountActivateError';
    throw new Exception('Parameter "ER_REQ_UID" is invalid.');
  }

  // Load configuration
  $erConfigurationInstance = new ErConfiguration();
  $configuration = $erConfigurationInstance->load($request['ER_UID']);

  // Validate if already completed
  if ($request['ER_REQ_COMPLETED'] != '0') {
    $type = 'accountActivateError';
    throw new Exception('Your account is already confirmed and activated.');
  }

  // Validate valid days
  $aux1         = explode(' ', $request['ER_REQ_DATE']);
  $aux2         = explode('-', $aux1[0]);
  $registerDate = mktime(0, 0, 0, $aux2[1], $aux2[2], $aux2[0]);
  $today        = time();
  $days         = ceil(($today - $registerDate) / 60 / 60 / 24);
  if ($days > $configuration['ER_VALID_DAYS']) {
    $type = 'accountActivateError';
    throw new Exception('The request was valid for ' . $configuration['ER_VALID_DAYS'] . ' days and is now expired. Please fill again the registration form.');
  }

  // Username exists?
  if (ExternalRegistrationUtils::userExists($request['ER_REQ_DATA']['__USR_USERNAME__'])) {
    throw new Exception('A user was created with the username "' . $request['ER_REQ_DATA']['__USR_USERNAME__'] . '", please fill the register form again.');
  }

  // Create user
  $user = array();
  $user['USR_FIRSTNAME'] = $request['ER_REQ_DATA']['__USR_FIRSTNAME__'];
  $user['USR_LASTNAME'] = $request['ER_REQ_DATA']['__USR_LASTNAME__'];
  $user['USR_EMAIL'] = $request['ER_REQ_DATA']['__USR_EMAIL__'];
  if ($request['ER_REQ_DATA']['__EMAIL_AS_USERNAME__'] == '1') {
    $user['USR_USERNAME'] = $request['ER_REQ_DATA']['__USR_EMAIL__'];
  }
  else {
    $user['USR_USERNAME'] = $request['ER_REQ_DATA']['__USR_USERNAME__'];
  }
  $user['USR_PASSWORD'] = md5($request['ER_REQ_DATA']['__PASSWORD__']);
  $userUid = ExternalRegistrationUtils::createUser($user);

  // Update request record
  $request['ER_REQ_DATA']['USR_UID'] = $userUid;
  $request['ER_REQ_COMPLETED'] = 1;
  $request['ER_REQ_COMPLETED_DATE'] = date('Y-m-d H:i:s');
  $erRequestsInstance->createOrUpdate($request);

  // Execute action assign
  switch ($configuration['ER_ACTION_ASSIGN']) {
    case 'TASK':
      if ($configuration['ER_OBJECT_UID'] == '') {
        $type = 'accountActivateError';
        throw new Exception('Task UID not defined.');
      }
      else {
        G::LoadClass('tasks');
        $tasksInstance = new Tasks();
        $tasksInstance->assignUser($configuration['ER_OBJECT_UID'], $userUid, 1);
      }
    break;
    case 'GROUP':
      if ($configuration['ER_OBJECT_UID'] == '') {
        $type = 'accountActivateError';
        throw new Exception('Group UID not defined.');
      }
      else {
        G::LoadClass('groups');
        $groupsInstance = new Groups();
        $groupsInstance->addUserToGroup($configuration['ER_OBJECT_UID'], $userUid);
      }
    break;
    case 'DEPARTMENT':
      if ($configuration['ER_OBJECT_UID'] == '') {
        $type = 'accountActivateError';
        throw new Exception('Department UID not defined.');
      }
      else {
        require_once 'classes/model/Department.php';
        $department = new Department();
        $department->addUserToDepartment($configuration['ER_OBJECT_UID'], $userUid, false);
      }
    break;
  }

  // Set some vars
  $_SESSION['PROCESS'] = $configuration['PRO_UID'];
  $data = $request['ER_REQ_DATA'];
  unset($data['__USR_FIRSTNAME__']);
  unset($data['__USR_LASTNAME__']);
  unset($data['__USR_EMAIL__']);
  unset($data['__USR_USERNAME__']);
  unset($data['__PASSWORD__']);
  unset($data['__CONFIRM_PASSWORD__']);
  unset($data['__CAPTCHA__']);
  unset($data['__EMAIL_AS_USERNAME__']);
  unset($data['USR_UID']);

  // Execute action execute trigger
  if ($configuration['ER_ACTION_EXECUTE_TRIGGER'] == '1') {
    require_once 'classes/model/Triggers.php';
    $triggerInstance = new Triggers();
    $trigger = $triggerInstance->load($configuration['TRI_UID']);

    G::LoadClass('pmScript');
    $oPMScript = new PMScript();
    $oPMScript->setFields($data);
    $oPMScript->setScript($trigger['TRI_WEBBOT']);
    $oPMScript->execute();
    $data = array_merge($data, $oPMScript->aFields);
  }

  // Execute action start case
  if ($configuration['ER_ACTION_START_CASE'] == '1') {
    G::LoadClass('case');
    $caseInstance = new Cases();
    $caseInfo = $caseInstance->startCase($configuration['TAS_UID'], $userUid);
    $caseData = $caseInstance->loadCase($caseInfo['APPLICATION']);
    $caseData['APP_STATUS'] = 'TO_DO';
    $caseData['APP_DATA'] = array_merge($caseData['APP_DATA'], $data);
    $caseInstance->updateCase($caseData['APP_UID'], $caseData);
  }

  // Display confirmation
  $message = 'Your account has now been confirmed and activated. You now have access to the System. Thank you.';
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/accountActivatedSuccessfully', '', array('MESSAGE' => $message));
}
catch (Exception $error) {
  // Display error
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/' . $type, '', array('MESSAGE' => $error->getMessage()));
}

G::RenderPage('publish', 'blank');