<?php

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtFieldsMap.php" );


  $fields['DOC_KT_TYPE_ID'] = G::GenerateUniqueID();;
  $fields['PRO_UID'] = G::GenerateUniqueID();;

  $fields['FIELDS_MAP'] = '';
  $fields['DESTINATION_PATH'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktFieldsMap';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktFieldsMapEdit', '', $fields, 'ktFieldsMapSave' );  
  G::RenderPage('publish');   
?>