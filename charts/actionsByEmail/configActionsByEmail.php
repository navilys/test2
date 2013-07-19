<?php
set_include_path(PATH_PLUGINS . 'actionsByEmail' . PATH_SEPARATOR . get_include_path());
require_once 'classes/model/AbeConfiguration.php';

// Get the configuration for the current task
if (isset($_REQUEST['data'])) {
  $data = G::json_decode(stripslashes($_REQUEST['data']));
}
else {
  // ToDo: Trigger a error
}

$task = new Task();
$taskFields = $task->load($data->uid);

$criteria = new Criteria();
$criteria->add(AbeConfigurationPeer::PRO_UID, $taskFields['PRO_UID']);
$criteria->add(AbeConfigurationPeer::TAS_UID, $data->uid);
$result = AbeConfigurationPeer::doSelectRS($criteria);
$result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$result->next();
if ($configuration = $result->getRow()) {
  $configuration['ABE_EMAIL_FIELD_VALUE'] = $configuration['ABE_EMAIL_FIELD'];
  $configuration['ABE_ACTION_FIELD_VALUE'] = $configuration['ABE_ACTION_FIELD'];
}
else {
  $configuration = array();
}
$configuration['PRO_UID'] = $taskFields['PRO_UID'];
$configuration['TAS_UID'] = $taskFields['TAS_UID'];
$configuration['SYS_LANG'] = SYS_LANG;
$configuration['IFORM'] = $data->iForm;
$configuration['INDEX'] = $data->index;

$templates   = array();
$templates[] = 'dummy';
$path        = PATH_DATA_MAILTEMPLATES . $taskFields['PRO_UID'] . PATH_SEP;
G::verifyPath($path, true);
if (!file_exists($path . 'actionsByEmail.html')) {
  @copy(PATH_PLUGINS . 'actionsByEmail' . PATH_SEP . 'data' . PATH_SEP . 'actionsByEmail.html', $path . 'actionsByEmail.html');
}
$directory = dir($path);
while ($object = $directory->read()) {
  if (($object !== '.') && ($object !== '..') && ($object !== 'alert_message.html')) {
    $templates[] = array('FILE' => $object, 'NAME' => $object);
  }
}
global $_DBArray;
$_DBArray['ABE_TEMPLATES'] = $templates;
$_SESSION['_DBArray'] = $_DBArray;

// Render the form
global $G_PUBLISH;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'actionsByEmail/configActionsByEmail', null, $configuration);