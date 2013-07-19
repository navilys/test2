<?php

function externalRegistrationSendEmail($erUid, $email, $data = array()) {
  // Include dependences
  set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
  require_once 'classes/class.ExternalRegistrationUtils.php';

  // Set email to send
  $data['__USR_EMAIL__'] = $email;

  // Save request and send email
  ExternalRegistrationUtils::createRequest($erUid, $data);
}

function getExternalRegistrationLink($erUid) {
  return (G::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/sys' .
         SYS_SYS . '/' . SYS_LANG . '/classic/externalRegistration/services/registrationForm?ER_UID=' .
         G::encrypt($erUid, URL_KEY);
}