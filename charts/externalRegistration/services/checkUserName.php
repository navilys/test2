<?php
$response = new stdclass();
$response->status = 'OK';
try {
  require_once 'classes/model/Users.php';
  $userInstance = new Users();
  $dataset = UsersPeer::doSelectRS($userInstance->loadByUsername($_REQUEST['username']));
  $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $dataset->next();
  $response->userExists = $dataset->getRow() ? true : false;
}
catch (Exception $error) {
  $response->status = 'ERROR';
  $response->message = $error->getMessage();
}
die(G::json_encode($response));