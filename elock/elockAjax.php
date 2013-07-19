<?php
//MERGED
if (! isset ( $_REQUEST ['action'] )) {
  $ruturn ['success'] = 'failure';
  $ruturn ['message'] = 'You may request an action';
  print json_encode ( $ruturn );
  die ();
}
if (! function_exists ( $_REQUEST ['action'] )) {
  $ruturn ['success'] = 'failure';
  $ruturn ['message'] = 'The requested action doesn\'t exists';
  print json_encode ( $ruturn );
  die ();
}

$functionName = $_REQUEST ['action'];
//var_dump($functionName);
$functionParams = isset($_REQUEST ['params'] ) ? $_REQUEST ['params'] : array ();

$functionName ( $functionParams );

function signDynaform(){

  $sw=false;
  $return['status']="";
  $return['message']="";

  if((isset($_REQUEST['form']))&&((isset($_REQUEST['elock'])))){
    if((isset($_REQUEST['elock']['REASON']))&&($_REQUEST['elock']['REASON']!="")){
      $reason=$_REQUEST['elock']['REASON'];
      $sw=true;
      $return['message'].="";//"($reason)";
    }else{
      $sw=false;
      $return['message'].="REASON is required. ";
    }
    if((isset($_REQUEST['elock']['PASSWORD']))&&($_REQUEST['elock']['PASSWORD']!="")){
      $reason=$_REQUEST['elock']['PASSWORD'];
      $sw=true;
      $return['message'].="";//"($reason)";
    }else{
      $sw=false;
      $return['message'].="PASSWORD is required. ";
    }
    if($sw){//Login
      //Signature steps HERE

      require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
      $elockObj = new elockClass ();


      $serializeData = serialize($_POST['form']);
      $elockArray=$_POST['elock'];
      $usr_username = "pmsigner";//$_SESSION['USR_USERNAME'];
      $pwd = $elockArray['PASSWORD']; //get Password from Form
      $operatorAuthToken = $elockObj->elockLogin($usr_username,$pwd);
      //G::pr($operatorAuthToken);
      if(strlen($operatorAuthToken)!=31){//A valid Token??
        $sw=false;
        $return['message'].=$operatorAuthToken; // Message error
      }
    }

    if($sw){


      //G::pr($operatorAuthToken);exit;
      $data = $serializeData;
      $bDetachedSignature = '1';
      $sessionId = $operatorAuthToken;
      $result = $elockObj->signDataInMemory($data,'','',$bDetachedSignature,$sessionId);
      $status = $elockObj->GetOperationStatusString($sessionId);
      //G::pr($status);
      //die();

      //$status = $elockObj->GetUserDetails($userId,$sessionId);
      $latestStatus=$status->GetOperationStatusStringResult;


      //if (!(is_null($result))) { //If there is a valid response
      if ($latestStatus=='Success') {

        $return['message']=$latestStatus;




        //Saving Data in Database
        //$form = $_POST['form'];
        //$UidDynaform = $form['UID_DYNAFORM'];
        //$UidApplication = $form['UID_APPLICATION'];
        $UidDynaform = $_SESSION['CURRENT_DYN_UID'];
        $UidApplication = $_SESSION['APPLICATION'];
        $Base64 = $result;
        $User = $usr_username;
        $Timestamp = time();

        
        require_once ( "classes/model/ElockDynaform.php" );

        //if exists the row in the database propel will update it, otherwise will insert.

        $tr = ElockDynaformPeer::retrieveByPK( $UidDynaform,$UidApplication );
        if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'ElockDynaform' ) ) {
          $tr = new ElockDynaform();
        }
        $tr->setUidDynaform( $UidDynaform );
        $tr->setUidApplication( $UidApplication );
        $tr->setBase64( $Base64 );
        $tr->setUser( $User );
        $tr->setTimestamp( $Timestamp );

        if ($tr->validate() ) {
          // we save it, since we get no validation errors, or do whatever else you like.
          $res = $tr->save();
        }

        $return['status']="OK";
      }else{
        $return['status']="ERROR";
        $return['message']=$latestStatus;
      }
    }else{
      $return['status']="ERROR";
    }
  }else{
    $return['status']="ERROR";
    $return['message']="There is no information to sign";
  }
  
  if($return['status']=="OK"){
    G::SendMessageText("The form was successfully signed.","INFO");
  }

  print "(" .json_encode($return). ")";
  return $return;


}

function validateDynaform(){

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $elockObj = new elockClass ();



  //Search if there is a record for the signed Dynaform
  require_once ( "classes/model/ElockDynaform.php" );
  $UidApplication=$_SESSION['APPLICATION'];
  $UidDynaform = $_SESSION['CURRENT_DYN_UID'];
  $tr = ElockDynaformPeer::retrieveByPK( $UidDynaform, $UidApplication  );
  //G::pr($UidApplication);
  //G::pr($UidDynaform);
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockDynaform' ) ) {
    $aRow['UID_DYNAFORM'] = $tr->getUidDynaform();
    $aRow['UID_APPLICATION'] = $tr->getUidApplication();
    $aRow['BASE64'] = $tr->getBase64();
    $aRow['USER'] = $tr->getUser();
    $aRow['timestamp'] = $tr->getTimestamp();
  }else{
    $aRow=array();
  }


  if((isset($aRow['BASE64']))&&($aRow['BASE64']!='')){
    $userId =  $elockObj->ElockOperatorUserName;
    $strPassword =  $elockObj->ElockOperatorUserPassword;
    $operatorAuthToken = $elockObj->elockLogin($userId,$strPassword);

    $originalData = serialize($_POST['form']);
     //$someError=stristr($operatorAuthToken,'Error');
    if(isset($someError))
    {
      echo "there is some error";
      $return['message']="Login Error";
        $return['status']="ERROR";
    }else{
      //$originalData='a';
      $base64EncodedSig= $aRow['BASE64'];
      $verifyResult = $elockObj->verifyData($base64EncodedSig,$originalData,$operatorAuthToken);
      //G::pr($verifyResult);
      $arrayResult=array();
      $arrayResult[0]="";
      if(isset($verifyResult->string)){
        $arrayResult = $verifyResult->string;
      }
      //G::pr($verifyResult['string']);
      //G::pr($arrayResult['0']);

      if($arrayResult['0'] == 'Data integrity check succeeded.')
      {
        $return['message']=implode('<br />',$arrayResult);
        $return['status']="SUCCESS";
        //G::pr($return['message']);
      }else{
        $return['message']="Dynaform not verified";
        $return['status']="ERROR";
      }
    }
  }else{
    $return['message']="Dynaform not verified";
    $return['status']="ERROR";
  }
  print "(" .json_encode($return). ")";
 // return $return;
 // print "(" .json_encode($return). ")";

}


function regenerateOutputDocument(){
  $stepUidObj=$_GET['stepUidObj'];
  $stepUid=$_GET['stepUid'];
  $appUid=$_GET['appUid'];
  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();
  $pluginObj->generateHtmlPdf ( $stepUidObj, $stepUid, $appUid );

  $return['status']="OK";
  $return['message']="Regenerated";

  print "(" .json_encode($return). ")";
}

function uploadOutputDocument(){

  include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
  include_once(PATH_PLUGINS.'elock'.PATH_SEP.'classes/class.pmFunctions.php');
  global $fields;

  $tempFilePath = $_FILES["file"]["tmp_name"];
  $sourcePath = $tempFilePath;
  //.'/'.basename($_FILES["file"]["name"]);
  //var_dump($sourcePath);
  //print_r($_FILES);
  //$fileContent = $objresource ->;

  //Setting Destination of File
  $destPath = "/Signature/".$_FILES["file"]["name"];
  //var_dump($destPath);

   

}
function signOutputDocument(){

  $return['message']="";
  if((isset($_REQUEST['form']))&&((isset($_REQUEST['elock'])))){
    if((isset($_REQUEST['elock']['REASON']))&&($_REQUEST['elock']['REASON']!="")){
      $reason=$_REQUEST['elock']['REASON'];
      $sw=true;
      $return['message'].="";//"($reason)";
    }else{
      $sw=false;
      $return['message'].="REASON is required. ";
    }
    if((isset($_REQUEST['elock']['PASSWORD']))&&($_REQUEST['elock']['PASSWORD']!="")){
      $reason=$_REQUEST['elock']['PASSWORD'];
      $sw=true;
      $return['message'].="";//"($reason)";
    }else{
      $sw=false;
      $return['message'].="PASSWORD is required. ";
    }
  }else{
    $sw=false;
    $return['message'].="Form is required. ";
  }
  if($sw){//Login
    require_once 'classes/model/OutputDocument.php';
    require_once 'classes/model/AppDocument.php';
    $stepUidObj=$_REQUEST['form']['stepUidObj'];
    $stepUid=$_REQUEST['form']['stepUid'];
    $appUid=$_REQUEST['form']['appUid'];

    $aAppDocUid=$_REQUEST['form']['aAppDocUid'];
    $aDocVersion=$_REQUEST['form']['aDocVersion'];

    try {
      //Get Documetn Info
      $oAppDocument = new AppDocument();
      $oAppDocument->Fields = $oAppDocument->load( $aAppDocUid, $aDocVersion );

      $info = pathinfo( $oAppDocument->getAppDocFilename() );

      $urlShowDoc = "../cases/cases_ShowOutputDocument?a=$aAppDocUid&ext=pdf&random=" . rand(0,100000);
      //Path of the Document
      $realPath = PATH_DOCUMENT . $appUid . '/outdocs/' . $info['basename'] . '.pdf';
      $changedPath = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/outdocs/';
      if(!file_exists($realPath)){
        throw (new Exception ( "The file " . $realPath . " doesn't exists." ));
      }

      //** Start Document Signing


      //Before this step the Signature of the Signer should be uploaded

      include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
      $elockObj = new elockClass ();
      $sourcePath = $realPath;
      $folder = $elockObj->ElockFTP_FOLDER;
      $destPath = $folder.'/'.$info['basename'] .   '.pdf' ;
      //var_dump($destPath);
      

      
      //$server = ($fields['ElockSERVER_IP']);
      $server = $elockObj->ElockSERVER_IP;
      //$server= '114.143.97.47';
      //var_dump($server);
      $connection = ftp_connect($server);

      //var_dump($connection);
      if(!$connection){
        throw (new Exception ( "No connection to " . $server ));
      }
      //$ftp_user_name = ($fields['ElockFTP_USER']);
      $ftp_user_name = $elockObj->ElockFTP_USER;
      //var_dump($ftp_user_name);


      //$ftp_user_pass = ($fields['ElockFTP_PASSWORD']);
      $ftp_user_pass = $elockObj->ElockFTP_PASSWORD;
      //var_dump($ftp_user_pass);

      $login = @ftp_login($connection, $ftp_user_name, $ftp_user_pass);
      //var_dump($login);

      if (!$connection || !$login) {
        throw (new Exception ( "Connection attempt failed! " . $server ));

      }

      $mode = FTP_BINARY;
      ftp_pasv($connection, false);
      $upload = @ftp_put($connection, $destPath, $sourcePath, $mode);
      //var_dump($upload);

      if (!$upload) {
        throw (new Exception ( 'FTP upload failed! <br><b>Destination Path:</b><br>'.$destPath."<br><b>Server:</b><br>$server" ));

      }

      ftp_close($connection);






      /* Signing of Output Document at the Elock Server */

      /*
       $userId = 'faisal';
       $strPassword = 'faisal';
  $serverPath="ftp://pmuser@demos.elock.com";
       $operatorAuthToken = elockLogin($userId,$strPassword);
       var_dump($operatorAuthToken);
       */
      $elockArray=$_POST['elock'];
      $usr_username = "pmsigner";//$_SESSION['USR_USERNAME'];
      $pwd = $elockArray['PASSWORD']; //get Password from Form
      $operatorAuthToken = $elockObj->elockLogin($usr_username,$pwd);

      $sessionId = $operatorAuthToken;
      $bDetachedSignature = '0';
      //$serverPath = ($fields['ElockFTP_PATH']);
      $serverPath = $elockObj->ElockFTP_PATH;
      //$documentpath = array();

     // $filePath = $serverPath.$destPath;
      $filePath = $serverPath.$info['basename'] .'.pdf' ;
      //var_dump($filePath);

      $latestTest = $elockObj->signDocumentInMemory('', $filePath,$filePath,$bDetachedSignature, $sessionId);
      //die;

      $status = $elockObj->GetOperationStatusString($operatorAuthToken);


      /* Download Output Document from Elock Server to LocalMachine through FTP */


      $connection = ftp_connect($server);

      $login = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
      //var_dump($login);

      if (!$connection || !$login) {
        throw (new Exception ( 'Connection attempt failed!' ));
         
      }

      $newSourcePath = $changedPath.$info['basename'] .  '.pdf' ;
      //var_dump($newSourcePath);
      $mode = FTP_BINARY;
      ftp_pasv($connection, false);
      //var_dump($newSourcePath);
      //var_dump($destPath);
      $result = ftp_get($connection, $newSourcePath, $destPath, $mode);
      //var_dump($result);

      ftp_close($connection);


       
      //** End Document Signing


      //Save a record of Signed Document in Table ELOCK_SIGNED_DOCUMENT
      require_once ( "classes/model/ElockSignedDocument.php" );

      //if exists the row in the database propel will update it, otherwise will insert.
      $tr = ElockSignedDocumentPeer::retrieveByPK( $aAppDocUid, $aDocVersion );
      if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'ElockSignedDocument' ) ) {
        $tr = new ElockSignedDocument();
      }
      $tr->setAppDocUid( $aAppDocUid );
      $tr->setDocVersion( $aDocVersion );
      $tr->setDocUid( $oAppDocument->Fields['DOC_UID'] );
      $tr->setUsrUid( $_SESSION['USER_LOGGED'] );
      $tr->setSignDate( date("Y-m-d H:i:s") );

      if ($tr->validate() ) {
        // we save it, since we get no validation errors, or do whatever else you like.
        $res = $tr->save();
      }
      else {
        // Something went wrong. We can now get the validationFailures and handle them.
        $msg = '';
        $validationFailuresArray = $tr->getValidationFailures();
        foreach($validationFailuresArray as $objValidationFailure) {
          $msg .= $objValidationFailure->getMessage() . "<br/>";
        }

        throw (new Exception ( $msg ));
      }


      $return['status']="OK";
      $return['message']="File Signed";

    } catch (Exception $e) {
      $return['status']="ERROR";
      $return['message']=$e->getMessage();

    }


  }else{
    $return['status']="ERROR";
  }
  //Return the Result
  print "(" .json_encode($return). ")";
}