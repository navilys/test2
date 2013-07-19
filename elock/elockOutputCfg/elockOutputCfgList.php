<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockOutputCfg';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockOutputCfg.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  ElockOutputCfgPeer::STP_UID );
  $Criteria->addSelectColumn (  ElockOutputCfgPeer::PRO_UID );
  $Criteria->addSelectColumn (  ElockOutputCfgPeer::TAS_UID );
  $Criteria->addSelectColumn (  ElockOutputCfgPeer::DOC_UID );

  $Criteria->add (  elockOutputCfgPeer::STP_UID, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'elock/elockOutputCfgList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
