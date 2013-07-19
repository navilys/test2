<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $DocKtTypeId = str_replace ( '"', '', $aux[$index++] );
  $ProUid = str_replace ( '"', '', $aux[$index++] );

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtFieldsMap.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = KtFieldsMapPeer::retrieveByPK( $DocKtTypeId, $ProUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) { 
     $fields['DOC_KT_TYPE_ID'] = $tr->getDocKtTypeId();
     $fields['LABEL_DOC_KT_TYPE_ID'] = $tr->getDocKtTypeId();
     $fields['PRO_UID'] = $tr->getProUid();
     $fields['LABEL_PRO_UID'] = $tr->getProUid();
     $fields['FIELDS_MAP'] = $tr->getFieldsMap();
     $fields['LABEL_FIELDS_MAP'] = $tr->getFieldsMap();
     $fields['DESTINATION_PATH'] = $tr->getDestinationPath();
     $fields['LABEL_DESTINATION_PATH'] = $tr->getDestinationPath();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktFieldsMap';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktFieldsMapDelete', '', $fields, 'ktFieldsMapDeleteExec' );
  G::RenderPage('publish');   
?>