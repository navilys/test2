<?php

$RBAC->requirePermissions('PM_SETUP', 'PM_USERS');

$G_MAIN_MENU            = 'processmaker';
$G_ID_MENU_SELECTED     = 'ID_CONFIGURATION';
$G_PUBLISH = new Publisher;

$G_PUBLISH->AddContent('view', 'ProductionAS400/configuration_load');
G::RenderPage('publish');