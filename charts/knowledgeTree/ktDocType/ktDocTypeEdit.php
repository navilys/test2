<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $ProUid = str_replace ( '"', '', $aux[0] );
  $DocUid = str_replace ( '"', '', $aux[1] );
  
  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) { 
     $fields['PRO_UID'] = $tr->getProUid();
     $fields['DOC_UID'] = $tr->getDocUid();
     $fields['DOC_KT_TYPE_ID'] = $tr->getDocKtTypeId();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktDocType';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktDocTypeEdit', '', $fields, 'ktDocTypeSave' );
  G::RenderPage('publish');   
?>