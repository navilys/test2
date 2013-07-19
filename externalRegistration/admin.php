<?php
$headPublisher =& headPublisher::getSingleton();
$headPublisher->addExtJsScript('externalRegistration/admin', false);
G::RenderPage('publish', 'extJs');