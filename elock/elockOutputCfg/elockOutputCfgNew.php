<?php

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockOutputCfg.php" );


  $fields['STP_UID'] = G::GenerateUniqueID();;

  $fields['PRO_UID'] = '';
  $fields['TAS_UID'] = '';
  $fields['DOC_UID'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockOutputCfg';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockOutputCfgEdit', '', $fields, 'elockOutputCfgSave' );  
  G::RenderPage('publish');   
?>