<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $ProUid = str_replace ( '"', '', $aux[$index++] );
  $DocUid = str_replace ( '"', '', $aux[$index++] );

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) { 
     $fields['PRO_UID'] = $tr->getProUid();
     $fields['LABEL_PRO_UID'] = $tr->getProUid();
     $fields['DOC_UID'] = $tr->getDocUid();
     $fields['LABEL_DOC_UID'] = $tr->getDocUid();
     $fields['DOC_KT_TYPE_ID'] = $tr->getDocKtTypeId();
     $fields['LABEL_DOC_KT_TYPE_ID'] = $tr->getDocKtTypeId();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktDocType';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktDocTypeDelete', '', $fields, 'ktDocTypeDeleteExec' );
  G::RenderPage('publish');   
?>