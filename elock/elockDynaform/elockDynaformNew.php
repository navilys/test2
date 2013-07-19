<?php

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockDynaform.php" );


  $fields['UID_DYNAFORM'] = G::GenerateUniqueID();;
  $fields['UID_APPLICATION'] = G::GenerateUniqueID();;

  $fields['BASE64'] = '';
  $fields['USER'] = '';
  $fields['timestamp'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockDynaform';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockDynaformEdit', '', $fields, 'elockDynaformSave' );  
  G::RenderPage('publish');   
?>