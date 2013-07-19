<?php

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtConfig.php" );


  $fields['USR_UID'] = G::GenerateUniqueID();;

  $fields['KT_USERNAME'] = '';
  $fields['KT_PASSWORD'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktConfig';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktConfigEdit', '', $fields, 'ktConfigSave' );  
  G::RenderPage('publish');   
?>