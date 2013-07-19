<?php

 /**
  * @section Filename
  * menupentahoreports.php
  * @subsection Description
  * This script generates the menu and also validates that if the menu dashboard is active use this option or if not generates a new value for the main menu.
  * @author Gustavo Cruz <gustavo@colosa.com>
  * @subsection Copyright
  * Copyright (C) Colosa Development Team 2010
  * <hr>
  * @package plugins.pentahoreports.menues
  */
  global $G_TMP_MENU;

 /**
  * reuse the dashboard option menu
  */
  $active = false;

// if the plugin is not set yet dont enable the reports and dashboard functions
if (file_exists(PATH_DATA.'pentaho.conf')&&file_exists( PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'pentahoreports.conf' )){
  foreach ( $G_TMP_MENU->Id as $key => $val ) {
    if ( $val == 'DASHBOARD' ) {
      $G_TMP_MENU->Options[$key] = 'pentahoreports/dashboard';
      $active = true;
    }
  }
  // if the menu is not active for this user put a new menu
  $G_TMP_MENU->AddIdRawOption('ID_PENTAHOREPORTS', 'pentahoreports/pentahoreportsList', G::LoadTranslation('ID_REPORTS') );
}
