<?php
G::LoadClass('pmFunctions');
G::LoadClass('case');
G::loadClass('configuration');
require_once ("classes/model/Dynaform.php");
global $G_PUBLISH;
$oHeadPublisher =& headPublisher::getSingleton();     
$conf = new Configurations;
$APP_UID = $_GET['appUid'];
$ADAPTIVEHEIGHT = $_GET['adaptiveHeight'];
$NUM_DOSSIER = $_GET['num_dossier'];
$oHeadPublisher->assign('APP_UID', $APP_UID);
$oHeadPublisher->assign('ADAPTIVEHEIGHT', $ADAPTIVEHEIGHT);
$oHeadPublisher->assign('NUM_DOSSIER', $NUM_DOSSIER);
$oHeadPublisher->addExtJsScript('convergenceList/actionGeneralInfo', true );    //adding a javascript file .js
$oHeadPublisher->addContent    ('convergenceList/caseHistoryDynaformPage'); //adding a html file  .html.      
$oHeadPublisher->assign('pageSize', $conf->getEnvSetting('casesListRowNumber'));    
G::RenderPage('publish', 'extJs');  