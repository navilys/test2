<?php

//calculating the max upload file size;
$POST_MAX_SIZE   = ini_get('post_max_size');
$mul = substr($POST_MAX_SIZE, -1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$postMaxSize = (int)$POST_MAX_SIZE * $mul;

$UPLOAD_MAX_SIZE = ini_get('upload_max_filesize');
$mul = substr($UPLOAD_MAX_SIZE, -1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$uploadMaxSize = (int)$UPLOAD_MAX_SIZE * $mul;

if ( $postMaxSize < $uploadMaxSize ) $uploadMaxSize = $postMaxSize;

G::loadClass ( 'pmFunctions' );
$queryOptions = "SELECT CFG_USR_ID AS CONFIG_USERS_ID, CFG_USR_FIELD_NAME AS FIELD_NAME, CFG_USR_DESCRIPTION AS DESCRIPTION, CFG_USR_TYPE AS TYPE, 
				 CFG_USR_TYPE_ACTION AS TYPE_ACTION, CFG_USR_PARAMETERS AS PARAMETERS
				 FROM PMT_CONFIG_USERS WHERE CFG_USR_STATUS = 'ACTIVE'
				 ";
$options = executeQuery($queryOptions);
$arrayOptions = Array();
foreach($options as $index)
{
	$arrayOptions[] = $index;
}

$oHeadPublisher =& headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript('fieldcontrol/users', true );    //adding a javascript file .js
$oHeadPublisher->assign('USR_UID', '');
$oHeadPublisher->assign('MODE', $_GET['MODE']);
$oHeadPublisher->assign('MAX_FILES_SIZE', ' (' . $UPLOAD_MAX_SIZE . ') ');
$oHeadPublisher->assign('arrayOptions', $arrayOptions );
G::RenderPage('publish', 'extJs');
