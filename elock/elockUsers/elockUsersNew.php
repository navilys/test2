<?php

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockUsers.php" );


  $fields['USR_USERNAME'] = G::GenerateUniqueID();;

  $fields['USR_PASSWORD'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockUsers';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockUsersEdit', '', $fields, 'elockUsersSave' );  
  G::RenderPage('publish');   
?>