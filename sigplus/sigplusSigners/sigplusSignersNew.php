<?php

  require_once ( PATH_PLUGINS . 'sigplus' . PATH_SEP . 'class.sigplus.php');
  $pluginObj = new sigplusClass ();

  require_once ( "classes/model/SigplusSigners.php" );

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'sigplusSigners';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'sigplus/sigplusSignersEdit', '', $fields, 'sigplusSignersSave' );  
  G::RenderPage('publish');

?>