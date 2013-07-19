<?php
  try {  	
  
    $G_MAIN_MENU = 'workflow';
    $G_SUB_MENU = 'sigplusSigners';
    $G_ID_MENU_SELECTED = '';
    $G_ID_SUB_MENU_SELECTED = '';
  
   // $sigUid = $_POST['SIG_UID'];

    $sigUid = '0390280a9380as91d18s20cxxx3980d9';

    $G_PUBLISH = new Publisher;
  
    require_once ( PATH_PLUGINS . 'sigplus' . PATH_SEP . 'class.sigplus.php');
    $pluginObj = new sigplusClass ();
  
    require_once ( "classes/model/SigplusSigners.php" );
  
    $fields = "era";


    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'sigplus/sigplusSignersView', $fields , array(),'');
    G::RenderPage('publish');
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
