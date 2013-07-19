<?php
//eAccelerator
$swEA = 0;

if (function_exists("eaccelerator_info")) {
  //$info = eaccelerator_info();
  
  //if (is_array($info)) {
    $swEA = 1;
  //}
}

///////
$oHeadPublisher->assign("EACCELERATOR_INSTALLED", $swEA);
$oHeadPublisher->assign("EACCELERATOR_CACHEKEYS", (function_exists("eaccelerator_get"))? 1 : 0);

$oHeadPublisher->addExtJsScript("pmMonitor/main", true); //adding a javascript file .js
$oHeadPublisher->addContent("pmMonitor/main"); //adding a html file  .html.

G::RenderPage("publish", "extJs");
?>