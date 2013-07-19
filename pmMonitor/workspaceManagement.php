<?php
require_once (PATH_PLUGINS . "pmMonitor" . PATH_SEP . "classes" . PATH_SEP . "class.wsmgmWorkspaceRestore.php");





$o = new workspaceRestore();

$G_MAIN_MENU = "processmaker";
$G_ID_MENU_SELECTED = "SETUP";
$G_PUBLISH = new Publisher();

global $G_TMP_MENU;

$oMenu = new Menu();
$oMenu->load("setup");
$toolItems = array();

$aFields = array();

G::LoadClass("serverConfiguration");
$oServerConf = &serverConf::getSingleton();

$dir = PATH_DB;
$filesArray = array();

if (file_exists($dir)) {
  if ($handle = opendir($dir)) {
    while (($file = readdir($handle)) !== false) {
      if (($file != ".") && ($file != "..")) {
        if (file_exists(PATH_DB . $file . "/db.php")) { //print $file."/db.php <hr>";
          $statusl = ($oServerConf->isWSDisabled($file))? G::LoadTranslation("ID_DISABLED") : G::LoadTranslation("ID_ENABLED");
          if (strcmp(SYS_SYS, $file) != 0) {
            $aFields[] = array (
              "WSP_ID" => $file, 
              "WSP_NAME" => $file, 
              "WSP_STATUS" => $statusl 
            );
          }
        }
      }
    }
     
    closedir($handle);
  }
}

for ($i = 0; $i < count($aFields) - 1; $i++) {
  for ($j = $i + 1; $j < count($aFields); $j++) {
    if (strtoupper(substr($aFields[$i]["WSP_ID"], 0, 1)) > strtoupper(substr($aFields[$j]["WSP_ID"], 0, 1))) {
      $x = $aFields[$i];
      $aFields[$i] = $aFields[$j];
      $aFields[$j] = $x;
    }
  }
}

foreach ($aFields as $i => $option) {
  $aWSInfo = $o->getWorkspaceInfo($option["WSP_ID"]);

  if ($aWSInfo === false) {
    continue;
  }

  $sImage = (isset($aWSInfo["logoName"]))? $aWSInfo["logoName"] : "";
  $nProc = $aWSInfo["num_processes"];
  $nCases = $aWSInfo["num_cases"];
  //$image = (isset($sImage) && ($sImage!=""))? "../setup/showLogoFile.php?id=".G::encrypt($sImage,"imagen")."&wsName=".$aWSInfo["wsName"]: "/images/logo_processmaker.gif";
  $image = (isset($sImage) && ($sImage != ""))? "../setup/showLogoFile.php?id=" . base64_encode($sImage) . "&wsName=" . $aWSInfo["wsName"] : "/images/logo_processmaker.gif";
  $link_option = "status_workspace?WSP_ID=" . $option["WSP_ID"];
    
  if ($option["WSP_STATUS"] == "Enabled") {
    $icon_option = "dialog-ok-apply.png";
    $icon_option_label = G::loadTranslation("ID_DISABLE_WORKSPACE");
    $status = "<font color=\"#154444\">".(strtoupper($option["WSP_STATUS"]))."</font>";
  }
  else {
    $icon_option = "dialog-cancel.png";
    $icon_option_label = G::loadTranslation("ID_ENABLE_WORKSPACE");
    $status = "<font color=\"#800000\">".(strtoupper($option["WSP_STATUS"]))."</font>";
  }
     
  $toolItems [] = array (
    "id" => $option["WSP_ID"], 
    "link" => "#", 
    "label" => $option["WSP_NAME"], 
    "icon" => $image, 
    "statusf" => $status ,
    "status" => strtoupper($option["WSP_STATUS"]),
    "icon_option"=>$icon_option,
    "icon_option_label"=>$icon_option_label,
    "link_option"=>$link_option,
    "nProc"=>$nProc,
    "nCases"=>$nCases
  );
}

$template = new TemplatePower(PATH_PLUGINS . "pmMonitor" . PATH_SEP . "workspaceManagement.html");
$template->prepare();

$template->assign("LeftWidth", "230");
$template->assign("contentHeight", "520");

foreach ($toolItems as $item) {
  $template->newBlock("tool_options");
  foreach ($item as $propertyName => $propertyValue) {
    $template->assign($propertyName, $propertyValue);
  }
}

$G_PUBLISH->AddContent("template", "", "", "", $template);
G::RenderPage("publishBlank", "blank");
?>