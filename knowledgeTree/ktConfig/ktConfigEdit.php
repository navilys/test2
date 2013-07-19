<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $UsrUid = str_replace ( '"', '', $aux[0] );
  
  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtConfig.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = KtConfigPeer::retrieveByPK( $UsrUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtConfig' ) ) { 
     $fields['USR_UID'] = $tr->getUsrUid();
     $fields['KT_USERNAME'] = $tr->getKtUsername();
     $fields['KT_PASSWORD'] = $tr->getKtPassword();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktConfig';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktConfigEdit', '', $fields, 'ktConfigSave' );
  G::RenderPage('publish');   
?>