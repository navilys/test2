<?php
if (strpos($_SERVER["REQUEST_URI"], "main_init") === false) {
  global $G_TMP_MENU;

  $G_TMP_MENU->AddIdRawOption("PLUGIN_REPTAB_PERMISSIONS", "#", "Permissions", "", "", "private");
  //$G_TMP_MENU->AddIdRawOption("PLUGIN_REPTAB_PERMISSIONS", "myFunc()", "Permissions", "", "", "private");
}
?>

