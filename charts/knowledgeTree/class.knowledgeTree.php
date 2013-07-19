<?php

require_once ('ktwsapi2.inc.php');

//require_once ('ktapi/ktapi.inc.php');

class KnowledgeTreeClass extends PMPlugin {

  var $sSessionId;
  var $ktapi;
  //var $KTWebService_WSDL = 'http://192.168.0.14:8080/ktwebservice/webservice.php?wsdl';
  //var $KTUpload_URL      = 'http://192.168.0.14:8080/ktwebservice/upload.php';
  var $KTWebService_WSDL = '';
  var $KTUpload_URL = '';
  var $KTDefaultUser = '';
  var $KTDefaultPass = '';
  var $KT_WIN = '';

  function __construct()
  {
    $this->sPluginFolder = 'knowledgeTree';
    set_include_path(
            PATH_PLUGINS . $this->sPluginFolder . PATH_SEPARATOR .
            get_include_path()
    );
    if (isset($_SESSION['KT_SESSION_ID']) && $_SESSION['KT_SESSION_ID'] != '' && $this->sSessionId != 1)
    {
      $this->sSessionId = $_SESSION['KT_SESSION_ID'];
    }
    ini_set('display_errors', 'On');
    ini_set('error_reporting', E_ERROR);

    $this->readConfig();


    require_once ( "classes/model/KtApplication.php" );
    require_once ( "classes/model/KtProcess.php" );
    require_once ( "classes/model/KtDocument.php" );

    if ($this->KTWebService_WSDL != "")
    {
      try
      {
        //krumo($this->KTWebService_WSDL);
        //krumo("jhl");
        if ($this->connected = $this->validateWSDLConnectivity(false))
        {
          $this->ktapi = new KTWSAPI($this->KTWebService_WSDL);
        }

        //krumo($this->ktapi);
      }
      catch (Exception $oError)
      {

      }
    }
  }

  function validateWSDLConnectivity($sw_message=true)
  {
    ini_set('allow_url_fopen', 1);
    $sContent = $this->file_get_conditional_contents($this->KTWebService_WSDL);
    // G::pr($sContent);die;
    $sw_connect = true;
    if ($sContent == '' || $sContent === false || strpos($sContent, 'address location') === false)
    {
      if ($sw_message)
      {
        $message = "Connection refused. <br>The WSDL '" . $this->KTWebService_WSDL . "' is invalid or server is not responding. $sContent";
        $messageType = "Warning";
        G::SendMessageText($message, $messageType);
      }
      $sw_connect = false;
    }
    return $sw_connect;
  }

  function readConfig()
  {
    return $this->readLocalConfig(SYS_SYS);
  }

  //this function reads the config file for this workspace
  function readLocalConfig($workspaceName)
  {
    $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
    //krumo($fileConf);
    if (!file_exists(dirname($fileConf)))
      throw ( new Exception("The directory " . dirname($fileConf) . " doesn't exists.") );

    if (!file_exists($fileConf))
    {
      $fields = array();
      $fields['KTWebService_WSDL'] = '';
      $fields['KTUpload_URL'] = '';
      $fields['KTDefaultUser'] = '';
      $fields['KTDefaultPass'] = '';
      $fields['KT_WIN'] = '';

      $content = serialize($fields);
      file_put_contents($fileConf, $content);
    }

    if (!file_exists($fileConf) || !is_writable($fileConf))
      throw ( new Exception("The file $fileConf doesn't exists or this file is not writable.") );

    $content = file_get_contents($fileConf);
    $fields = unserialize($content);

    //If these fields are empty then fill with default
    $fields['KTWebService_WSDL'] = $fields['KTWebService_WSDL'] != "" ? $fields['KTWebService_WSDL'] : "http://serverkt/knowledgetree/ktwebservice/webservice.php?wsdl";
    $fields['KTUpload_URL'] = $fields['KTUpload_URL'] != "" ? $fields['KTUpload_URL'] : "http://serverkt/knowledgetree/ktwebservice/upload.php";

    $this->KTWebService_WSDL = $fields['KTWebService_WSDL'];
    $this->KTUpload_URL = $fields['KTUpload_URL'];
    $this->KTDefaultUser = $fields['KTDefaultUser'];
    $this->KTDefaultPass = $fields['KTDefaultPass'];
    $this->KT_WIN =  $fields['KT_WIN'];

    define('KTUploadURL', $this->KTUpload_URL);

    return $fields;
  }

  //update the config data for this workspace
  function updateLocalConfig($workspaceName, $oData)
  {
    if (isset($oData['form']['ACCEPT'])) {
        unset($oData['form']['ACCEPT']);
    }
    if (isset($oData['form'])) {
        $content = serialize($oData['form']);
    } else {
        $content = serialize($oData);
    }
    $fileConf = PATH_DB . $workspaceName . PATH_SEP . $this->sPluginFolder . '.conf';
    if (!is_writable(dirname($fileConf))) {
      throw ( new Exception("The directory " . dirname($fileConf) . " doesn't exists or this directory is not writable.") );
    }

    if (file_exists($fileConf) && !is_writable($fileConf)) {
        throw ( new Exception("The file $fileConf doesn't exists or this file is not writable.") );
    }
    try {
        G::LoadClass( 'configuration' );
        $preferences = new Configurations();
        $arr['KT_WIN'] = $oData['form']['KT_WIN'];
        $arr['dateSave'] = date( 'Y-m-d H:i:s' );
        $config = Array ();
        $config[] = $arr;
        $preferences->aConfig = $config;
        $preferences->saveConfig( 'KT_PREFERENCES', '', '', '');

    }
    catch ( Exception $e ) {
        $G_PUBLISH = new Publisher;
        $aMessage['MESSAGE'] = $e->getMessage();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
        G::RenderPage( 'publish', 'blank' );
    }

    file_put_contents($fileConf, $content);
    return true;
  }

  function getFieldsForPageSetup()
  {
    return $this->readLocalConfig(SYS_SYS);
  }

  //update fields
  function updateFieldsForPageSetup($oData)
  {
    return $this->updateLocalConfig(SYS_SYS, $oData);
  }

  function file_get_conditional_contents($szURL)
  {


    $pCurl = curl_init();
    curl_setopt($pCurl, CURLOPT_URL, $szURL);
    curl_setopt($pCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($pCurl, CURLOPT_HEADER, true);
    curl_setopt($pCurl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($pCurl, CURLOPT_AUTOREFERER, true);
    //To avoid SSL error
    curl_setopt($pCurl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($pCurl, CURLOPT_SSL_VERIFYPEER, 0);
    /*
      curl_setopt($pCurl, CURLOPT_CAINFO,  getcwd().'/cert/ca.crt');
      curl_setopt($pCurl, CURLOPT_CAPATH,  getcwd());

      curl_setopt($pCurl, CURLOPT_SSLCERT, getcwd().'/cert/mycert.pem');
      curl_setopt($pCurl, CURLOPT_SSLCERTPASSWD, 'password');
     */
    //To avoid timeouts
    curl_setopt($pCurl, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($pCurl, CURLOPT_TIMEOUT, 200);


    curl_setopt($pCurl, CURLOPT_NOPROGRESS, true);
    //option debug writing in error_log
    curl_setopt($pCurl, CURLOPT_VERBOSE, false);



    $szContents = curl_exec($pCurl);
    $aInfo = curl_getinfo($pCurl);
    if ($aInfo['http_code'] === 200)
    {
      return $szContents;
    }
    else
    {
      //print "<h3>error</h3>";
      return curl_error($pCurl);
    }

    return false;
  }

  function verifyUserConfiguration($UsrUid)
  {
    require_once ( "classes/model/KtConfig.php" );
    $tr = KtConfigPeer::retrieveByPK($UsrUid);
    if (( is_object($tr) && get_class($tr) == 'KtConfig'))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  function ktUserConfigBreakStep($oData)
  {
    if ($this->validateWSDLConnectivity(false))
    {
      $ktDocTypeId = $this->getDocKtTypeId($oData['PRO_UID'], $oData['DOC_UID']);

      if ($ktDocTypeId == "")
      {//If there is no configuration for this document then do nothing
        return true; //Do not delete local copy
        exit;
      }


      $sw_verify = $this->verifyUserConfiguration($oData['USR_UID']);
      if (!$sw_verify)
      {
        global $G_PUBLISH;
        $G_PUBLISH->AddContent('view', 'knowledgeTree/ktUserConfigBreakStep');
        G::RenderPage('publish', 'blank');
        exit();
      }
    }
    return true;
  }

  function kt_login()
  {

    require_once ( "classes/model/KtConfig.php" );
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtConfigPeer::retrieveByPK($_SESSION['USER_LOGGED']);

    if (( is_object($tr) && get_class($tr) == 'KtConfig'))
    {
      $loginInfo->lName = $tr->getKtUsername();
      $loginInfo->lPassword = G::decrypt($tr->getKtPassword(), $tr->getUsrUid());
    }






    if ($this->validateWSDLConnectivity())
    {//There is connectivity
      $response = $this->ktapi->start_session($loginInfo->lName, $loginInfo->lPassword, $_SERVER['REMOTE_ADDR'], 'ProcessMaker');

      //to do: improve return pear error to upper level call
      if (PEAR::isError($response))
      {
        $loginInfo->lError = $response->getMessage();
      }
      else
      {
        $loginInfo->lSession = $this->ktapi->session;
        $message = "Login to Knowledgetree succeed. (" . $this->ktapi->session . ")";
        $messageType = "Info";
        $_SESSION['KT_SESSION_ID'] = $loginInfo->lSession;
        setcookie("PHPSESSID", $loginInfo->lSession, 0, "/", "localhost", false, false);
      }
    }
    else
    {
      $loginInfo->lError = "Connection Refused";
    }
    //G::pr($loginInfo);
    //G::SendMessageText("KT-SSO Status: ".$message,$messageType);

    return $loginInfo;
  }

  function start_session()
  {
    $this->ktapi->session = $this->sSessionId;
    return;
    $connected = false;
    ini_set('allow_url_fopen', 1);
    $sContent = $this->file_get_conditional_contents($this->KTWebService_WSDL);
    //$sContent = file_get_contents($this->KTWebService_WSDL);
    if ($sContent == '' || $sContent === false || strpos($sContent, 'address location') === false)
    {
      throw ( new Exception("Connection refused. The WSDL '" . $this->KTWebService_WSDL . "' is invalid or server is not responding."));
    }

    $this->ktapi = new KTWSAPI($this->KTWebService_WSDL);


    if (!$connected)
    {
      $response = $this->ktapi->start_session($this->KTUser, $this->KTPass);

      //to do: improve return pear error to upper level call
      if (PEAR::isError($response))
      {
        unset($_SESSION['KT_SESSION_ID']);
        throw ( new Exception($response->getMessage()));
        return $response;
      }
      else
      {
        $this->sSessionId = $this->ktapi->session;
        $_SESSION['KT_SESSION_ID'] = $this->sSessionId;
      }
    }

    return $response;
  }

  function getPMFolder()
  {
    $response = $root = $this->ktapi->get_folder_by_id($this->PM_FOLDER_ID);
    // to do: $response = $root = $this->ktapi->get_folder_by_name ( 'ProcessMaker' );
    return $response;
  }

  // return the content of specified folder
  // n folder n
  // 0 PM Folder
  function getListing($sApplicationUid, $docType ="")
  {
    //$this->readConfig();
    $listing = array();

    $c = new Criteria('workflow');
    $c->add(KtDocumentPeer::APP_UID, $sApplicationUid);
    if ($docType != "")
    {
      $c->add(KtDocumentPeer::DOC_PMTYPE, $docType);
    }
    $rs = KtDocumentPeer::doSelectRS($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();

    while (is_array($row))
    {
      $document = new stdClass();
      $document->id = $row['KT_DOCUMENT_ID'];
      $document->title = $row['KT_DOCUMENT_TITLE'];
      $document->item_type = $row['DOC_PMTYPE'];
      $document->type = $row['DOC_TYPE'];
      $document->filename = $row['DOC_UID'];
      $document->created_date = $row['KT_CREATE_DATE'];
      $document->downloadScript = "../knowledgeTree/services/documentShow?a=" . $document->filename . "&b=" . $document->id . "&t=" . $document->type . "&r=" . rand(1000, 10000);
      $listing[] = $document;
      $rs->next();
      $row = $rs->getRow();
    }
    return $listing;
  }

  // return the content of specified folder
  // n folder n
  // 0 PM Folder
  function downloadDocument($docId)
  {

    $this->ktapi->session = $this->sSessionId = NULL;
    $loginInfo = $this->kt_login();
    $this->sSessionId = $loginInfo->lSession;
    $document = $this->ktapi->get_document_by_id(intval($docId));
    if (PEAR::isError($document))
    {
      $loginInfo = $this->kt_login();
      if (isset($loginInfo->lError))
      {
        if ($loginInfo->lError != "")
        {
          throw ( new Exception($loginInfo->lError) );
        }
      }
      $document = $this->ktapi->get_document_by_id(intval($docId));
    }

    if (PEAR::isError($document))
    {

      throw ( new Exception($document->getMessage()) );
    }

    $document->download(null, PATH_DOCUMENT);
    return $document->filename;
  }

  function caseDocumentList($oData)
  {
    global $_DBArray;

    $this->readConfig();

    //$response = $this->start_session();
    //if (PEAR::isError($response) ) {
    //  return $response;
    //}
    //$kt_application = KtApplicationPeer::retrieveByPk( $oData->sApplicationUid );
    //if ( is_null ( $kt_application ) ) {
    //  throw ( new Exception ("The folder for case " . $oData->sApplicationUid ." is not valid." ) );
    //}
    //$folder = $this->ktapi->get_folder_by_id ( $kt_application->getKtFolderId () );
    //if (PEAR::isError($folder)) {
    //  return $folder;
    //}
    //$listing = $this->getListing( $kt_application->getKtFolderId () );
    $listing = $this->getListing($oData->sApplicationUid, $oData->PMType);
    if (PEAR::isError($listing))
    {
      throw ( new Exception($listing->getMessage()) );
    }


    // lets display the items
    $items[] = array('id' => 'char', 'title' => 'char', 'type' => 'char', 'creator' => 'char',
        'modifiedBy' => 'char', 'filename' => 'char', 'size' => 'char', 'mime' => 'char');
    if (is_array($listing))
      foreach ($listing as $folderitem)
      {
        $items[] = array(
            'id' => $folderitem->id,
            'title' => $folderitem->title,
            'type' => $folderitem->item_type == 'OUTPUT' ? 'Output' : $folderitem->item_type == 'INPUT' ? 'Input' : 'Folder',
            'creator' => isset($folderitem->creator) ? $folderitem->creator : '',
            'modifiedby' => isset($folderitem->modifiedby) ? $folderitem->modifiedby : '',
            //'filename'  => $folderitem->filename,
            //'filename'  => substr($folderitem->filename, 0, strrpos($folderitem->filename, '.' ) ),
            'filename' => $folderitem->filename,
            'size' => isset($folderitem->size) ? $folderitem->size : '',
            'created_date' => $folderitem->created_date,
            'type' => $folderitem->type,
            'created_date' => $folderitem->created_date,
            'random' => rand(1000, 10000)
        );
      }
    $_DBArray['KT_Listing'] = $items;
    $_SESSION['_DBArray'] = $_DBArray;

    G::LoadClass('ArrayPeer');
    $c = new Criteria('dbarray');
    $c->setDBArrayTable('KT_Listing');
    $c->addAscendingOrderByColumn('id');

    if ((isset($oData->returnList)) && ($oData->returnList))
    {
      return $listing;
    }
    else
    {
      /* kt end */
      global $G_PUBLISH;
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'knowledgeTree/documentList', $c);
    }
  }

  // return the content of specified folder
  // 1 root folder
  // 0 PM Folder
  function createCaseFolder($oData)
  {
    return;
    $this->readConfig();
    $this->start_session();
    $root = $this->ktapi->get_folder_by_id($this->PM_FOLDER_ID);

    if (PEAR::isError($root))
    {
      throw new Exception($root->getMessage());
      return $root;
    }

    require_once ( "classes/model/Application.php" );
    require_once ( "classes/model/Process.php" );

    //$process     = ProcessPeer::retrieveByPk( $oData->sProcessUid );
    $application = ApplicationPeer::retrieveByPk($oData->sApplicationUid);
    $kt_process = KtProcessPeer::retrieveByPk($oData->sProcessUid);

    if (is_null($kt_process))
    {
      $folderProcess = $root->add_folder($oData->sProcessTitle);
      if (PEAR::isError($folderProcess))
      {
        throw new Exception($folderProcess->getMessage());
      }

      $kt_process2 = new KtProcess();
      $kt_process2->setProUid($oData->sProcessUid);
      $kt_process2->setKtFolderId($folderProcess->folder_id);
      $kt_process2->setKtParentId($folderProcess->parent_id);
      $kt_process2->setKtFolderName($folderProcess->folder_name);
      $kt_process2->setKtFullPath($folderProcess->full_path);
      $kt_process2->setKtCreateUser($oData->sUserUid);
      $kt_process2->setKtCreateDate("now");
      $kt_process2->setKtUpdateDate("now");
      $kt_process2->Save();
    }
    else
    {
      $folderProcess = $this->ktapi->get_folder_by_id($kt_process->getKtFolderId());
    }

    if (PEAR::isError($folderProcess))
    {
      throw new Exception($folderProcess->getMessage());
    }

    //here we can select how can we store the folder name>
    // option A: the APP_NUMBER   a secuencial number
    $folderCase = $folderProcess->add_folder('Case ' . $application->getAppNumber());
    $folderAttach = $folderCase->add_folder('Attachments');
    $folderOutput = $folderCase->add_folder('Output Documents');
    $folderEmail = $folderCase->add_folder('Emails');
    // option B: the APP_UID      a guid number
    //$folder = $folderProcess->add_folder( $oData->sApplicationUid );

    $kt_application = new KtApplication();
    $kt_application->setAppUid($oData->sApplicationUid);
    $kt_application->setKtFolderId($folderCase->folder_id);
    $kt_application->setKtParentId($folderCase->parent_id);
    $kt_application->setKtFolderName($folderCase->folder_name);
    $kt_application->setKtFullPath($folderCase->full_path);
    $kt_application->setKtFolderAttachment($folderAttach->folder_id);
    $kt_application->setKtFolderOutput($folderAttach->folder_id);
    $kt_application->setKtFolderEmail($folderAttach->folder_id);
    $kt_application->setKtCreateUser($oData->sUserUid);
    $kt_application->setKtCreateDate("now");
    $kt_application->setKtUpdateDate("now");
    $kt_application->Save();

    return $folder;
  }

  // return the content of specified folder
  // 1 root folder
  // 0 PM Folder
  function addDocument($oData)
  {
    //set_include_path(
    //  PATH_PLUGINS . 'knowledgeTree' . PATH_SEPARATOR .
    //  get_include_path()
    //);
    //$this->readConfig();
    if (!$oData->sFileType)
    {
      $oData->sFileType = "";
    }
    //print "<h3>oData</h3>";print_r($oData);print "<hr>";
    //$con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
    $kt_application = KtApplicationPeer::retrieveByPk($oData->sApplicationUid);
    //print "<h3>ktApplication</h3>";print_r($kt_application);print "<hr>";
    $oApplication = new Application;
    $appFields = $oApplication->Load($oData->sApplicationUid);
    //print "<h3>appFields</h3>";print_r($appFields);print "<hr>";

    require_once ( "classes/model/AppDocument.php" );
    $oAppDocument = new AppDocument();
    $documentObj = $oAppDocument->load($oData->sDocumentUid, $oData->iVersion);
    //print "<h3>docObj</h3>";print_r($documentObj);print "<hr>";

    $ktDocTypeId = $this->getDocKtTypeId($appFields['PRO_UID'], $documentObj['DOC_UID']);

    if ($ktDocTypeId == "")
    {//If there is no configuration for this document then do nothing
      return false; //Do not delete local copy
      exit;
    }
    if (!$this->validateWSDLConnectivity())
    {
      return false; //Do not delete local file.. and do nothing no connection!
    }
    //print "<h3>ktdoctypeid</h3>";print_r($ktDocTypeId);print "<hr>";
    $destinationPath = $this->getDestinationPath($_SESSION['PROCESS'], $ktDocTypeId, $oData);
    //print "<h3>destinationpath</h3>";print_r($destinationPath);print "<hr>";

    $destinationPathParsed = G::replaceDataField($destinationPath, $appFields);
    $destinationPathParsed = G::replaceDataField($destinationPath, unserialize($appFields['APP_DATA']));
    //print "<h3>destinationpathparsed</h3>";print_r($destinationPathParsed);print "<hr>";

    $destinationPathArray = explode("/", $destinationPathParsed);
    //print "<h3>destinationpatharray</h3>";print_r($destinationPathArray);print "<hr>";


    $fieldsMap = $this->getFieldsMap($_SESSION['PROCESS'], $ktDocTypeId, $oData);
    //print "<h3>fieldsmap</h3>";print_r($fieldsMap);print "<hr>";

    $fieldsMapParsed = array();
    foreach ($fieldsMap as $key => $value)
    {
      $keyArray = explode("--", $key);
      $fieldsMapParsed[$keyArray[0]][$keyArray[1]] = G::replaceDataField("@#" . $value, $appFields);
      $fieldsMapParsed[$keyArray[0]][$keyArray[1]] = G::replaceDataField("@#" . $value, unserialize($appFields['APP_DATA']));
    }
    //print "<h3>fieldsMapParsed</h3>";print_r($fieldsMapParsed);print "<hr>";
    // $this->readConfig();
    // $this->start_session ();
    //$this->ktapi->session=$this->sSessionId;
    $loginInfo = $this->kt_login();
    if (isset($loginInfo->lError))
    {
      if ($loginInfo->lError != "")
      {
        G::SendMessageText("Unable to add the document to Knowledgetree.  " . $loginInfo->lError, "ERROR");
        return false;
      }
    }


    $root = $this->ktapi->get_folder_by_id(1);


    if (PEAR::isError($root))
    {
      //throw new Exception ( $root->getMessage() );
      //return $root;
      G::SendMessageText("Unable to add the document to Knowledgetree.  " . $root->getMessage(), "ERROR");
      return false;
    }
    //throw ( new Exception ( "JHL was here" ));
    //Create the path in KT if is necessary

    foreach ($destinationPathArray as $folderName)
    {


      if ($folderName != "")
      {
        //    print "--> $folderName<br>";
        //print_r($root->folder_name);print "<br>";
        //print_r($root->full_path);print "<hr>";
        //print_r($root->folder_id);print "<hr>";

        $folderExist = $root->get_folder_by_name($folderName, $root->folder_id);
        //print_r($folderExist);
        //die;
        if (PEAR::isError($folderExist))
        {

          $folderProcess = $root->add_folder($folderName);
          if (PEAR::isError($folderProcess))
          {
            //throw new Exception ( $folderProcess->getMessage() );
            G::SendMessageText("Unable to add the document to Knowledgetree.  " . $folderProcess->getMessage(), "ERROR");
            return false;
          }
          $root = $folderProcess;
        }
        else
        {
          $root = $folderExist;
        }
      }
    }










    //die;
    /*
      if ( is_null($kt_application) ) {
      $oProcess = new Process;
      $proFields = $oProcess->Load ( $appFields['PRO_UID'] );

      $folderData = new folderData ($appFields['PRO_UID'], $proFields['PRO_TITLE'], $oData->sApplicationUid, $appFields['APP_TITLE'], $oData->sUserUid );
      $this->createCaseFolder ( $folderData );
      $kt_application = KtApplicationPeer::retrieveByPk( $oData->sApplicationUid );
      }
      $this->start_session ();

      //$root = $this->ktapi->get_folder_by_id ( $kt_application->getKtFolderId () );
      if ( $oData->bUseOutputFolder )
      $root = $this->ktapi->get_folder_by_id ( $kt_application->getKtFolderOutput () );
      else
      $root = $this->ktapi->get_folder_by_id ( $kt_application->getKtFolderAttachment () );
     */
    if (PEAR::isError($root))
    {
      //throw ( new Exception ( $root->getMessage() ));
      G::SendMessageText("Unable to add the document to Knowledgetree.  " . $root->getMessage(), "ERROR");
      return false;
    }

    if (file_exists($oData->sFilename))
    {
      $kt_document = KtDocumentPeer::retrieveByPk($oData->sDocumentUid, $oData->sFileType);

      //if not exists the document in the KT Folder, create a row and upload the document
      if (is_null($kt_document))
      {
        $kt_document = new KtDocument();

        $response = $root->add_document($oData->sFilename, $oData->sFileTitle, $ktDocTypeId);
        //krumo($response);
        //throw ( new Exception ( "JHL was here" ));
        //print "<h1>A</h1>";
        // G::pr($response);
        if (PEAR::isError($response))
        {
          G::SendMessageText("Unable to add the document to Knowledgetree.  " . $response->message, "ERROR");

          return false;
        }

        $metadata = $response->get_metadata();
        //print "<h1>B</h1>";
        // G::pr($metadata);
        if (PEAR::isError($metadata))
        {
          G::SendMessageText("Unable to add the document to Knowledgetree.  " . $metadata->message, "ERROR");
          die();
          return false;
        }

        foreach ($metadata->metadata as $key => $fieldsetObj)
        {
          $fieldSetName = str_replace(" ", "_", $fieldsetObj->fieldset);
          foreach ($fieldsetObj->fields as $key1 => $fieldObj)
          {
            $fieldName = str_replace(" ", "_", $fieldObj->name);
            //print "<h3>VALUE FOR [$fieldSetName] [$fieldName]</h3>";print_r($fieldsMapParsed[$fieldSetName][$fieldName]);print "<hr>";
            //print "<h3>METADATA FOR [$fieldSetName] [$fieldName]</h3>";print_r($metadata->metadata[$key]->fields[$key1]);print "<hr>";
            $metadata->metadata[$key]->fields[$key1]->value = $fieldsMapParsed[$fieldSetName][$fieldName];
            //print "<h3>METADATA WITH VALUE FOR [$fieldSetName] [$fieldName]</h3>";print_r($metadata->metadata[$key]->fields[$key1]);print "<hr>";
          }
        }
        $updateMetadata = $response->update_metadata($metadata->metadata);
        //print "<h3>updatedMetadata</h3>";print_r($updateMetadata);print "<hr>";
        //die;

        if (PEAR::isError($updateMetadata))
        {
          //throw ( new Exception ( $updateMetadata->getMessage() ));
          G::SendMessageText("Unable to add the document to Knowledgetree.  " . $updateMetadata->getMessage(), "ERROR");
          return false;
        }
      }
      //the checking methods in the KT version  is not working, so this feature is disabled
      //if the document exists make a checkin checkout for the document
      else
      {
        $docId = $kt_document->getKtDocumentId();
        $document = $this->ktapi->get_document_by_id(intval($docId));
        /*
          $response = $document->checkout( 'generated output document by ProcessMaker at '. date ('Y-m-d H:i') , PATH_DOCUMENT );
          if ( $response->message == 'The document is checked out.' )
          print $response->message ;
          else
          krumo ( $response );
          $response = $document->checkin ($oData->sFilename, 'update '. $oData->sFileTitle, false );
          krumo ( $response);
          krumo ( $document ); die;
         */
        $response = $root->add_document($oData->sFilename, $oData->sFileTitle);
        if (PEAR::isError($response))
        {
          //throw ( new Exception ( $response->getMessage() ));
          G::SendMessageText("Unable to add the document to Knowledgetree.  " . $response->getMessage(), "ERROR");
          return false;
        }
      }

      $kt_document->setDocUid($oData->sDocumentUid); //
      $kt_document->setProUid($appFields['PRO_UID']);
      $kt_document->setAppUid($oData->sApplicationUid);
      $kt_document->setKtDocumentId($response->document_id); //
      $kt_document->setKtStatus($response->document_type);  //
      $kt_document->setKtDocumentTitle($response->title);
      $kt_document->setKtFullPath($response->full_path);
      $kt_document->setKtCreateUser($oData->sUserUid);
      $kt_document->setKtCreateDate("now");
      $kt_document->setKtUpdateDate("now");

      $kt_document->setDocType($oData->sFileType);
      $kt_document->setDocPmType($oData->bUseOutputFolder ? "OUTPUT" : "INPUT");


      $kt_document->Save();

      G::SendMessageText("" . $response->full_path . " was successfully saved in Knowledgetree", "INFO");

      return true; //Delete the local file
    }
    else
    {
      //throw ( new Exception ( "error, the uploaded file is invalid or doesn't exists." ));
      G::SendMessageText("error, the uploaded file is invalid or doesn't exists.", "ERROR");
      return false;
    }
    //return $response;
  }

  function setup()
  {
    $response = $root = $ktapi->get_root_folder();
    if (PEAR::isError($response))
    {
      print $response->getMessage();
      exit;
    }
  }

  function kt_get_listing($folderId="ROOT", $depth=1, $what="DF")
  {
    if ($folderId == "ROOT")
      $folderId = $this->PM_FOLDER_ID;
    //krumo($this);
    $loginInfo = $this->kt_login();
    if (isset($loginInfo->lError))
    {
      if ($loginInfo->lError != "")
      {
        G::SendMessageText("Unable to get requested information from Knowledgetree.  " . $loginInfo->lError, "ERROR");
        return false;
      }
    }

    $folderObj = $this->ktapi->get_folder_by_id($folderId);
    //krumo($folderObj);
    if (PEAR::isError($folderObj))
    {
      return $folderObj->getMessage();
      exit;
    }
    $folderList = $folderObj->get_listing($depth, $what);
    //krumo($folderList);
    return array('parent' => $folderObj, 'list' => $folderList);
  }

  function kt_get_documentTypes()
  {


    $loginInfo = $this->kt_login();
    if (isset($loginInfo->lError))
    {
      if ($loginInfo->lError != "")
      {
        G::SendMessageText("Unable to get requested information from Knowledgetree.  " . $loginInfo->lError, "ERROR");
        return false;
      }
    }

    $documentTypes = $this->ktapi->soapclient->get_document_types($this->ktapi->session);

    if ($documentTypes->status_code != 0)
    {
      return new PEAR_Error($documentTypes->message);
    }
    foreach ($documentTypes->document_types as $typeId => $typeName)
    {
      $typeFields = $this->ktapi->soapclient->get_document_type_metadata($this->ktapi->session, $typeName);
    }

    if (PEAR::isError($documentTypes))
    {
      return $documentTypes->getMessage();
      exit;
    }
    return $documentTypes;
  }

  function kt_get_documentTypeFields($typeName)
  {
    //krumo($this);
    $loginInfo = $this->kt_login();
    if (isset($loginInfo->lError))
    {
      if ($loginInfo->lError != "")
      {
        G::SendMessageText("Unable to get requested information from Knowledgetree.  " . $loginInfo->lError, "ERROR");
        return false;
      }
    }
    $typeFields = $this->ktapi->soapclient->get_document_type_metadata($this->ktapi->session, $typeName);

    if (PEAR::isError($documentTypes))
    {
      return $documentTypes->getMessage();
      exit;
    }
    return $typeFields;
  }

  function kt_search($query, $options)
  {
    //krumo($this);
    //$query='(GeneralText contains "'.$query.'")';
    $loginInfo = $this->kt_login();
    if (isset($loginInfo->lError))
    {
      if ($loginInfo->lError != "")
      {
        G::SendMessageText("Unable to get requested information from Knowledgetree.  " . $loginInfo->lError, "ERROR");
        return false;
      }
    }
    $searchResult = $this->ktapi->soapclient->search($this->ktapi->session, $query, $options);

    if (PEAR::isError($searchResult))
    {
      krumo($searchResult);
      return $searchResult->getMessage();
      exit;
    }
    return $searchResult;
  }

  function getDestinationPath($processId, $documentType, $oData=NULL)
  {
    $destinationPath = "--Not defined--";
    require_once ( "classes/model/KtFieldsMap.php" );
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtFieldsMapPeer::retrieveByPK($documentType, $processId);

    if (( is_object($tr) && get_class($tr) == 'KtFieldsMap'))
    {
      if ($tr->getDestinationPath() != "")
      {
        $destinationPath = $tr->getDestinationPath();
      }
    }
    elseif ($oData)
    {


      require_once ( "classes/model/Application.php" );

      $application = ApplicationPeer::retrieveByPk($oData->sApplicationUid);




      $destinationPath = "/";
      $destinationPath .='Case ' . $application->getAppNumber() . "/";
      if ($oData->bUseOutputFolder)
      {
        $destinationPath .='Output Documents';
      }
      else
      {
        $destinationPath .='Attachments';
      }
    }
    return $destinationPath;
  }

  function getDocKtTypeId($ProUid, $DocUid)
  {
    require_once ( "classes/model/KtDocType.php" );
    $tr = KtDocTypePeer::retrieveByPK($ProUid, $DocUid);
    //$ktDocTypeId="Default";
    $ktDocTypeId = "";
    if (( is_object($tr) && get_class($tr) == 'KtDocType'))
    {
      $ktDocTypeId = $tr->getDocKtTypeId();
    }
    return $ktDocTypeId;
  }

  function getFieldsMap($processId, $documentType)
  {

    require_once ( "classes/model/KtFieldsMap.php" );
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtFieldsMapPeer::retrieveByPK($documentType, $processId);

    if (( is_object($tr) && get_class($tr) == 'KtFieldsMap'))
    {

      $fieldsMap = unserialize($tr->getFieldsMap());
    }
    return $fieldsMap;
  }
  
  function getUrlDownload($appdocument_uid)
  {
    $url = null;
    
    $criteria = new Criteria("workflow");
    
    $criteria->add(KtDocumentPeer::DOC_UID, $appdocument_uid);
    
    $rs = KtDocumentPeer::doSelectRS($criteria);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    
    while ($rs->next()) {
      $row = $rs->getRow();
      
      $url = "../knowledgeTree/documentShow?kta=" . $row["KT_DOCUMENT_TITLE"] . "&b=" . $row["KT_DOCUMENT_ID"] . "&t=" . $row["DOC_TYPE"] . "&r=" . rand(1000, 10000);
      //$url = "../knowledgeTree/services/documentShow?a=" . $row["DOC_UID"] . "&b=" . $row["KT_DOCUMENT_ID"] . "&t=" . $row["DOC_TYPE"] . "&r=" . rand(1000, 10000);
    }
    
    return $url;
  }
}
?>