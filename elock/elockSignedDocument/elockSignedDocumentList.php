<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockSignedDocument';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockSignedDocument.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  ElockSignedDocumentPeer::APP_DOC_UID );
  $Criteria->addSelectColumn (  ElockSignedDocumentPeer::DOC_VERSION );
  $Criteria->addSelectColumn (  ElockSignedDocumentPeer::DOC_UID );
  $Criteria->addSelectColumn (  ElockSignedDocumentPeer::USR_UID );
  $Criteria->addSelectColumn (  ElockSignedDocumentPeer::SIGN_DATE );

  $Criteria->add (  elockSignedDocumentPeer::APP_DOC_UID, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'elock/elockSignedDocumentList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
