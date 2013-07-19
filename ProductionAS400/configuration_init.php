<?php

$RBAC->requirePermissions("PM_SETUP", "PM_USERS");

///////
$adminSelected = null;

if (isset($_REQUEST["s"])) {
  $adminSelected = $_REQUEST["s"];
}
else {
  if (isset($_SESSION["ADMIN_SELECTED"])) {
    $adminSelected = $_SESSION["ADMIN_SELECTED"];
  }
}
//G::pr("admin ".$adminSelected);die; 
///////
$oHeadPublisher = &headPublisher::getSingleton();

global $G_TMP_MENU;

$oMenu = new Menu();
$oMenu->load("setup");
$items = array();
$menuTypes = array();
$menuTypes[0] = array('id' => 'configuration', 'title' => 'Configuration');
$tabItems = array();
$i = 0;
//$menuTypes = array_unique( $oMenu->Types );

foreach ($menuTypes as $menuType) {
   $tabItems[$i]->id = $menuType['id'];
   $LABEL_TRANSLATION = G::LoadTranslation("ID_" . strtoupper($menuType['title']));
  if (substr($LABEL_TRANSLATION, 0, 2) !== "**") {
    $title = $LABEL_TRANSLATION;
  }
  else {
    $title = str_replace("_", " ", ucwords($menuType));
  }
  
  $tabItems[$i]->title = $title;
  $i++;
}

$tabActive = "";
$tabActive="configuration";
///////
$oHeadPublisher->addExtJsScript("ProductionAS400/configurationOptions", true); //adding a javascript file .js 
$oHeadPublisher->addContent("ProductionAS400/configuration"); //adding a html file .html.
$oHeadPublisher->assign("tabActive", $tabActive);
$oHeadPublisher->assign("tabItems", $tabItems);
$oHeadPublisher->assign("_item_selected", (($adminSelected != null)? $adminSelected : ""));

G::RenderPage("publish", "extJs");

//this patch enables the load of the plugin list panel inside de main admin panel iframe
if (isset($_GET["action"]) && $_GET["action"] == "pluginsList") {
  echo "
  <script type=\"text/javascript\">
  
  document.getElementById(\"setup-frame\").src = \"pluginsList\";
  </script>
  ";
}
