<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktConfig';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtConfig.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  KtConfigPeer::USR_UID );
  $Criteria->addSelectColumn (  KtConfigPeer::KT_USERNAME );
  $Criteria->addSelectColumn (  KtConfigPeer::KT_PASSWORD );

  $Criteria->add (  ktConfigPeer::USR_UID, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'knowledgeTree/ktConfigList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
