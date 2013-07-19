<?php
G::LoadClass("plugin");

require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
$pluginObj = new elockClass ();

require_once 'classes/model/Step.php';
require_once 'classes/model/OutputDocument.php';
require_once 'classes/model/ElockOutputCfg.php';

 

try {

	//get the sigplus step
	$stepUidObj = $_GET['UID'];
	$stepPosition = $_GET['POSITION'];
	$tasUid = $_SESSION['TASK'];
	$oCriteria = new Criteria ( 'workflow' );
	$oCriteria->add ( StepPeer::STEP_UID_OBJ, $stepUidObj);
	$oCriteria->add ( StepPeer::TAS_UID, $tasUid);
	$oCriteria->add ( StepPeer::STEP_POSITION, $stepPosition );
	$oDataset = StepPeer::doSelectRS ( $oCriteria );
	$oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
	$oDataset->next ();
	$aRow = $oDataset->getRow ();
	//G::pr($aRow);
	$appUid  = $_SESSION['APPLICATION'];
	$tasUid  = $aRow['TAS_UID'];
	$stepUid = $aRow['STEP_UID'];
	$docTitle = '';

	//now 
	$objSigplus = ElockOutputCfgPeer::retrieveByPK($stepUid);
	if ( ( is_object ( $objSigplus ) &&  get_class ($objSigplus) == 'ElockOutputCfg' ) ) {
		//$fields = array();
		//$fields = unserialize($objSigplus->getSigSigners());
		$docUid = $objSigplus->getDocUid();
		//G::pr($docUid);

	}else {
		 
		throw new Exception("error - no output doc assigned");

	}
	//get the OutputDocument
	$doc = OutputDocumentPeer::retrieveByPK($docUid);
	if ( ( is_object ( $doc ) &&  get_class ($doc) == 'OutputDocument' ) ) {
		$docTitle = $doc->getOutDocTitle() ;
		if ( $doc->getOutDocGenerate() != 'PDF' ) {
			$doc->setOutDocGenerate('PDF') ;
			$doc->save();
		};		 
	}else{
		throw new Exception("The selected Output Doc does't exist. Plrease contact...");
	}

	//Include navigation header
	include('headerNav.php');

	//G::pr($docTitle);
	
	//Count generated docs
	$c = new Criteria('workflow');
    $c->clearSelectColumns();
    $c->addSelectColumn('COUNT(*)');
    $c->add ( AppDocumentPeer::APP_UID, $appUid);
    $c->add ( AppDocumentPeer::DOC_UID, $docUid);

    $rs = AppDocumentPeer::doSelectRS($c);
    $rs->next();
    $row1 = $rs->getRow();
    //G::pr($row1);
    $countAppOutputDocument = $row1[0];
    //If there is no an Output Doc generated then generate
    //G::pr("If there is no an Output Doc generated then generate.");
    //G::pr("Generatin Output Document");
    if ( $countAppOutputDocument == 0 ) {
      $pluginObj->generateHtmlPdf ( $stepUidObj, $stepUid, $appUid );
    };
    
    
    //get the AppOutputDocument APP_DOC_UID
  $oCriteria = new Criteria ( 'workflow' );
  $oCriteria->add ( AppDocumentPeer::APP_UID, $appUid);
  $oCriteria->add ( AppDocumentPeer::DOC_UID, $docUid);
  $oCriteria->addDescendingOrderByColumn ( AppDocumentPeer::DOC_VERSION );
  $oDataset = AppDocumentPeer::doSelectRS ( $oCriteria );
  $oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
  $oDataset->next ();
  $aAppDocRow = $oDataset->getRow ();
  
    
    //If the current Output Document is Signed (Search in our table) show the Link to download, enable Continue and show an option to Re Generate (new version)
    //G::pr("If the current Output Document is Signed (Search in our table) show the Link to download, enable Continue and show an option to Re Generate (new version)");
  //G::pr($aAppDocRow);
  
  if ( is_array ( $aAppDocRow ) ) {// Should exist a file
    $aAppDocUid  = $aAppDocRow['APP_DOC_UID']; 
    $aDocVersion = $aAppDocRow['DOC_VERSION']; 

    $oAppDocument = new AppDocument();
    $oAppDocument->Fields = $oAppDocument->load( $aAppDocUid, $aDocVersion );
    //G::pr($oAppDocument->Fields);
    //Set variables
    $docName=$oAppDocument->Fields['APP_DOC_FILENAME'];
    $docCreateDate=$oAppDocument->Fields['APP_DOC_CREATE_DATE'];
    $docVersion=$oAppDocument->Fields['DOC_VERSION'];
    
    
    
    $info = pathinfo( $oAppDocument->getAppDocFilename() );
    $urlUploadDoc="/uploadSignature";
    $urlShowDoc = "../cases/cases_ShowOutputDocument?a=$aAppDocUid&ext=pdf&random=" . rand(0,100000);
    $realPath = PATH_DOCUMENT . $appUid . '/outdocs/' . $info['basename'] . '.pdf';
    
    //G::pr($realPath);
    //if ( file_exists( $realPath )
    //Check if the file was signed or not
    require_once ( "classes/model/ElockSignedDocument.php" );
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ElockSignedDocumentPeer::retrieveByPK( $aAppDocUid, $aDocVersion  );    
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockSignedDocument' ) ) { 
       //$fields['APP_DOC_UID'] = $tr->getAppDocUid();
       //$fields['DOC_VERSION'] = $tr->getDocVersion();
       //$fields['DOC_UID'] = $tr->getDocUid();
       //$fields['USR_UID'] = $tr->getUsrUid();
       //$fields['SIGN_DATE'] = $tr->getSignDate();
       $signDate=$tr->getSignDate();
       $signUser = $tr->getUsrUid();
       $sw_signed=true;
    }else{
      $sw_signed=false;
      $signDate="Not Signed";
    }
    
  }
  else {
    throw new Exception("There is no a valid Output Document");
  }
  
  //G::pr($urlShowDoc);
  
  G::LoadClass('case');
  $oApp= new Cases();
  $aFields = $oApp->loadCase( $appUid );
  $aData = $aFields['APP_DATA'];
  
    //G::pr("Link to signed");
    //G::pr("Continue Button");
    //G::pr("Re Generate/NewVersion");
    
    //else disable Continue, enable regenerate, enable sign
    
    
    /*
    if ( $countAppOutputDocument == 0 ) {
      $pluginObj->generateHtmlPdf ( $stepUidObj, $stepUid, $appUid );
    }
    */
    
    
    $elockTpl = PATH_PLUGINS . 'elock' . PATH_SEP . 'stepOutputSignatureTemp.html';
  $template = new TemplatePower( $elockTpl );
  $template->prepare();
  //$template->assign( 'URLCancel',   $urlCancel );
  $template->assignGlobal( 'stpid', $stepUid );
  $template->assignGlobal( 'stepUidObj', $stepUidObj );
  $template->assign( 'docTitle', $docTitle );
  $template->assignGlobal( 'urlUploadDoc', $urlUploadDoc );
  $template->assignGlobal( 'urlShowDoc', $urlShowDoc );
  $template->assignGlobal( 'appUid', $appUid );
  $template->assignGlobal( 'aAppDocUid', $aAppDocUid );
  $template->assignGlobal( 'aDocVersion', $aDocVersion );
  
  


  $template->assignGlobal( 'docName', $docName );  
  $template->assignGlobal( 'docCreateDate', $docCreateDate ); 
  $template->assignGlobal( 'docVersion', $docVersion ); 

  
  
  if ( $sw_signed ) {
    $template->gotoBlock( "_ROOT" );
    $template->newBlock( 'SIGNED' );
    $template->assign( 'signDate', $signDate );
    $template->assign( 'signUser', $signUser );
    
    $template->assign("nextStep",$aNextStep['PAGE']);
    //$template->assign ( 'countAppOutputDocument',   $countAppOutputDocument );
  }
  else {
    //Should verify if the user exists or not. If not then create
    
    
    
    $template->gotoBlock( "_ROOT" );
    $template->newBlock( 'TOBESIGNED' );
    if($pluginObj->DEMO_MODE=="On"){
        $newUser="For demo purposes please use the default password pm123";
        $template->assign( 'newUser', $newUser);
    }
    //$template->assign ( 'countAppOutputDocument',   $countAppOutputDocument );
  }
  
//  $template->assign( 'continueDisabled', $iSigned == $iSigners ? '' : 'disabled' );
  //$template->assign( 'continueDisabled', '' );
  

  print $template->getOutputContent();



} catch (Exception $e) {
	G::pr($e->getMessage());
}