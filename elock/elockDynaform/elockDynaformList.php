<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockDynaform';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockDynaform.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  ElockDynaformPeer::UID_DYNAFORM );
  $Criteria->addSelectColumn (  ElockDynaformPeer::UID_APPLICATION );
  $Criteria->addSelectColumn (  ElockDynaformPeer::BASE64 );
  $Criteria->addSelectColumn (  ElockDynaformPeer::USER );
  $Criteria->addSelectColumn (  ElockDynaformPeer::timestamp );

  $Criteria->add (  elockDynaformPeer::UID_DYNAFORM, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'elock/elockDynaformList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
