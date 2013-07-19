<?php
$oHeadPublisher = & headPublisher::getSingleton ();

// $oHeadPublisher->assign("aStoreFields", $aStoreFields);

$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/functions', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/globalVariablesForm', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/globalVariablesGrid', false, true );
$oHeadPublisher->addContent ('globalVariablesTemplate');

G::RenderPage ('publish', 'extJs');

