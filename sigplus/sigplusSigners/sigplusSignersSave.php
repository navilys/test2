<?php
try {
  require_once (PATH_PLUGINS . "sigplus" . PATH_SEP . "class.sigplus.php");
  $pluginObj = new sigplusClass();
  
  require_once ("classes/model/SigplusSigners.php");
  
  $form = $_POST["form"];
  
  $pk = $form["STP_UID"];
  $proUid = $form["PRO_UID"];
  $form["SIG_SIGNERS"] = serialize($form["SIGNERS_GRID"]);

  //If exists the row in the database propel will update it, otherwise will insert.
  $tr = SigplusSignersPeer::retrieveByPK($pk);
  if (!(is_object($tr) &&  get_class($tr) == "SigplusSigners")) {
    $tr = new SigplusSigners();
      
    $tr->setStpUid($form["STP_UID"]);
    $tr->setProUid($form["PRO_UID"]);
    $tr->setTasUid($form["TAS_UID"]);
    $tr->setDocUid($form["DOC_UID"]);
    $tr->setSigSigners($form["SIG_SIGNERS"]);
  }
  else {
    $tr->setDocUid($form["DOC_UID"]);
    $tr->setSigSigners($form["SIG_SIGNERS"]);
  }

  if($tr->validate()) {
    $res = $tr->save();
  }
  else {
    //Something went wrong. We can now get the validationFailures and handle them.
    $msg = null;
    $validationFailuresArray = $tr->getValidationFailures();
    
    foreach ($validationFailuresArray as $objValidationFailure) {
      $msg = $msg . $objValidationFailure->getMessage() . "<br />";
    }
    //return array("codError" => -100, "rowsAffected" => 0, "message" => $msg);
  }

  G::Header("location: ../../processes/processes_Map?PRO_UID=" . $proUid);
}
catch (Exception $e) {
  $template = new TemplatePower(PATH_PLUGINS . "sigplus" . PATH_SEP . "messageShow.html");
  $template->prepare();
  $template->assign("TITLE",   "Caught Exception");
  $template->assign("MESSAGE", $e->getMessage());
  
  $contentData = $template;
  
  $G_PUBLISH = new Publisher;
  
  $G_PUBLISH->AddContent("template", null, null, null, $contentData);
  G::RenderPage("publish", "blank");
}
?>