<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;

  require_once ( PATH_PLUGINS . 'sigplus' . PATH_SEP . 'class.sigplus.php');
  $pluginObj = new sigplusClass ();

  require_once ( "classes/model/SigplusSigners.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = SigplusSignersPeer::retrieveByPK(   );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'SigplusSigners' ) ) { 
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'sigplusSigners';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'sigplus/sigplusSignersDelete', '', $fields, 'sigplusSignersDeleteExec' );
  G::RenderPage('publish');   
?>