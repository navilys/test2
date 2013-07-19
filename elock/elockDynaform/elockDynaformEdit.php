<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $UidDynaform = str_replace ( '"', '', $aux[0] );
  $UidApplication = str_replace ( '"', '', $aux[1] );
  
  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockDynaform.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = ElockDynaformPeer::retrieveByPK( $UidDynaform, $UidApplication  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockDynaform' ) ) { 
     $fields['UID_DYNAFORM'] = $tr->getUidDynaform();
     $fields['UID_APPLICATION'] = $tr->getUidApplication();
     $fields['BASE64'] = $tr->getBase64();
     $fields['USER'] = $tr->getUser();
     $fields['timestamp'] = $tr->getTimestamp();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockDynaform';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockDynaformEdit', '', $fields, 'elockDynaformSave' );
  G::RenderPage('publish');   
?>