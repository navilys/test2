<?php

// code inside folder
//  require_once ( PATH_PLUGINS . 'sigplus' . PATH_SEP . 'class.sigplus.php');
//  $pluginObj = new sigplusClass ();
//  require_once ( "classes/model/SigplusSigners.php" );
//
//  // $stepUid = $GET['STEP_UID'];
//  $stepUid = '7415424644b13ebf70da5d9010114914';
//
//  $tr = SigplusSignersPeer::retrieveByPK($stepUid);
//  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'SigplusSigners' ) ) {
//    $fields = array();
//    $fields = unserialize($tr->getSigSigners());
//  } else {
//    $fields = array();
//  }
//
//  $signers = array ();
//
//  foreach($fields as $value){
//    $signers[] = $value['signer_name'];
//  }
//  print_r($signers);

  $G_PUBLISH = new Publisher;
  $aMessage['TITLE'] = "Please Start the Signing Process";
  $aMessage['BODY']  = "";

  $G_PUBLISH->AddContent('smarty', 'sigplus/sigplusSignersStart', '', '', $aMessage );
  G::RenderPage('publish');
  //end signers array pass

?>
