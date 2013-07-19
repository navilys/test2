<?php
/**
 * @section Filename
 * dashboard.php
 * @subsection Description
 * Render page for the default or personal Dashboard
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010  
 * <hr>
 * @package plugins.pentahoreports.scripts
 */
 /**
  * The main menu definition
  */
  $G_MAIN_MENU = 'processmaker';
 /**
  * The sub menu definition
  */
  $G_ID_MENU_SELECTED     = 'DASHBOARD';

 /**
  * if there is a configuration file, then process the petition
  */
if (file_exists(PATH_DATA.'pentaho.conf')){
  $G_PUBLISH = new Publisher;
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->disableHeaderScripts();
  $G_PUBLISH->AddContent( 'view', 'pentahoreports/dashboardView' );
  G::RenderPage( "publish" );
} else {
  /**
   * if not, render the cases view
   */
  G::header('location: ../cases/main');
}
