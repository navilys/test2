<?php
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");





$_SESSION["DASHLET_URL_CASEOPEN"] = (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"]))? Library::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . $_GET["sysSkin"] . "/cases/open?" . $_SERVER["QUERY_STRING"] : null;





//$RBAC->requirePermissions("PM_DASHBOARD");

$G_MAIN_MENU        = "processmaker";
$G_ID_MENU_SELECTED = "DASHBOARD";

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent("view", "advancedDashboards/dashletCasesDrillDownCaseOpen.html");

G::RenderPage("publish");
?>