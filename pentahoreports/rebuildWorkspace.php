<?php
/**
 * @section Filename
 * rebuildWorkspace.php
 * @subsection Description
 * This is the View of all users for a role
 *
 * @deprecated
 * @Date 17/05/2010
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

try {
 
  /* Render page */
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'ID_PENTAHOREPORTS';
  $G_SUB_MENU             = ''; //pentahoreports/submenu';
  $G_ID_SUB_MENU_SELECTED = 'ID_PENTAHOREPORTSLIST';

  require_once ( "class.pentahoreports.php" );
  $objPentaho = new pentahoreportsClass();
  $objPentaho->readLocalConfig( SYS_SYS );
  // Create a new solution workspace
  $result = $objPentaho->createNewSolutionWorkspace( SYS_SYS );
  die;
      	
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoReportsListTree' );
  G::RenderPage( "publish-treeview" );
}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pentahoreports/showMessage', '', $aMessage );
  G::RenderPage('publishBlank', 'blank');
}
