<?php
global $G_PUBLISH;
$G_PUBLISH = new Publisher();

try {
  // Validations
  if (!isset($_REQUEST['APP_UID'])) {
    $_REQUEST['APP_UID'] = '';
  }
  if (!isset($_REQUEST['DEL_INDEX'])) {
    $_REQUEST['DEL_INDEX'] = '';
  }
  if ($_REQUEST['APP_UID'] == '') {
    throw new Exception('The parameter APP_UID is empty.');
  }
  if ($_REQUEST['DEL_INDEX'] == '') {
    throw new Exception('The parameter DEL_INDEX is empty.');
  }
  G::LoadClass('case');
  $cases = new Cases();
  $caseFields = $cases->loadCase(G::decrypt($_REQUEST['APP_UID'], URL_KEY),G::decrypt($_REQUEST['DEL_INDEX'], URL_KEY));
  if (is_null($caseFields['DEL_FINISH_DATE'])) {
    $action = 'dataFormPost.php?APP_UID=' . $_REQUEST['APP_UID'] . '&DEL_INDEX=' . $_REQUEST['DEL_INDEX'] . '&ABER=' . $_REQUEST['ABER'];
    $G_PUBLISH->AddContent('dynaform', 'xmlform', $caseFields['PRO_UID'] . '/' . G::decrypt($_REQUEST['DYN_UID'], URL_KEY), '', $caseFields['APP_DATA'], $action);
  }
  else {
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', array('MESSAGE' => '<strong>The form has already been filled and sent.</strong>'));
  }
}
catch (Exception $error) {
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', array('MESSAGE' => $error->getMessage()));
}

G::RenderPage('publish', 'blank');