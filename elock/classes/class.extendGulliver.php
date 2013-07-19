<?php

class XmlForm_Field_CompositeElock extends XmlForm_Field{
  var $withoutLabel = true;
  function render($value = NULL, &$owner) {
    $newUser="";
    $message="";
    //Here comes the code to verify if the user exists... if not then create, send the password to its email.. show a message in the Composite
    //If is created then $newUser="Your password was sent to your email";
    require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
    $elockObj = new elockClass ();
 

    if(($this->mode === 'edit')&& isset($_SESSION['APPLICATION'])&&($elockObj->serverActive)){//Only when needs to be signed and we are in a application
      $operatorAuthToken=$elockObj->elockLogin($elockObj->ElockOperatorUserName, $elockObj->ElockOperatorUserPassword);
      $usr_username = $_SESSION['USR_USERNAME'];
      if($elockObj->DEMO_MODE=="On"){
        $usr_username="pmsigner";
        $message="For demo purposes please use the default password pm123";
      }else{
      
      $UserDetails = $elockObj->GetUserDetails($usr_username,$operatorAuthToken);
     
      
      if((is_null($UserDetails))&&($operatorAuthToken)){ //The user doesn't exist in Elock -> Auto Create
        //Get current User details
        //die("asdasdas");
        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $aUser = $oUser->load($_SESSION['USER_LOGGED']);
        //G::pr($aUser);
        //With this we can get the user logged information
        $displayName=$aUser['USR_FIRSTNAME']." ".$aUser['USR_LASTNAME']; // The display name will be the user complete name
        $email=$aUser['USR_EMAIL'];

        $newUser="Your account for Elock was created(".$usr_username."). Your new password was sent to your email(".$email.")";

        $pwd = G::generate_password();
        $userType= '2';//1=> Operator & 2=> Signer
        //$cn = "Elock";   //CN is Common Name
        $cn = $usr_username;   //CN is Common Name
        $ou="Processmaker"; //OU is organizational unit
        $userDN ='CN='.$cn.';OU='.$ou.';'; //userDN is user distinguished name
        //$email = "ankit.mishar@bistasolutions.com";
        $UserCreatedResult = $elockObj->AddNewSignUser($usr_username,$displayName,$userType,$userDN,$pwd,$email,$elockObj->ElockAPP_PASSWORD);

        //Sen Email
        //First.. easy way
        //$aFields['password']=$pwd;
        //PMFSendMessage($_SESSION['APPLICATION'], 'info@processmaker.com', $email, '', 'hugo@colosa.com;', 'Elock User Activation', '/plugins/elock/elockUserNotification.html', $aFields);

        //Second way
        $sTo=$email;
        $sSubject = "This Business Process is powered by <b>ProcessMaker</b>.";
        $sBody = "
        <table style=\"background-color: white; font-family: Arial,Helvetica,sans-serif; color: black; font-size: 11px; text-align: left;\" cellpadding='10' cellspacing='0' width='100%'>
        <tbody>
        <tr><td style='font-size: 14px;'>Elock PASSWORD =>$pwd </td></tr>
        <tr><td style='vertical-align:middel;'>
        <br /><hr><b>This Business Process is powered by ProcessMaker.<b><br />
        <a href='http://www.processmaker.com' style='color:#c40000;'>www.processmaker.com</a><br /></td>
        </tr></tbody></table>";
        G::LoadClass('spool');
        $aSetup = getEmailConfiguration();
        $oSpool = new spoolRun();
        $oSpool->setConfig( array(
            'MESS_ENGINE'   => $aSetup['MESS_ENGINE'],
                        'MESS_SERVER'   => $aSetup['MESS_SERVER'],
                        'MESS_PORT'     => $aSetup['MESS_PORT'],
                        'MESS_ACCOUNT'  => $aSetup['MESS_ACCOUNT'],
                        'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],
                        'SMTPAuth'      => $aSetup['MESS_RAUTH']
        ));
        $sFrom="info@processmaker.com";
        $sFrom = $sFrom . ' <' . $aSetup['MESS_ACCOUNT'] . '>';
         
        $oSpool->create(array(
            'msg_uid'          => '',
            'app_uid'          => '',
            'del_index'        => 0,
            'app_msg_type'     => 'TEST',
            'app_msg_subject'  => $sSubject,
            'app_msg_from'     => $sFrom,
            'app_msg_to'       => $sTo,
            'app_msg_body'     => $sBody,
            'app_msg_cc'       => '',
            'app_msg_bcc'      => '',
            'app_msg_attach'   => '',
            'app_msg_template' => '',
            'app_msg_status'   => 'pending'
            ));
            $oSpool->sendMail();
      }
    }
    }
    $htmlComposite='
    
    		<fieldset title="Digital Signature">
					<legend><b><image src="/plugin/elock/images/E-Lock_logo.jpg" valign="middle" height="30">Digital Signature</b></legend>
					<label for="elock[REASON]">Reason:</label>
					<input type="text" value="" id="elock[REASON]" name="elock[REASON]"> 
					
					<br>
					<label for="elock[PASSWORD]">Password:</label>
					<input type="password" value="" id="elock[PASSWORD]" name="elock[PASSWORD]"> 
					<div id="elockMessage" name="elockMessage" style="color:red">'.$newUser.'</div>
					<div id="elockMessage2" name="elockMessage2" style="color:blue">'.$message.'</div>
					<br>
					<input type="hidden" id="action" name="action" value="signDynaform">
					<center><input type="button" onClick="if(!this.form.onsubmit()) return; document.getElementById(\'elockMessage\').innerHTML=\'<img src=/js/maborak/core/images/loader_B.gif> <i> Processing..</i>\'; this.disabled=true; eObj=eval(ajax_post(\'../elock/elockAjax\',this.form,\'POST\'));if(eObj.status==\'ERROR\'){document.getElementById(\'elockMessage\').innerHTML=eObj.message; this.disabled=false;}else{this.form.submit();}" class="module_app_button___gray {$this->className}" value="Sign and Save"></center>
				</fieldset>		
		
    ';



    //$html.='<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\' >' . $this->htmlentities ( $this->label ) . '</span>';
    $htmlCompositeView='
    
    		<fieldset title="Digital Signature">
					<legend><b><image src="/plugin/elock/images/E-Lock_logo.jpg" valign="middle" height="30">Digital Signature</b></legend>
					<center>
					<img id="statusImg" name="statusImg" src="/images/Refresh.png">
					<br>
					<div id="elockMessage" name="elockMessage" style="align:center;color:blue; "> </div>
					</center>
					<!--
					<img src="/images/dialog-ok-apply.png">
					<img src="/images/e_Delete.png">
					-->
					<input type="hidden" id="action" name="action" value="validateDynaform">
					<input type="hidden" id="idForm" name="idForm" value="">
					<center><br><input type="button" onClick="this.form.idForm.value=this.form.name;document.getElementById(\'elockMessage\').innerHTML=\'<img src=/js/maborak/core/images/loader_B.gif> <i> Validating, please wait..</i>\'; this.disabled=true;eObj=eval(ajax_post(\'../elock/elockAjax\',this.form,\'POST\'));if(eObj.status==\'ERROR\'){document.getElementById(\'statusImg\').src=\'/images/e_Delete.png\';}else{document.getElementById(\'statusImg\').src=\'/images/dialog-ok-apply.png\';};document.getElementById(\'elockMessage\').innerHTML=eObj.message; this.disabled=false;" class="module_app_button___gray {$this->className}" value="Validate"></center>
				</fieldset>		
		
    ';

    if(isset($elockObj->serverActive)){
    if(!$elockObj->serverActive){
      $htmlComposite=$htmlCompositeView='
    
            <fieldset title="Digital Signature">
                    <legend><b><image src="/plugin/elock/images/E-Lock_logo.jpg" valign="middle" height="30">Digital Signature</b></legend>
                    <center><div id="elockMessage" name="elockMessage" style="align:center;color:red; width:70%;border:1px solid red;margin:5px">Is not possible to reach eLock server due a connectivity error. </div></center>
                </fieldset>     
        
    ';

    }
    }
    

    if ($this->mode === 'edit') {
      //if ($this->readOnly)
      //  return $htmlComposite;
      //else
      return $htmlComposite;
    } elseif ($this->mode === 'view') {
      return $htmlCompositeView;
    } else {
      return $htmlCompositeView;
    }
  }


  function renderGrid( $value = NULL, $owner=NULL ){
    return $this->render( $value , $owner );
  }
  function renderTable( $values='' , $owner )
  {
    //$result = $this->htmlentities( $values , ENT_COMPAT, 'utf-8');
    //return $result;
    return $this->render( $values , $owner );
  }




}

?>