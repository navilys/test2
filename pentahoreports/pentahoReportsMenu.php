<?php
try {
 /**
  * @section Filename
  * pentahoReportsMenu.php
  * @subsection Description
  * Render the Reports List main view
  * @subsection copyright
  * Copyright (C) 2004 - 2010 Colosa Inc.23
  * <hr>
  * @package plugins.pentahoreports.scripts
  */

  /**
   * The main menu rendered
   */
  $G_MAIN_MENU = 'processmaker';

  /**
   * The main menu selected
   */
  $G_ID_MENU_SELECTED     = 'ID_PENTAHOREPORTS';

  /**
   * The sub menu rendered
   */
  $G_SUB_MENU             = ''; 

  /**
   * The sub menu selected
   */
  $G_ID_SUB_MENU_SELECTED = 'ID_PENTAHOREPORTSLIST';

  /**
   * setting the headers
   */
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptCode(file_get_contents(PATH_PLUGINS . 'pentahoreports/pentahoReportsList.js')); 

  /**
   * getting the main pentahoreports class 
   */
  require_once ( "class.pentahoreports.php" );

  /**
   * Defining the Object pentaho 
   */
  $objPentaho = new pentahoreportsClass();
  $objPentaho->readLocalConfig( SYS_SYS );
  
  /** 
   * getting the folder content
   */
  $folderContent = $objPentaho->getSolutionWorkspace( SYS_SYS , $_SESSION['_DBArray']['processes'] );

  /**
   * publishing the pentaho reports list tree
   */
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoReportsListTree' );
  G::RenderPage( "publish-treeview" , "blank");

} catch ( Exception $e ) {
  $G_PUBLISH = new Publisher;
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pentahoreports/showMessage', '', $aMessage );
  G::RenderPage('publish');
}
