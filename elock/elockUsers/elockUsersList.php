<?php
  try {  	

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'elockUsers';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';

  $G_PUBLISH = new Publisher;

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockUsers.php" );

  $Criteria = new Criteria('workflow');
  $Criteria->clearSelectColumns ( );
  
  $Criteria->addSelectColumn (  ElockUsersPeer::USR_USERNAME );
  $Criteria->addSelectColumn (  ElockUsersPeer::USR_PASSWORD );

  $Criteria->add (  elockUsersPeer::USR_USERNAME, "xx" , CRITERIA::NOT_EQUAL );
  
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'elock/elockUsersList', $Criteria , array(),'');
  G::RenderPage('publish');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
