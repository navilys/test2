<?php
/**
 * class.elock.php
 * This class have all the API functions for an ELOCK Integration
 * The Integration of ProcessMaker
 *
 */

class elockClass extends PMPlugin {

  private $ElockWebService_WSDL;
  private $soapObj;
  private $client;


  function __construct() {
    $this->sPluginFolder = 'elock';
    set_include_path ( PATH_PLUGINS . 'elock' . PATH_SEPARATOR . get_include_path () );
    $this->readConfig ();
//G::pr($this->ElockWebService_WSDL);
    if (($this->ElockWebService_WSDL != "")&&isset($_SESSION['APPLICATION'])) {
      try {
        //krumo($this->ElockWebService_WSDL);
        //krumo("jhl");
        if ($this->validateWSDLConnectivity (false)) {
          //$this->ktapi = new KTWSAPI( $this->KTWebService_WSDL );
          $this->setwsdlurl($this->ElockWebService_WSDL);
          $this->serverActive=true;
        }else{
          $this->serverActive=false;
        }
        //krumo($this->ktapi);
      } catch ( Exception $oError ) {

      }

    }

  }
  function file_get_conditional_contents($szURL) {
    $pCurl = curl_init ( $szURL );

    curl_setopt ( $pCurl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $pCurl, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt ( $pCurl, CURLOPT_TIMEOUT, 10 );

    $szContents = curl_exec ( $pCurl );
    $aInfo = curl_getinfo ( $pCurl );

    if ($aInfo ['http_code'] === 200) {
      return $szContents;
    }

    return false;
  }
  /*
   * General/Common PLugin functions
   */
  function validateWSDLConnectivity($sw_message=true) {
    ini_set ( 'allow_url_fopen', 1 );
    $sContent = $this->file_get_conditional_contents ( $this->ElockWebService_WSDL );
    $sw_connect = true;
    if ($sContent == '' || $sContent === false || strpos ( $sContent, 'address location' ) === false) {
      if($sw_message){
        $message = "Connection refused. <br>The WSDL '" . $this->ElockWebService_WSDL . "' is invalid or server is not responding.";
        $messageType = "Warning";
        G::SendMessageText ( $message, $messageType );
      }
      $sw_connect = false;
    }
    return $sw_connect;
  }
  function setup() {
  }
  function readConfig() {
    return $this->readLocalConfig ( SYS_SYS );
  }
  //this function reads the config file for this workspace
  function readLocalConfig($workspaceName) {
    $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
    //krumo($fileConf);
    if (! file_exists ( dirname ( $fileConf ) ))
    throw (new Exception ( "The directory " . dirname ( $fileConf ) . " doesn't exists." ));

    if (! file_exists ( $fileConf )) {
      $fields = array ();
      $fields ['ElockWebService_WSDL'] = '';
      $fields ['ElockOperatorUserName'] = '';
      $fields ['ElockOperatorUserPassword'] = '';
      $fields ['ElockAPP_PASSWORD'] = '';
      $fields ['ElockFTP_PATH'] = '';
      $fields ['ElockFTP_FOLDER'] = '';
      $fields ['ElockSERVER_IP'] = '';
      $fields ['ElockFTP_USER'] = '';
      $fields ['ElockFTP_PASSWORD'] = '';
      $fields ['DEMO_MODE'] = 'On';


      $content = serialize ( $fields );
      file_put_contents ( $fileConf, $content );
    }

    if (! file_exists ( $fileConf ) || ! is_writable ( $fileConf ))
    throw (new Exception ( "The file $fileConf doesn't exists or this file is not writable." ));

    $content = file_get_contents ( $fileConf );
    $fields = unserialize ( $content );
    //G::pr($fields);
    //If these fields are empty then fill with default
    $fields ['ElockWebService_WSDL'] = isset($fields ['ElockWebService_WSDL']) && $fields ['ElockWebService_WSDL'] != "" ? $fields ['ElockWebService_WSDL'] : "";

    $fields ['ElockOperatorUserName'] = isset($fields ['ElockOperatorUserName']) && $fields ['ElockOperatorUserName'] != "" ? $fields ['ElockOperatorUserName'] : "";
    $fields ['ElockOperatorUserPassword'] = isset($fields ['ElockOperatorUserPassword']) && $fields ['ElockOperatorUserPassword'] != "" ? $fields ['ElockOperatorUserPassword'] : "";
    $fields ['ElockAPP_PASSWORD'] = isset($fields ['ElockAPP_PASSWORD']) && $fields ['ElockAPP_PASSWORD'] != "" ? $fields ['ElockAPP_PASSWORD'] : "";
    $fields ['ElockFTP_PATH'] = isset($fields ['ElockFTP_PATH']) && $fields ['ElockFTP_PATH'] != "" ? $fields ['ElockFTP_PATH'] : "";
    $fields ['ElockFTP_FOLDER'] = isset($fields ['ElockFTP_FOLDER']) && $fields ['ElockFTP_FOLDER'] != "" ? $fields ['ElockFTP_FOLDER'] : "";
    $fields ['ElockSERVER_IP'] = isset($fields ['ElockSERVER_IP']) && $fields ['ElockSERVER_IP'] != "" ? $fields ['ElockSERVER_IP'] : "";
    $fields ['ElockFTP_USER'] = isset($fields ['ElockFTP_USER']) && $fields ['ElockFTP_USER'] != "" ? $fields ['ElockFTP_USER'] : "";
    $fields ['ElockFTP_PASSWORD'] = isset($fields ['ElockFTP_PASSWORD']) && $fields ['ElockFTP_PASSWORD'] != "" ? $fields ['ElockFTP_PASSWORD'] : "";
    $fields ['DEMO_MODE'] = isset($fields ['DEMO_MODE']) && $fields ['DEMO_MODE'] != "" ? $fields ['DEMO_MODE'] : "On";

    if($fields ['DEMO_MODE']=="On"){
      $this->ElockWebService_WSDL = "http://mobisigner.elock.com:8080/mobisigner?wsdl";
      $this->ElockOperatorUserName = "pmoperator";
      $this->ElockOperatorUserPassword = "pm123";
      $this->ElockAPP_PASSWORD = "PetVbgtnbntwbu";
      $this->ElockFTP_PATH = "d:/mobiSignerData/";
      $this->ElockFTP_FOLDER = "pm";
      $this->ElockSERVER_IP = "mobisigner.elock.com";
      $this->ElockFTP_USER = "pmuser";
      $this->ElockFTP_PASSWORD = "elock15092010";
      $this->DEMO_MODE = $fields ['DEMO_MODE'];
    }else{
      $this->ElockWebService_WSDL = $fields ['ElockWebService_WSDL'];
      $this->ElockOperatorUserName = $fields ['ElockOperatorUserName'];
      $this->ElockOperatorUserPassword = $fields ['ElockOperatorUserPassword'];
      $this->ElockAPP_PASSWORD = $fields ['ElockAPP_PASSWORD'];
      $this->ElockFTP_PATH = $fields ['ElockFTP_PATH'];
      $this->ElockFTP_FOLDER = $fields ['ElockFTP_FOLDER'];
      $this->ElockSERVER_IP = $fields ['ElockSERVER_IP'];
      $this->ElockFTP_USER = $fields ['ElockFTP_USER'];
      $this->ElockFTP_PASSWORD = $fields ['ElockFTP_PASSWORD'];
      $this->DEMO_MODE = $fields ['DEMO_MODE'];
    }
    return $fields;
  }
  //update the config data for this workspace
  function updateLocalConfig($workspaceName, $oData) {
    if (isset ( $oData ['form'] ['ACCEPT'] ))
    unset ( $oData ['form'] ['ACCEPT'] );
    if (isset ( $oData ['form'] ))
    $content = serialize ( $oData ['form'] );
    else
    $content = serialize ( $oData );
    $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
    if (! is_writable ( dirname ( $fileConf ) ))
    throw (new Exception ( "The directory " . dirname ( $fileConf ) . " doesn't exists or this directory is not writable." ));

    if (file_exists ( $fileConf ) && ! is_writable ( $fileConf ))
    throw (new Exception ( "The file $fileConf doesn't exists or this file is not writable." ));

    file_put_contents ( $fileConf, $content );
    return true;
  }

  function getFieldsForPageSetup() {
    return $this->readLocalConfig ( SYS_SYS );
  }

  //update fields
  function updateFieldsForPageSetup($oData) {
    return $this->updateLocalConfig ( SYS_SYS, $oData );
  }

  /*
   * Specific E-Lock API Functions
   */

   
  public function setwsdlurl($wsdl)
  {
    $this->wsdlurl = $this->ElockWebService_WSDL;
  }

  function showMsg($msg)
  {
  }

  function loadSOAPClient()
  {
    // try to load ur SOAP Client here
    try
    {
       
      $this->client = new SoapClient($this->ElockWebService_WSDL,array('trace' => true,'soap_version'=>SOAP_1_1));
      //G::pr($this->client);
      if($this->client instanceof SoapClient)
      {
      }
      else
      {
        echo "Exception Occured"; exit;
      }
    }
    catch(Exception $e)
    {
      echo $e;exit;
    }
  }

  function callWsMethod($methodName,$paramArray)
  {

    if(is_null($this->soapObj))
    {

      $this->loadSOAPClient();

    }
    $result = $this->client->__soapCall($methodName, array('parameters' => $paramArray));
    return $result;
  }

  function parseCaseVariable($variable,$aData) {
    $subject = $variable;
    $pattern = '/^[@][@#][a-zA-Z0-9_]+/';
    preg_match($pattern, $subject, $matchPre);
    if (isset($matchPre[0])&&$matchPre[0]!="") {
      $match = substr($matchPre[0], 2);
      if (isset($aData[$match])) {
        return $aData[$match];
      }
      else {
        return null;
      }
    }
    else {
      return null;
    }
  }

  function generateHtmlPdf ( $stepUidObj, $stepUid, $appUid ) {
    require_once 'classes/model/Step.php';
    require_once 'classes/model/OutputDocument.php';


    //get the step
    $oCriteria = new Criteria ( 'workflow' );
    $oCriteria->add ( StepPeer::STEP_UID_OBJ, $stepUidObj);
    $oCriteria->add ( StepPeer::STEP_UID, $stepUid);
    $oDataset = StepPeer::doSelectRS ( $oCriteria );
    $oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next ();
    $aRow = $oDataset->getRow ();
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
    //get the OutputDocument Row
    $doc = OutputDocumentPeer::retrieveByPK($docUid);
    if ( ( is_object ( $doc ) &&  get_class ($doc) == 'OutputDocument' ) ) {
      $docTitle = $doc->getOutDocTitle() ;
      if ( $doc->getOutDocGenerate() != 'PDF' ) {
    		  $doc->setOutDocGenerate('PDF') ;
    		  $doc->save();
      };
    }

    G::LoadClass('case');
    $oApp= new Cases();
    $aFields = $oApp->loadCase( $appUid );
    $aData = $aFields['APP_DATA'];
    /*
     $signers = array ();

     $lang = defined ('SYS_LANG') ? SYS_LANG : 'en';
     foreach($fields as $value) {
     if( $this->parseCaseVariable($value['signer_name'],$aData)!=null){
     $signers[] = $this->parseCaseVariable($value['signer_name'],$aData);
     }
     else {
     $signers[] = $value['signer_name'];
     }
     //create the variable for the image url, this url will be include like an image
     $index =  count($signers)-1;
     $imageUrl = 'http://' .$_SERVER['SERVER_NAME'] . ':' .$_SERVER['SERVER_PORT'] . '/sys' .SYS_SYS.'/' .$lang . '/green/sigplus/services/download';
     $imageUrl .= "?stpid=$stepUid&sigid=$index&appid=$appUid&tasid=$tasUid";
     $imageTag = "<img height='93' border='1' width='364' src='$imageUrl' />";

     $aData['IMAGE_SIGNER' . ($index+1) ] = $imageTag;

     }
     //end code parsing

     $iSigners = count ($signers);
     */
    //now we are trying to generate the output
    $oOutputDocument = new OutputDocument();

    $docFilename  = $doc->getOutDocFilename() ;
    $docTemplate  = $doc->getOutDocTemplate() ;
    $docLandscape = (boolean)$doc->getOutDocLandscape() ;

    $sFilename = ereg_replace('[^A-Za-z0-9_]', '_', G::replaceDataField($docFilename, $aData ) );
    if ( $sFilename == '' ) $sFilename='_';

    //check if the folder exists
    $pathOutput = PATH_DOCUMENT . $appUid . PATH_SEP . 'outdocs'. PATH_SEP ;
    G::mk_dir ( $pathOutput );
    $pdfFile = $pathOutput . $sFilename  . '.pdf';

    $oOutputDocument->generate( $docUid, $aData, $pathOutput, $sFilename, $docTemplate, $docLandscape );
     
    //******** Hugo's code to versioning a document ********(begin)**********
    require_once 'classes/model/AppFolder.php';
    require_once 'classes/model/AppDocument.php';

    //Get the Custom Folder ID (create if necessary)
    $oFolder=new AppFolder();
    $folderId=$oFolder->createFromPath($doc->getOutDocDestinationPath());

    //Tags
    $fileTags=$oFolder->parseTags($doc->getOutDocTags());

    //Get last Document Version and apply versioning if is enabled

    $oAppDocument= new AppDocument();
    $lastDocVersion=$oAppDocument->getLastDocVersion($doc->getOutDocUid(),$_SESSION['APPLICATION']);

    //if(($aOD['OUT_DOC_VERSIONING'])||($lastDocVersion==0)){
    //  $lastDocVersion++;
    //}

    $oCriteria = new Criteria('workflow');
    $oCriteria->add(AppDocumentPeer::APP_UID,      $_SESSION['APPLICATION']);
    $oCriteria->add(AppDocumentPeer::DEL_INDEX,    $_SESSION['INDEX']);
    $oCriteria->add(AppDocumentPeer::DOC_UID,      $doc->getOutDocUid());
    $oCriteria->add(AppDocumentPeer::DOC_VERSION,      $lastDocVersion);
    $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
    $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    if(($doc->getOutDocVersioning())&&($lastDocVersion!=0)){//Create new Version of current output
      if ($aRow = $oDataset->getRow()) {
        $aFields = array('APP_DOC_UID'         => $aRow['APP_DOC_UID'],
                                 'APP_UID'             => $_SESSION['APPLICATION'],
                                 'DEL_INDEX'           => $_SESSION['INDEX'],
                                 'DOC_UID'             => $doc->getOutDocUid(),
                                 'DOC_VERSION'         => $lastDocVersion+1,
                                 'USR_UID'             => $_SESSION['USER_LOGGED'],
                                 'APP_DOC_TYPE'        => 'OUTPUT',
                                 'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                                 'APP_DOC_FILENAME'    => $sFilename,
                                 'FOLDER_UID'          => $folderId,
                                 'APP_DOC_TAGS'        => $fileTags);
        $oAppDocument = new AppDocument();
        $oAppDocument->create($aFields);
        $sDocUID = $aRow['APP_DOC_UID'];
      }
    }else{//No versioning so Update a current Output or Create new if no exist
      if ($aRow = $oDataset->getRow()) { //Update
        $aFields = array('APP_DOC_UID'         => $aRow['APP_DOC_UID'],
                                 'APP_UID'             => $_SESSION['APPLICATION'],
                                 'DEL_INDEX'           => $_SESSION['INDEX'],
                                 'DOC_UID'             => $doc->getOutDocUid(),
                                 'DOC_VERSION'         => $lastDocVersion,
                                 'USR_UID'             => $_SESSION['USER_LOGGED'],
                                 'APP_DOC_TYPE'        => 'OUTPUT',
                                 'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                                 'APP_DOC_FILENAME'    => $sFilename,
                                 'FOLDER_UID'          => $folderId,
                                 'APP_DOC_TAGS'        => $fileTags);
        $oAppDocument = new AppDocument();
        $oAppDocument->update($aFields);
        $sDocUID = $aRow['APP_DOC_UID'];
      }else{ //create
        if($lastDocVersion==0) $lastDocVersion++;
        $aFields = array('APP_UID'             => $_SESSION['APPLICATION'],
                             'DEL_INDEX'           => $_SESSION['INDEX'],
                             'DOC_UID'             => $doc->getOutDocUid(),
                             'DOC_VERSION'         => $lastDocVersion,
                             'USR_UID'             => $_SESSION['USER_LOGGED'],
                             'APP_DOC_TYPE'        => 'OUTPUT',
                             'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                             'APP_DOC_FILENAME'    => $sFilename,
                             'FOLDER_UID'          => $folderId,
                             'APP_DOC_TAGS'        => $fileTags);
        $oAppDocument = new AppDocument();
        $sDocUID = $oAppDocument->create($aFields);
      }
    }


    //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    if ( $oPluginRegistry->existsTrigger ( PM_UPLOAD_DOCUMENT ) && class_exists ('uploadDocumentData' ) ) {

      $sPathName = PATH_DOCUMENT . $_SESSION['APPLICATION'] . PATH_SEP;

      $oData['APP_UID']   = $_SESSION['APPLICATION'];
      $oData['ATTACHMENT_FOLDER'] = true;
      
      $aOD = $oOutputDocument->load( $doc->getOutDocUid() );
    switch($aOD['OUT_DOC_GENERATE']){
                case "BOTH":
                    $documentData = new uploadDocumentData (
                                  $_SESSION['APPLICATION'],
                                  $_SESSION['USER_LOGGED'],
                                  $pathOutput . $sFilename . '.pdf',
                                  $sFilename. '.pdf',
                                  $sDocUID,
                                  $oAppDocument->getDocVersion()
                                  );

                    $documentData->sFileType = "PDF";
                    $documentData->bUseOutputFolder = true;
                    $uploadReturn=$oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
                    if($uploadReturn){//Only delete if the file was saved correctly
                        unlink ( $pathOutput . $sFilename. '.pdf' );
                    }
                    


                    $documentData = new uploadDocumentData (
                                  $_SESSION['APPLICATION'],
                                  $_SESSION['USER_LOGGED'],
                                  $pathOutput . $sFilename . '.doc',
                                  $sFilename. '.doc',
                                  $sDocUID,
                                  $oAppDocument->getDocVersion()
                                  );

                    $documentData->sFileType = "DOC";
                    $documentData->bUseOutputFolder = true;
                    $uploadReturn=$oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
                    if($uploadReturn){//Only delete if the file was saved correctly
                        unlink ( $pathOutput . $sFilename. '.doc' );
                    }

                break;
                case "PDF":
                    $documentData = new uploadDocumentData (
                                  $_SESSION['APPLICATION'],
                                  $_SESSION['USER_LOGGED'],
                                  $pathOutput . $sFilename . '.pdf',
                                  $sFilename. '.pdf',
                                  $sDocUID,
                                  $oAppDocument->getDocVersion()
                                  );


                    $documentData->sFileType = "PDF";
                    $documentData->bUseOutputFolder = true;
                    $uploadReturn=$oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
                    if($uploadReturn){//Only delete if the file was saved correctly
                        unlink ( $pathOutput . $sFilename. '.pdf' );
                    }
                break;
                case "DOC":
                    $documentData = new uploadDocumentData (
                                  $_SESSION['APPLICATION'],
                                  $_SESSION['USER_LOGGED'],
                                  $pathOutput . $sFilename . '.doc',
                                  $sFilename. '.doc',
                                  $sDocUID,
                                  $oAppDocument->getDocVersion()
                                  );

                    $documentData->sFileType = "DOC";
                    $documentData->bUseOutputFolder = true;
                    $uploadReturn=$oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
                    if($uploadReturn){//Only delete if the file was saved correctly
                        unlink ( $pathOutput . $sFilename. '.doc' );
                    }
                break;
            }

    }


    //******** Hugo's code to versioning a document *******(end)***********
  }

  function elockLogin($userId,$strPassword){
     
    $paramArr = array();
    //$paramOperatorUser = array("userId"=>$this->ElockOperatorUserName, "strPassword"=>$this->ElockOperatorUserPassword);
    $paramOperatorUser = array("userId"=> $userId, "strPassword" => $strPassword);// the user name and password should be of the loged user


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

    if (!($this->validateWSDLConnectivity ())) {
      return false;
    }

    //G::pr($paramOperatorUser);
    $retVal = $this->callWsMethod("AuthenticateUser",$paramOperatorUser);
    //var_dump($retVal);
    //G::pr($retVal);
    $operatorAuthToken = $retVal->AuthenticateUserResult;

    $paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword,"sessionId"=>$operatorAuthToken);
    //var_dump($paramOperatorUser);
    $result = $this->callWsMethod("AuthenticateSigner",$paramOperatorUser);
    //var_dump($result);
    $operatorAuthTokenSign = $result->AuthenticateSignerResult;
    //G::pr($result);
    //var_dump($operatorAuthTokenSign);

    return $operatorAuthToken;

  }
  function GetUserDetails($userId,$operatorAuthToken)
  {

    $paramArr = array();
    //$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

    $paramGetUserDetails = array("userId"=>$userId,"sessionId"=>$operatorAuthToken);
    //var_dump($paramGetUserDetails);
    $GetUserDetailsResultObject =  $this->callWsMethod("GetUserDetails",$paramGetUserDetails);
    //echo "this is the one";
    //var_dump($GetUserDetailsResultObject);

    //var_dump($VerifySignedDataResultObject);
    $GetUserDetailsOutputResult = $GetUserDetailsResultObject->GetUserDetailsResult;
    //G::pr($GetUserDetailsOutputResult);
    //echo "result Data";
    //echo "$VerifySignedDataResult[0]";
    //var_dump($VerifySignedDataResult);
    //echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
    //var_dump($GetUserDetailsOutputResult);
    return $GetUserDetailsOutputResult;



  }

  function signDataInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$operatorAuthToken)
  {



    $paramArr = array();
    //$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin


    $paramSignDataInMemory = array("data"=>$data,"filePath"=>'',"outFilePath"=>'',"bDetachedSignature"=>$bDetachedSignature,"sessionId"=>$operatorAuthToken);
    //var_dump($paramSignDataInMemory);
    $SignDataInMemoryResultObject =  $this->callWsMethod("SignDataInMemory",$paramSignDataInMemory);
    //echo "Final SIgned Data";
    //var_dump($SignDataInMemoryResultObject);
    $SignDataInMemoryResult = $SignDataInMemoryResultObject->SignDataInMemoryResult;

    //var_dump($SignDataInMemoryResult);
    //echo "SignWorkspaceFiles Result: " . $SignDataInMemoryResult;

    return $SignDataInMemoryResult;


  }

  function GetOperationStatusString($operatorAuthToken){

    $paramArr = array();
    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

    $paramGetOperationStatusString = array("sessionId"=>$operatorAuthToken);
    //var_dump($paramVerifySignedData);
    $GetOperationStatusStringResultObject =  $this->callWsMethod("GetOperationStatusString",$paramGetOperationStatusString);


    //var_dump($VerifySignedDataResultObject);
    //G::pr($GetOperationStatusStringResultObject);
    $GetOperationStatusStringResult = $GetOperationStatusStringResultObject->GetOperationStatusStringResult;

    //echo "result Data";
    //echo "$VerifySignedDataResult[0]";
    //var_dump($VerifySignedDataResult);
    //echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
    return $GetOperationStatusStringResultObject;


  }

  function AddNewSignUser($userId,$displayName,$userType,$userDN,$pwd,$email,$appPassword)
  {



    $paramArr = array();
    //$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin


    $paramAddUser = array("userId"=>$userId,"displayName"=>$displayName,"userType"=>$userType,"userDN"=>$userDN,"pwd"=>$pwd,"email"=>$email,"appPassword"=>$appPassword);
    //var_dump($paramAddUser);
    $AddUserResultObject =  $this->callWsMethod("AddUser",$paramAddUser);
    //echo "this is the one";
    //var_dump($AddUserResultObject);
    //G::pr($AddUserResultObject);
    //var_dump($VerifySignedDataResultObject);
    $AddUserResult = $AddUserResultObject->AddUserResult;

    //echo "result Data";
    //echo "$VerifySignedDataResult[0]";
    //var_dump($VerifySignedDataResult);
    //echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
    //var_dump($AddUserResult);
    return $AddUserResult;



  }
  function verifyData($base64EncodedSig,$originalData,$operatorAuthToken)
  {


    $paramArr = array();
    //$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin



    $paramVerifySignedData = array("base64EncodedSig"=>$base64EncodedSig,"originalData"=>$originalData,"sessionId"=>$operatorAuthToken);
    //var_dump($paramVerifySignedData);
    $VerifySignedDataResultObject =  $this->callWsMethod("VerifySignedData",$paramVerifySignedData);
    //G::pr(implode("<br>",$VerifySignedDataResultObject->VerifySignedDataResult->string));

    //var_dump($VerifySignedDataResultObject);
    $VerifySignedDataResult = $VerifySignedDataResultObject->VerifySignedDataResult;


    //echo "result Data";
    //echo "$VerifySignedDataResult[0]";
    //var_dump($VerifySignedDataResult);
    //echo "VerifySignedData Result: " . $VerifySignedDataResultObject;
    return $VerifySignedDataResult;


  }
  function signDocumentInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$sessionId)
  {


    $userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

    $paramSignDataInMemory = array("data"=>'',"filePath"=>$filePath,"outFilePath"=>$outFilePath,"bDetachedSignature"=>$bDetachedSignature,"sessionId"=>$sessionId);
    //var_dump($paramSignDataInMemory);
    $SignDataInMemoryResultObject =  $this->callWsMethod("SignDataInMemory",$paramSignDataInMemory);
    //var_dump($SignDataInMemoryResultObject);
    $SignDataInMemoryResult = $SignDataInMemoryResultObject->SignDataInMemoryResult;
    //var_dump($SignDataInMemoryResult);
    //echo "SignWorkspaceFiles Result: " . $SignDataInMemoryResult;

    return $SignDataInMemoryResult;

  }
  function GetAllSignerList($operatorAuthToken){
    $paramGetSignerList = array("sessionId"=>$operatorAuthToken);
    //var_dump($paramGetSignerList);
    $GetSignerListResultObject =  $this->callWsMethod("GetSignerList",$paramGetSignerList);
    //echo "this is the one";
    //var_dump($GetSignerListResultObject);

    //var_dump($VerifySignedDataResultObject);
    $GetSignerListResult = $GetSignerListResultObject->GetSignerListResult;
    $newList = $GetSignerListResult->string;
    //var_dump($newList);
    //echo "result Data";
    //echo "$VerifySignedDataResult[0]";
    //var_dump($VerifySignedDataResult);
    //echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
    //echo "this is an object";
    //var_dump($GetSignerListResult);
    return $newList;



  }

}