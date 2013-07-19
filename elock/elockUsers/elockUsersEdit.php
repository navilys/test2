<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $UsrUsername = str_replace ( '"', '', $aux[0] );
  
  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockUsers.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = ElockUsersPeer::retrieveByPK( $UsrUsername  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockUsers' ) ) { 
     $fields['USR_USERNAME'] = $tr->getUsrUsername();
     $fields['USR_PASSWORD'] = $tr->getUsrPassword();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockUsers';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockUsersEdit', '', $fields, 'elockUsersSave' );
  G::RenderPage('publish');   
?>