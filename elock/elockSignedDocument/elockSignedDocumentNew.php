<?php

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockSignedDocument.php" );


  $fields['APP_DOC_UID'] = G::GenerateUniqueID();;
  $fields['DOC_VERSION'] = G::GenerateUniqueID();;

  $fields['DOC_UID'] = '';
  $fields['USR_UID'] = '';
  $fields['SIGN_DATE'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockSignedDocument';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockSignedDocumentEdit', '', $fields, 'elockSignedDocumentSave' );  
  G::RenderPage('publish');   
?>