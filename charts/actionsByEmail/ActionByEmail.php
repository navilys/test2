<?php

  $oHeadPublisher->addExtJsScript('actionsByEmail/report', false );    //adding a javascript file .js  
  //$oHeadPublisher->addContent('cases/casesListExtJs'); //adding a html file  .html.

  G::RenderPage('publish', 'extJs');
