<?php

G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );
$oHeadPublisher = & headPublisher::getSingleton ();
//if(isset($_SESSION['APPLICATION']) && $_SESSION['APPLICATION'] != '')
	//$_SESSION['APPLICATION'] = '';
$appUid = $_REQUEST['APP_UID'];
$ADAPTIVEHEIGHT = $_REQUEST['ADAPTIVEHEIGHT'];

$oHeadPublisher->assign('APP_UID', $appUid);
$oHeadPublisher->assign('ADAPTIVEHEIGHT', $ADAPTIVEHEIGHT);
$oHeadPublisher->addExtJsScript ( PATH_PLUGINS . SYS_COLLECTION . '/cases_Edit', true, true );
G::RenderPage ( 'publish', 'extJs' );

?> 

