<?php
try {
  $oHeadPublisher = &headPublisher::getSingleton();
  
  $oHeadPublisher->addContent("aquitaineProject/aquitaineProjectApplication2"); //Adding a html file .html.
  $oHeadPublisher->addExtJsScript("aquitaineProject/aquitaineProjectApplication2", false); //Adding a javascript file .js

  G::RenderPage("publish", "extJs");
} catch (Exception $e) {
  $G_PUBLISH = new Publisher;
  
  $aMessage["MESSAGE"] = $e->getMessage();
  $G_PUBLISH->AddContent("xmlform", "xmlform", "aquitaineProject/messageShow", "", $aMessage);
  G::RenderPage("publish", "blank");
}
?>