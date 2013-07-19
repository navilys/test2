<?php
try {
  $oHeadPublisher = &headPublisher::getSingleton();
  
  $oHeadPublisher->addContent("convergenceList/convergenceListApplication2"); //Adding a html file .html.
  $oHeadPublisher->addExtJsScript("convergenceList/convergenceListApplication2", false); //Adding a javascript file .js

  G::RenderPage("publish", "extJs");
} catch (Exception $e) {
  $G_PUBLISH = new Publisher;
  
  $aMessage["MESSAGE"] = $e->getMessage();
  $G_PUBLISH->AddContent("xmlform", "xmlform", "convergenceList/messageShow", "", $aMessage);
  G::RenderPage("publish", "blank");
}
?>