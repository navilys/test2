<?php
//Obtain previous and next step - Start
  try {
    $oCase         = new Cases();
    $aNextStep     = $oCase->getNextStep(    $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
    $aPreviousStep = $oCase->getPreviousStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
  }
  catch ( Exception $e ) {
    $_SESSION['G_MESSAGE']      = $e->getMessage();
    $_SESSION['G_MESSAGE_TYPE'] = 'error';
    G::header('location: cases_List' );
  }
  //Obtain previous and next step - End
  //krumo($aPreviousStep);
  //krumo($aNextStep);



  //$aNextStep['LABEL']="<font color='red'>No step</font>";
  //$aPreviousStep['LABEL']="<font color='red'>No step</font>";

  $c = new Criteria();
  $c->add ( StepPeer::PRO_UID, $_SESSION['PROCESS'] );
  $c->add ( StepPeer::TAS_UID, $_SESSION['TASK'] );
  $c->addAscendingOrderByColumn ( StepPeer::STEP_POSITION );

  // classes
  G::LoadClass('pmScript');
  G::LoadClass('case');

  $oPluginRegistry = &PMPluginRegistry::getSingleton();
  $externalSteps   = $oPluginRegistry->getSteps();


  $rs = StepPeer::doSelect( $c );

  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $oPMScript = new PMScript();
  $oPMScript->setFields($Fields['APP_DATA'] );
  foreach ( $rs as $key => $aRow  )
  {
	  $bAccessStep = false;
	  if ($aRow->getStepCondition() != '') {
		  $oPMScript->setScript( $aRow->getStepCondition() );
    	$bAccessStep = $oPMScript->evaluate();
	  }
	  else {
		  $bAccessStep = true;
    }

   if ($bAccessStep)
   {
     switch( $aRow->getStepTypeObj() )
     {
     	case 'DYNAFORM':
     	  $oDocument = DynaformPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getDynTitle(); break;
     	case 'OUTPUT_DOCUMENT':
     	  $oDocument = OutputDocumentPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getOutDocTitle(); break;
     	case 'INPUT_DOCUMENT':
     	  $oDocument = InputDocumentPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getInpDocTitle();
     	  $sType     = $oDocument->getInpDocFormNeeded(); break;
     	case 'EXTERNAL':
  	    $stepTitle          = 'unknown ' . $aRow->getStepUidObj();
      	foreach ( $externalSteps as $key=>$val ) {
      	  if ( $val->sStepId == $aRow->getStepUidObj() )
      		  $stepTitle = $val->sStepTitle;
      	}
     	  //$sType     = $oDocument->getInpDocFormNeeded();
     	  break;
     	default:
     	  $stepTitle = $aRow->getStepUid();
     }

     //krumo($stepTitle);
     //krumo($aRow->getStepUidObj());

     if($aNextStep['UID']==$aRow->getStepUidObj()){
     	$aNextStep['LABEL']=$stepTitle;
     }
   	 if($aPreviousStep['UID']==$aRow->getStepUidObj()){
     	$aPreviousStep['LABEL']=$stepTitle;
     }
    }
  }


   //krumo($aNextStep);
   //krumo($aPreviousStep);


  $TplHeaderNav = new TemplatePower(PATH_PLUGIN_ELOCK."/templates/headerNav.tpl.php");
  $TplHeaderNav->prepare();

  if($aNextStep){
  	if($aNextStep['TYPE']=="DERIVATION") $aNextStep['LABEL']="";
  	$TplHeaderNav->assign("NEXT_STEP",$aNextStep['PAGE']);
  	//$TplHeaderNav->assign("NEXT_STEP_LABEL",$aNextStep['LABEL']);
  	$TplHeaderNav->assign("NEXT_STEP_VIEW","");
  }else{
  	$TplHeaderNav->assign("NEXT_STEP_VIEW","display:none");
  }

  if($aPreviousStep){
  	$TplHeaderNav->assign("PREV_STEP",$aPreviousStep['PAGE']);
  	//$TplHeaderNav->assign("PREV_STEP_LABEL",$aPreviousStep['LABEL']);
  	$TplHeaderNav->assign("PREV_STEP_VIEW","");
  }else{
  	$TplHeaderNav->assign("PREV_STEP_VIEW","display:none");
  }
  $TplHeaderNav->printToScreen();


?>