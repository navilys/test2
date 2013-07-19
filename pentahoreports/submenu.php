<?php
/**
 * submenu.php
 * submenu that renders the old interface list and content
 * 
 * @deprecated
 * @package plugins.pentahoreports.scripts
 * @Date 17/05/2010
 */
  global $G_TMP_MENU;
  $G_TMP_MENU->AddIdRawOption('ID_PENTAHOREPORTSLIST',   'pentahoreports/pentahoreportsList', "List" );
  $G_TMP_MENU->AddIdRawOption('ID_PENTAHOREPORTSIFRAME', 'pentahoreports/iframe', "Iframe" );

?>
