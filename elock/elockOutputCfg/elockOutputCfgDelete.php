<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $StpUid = str_replace ( '"', '', $aux[$index++] );

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockOutputCfg.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = ElockOutputCfgPeer::retrieveByPK( $StpUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockOutputCfg' ) ) { 
     $fields['STP_UID'] = $tr->getStpUid();
     $fields['LABEL_STP_UID'] = $tr->getStpUid();
     $fields['PRO_UID'] = $tr->getProUid();
     $fields['LABEL_PRO_UID'] = $tr->getProUid();
     $fields['TAS_UID'] = $tr->getTasUid();
     $fields['LABEL_TAS_UID'] = $tr->getTasUid();
     $fields['DOC_UID'] = $tr->getDocUid();
     $fields['LABEL_DOC_UID'] = $tr->getDocUid();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockOutputCfg';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockOutputCfgDelete', '', $fields, 'elockOutputCfgDeleteExec' );
  G::RenderPage('publish');   
?>