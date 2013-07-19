<?php
/*
 * ProcessMaker In Outlook REST Services by Nightlies.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

if (!isset($_REQUEST['method'])) {
  $_REQUEST['method'] = '';
}

if (!isset($_REQUEST['o'])) {
  $_REQUEST['o'] = 'xml';
}

if (!isset($_SESSION['__OUTLOOK_CONNECTOR__'])) {
  $_SESSION['__OUTLOOK_CONNECTOR__'] = true;
}

require_once PATH_PLUGINS . 'N_InOutlook/classes/core/class.' . $_REQUEST['o'] . 'Service.php';
require_once PATH_PLUGINS . 'N_InOutlook/classes/class.inOutlook.php';
$inOutlook = new InOutlook();

switch ($_REQUEST['method']) {
  case '':
  default:
    $inOutlook->healthCheck();
  break;
  case 'authenticate':
    $username = Nightlies::decrypt((isset($_REQUEST['username']) ? $_REQUEST['username'] : ''), Nightlies::key);
    $password = Nightlies::decrypt((isset($_REQUEST['password']) ? $_REQUEST['password'] : ''), Nightlies::key);
    $inOutlook->authenticate($username, $password);
  break;
  case 'getNewCases':
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    $inOutlook->getNewCases($userUID);
    $inOutlook->write();
  break;
  case 'checkChanges':
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    $result = $inOutlook->getNewCases($userUID);
    $response = new stdclass();
    $response->status = 'OK';
    if ($_REQUEST['action'] == 'draft') {
        $response->changes = $_REQUEST['counter'] != $result->data->draft;
    }
    if ($_REQUEST['action'] == 'todo') {
        $response->changes = $_REQUEST['counter'] != $result->data->to_do;
    }
    die(G::json_encode($response));
  break;
  case 'openPluginPage':
    if (!isset($_REQUEST['page'])) {
      $_REQUEST['page'] = '';
    }
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    list($_SESSION['USER_LOGGED'], $_SESSION['USR_USERNAME'], $_SESSION['USR_FULLNAME']) = $inOutlook->authorize($userUID);
    if (($_SESSION['USER_LOGGED'] != '') && ($_REQUEST['page'] != '')) {
      G::header('Location: ../' . $_REQUEST['page']);
      die;
    }
  break;
  case 'openCasesList':
    if (!isset($_REQUEST['type'])) {
      $_REQUEST['type'] = 'todo';
    }
    if ($_REQUEST['type'] == '') {
      $_REQUEST['type'] = 'todo';
    }
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    list($_SESSION['USER_LOGGED'], $_SESSION['USR_USERNAME'], $_SESSION['USR_FULLNAME']) = $inOutlook->authorize($userUID);
    if ($_SESSION['USER_LOGGED'] != '') {
      G::header('Location: ../casesList?action=' . $_REQUEST['type']);
      die;
    }
  break;
  case 'openStartCase':
    if (!isset($_REQUEST['type'])) {
      $_REQUEST['type'] = 'startCase';
    }
    if ($_REQUEST['type'] == '') {
      $_REQUEST['type'] = 'startCase';
    }
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    list($_SESSION['USER_LOGGED'], $_SESSION['USR_USERNAME'], $_SESSION['USR_FULLNAME']) = $inOutlook->authorize($userUID);
    if ($_SESSION['USER_LOGGED'] != '') {
      G::header('Location: ../newCase');
      die;
    }
  break;
  case 'getInitialTasks':
    if (!isset($_REQUEST['userUID'])) {
      $_REQUEST['userUID'] = '';
    }
    if (!isset($_REQUEST['language'])) {
      $_REQUEST['language'] = '';
    }
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
    $inOutlook->getInitialTasks($userUID, $_REQUEST['language']);
  break;
  case 'autopilot':
    $userUID = Nightlies::decrypt((isset($_REQUEST['userUID']) ? $_REQUEST['userUID'] : ''), Nightlies::key);
  	$inOutlook->autopilot($userUID,
                          (isset($_REQUEST['task']) ? $_REQUEST['task'] : ''),
                          (isset($_REQUEST['from']) ? $_REQUEST['from'] : ''),
                          (isset($_REQUEST['to']) ? $_REQUEST['to'] : ''),
                          (isset($_REQUEST['cc']) ? $_REQUEST['cc'] : ''),
                          (isset($_REQUEST['bcc']) ? $_REQUEST['bcc'] : ''),
                          (isset($_REQUEST['subject']) ? $_REQUEST['subject'] : ''),
                          (isset($_REQUEST['body']) ? $_REQUEST['body'] : ''));
  break;
}

?>