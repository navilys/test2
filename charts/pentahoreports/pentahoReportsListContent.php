<?php
try {
 /**
  * Render reports Content old view, previous to the panels render.
  * @package plugins.pentahoreports.scripts
  * @deprecated 
  */

  $G_MAIN_MENU = 'processmaker';
  $G_ID_MENU_SELECTED     = 'ID_PENTAHOREPORTS';
  $G_SUB_MENU             = ''; 
  $G_ID_SUB_MENU_SELECTED = 'ID_PENTAHOREPORTSLIST';
  
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptCode(file_get_contents(PATH_PLUGINS . 'pentahoreports/pentahoReportsList.js'));
  
  require_once ( "class.pentahoreports.php" );
  $objPentaho = new pentahoreportsClass();
  $objPentaho->readLocalConfig( SYS_SYS );

echo("entar aqui ...");
  $folderContent = $objPentaho->getSolutionWorkspace( SYS_SYS , $_SESSION['_DBArray']['processes'] );
	
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoReportsListTree' );
  $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoReportsListSeparator' );
  $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoReportsContentList', '', '', array());
  G::RenderPage( "publish-treeview" , "blank" );
}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pentahoreports/showMessage', '', $aMessage );
  G::RenderPage('publish');
}
