<?php
G::LoadClass("plugin");

require_once (PATH_PLUGINS . "sigplus" . PATH_SEP . "class.sigplus.php");

$pluginObj = new sigplusClass();

require_once ("classes/model/Step.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/SigplusSigners.php");

$stepUidObj = $_GET["UID"];
$stepPosition = $_GET["POSITION"];
$tasUid = $_SESSION["TASK"];

//Get the sigplus step
$oCriteria = new Criteria("workflow");
$oCriteria->add(StepPeer::STEP_UID_OBJ, $stepUidObj);
$oCriteria->add(StepPeer::TAS_UID, $tasUid);
$oCriteria->add(StepPeer::STEP_POSITION, $stepPosition);
$oDataset = StepPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aRow = $oDataset->getRow();

$appUid  = $_SESSION["APPLICATION"];
$tasUid  = $aRow["TAS_UID"];
$stepUid = $aRow["STEP_UID"];
$docTitle = null;

//Now get the sigplus signers
$objSigplus = SigplusSignersPeer::retrieveByPK($stepUid);

if ((is_object($objSigplus) && get_class($objSigplus) == "SigplusSigners")) {
  $fields = array();
  $fields = unserialize($objSigplus->getSigSigners());
  $docUid = $objSigplus->getDocUid();
}
else {
  $objSigplus = new SigplusSigners();
  $fields = array();
  $docUid = null;
}

if ($docUid == null) {
  $template = new TemplatePower(PATH_PLUGINS . "sigplus" . PATH_SEP . "messageShow.html");
  $template->prepare();
  $template->assign("TITLE",   "Alert");
  $template->assign("MESSAGE", "Has no record of signers, consult with the system administrator.");

  echo $template->getOutputContent();

  //$contentData = $template;

  //$G_PUBLISH = new Publisher;

  //$G_PUBLISH->AddContent("template", null, null, null, $contentData);
  //G::RenderPage("publish", "blank");

  exit(0);
}

//Get the OutputDocument
$doc = OutputDocumentPeer::retrieveByPK($docUid);

if ((is_object($doc) && get_class($doc) == "OutputDocument")) {
  $docTitle = $doc->getOutDocTitle() ;
  if ($doc->getOutDocGenerate() != "PDF") {
    $doc->setOutDocGenerate("PDF") ;
    $doc->save();
  };
}

//Count how many AppOutputDocument APP_DOC_UID documents we have, if there isnt we need to create the document without digital signs
$countAppOutputDocument = $objSigplus->countAppOutputDocument($appUid, $docUid);
if ($countAppOutputDocument == 0) {
  $pluginObj->generateHtmlPdf($stepUidObj, $stepUid, $appUid);
};

//Get the AppOutputDocument APP_DOC_UID
$oCriteria = new Criteria("workflow");
$oCriteria->add(AppDocumentPeer::APP_UID, $appUid);
$oCriteria->add(AppDocumentPeer::DOC_UID, $docUid);
$oCriteria->addDescendingOrderByColumn(AppDocumentPeer::DOC_VERSION);
$oDataset = AppDocumentPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aAppDocRow = $oDataset->getRow();

if (is_array($aAppDocRow)) {
  $aAppDocUid  = $aAppDocRow["APP_DOC_UID"];
  $aDocVersion = $aAppDocRow["DOC_VERSION"];

  $oAppDocument = new AppDocument();
  $oAppDocument->Fields = $oAppDocument->load($aAppDocUid, $aDocVersion);
  $info = pathinfo($oAppDocument->getAppDocFilename());
  $urlShowDoc = "../cases/cases_ShowOutputDocument?a=$aAppDocUid&ext=pdf&random=" . rand(0, 100000);
  $realPath = PATH_DOCUMENT . $appUid . "/outdocs/" . $info["basename"] . ".pdf";

  if (file_exists($realPath) && $countAppOutputDocument > 1) {
    $divDisplayDocument = "block";
    $divDisplaySigners  = "none";
  }
  else {
    $divDisplayDocument = "none";
    $divDisplaySigners  = "block";
  }
}
else {
  $aAppDocUid  = "";
  $aDocVersion = 0;
  $urlShowDoc = "../cases/cases_ShowOutputDocument?a=$aAppDocUid&ext=pdf&random=" . rand(0, 100000);
  //$urlShowDoc = "../sigplus/cases_ShowOutputDocument?a=$aAppDocUid&ext=pdf&stepUidObj=".$stepUidObj."&stepUid=".$stepUid."&appUid=".$appUid."&random=" . rand(0, 100000);
  $divDisplayDocument = "none";
  $divDisplaySigners  = "block";
}

G::LoadClass("case");
$oApp= new Cases();
$aFields = $oApp->loadCase($appUid);
$aData = $aFields["APP_DATA"];

$signers = array();

foreach ($fields as $value) {
  if (sigplusClass::parseCaseVariable($value["signer_name"], $aData) != null) {
    $signers[] = sigplusClass::parseCaseVariable($value["signer_name"], $aData);
  }
  else {
    $signers[] = $value["signer_name"];
  }
}

$numSigner = count($signers);
$iSigners  = $numSigner - 1;

//Filesize of all images
$pathSign = PATH_DB . SYS_SYS . PATH_SEP . "sigplus" . PATH_SEP . $appUid . PATH_SEP . $tasUid . PATH_SEP . $stepUid . PATH_SEP;

//For security reasons delete all images
for ($i = 0; $i <= $numSigner - 1; $i++) {
  $filename = $pathSign . $i . ".jpg";
  if (file_exists($filename)) {
    unlink($filename);
  }

  $filename = $pathSign . $i . ".sig";
  if (file_exists($filename)) {
    unlink($filename);
  }
}

$sSignArray = "[";
for ($i = 0; $i <= $numSigner - 1; $i++) {
  $filename = $pathSign . $i . ".jpg";
  if (file_exists($filename)) {
    $sSignArray = $sSignArray . filemtime($filename);
  }
  else {
    $sSignArray = $sSignArray . "0";
  }

  $sSignArray = $sSignArray . (($i < $numSigner - 1)? ", " : null);
}

$sSignArray = $sSignArray . "]";

$appUid = $_SESSION["APPLICATION"];

global $Fields;

/*vvvvv
$sigplusTpl = PATH_PLUGINS . "sigplus" . PATH_SEP . "stepSigplusTemp.html";
$template = new TemplatePower($sigplusTpl);
$template->prepare();
//$template->assign("URL",        $url); //vvvvv
//$template->assign("URLCancel",  $urlCancel); //vvvvv
$template->assign("stpid",      $stepUid);
$template->assign("stepUidObj", $stepUidObj);
$template->assign("sSignArray", $sSignArray);
$template->assign("iSigners",   $iSigners);
$template->assign("docTitle",   $docTitle ); //quitar
$template->assign("urlShowDoc", $urlShowDoc);
$template->assign("divDisplayDocument", $divDisplayDocument);
$template->assign("divDisplaySigners",  $divDisplaySigners);
*/

//Start  Alvaro
if (!isset($Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"])) {
  $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"] = "";
}
//Start  Alvaro

$previousStep = $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"];
$previousStepLabel = $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP_LABEL"];
$nextStep = $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP"];
$nextStepLabel = $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP_LABEL"];

/*vvvvv
$template->assign("previousStep",      $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"]);
$template->assign("previousStepLabel", $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP_LABEL"]);
$template->assign("nextStep",          $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP"]);
$template->assign("nextStepLabel",     $Fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP_LABEL"]);

if ($countAppOutputDocument > 1) {
  $template->gotoBlock("_ROOT");
  $template->newBlock("openSigned");
  $template->assign("countAppOutputDocument", $countAppOutputDocument);
}
else {
  $template->gotoBlock("_ROOT");
  $template->newBlock("openUnsigned");
  $template->assign("countAppOutputDocument", $countAppOutputDocument);
}

$template->assign("continueDisabled", "");
*/

$actualSigner = "";

if (is_array($signers)) {
  foreach ($signers as $key => $value) {
    if ($actualSigner == "") $actualSigner = $value;

    /*vvvvv
    $template->gotoBlock("_ROOT");
    $template->newBlock("signers");
    $template->assign ("signer", htmlentities($value, ENT_COMPAT));
    $template->assign ("stpid", $stepUid);
    $template->assign ("sigid", $key);
    $template->assign ("appid", $appUid);
    $template->assign ("tasid", $tasUid);

    //if ($key % 2 == 0) $template->newBlock("startTR");
    //if ($key % 2 == 1) $template->newBlock("endTR");
    */
  }
}

//print $template->getOutputContent();
//die;

$div = 1;

if (count($signers) > 2) {
  $div = 2;
}
?>

<form id="1" class="formDefault" method="post" action="#">
	<DIV id="Signatures" style=" margin:0px;" align="center">
		<div class="borderForm" style="width:950px; padding-left:0; padding-right:0; border-width:1;">
			<div class="boxTop">
				<div class="a"></div>
				<div class="b"></div>
				<div class="c"></div>
			</div>
  		<div class="content"  style="height:100%;" >
    		<table width="950px" >
    		<tr>
    		  <td valign='top'>
    		  <table cellspacing="0" cellpadding="0" border="0" width="100%" >
    		    <tr>
    		      <td colspan='2'>
    		        <table width="100%" cellspacing="0" cellpadding="0" id="stepsid">
	    		        <tr>
	    		          <td class="tableOption" width="120px" align="left">
	    		           <img width="6" src="/images/bulletButtonLeft.gif"/>
	    		           <a id="form[DYN_BACKWARD]" class="tableOption" onclick="" name="form[DYN_BACKWARD]" href="<?php echo $previousStep; ?>"><?php echo $previousStepLabel; ?></a>
	    		          </td>
	    		          <td class="tableOption" width='400px'> </td>
	    		          <td class="tableOption" width="120px" align="right">
	    		           <img width="6" src="/images/bulletButton.gif"/>
	    		           <a id="form[DYN_FORWARD]" class="tableOption" onclick="" name="form[DYN_FORWARD]" href="<?php echo $nextStep; ?>"><?php echo $nextStepLabel; ?></a>
	    		          </td>
	    		        </tr>
    		        </table>
    		      </td>
    		    </tr>
    		    <tr>
    		      <td class="FormTitle" align="" colspan="2">
    		      <span >Sign document <?php echo $docTitle; ?></span>
    		      </td>
    		    </tr>
    		    <tr>
    		      <td colspan="2">
    		        <table cellspacing="0" cellpadding="0" border="0" width="100%" id="divDocument" style="display:<?php echo $divDisplayDocument; ?>;">
    		        <tr ><td class="FormContent" colspan='2' ><br></td></tr>
    		        <tr >
    		          <td class="FormContent" align="" width="20px">
    		            <span id='label1'></span>
    		          </td>
    		          <td align="center">
    		          <input id='btnOpenSigned' type='button' value= 'Open signed document' onclick="openUnsigned();" width="90px">&nbsp;&nbsp;&nbsp;
    		          <input id='btnStartAgain' type='button' value= 'Sign again' onclick="startSigning();" width="80px">
    		            &nbsp;
    		          </td>
    		        </tr>
    		        </table>
    		      </td>
    		    </tr>

    		    <tr>
    		      <td colspan="2" width="100%">
    		        <table cellspacing="0" cellpadding="0" border="0" width="100%" id="divSigners" style="display:<?php echo $divDisplaySigners; ?>;">
    		         <tr>
										<td align="center" width="100%" valign="top">
    		         			<span id="spanActualSigner" style="font-size:14px;font-weight:bold;"><?php echo $actualSigner; ?></span>
    		         			<br />
    		         			<applet id="SigPlusApplet" code="com.nightlies.SigPlusApplet.class" codebase="" archive="/plugin/sigplus/SigPlusApplet-1.0-jar-with-dependencies.jar" width="375" height="200"></applet>
    		         			<br />
    		         			<table cellspacing="0" cellpadding="0" border="0" width="100%" >
    		          			<tr>
    		          				<td align="center">
						  							<?php if ( $countAppOutputDocument <= 1 ) { ?>
						    		      		<input id='btnOpen' type='button' value= 'Open unsigned document' onclick="generateAndOpenUnsigned( '<?php echo $stepUidObj; ?>', '<?php echo $stepUid; ?>');" width="90px">&nbsp;
						  							<?php } if ( $countAppOutputDocument > 1 ) { ?>
						    		      		<input id='btnOpen' type='button' value= 'Open signed document' onclick="generateAndOpenUnsigned( '<?php echo $stepUidObj; ?>', '<?php echo $stepUid; ?>');" width="90px">&nbsp;
						  							<?php } ?>
						    		      	<input id='btnContinue' type='button' value= 'Continue' disabled onclick="continueGenerate( '<?php echo $stepUidObj; ?>', '<?php echo $stepUid; ?>');" width="90px">
						    		      </td>
    					          </tr>
    		        			</table>
    		         		</td>
    		        		<td valign="center" align="center" width="100%">
    		        			<table cellspacing="0" cellpadding="0" border="0" width="100%">
		        						<?php foreach ( $signers as $key => $value ) {
  													if ( $key % $div == 0 ) {
      									?>
    		        				<tr>
    		        					<?php } ?>
    		        					<td valign="top" align="center">
    		        						<table cellspacing="0" cellpadding="0" border="1">
    		        							<tr>
    		        								<td>
    		        								<a href="#" onclick="changeThisImage(<?php echo $key; ?>);return false;"><img border='0' id='sign<?php echo $key; ?>' src='../sigplus/services/download?stpid=<?php echo $stepUid; ?>&sigid=<?php echo $key; ?>&appid=<?php echo $appUid; ?>&tasid=<?php echo $tasUid; ?>' width='210' height='70'>
    		        								</a>
    		        								</td>
    		        							</tr>
    		        							<tr>
    		        								<td id="tdSigner<?php echo $key; ?>" class='' style='text-transform: uppercase;height:20px;text-align: center;padding-right: 5;padding-left: 5;background-color:#347898;color: white;'>
    		        									<?php echo htmlentities($value, ENT_COMPAT); ?>
    		        								</td>
    		        							</tr>
    		        						</table>
    		        					</td>
    		        					<?php
    		        						if ( $key % $div == 1 ) {
    		        					?>
    		        				</tr>
    											<?php }
    												} ?>
    		        			</table>
    								</td>
    							</tr>
    		        </table>
    		      </td>
    		    </tr>
    		  </table>
    		  </td>
    		</tr>
    		<tr>
    		  <td class="FormContent" align="" colspan="2">
    		    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    		      <tr>
    		      <td class="FormContent" align="" width="20px">
    		      	<span id='label1'></span>
    		      </td>
    		      </tr>
    		    </table>
    		  </td>
    		</tr>
    		  </div>

    		  </td>
    		</tr>
    		</table>
    	</div>
  		<div class="boxBottom">
  			<div class="a"></div>
  			<div class="b"></div>
  			<div class="c"></div>
  		</div>
 		</div>
	</DIV>
<input id="appid" type="hidden" value="<?php echo $appUid; ?>" />
<input id="tasid" type="hidden" value="<?php echo $tasUid; ?>" />
<input id="stpid" type="hidden" value="<?php echo $stepUid; ?>" />
<input id="sigid" type="hidden" value="0" />
</form>
<script language="javascript">
	if(document.getElementById('btnOpen').value == 'Open unsigned document'){document.getElementById('stepsid').style.display = 'none';}else{document.getElementById('stepsid').style.display = 'block';}
  //setTimeout ( checkSignatures, 2000 );
		var signArray = [];
  var oldArray = <?php echo $sSignArray; ?>;
		var iSigners = <?php echo $numSigner; ?>;

  function startSigning() {
  	document.getElementById('divDocument').style.display = 'none';
  	document.getElementById('divSigners').style.display = 'block';
   //document.getElementById('btnStart').disabled = true;
   //document.getElementById('btnCancel').disabled = false;

			//document.getElementById('label').innerHTML = r;
  }

  function openUnsigned() {
    window.open( '<?php echo $urlShowDoc; ?>', 'outputDocument' );
  }

  function checkSignatures() {
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url: "../sigplus/ajax?numsgr=<?php echo $numSigner; ?>&stpid=<?php echo $stepUid; ?>",
        args: "",
        async: false
      });
      oRPC.make();//Added by JC
      //oRPC.callback = function(oRPC) {
        r = oRPC.xmlhttp.responseText;
        signArray = eval("(" + r + ")");
        //document.getElementById('label').innerHTML = r;

								var isigner = 0;
								var isignerEmpty = -1;
								var sw = 1;

        for (var i = 0; i <= <?php echo $numSigner; ?> - 1; i++) {
										if (signArray[i] > oldArray[i]) {
            image = document.getElementById('sign' + i);
            //image.src = image.src ;
            var aux = image.src;
            image.src = aux.replace('-1', i) + "&fmtime=" + signArray[i];

												oldArray[i] = signArray[i];
          }
										if (signArray[i] > 0) {
												isigner = isigner + 1;
										}
          if (signArray[i] == 0 && sw == 1) {
            isignerEmpty = i;
												sw = 0;
          }
        }

								document.getElementById("spanActualSigner").innerHTML = "";
								if (isignerEmpty > -1) {
          document.getElementById("sigid").value = isignerEmpty;
          document.getElementById("spanActualSigner").innerHTML = document.getElementById("tdSigner" + isignerEmpty).innerHTML;
        }

        document.getElementById('btnContinue').disabled = true;
        if (isigner - 1 == <?php echo $numSigner; ?> - 1) {
          //document.getElementById('btnStart').disabled = true;
          //document.getElementById('btnCancel').disabled = true;
          document.getElementById('btnContinue').disabled = false;
        }
        else {
          //setTimeout ( checkSignatures, 5000 );
        }
      //}.extend(this);
      //oRPC.make();
  }

  function generateAndOpenUnsigned(sigid, stepid) {
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  '../sigplus/sigplusGenerateHtmlPdf?sigid='  + sigid + '&stepid=' + stepid ,
        args: ''
      });
      oRPC.callback = function(oRPC) {
        r = oRPC.xmlhttp.responseText;
      }.extend(this);
      oRPC.make();

    window.open( '<?php echo $urlShowDoc; ?>', 'outputDocument' );
  }

  function continueGenerate(sigid, stepid) {
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  '../sigplus/sigplusGenerateHtmlPdf?sigid='  + sigid + '&stepid=' + stepid ,
        args: ''
      });
      oRPC.callback = function(oRPC) {
        r = oRPC.xmlhttp.responseText;
        //document.getElementById('divDocument').style.display = 'block';
        //document.getElementById('divSigners').style.display = 'none';
        //alert ( r );
        window.location = window.location;
      }.extend(this);
      oRPC.make();
  }

  function changeThisImage(theIndex) {
    document.getElementById('sign' + theIndex).src = '../sigplus/services/download?stpid=' + document.getElementById('stpid').value + '&sigid=-1&appid=' + document.getElementById('appid').value + '&tasid=' + document.getElementById('tasid').value + '&fmtime=' + Math.random();
    document.getElementById('sigid').value = theIndex;
    document.getElementById('spanActualSigner').innerHTML = document.getElementById('tdSigner' + theIndex).innerHTML;
    document.getElementById('SigPlusApplet').showSigner();
  }
</script>