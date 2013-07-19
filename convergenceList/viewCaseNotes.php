<?php
//G::pr($_REQUEST);

require_once ( "classes/model/AppNotes.php" );
    $appUid = $_REQUEST['APP_UID'];

    //$usrUid   = isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : "";
    $appNotes = new AppNotes();
    $response = $appNotes->getNotesList($appUid, '', 0, 100000);
    //G::pr($response['array']);

	G::LoadClass('pmFunctions');	
	G::loadClass('configuration');	
	global $G_PUBLISH;
	$oHeadPublisher =& headPublisher::getSingleton();     
	$conf = new Configurations;
	$APP_UID = $appUid;
	$oHeadPublisher->assign('APP_UID', $APP_UID);
	$oHeadPublisher->addExtJsScript('convergenceList/viewCaseNotes', true );    //adding a javascript file .js
	$oHeadPublisher->addContent    ('convergenceList/caseHistoryDynaformPage'); //adding a html file  .html.      
	$oHeadPublisher->assign('pageSize', $conf->getEnvSetting('casesListRowNumber'));    
	G::RenderPage('publish', 'extJs');


?>
