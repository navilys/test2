<?php    
    G::LoadClass('case');
    G::LoadClass('configuration');
    G::loadClass('pmFunctions');    
    $oHeadPublisher   =& headPublisher::getSingleton();    
    $oHeadPublisher->addExtJsScript(PATH_PLUGINS.SYS_COLLECTION.'/adminAutorisations', false, true); 
    G::RenderPage('publish', 'extJs');
?> 