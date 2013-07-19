<?php
try {
  // Get the additiona information
  if (isset($_REQUEST['DynaformRequiredFields'])) {
    $additionalInfo = (array) @G::json_decode($_REQUEST['DynaformRequiredFields']);
    $_REQUEST['form'] = array_merge($_REQUEST['form'], $additionalInfo);
  }

  // Validations
  if (!isset($_REQUEST['form']['__EMAIL_AS_USERNAME__'])) {
    $_REQUEST['form']['__EMAIL_AS_USERNAME__'] = '0';
  }

  // Check password using policy
  require_once 'classes/model/UsersProperties.php';
  $userProperty = new UsersProperties();
  $errors = $userProperty->validatePassword($_REQUEST['form']['__PASSWORD__'], date('Y-m-d'), 0);
  if (!empty($errors)) {
    $message = G::LoadTranslation('ID_POLICY_ALERT') . ':<br /><br />';
    foreach ($errors as $errorCode)  {
      switch ($errorCode) {
        case 'ID_PPP_MINIMUM_LENGTH':
          $message .= ' - ' . G::LoadTranslation($errorCode) . ': ' . PPP_MINIMUM_LENGTH . '<br />';
        break;
        case 'ID_PPP_MAXIMUM_LENGTH':
          $message .= ' - ' . G::LoadTranslation($errorCode) . ': ' . PPP_MAXIMUM_LENGTH . '<br />';
        break;
        case 'ID_PPP_EXPIRATION_IN':
          $message .= ' - ' . G::LoadTranslation($errorCode) . ' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
        break;
        default:
          $message .= ' - ' . G::LoadTranslation($errorCode) . '<br />';
        break;
      }
    }
    $message .= '<br />Please enter a different password.';
    throw new Exception($message);
  }

  // Check captcha
  require_once PATH_PLUGINS . 'externalRegistration' . PATH_SEP . 'public_html' . PATH_SEP . 'securimage' . PATH_SEP . 'securimage.php';
  $securimage = new Securimage();
  if (!$securimage->check($_REQUEST['form']['__CAPTCHA__'])) {
    throw new Exception('CAPTCHA code incorrect');
  }

  // Include dependences
  set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
  require_once 'classes/class.ExternalRegistrationUtils.php';

  // Decrypt ER_UID
  $erUid = G::decrypt($_REQUEST['ER_UID'], URL_KEY);

  // Save request and send email
  ExternalRegistrationUtils::createRequest($erUid, $_REQUEST['form']);

  // Redirect to confirmation page
  G::header('Location: registrationFormConfirm');
  die();
}
catch(Exception $error) {
  unset($_REQUEST['form']['__PASSWORD__']);
  unset($_REQUEST['form']['__CONFIRM_PASSWORD__']);
  unset($_REQUEST['form']['__CAPTCHA__']);
  $_SESSION['__EXTERNAL_REGISTRATION_DATA__'] = $_REQUEST['form'];
  G::SendMessageText($error->getMessage(), 'warning');
  G::header('Location: registrationForm?ER_UID=' . $_REQUEST['ER_UID']);
  die();
}