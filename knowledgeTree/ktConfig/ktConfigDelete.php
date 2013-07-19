<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $UsrUid = str_replace ( '"', '', $aux[$index++] );

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtConfig.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = KtConfigPeer::retrieveByPK( $UsrUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtConfig' ) ) { 
     $fields['USR_UID'] = $tr->getUsrUid();
     $fields['LABEL_USR_UID'] = $tr->getUsrUid();
     $fields['KT_USERNAME'] = $tr->getKtUsername();
     $fields['LABEL_KT_USERNAME'] = $tr->getKtUsername();
     $fields['KT_PASSWORD'] = $tr->getKtPassword();
     $fields['LABEL_KT_PASSWORD'] = $tr->getKtPassword();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'ktConfig';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'knowledgeTree/ktConfigDelete', '', $fields, 'ktConfigDeleteExec' );
  G::RenderPage('publish');   
?>