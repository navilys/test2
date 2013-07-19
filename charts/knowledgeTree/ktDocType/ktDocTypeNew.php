<?php

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );


  $fields['PRO_UID'] = G::GenerateUniqueID();;
  $fields['DOC_UID'] = G::GenerateUniqueID();;

  $fields['DOC_KT_TYPE_ID'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktDocType';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktDocTypeEdit', '', $fields, 'ktDocTypeSave' );  
  G::RenderPage('publish');   
?>