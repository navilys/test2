<?php

$oHeadPublisher = & headPublisher::getSingleton ();

// $oHeadPublisher->assign("aStoreFields", $aStoreFields);

$oHeadPublisher->addContent( "globalVariablesTemplate" );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/functions', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/businessRulesGridExcelFiles', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/businessRulesGridPmrlFiles', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/businessRulesMain', false, true );
G::RenderPage ('publish', 'extJs');
