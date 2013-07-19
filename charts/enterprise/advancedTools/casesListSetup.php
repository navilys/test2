<?php
global $RBAC;

if($RBAC->userCanAccess("PM_SETUP") != 1 || $RBAC->userCanAccess("PM_SETUP_ADVANCE") != 1){
    G::SendTemporalMessage("ID_USER_HAVENT_RIGHTS_PAGE", "error", "labels");
    exit(0);
}

//Validating if the template files for the cases setup has been already copied
if (!file_exists(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js")){
    copy(PATH_PLUGINS . "enterprise" . PATH_SEP . "advancedTools" . PATH_SEP . "casesListSetup.js"  , PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js");
}

if (!file_exists(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html")){
    copy(PATH_PLUGINS . "enterprise" . PATH_SEP . "advancedTools" . PATH_SEP . "casesListSetup.html", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html");
}

$availableFields = array();

$oHeadPublisher = &headPublisher::getSingleton();
$oHeadPublisher->addContent("cases/casesListSetup"); //Adding a html file .html.
$oHeadPublisher->addExtJsScript("cases/casesListSetup", false); //Adding a javascript file .js
$oHeadPublisher->assignNumber("pageSize", 20); //sending the page size
$oHeadPublisher->assignNumber("availableFields", G::json_encode($availableFields));

G::RenderPage("publish", "extJs");

