<?php

require_once 'classes/model/ErConfiguration.php';
require_once 'classes/model/ErRequests.php';

class ExternalRegistrationUtils {

  public static function createRequest($erUid, $requestData) {
    try {
      // Load configuration
      $erConfigurationInstance = new ErConfiguration();
      $configuration = $erConfigurationInstance->load($erUid);

      // Save record in request table
      $data = array('ER_REQ_UID' => '',
                    'ER_UID' => $erUid,
                    'ER_REQ_DATA' => $requestData,
                    'ER_REQ_COMPLETED' => 0);
      $erRequestsInstance = new ErRequests();
      $erUidReq = $erRequestsInstance->createOrUpdate($data);

      // Construct confirm registration link
      $urlPart = substr(SYS_SKIN, 0, 2) == 'ux' && SYS_SKIN != 'uxs' ? '/main/login' : '/login/login';
      $requestData['__URL__'] =  (G::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .
                                    '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN .
                                    $urlPart;
      $requestData['__ER__'] = (G::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .
                                    '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN .
                                    '/externalRegistration/services/confirmRegistration?ER_REQ_UID=' .
                                    G::encrypt($erUidReq, URL_KEY);

      // Set USR_USERNAME with USR_EMAIL if is necessary
      //if ($requestData['__EMAIL_AS_USERNAME__'] == '1') {
     // $requestData['__USR_USERNAME__'] = $requestData['__USR_EMAIL__'];
      //}
      // Construct the body
      $body = file_get_contents(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $configuration['PRO_UID'] . PATH_SEP . $configuration['ER_TEMPLATE']);
      $body = G::replaceDataField($body, $requestData);

      // Send confirmation email
      self::sendEmail($requestData['__USR_EMAIL__'], $configuration['ER_TITLE'], $body);
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  public static function sendEmail($email, $subject, $body) {
    try {
      G::LoadClass('pmFunctions');
      $emailSetup = getEmailConfiguration();
      if (!empty($emailSetup)) {
        G::LoadClass('spool');
        $spoolRunInstance = new spoolRun();

        $spoolRunInstance->setConfig(array(
          'MESS_ENGINE'   => $emailSetup['MESS_ENGINE'],
          'MESS_SERVER'   => $emailSetup['MESS_SERVER'],
          'MESS_PORT'     => $emailSetup['MESS_PORT'],
          'MESS_ACCOUNT'  => $emailSetup['MESS_ACCOUNT'],
          'MESS_PASSWORD' => $emailSetup['MESS_PASSWORD'],
          'SMTPAuth'      => $emailSetup['MESS_RAUTH'],
          'SMTPSecure'    => isset($emailSetup['SMTPSecure']) ? $emailSetup['SMTPSecure'] : 'none'
        ));

        $spoolRunInstance->create(array(
          'msg_uid'          => '',
          'app_uid'          => '',
          'del_index'        => 0,
          'app_msg_type'     => 'EXTERNAL_REGISTRATION',
          'app_msg_subject'  => $subject,
          'app_msg_from'     => 'ProcessMaker External Registration <' . $emailSetup['MESS_ACCOUNT'] . '>',
          'app_msg_to'       => $email,
          'app_msg_body'     => $body,
          'app_msg_cc'       => '',
          'app_msg_bcc'      => '',
          'app_msg_attach'   => '',
          'app_msg_template' => '',
          'app_msg_status'   => 'pending',
          'app_msg_attach'   => ''
        ));

        $spoolRunInstance->sendMail();

        if ($spoolRunInstance->status != 'sent') {
          throw new Exception('Error sending confirmation email.<br />Please contact to your system administrator.');
        }
      }
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  public static function createUser($data) {
    try {
      // Load dependences
      global $RBAC;
      $RBAC->initRBAC();
      require_once 'classes/model/Users.php';

      // Create in rbac
      $user['USR_USERNAME']    = $data['USR_USERNAME'];
      $user['USR_PASSWORD']    = $data['USR_PASSWORD'];
      $user['USR_FIRSTNAME']   = $data['USR_FIRSTNAME'];
      $user['USR_LASTNAME']    = $data['USR_LASTNAME'];
      $user['USR_EMAIL']       = $data['USR_EMAIL'];
      $user['USR_DUE_DATE']    = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j'), date('Y') + 5));
      $user['USR_CREATE_DATE'] = date('Y-m-d H:i:s');
      $user['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
      $user['USR_BIRTHDAY']    = date('Y-m-d');
      $user['USR_STATUS']      = 1;
      $userUid                 = $RBAC->createUser($user, 'PROCESSMAKER_OPERATOR');

      // Create in workflow
      $user['USR_STATUS']      = 'ACTIVE';
      $user['USR_UID']         = $userUid;
      $user['USR_PASSWORD']    = md5($userUid);
      $user['USR_ROLE']        = 'PROCESSMAKER_OPERATOR';
      $userInstance = new Users();
      $userInstance->create($user);

      return $userUid;
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  public static function userExists($username) {
    require_once 'classes/model/Users.php';
    $criteria = new Criteria();
    $criteria->addSelectColumn(UsersPeer::USR_UID);
    $criteria->add(UsersPeer::USR_USERNAME, $username);
    return (UsersPeer::doCount($criteria) > 0);
  }

}
