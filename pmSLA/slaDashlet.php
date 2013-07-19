<?php
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$c = new Configurations();
$configPage = $c->getConfiguration('additionalTablesList', 'pageSize','',$_SESSION['USER_LOGGED']);
$Config['pageSize'] = isset($configPage['pageSize']) ? $configPage['pageSize'] : 20;

$oHeadPublisher = & headPublisher::getSingleton ();

$oHeadPublisher->assign("FORMATS", $c->getFormats());
$oHeadPublisher->assign("CONFIG", $Config);
// $oHeadPublisher->assign("aStoreFields", $aStoreFields);

$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/labelsplugin', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaDashlet', false, true );
G::RenderPage ('publish', 'extJs');

