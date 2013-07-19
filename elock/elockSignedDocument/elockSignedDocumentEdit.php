<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $AppDocUid = str_replace ( '"', '', $aux[0] );
  $DocVersion = str_replace ( '"', '', $aux[1] );
  
  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockSignedDocument.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = ElockSignedDocumentPeer::retrieveByPK( $AppDocUid, $DocVersion  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockSignedDocument' ) ) { 
     $fields['APP_DOC_UID'] = $tr->getAppDocUid();
     $fields['DOC_VERSION'] = $tr->getDocVersion();
     $fields['DOC_UID'] = $tr->getDocUid();
     $fields['USR_UID'] = $tr->getUsrUid();
     $fields['SIGN_DATE'] = $tr->getSignDate();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockSignedDocument';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockSignedDocumentEdit', '', $fields, 'elockSignedDocumentSave' );
  G::RenderPage('publish');   
?>