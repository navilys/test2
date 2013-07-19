<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktFieldsMap';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtFieldsMap.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  KtFieldsMapPeer::DOC_KT_TYPE_ID );
  $Criteria->addSelectColumn (  KtFieldsMapPeer::PRO_UID );
  $Criteria->addSelectColumn (  KtFieldsMapPeer::FIELDS_MAP );
  $Criteria->addSelectColumn (  KtFieldsMapPeer::DESTINATION_PATH );

  $Criteria->add (  ktFieldsMapPeer::DOC_KT_TYPE_ID, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'knowledgeTree/ktFieldsMapList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
