<?php
$workspace_id = $_GET["workspace_id"];

G::LoadClass("serverConfiguration");

$oServerConf = &serverConf::getSingleton();
$oServerConf->changeStatusWS($workspace_id);

G::header("Location: workspaceManagement");
?>