<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $UidDynaform = str_replace ( '"', '', $aux[$index++] );
  $UidApplication = str_replace ( '"', '', $aux[$index++] );

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockDynaform.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = ElockDynaformPeer::retrieveByPK( $UidDynaform, $UidApplication  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockDynaform' ) ) { 
     $fields['UID_DYNAFORM'] = $tr->getUidDynaform();
     $fields['LABEL_UID_DYNAFORM'] = $tr->getUidDynaform();
     $fields['UID_APPLICATION'] = $tr->getUidApplication();
     $fields['LABEL_UID_APPLICATION'] = $tr->getUidApplication();
     $fields['BASE64'] = $tr->getBase64();
     $fields['LABEL_BASE64'] = $tr->getBase64();
     $fields['USER'] = $tr->getUser();
     $fields['LABEL_USER'] = $tr->getUser();
     $fields['timestamp'] = $tr->getTimestamp();
     $fields['LABEL_timestamp'] = $tr->getTimestamp();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockDynaform';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockDynaformDelete', '', $fields, 'elockDynaformDeleteExec' );
  G::RenderPage('publish');   
?>