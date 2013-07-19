<?php
global $G_TMP_MENU;

if (class_exists("pmLicenseManager")) {
  $pmLicenseManagerO = &pmLicenseManager::getSingleton();
  $licenseStatusInfo = $pmLicenseManagerO->getCurrentLicenseStatus();
  $licStatusMsg = null;
  
  if ((isset($pmLicenseManagerO->plan)) && ($pmLicenseManagerO->plan != "")) {
    $lines = explode(" - ", $pmLicenseManagerO->plan);
    if (isset($lines[0])) {
      $licStatusMsg .= "<br><i><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lines[0] . "</small></i>";
    }
    if ((isset($lines[1])) && ($lines[1] != $lines[0])) {
      $licStatusMsg .= "<br><i><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lines[1] . "</small></i>";
    }
  }
  
  if($licenseStatusInfo["message"] != "") {
    $licStatusMsg = "&nbsp;<font color=\"red\">(" . $licenseStatusInfo["message"] . ")</font>";
  }
  
  $G_TMP_MENU->AddIdRawOption("PMENTERPRISE", "../enterprise/addonsStore", "Enterprise Plugins Manager" . $licStatusMsg, "", "", "admToolsContent");
  
  if (isset($pmLicenseManagerO->result) && ($pmLicenseManagerO->result == "OK")) {
    if (file_exists(PATH_HOME . "engine" . PATH_SEP . "methods" . PATH_SEP . "cases" . PATH_SEP . "casesListExtJs.php")) {
      $G_TMP_MENU->AddIdRawOption("CASES_LIST_SETUP", "../enterprise/advancedTools/casesListSetup", G::LoadTranslation("ID_CASES_LIST_SETUP"), "", "", "settings");
    }
  }
}
?>