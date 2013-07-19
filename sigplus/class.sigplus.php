<?php
/**
 * class.sigplus.php
 *
 */

class sigplusClass extends PMPlugin
{  function __construct ()
   {  set_include_path(
        PATH_PLUGINS . 'sigplus' . PATH_SEPARATOR .
        get_include_path()
      );
   }

   function setup()
   {
   }

   function getFieldsForPageSetup()
   {
   }

   function updateFieldsForPageSetup()
   {
   }

   function parseCaseVariable($variable,$aData)
			{  $subject = $variable;
      $pattern = '/^[@][@#][a-zA-Z0-9_]+/';
      preg_match($pattern, $subject, $matchPre);
      
						if (isset($matchPre[0]) && $matchPre[0] != "") {
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
    
   function generateHtmlPdf($stepUidObj, $stepUid, $appUid)
   {  G::LoadClass("case");
      $oApp = new Cases();
      $aFields = $oApp->loadCase($appUid);
      $aData = $aFields["APP_DATA"];

      //Get the sigplus step
      $oCriteria = new Criteria("workflow");
      $oCriteria->add(StepPeer::STEP_UID_OBJ, $stepUidObj);
      $oCriteria->add(StepPeer::STEP_UID, $stepUid);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();

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
        $fields = array();
        $docUid = null;
      }

      //Get the OutputDocument Row
      $doc = OutputDocumentPeer::retrieveByPK($docUid);
      if ((is_object($doc) && get_class($doc) == "OutputDocument")) {
        $docTitle = $doc->getOutDocTitle() ;
        if ($doc->getOutDocGenerate() != "PDF") {
          $doc->setOutDocGenerate("PDF") ;
          $doc->save();
        };
      }

      $signers = array();
      $lang = (defined("SYS_LANG"))? SYS_LANG : "en";

      foreach ($fields as $value) {
        if($this->parseCaseVariable($value["signer_name"], $aData) != null){
          $signers[] = $this->parseCaseVariable($value["signer_name"], $aData);
        }
        else {
          $signers[] = $value["signer_name"];
        }

        //Create the variable for the image url, this url will be include like an image
        $index = count($signers) - 1;
        $imageUrl = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . "/sys" . SYS_SYS . "/" . $lang . "/" . SYS_SKIN. "/sigplus/services/download";
        $imageUrl = $imageUrl . "?stpid=$stepUid&sigid=$index&appid=$appUid&tasid=$tasUid";
        $imageTag = "<img height=\"93\" border=\"1\" width=\"364\" src=\"$imageUrl\" />";
        $aData["IMAGE_SIGNER" . ($index + 1)] = $imageTag;
      }

      //End code parsing
      $iSigners = count($signers);

      //Now we are trying to generate the output
      $docFilename  = $doc->getOutDocFilename();
      $docTemplate  = $doc->getOutDocTemplate();
      $docLandscape = (boolean)($doc->getOutDocLandscape());

      $sFilename = ereg_replace("[^A-Za-z0-9_]", "_", G::replaceDataField($docFilename, $aData));
      if ($sFilename == "") {
        $sFilename = "_";
      }

      //Check if the folder exists
      $pathOutput = PATH_DOCUMENT . $appUid . PATH_SEP . "outdocs" . PATH_SEP ;
      G::mk_dir($pathOutput);
      //$pdfFile = $pathOutput . $sFilename . ".pdf";

      $oOutputDocument = new OutputDocument();
      $oOutputDocument->generate($docUid, $aData, $pathOutput, $sFilename, $docTemplate, $docLandscape);

      //********** Hugo's code to versioning a document **********(begin)**********
      require_once "classes/model/AppFolder.php";
      require_once "classes/model/AppDocument.php";

      //Get the Custom Folder ID (create if necessary)
      $oFolder = new AppFolder();
      $folderId = $oFolder->createFromPath($doc->getOutDocDestinationPath());

      //Tags
      $fileTags = $oFolder->parseTags($doc->getOutDocTags());

      //Get last Document Version and apply versioning if is enabled
      $oAppDocument = new AppDocument();
      $lastDocVersion = $oAppDocument->getLastDocVersion($doc->getOutDocUid(), $_SESSION["APPLICATION"]);

      //if(($aOD["OUT_DOC_VERSIONING"]) || ($lastDocVersion == 0)){
      //  $lastDocVersion++;
      //}

      $oCriteria = new Criteria("workflow");
      $oCriteria->add(AppDocumentPeer::APP_UID,      $_SESSION["APPLICATION"]);
      $oCriteria->add(AppDocumentPeer::DEL_INDEX,    $_SESSION["INDEX"]);
      $oCriteria->add(AppDocumentPeer::DOC_UID,      $doc->getOutDocUid());
      $oCriteria->add(AppDocumentPeer::DOC_VERSION,  $lastDocVersion);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, "OUTPUT");
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();

      if ($doc->getOutDocVersioning() && ($lastDocVersion != 0)) { //Create new Version of current output
        if ($aRow == $oDataset->getRow()) {
          $aFields = array("APP_DOC_UID"         => $aRow["APP_DOC_UID"],
                           "APP_UID"             => $_SESSION["APPLICATION"],
                           "DEL_INDEX"           => $_SESSION["INDEX"],
                           "DOC_UID"             => $doc->getOutDocUid(),
                           "DOC_VERSION"         => $lastDocVersion + 1,
                           "USR_UID"             => $_SESSION["USER_LOGGED"],
                           "APP_DOC_TYPE"        => "OUTPUT",
                           "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                           "APP_DOC_FILENAME"    => $sFilename,
                           "FOLDER_UID"          => $folderId,
                           "APP_DOC_TAGS"        => $fileTags);

          $oAppDocument = new AppDocument();
          $oAppDocument->create($aFields);

          $sDocUID = $aRow["APP_DOC_UID"];
        }
        else { //Create when the case is paused the processmaker is generating a new index and is necesary to create a new document
          if ($lastDocVersion == 0 ) {
            $lastDocVersion++;
          }

          $aFields = array("APP_UID"             => $_SESSION["APPLICATION"],
                           "DEL_INDEX"           => $_SESSION["INDEX"],
                           "DOC_UID"             => $doc->getOutDocUid(),
                           "DOC_VERSION"         => $lastDocVersion,
                           "USR_UID"             => $_SESSION["USER_LOGGED"],
                           "APP_DOC_TYPE"        => "OUTPUT",
                           "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                           "APP_DOC_FILENAME"    => $sFilename,
                           "FOLDER_UID"          => $folderId,
                           "APP_DOC_TAGS"        => $fileTags);

          $oAppDocument = new AppDocument();
          $sDocUID = $oAppDocument->create($aFields);
        }
      }
      else { //No versioning so Update a current Output or Create new if no exist
        if ($aRow == $oDataset->getRow()) { //Update
          $aFields = array("APP_DOC_UID"         => $aRow["APP_DOC_UID"],
                           "APP_UID"             => $_SESSION["APPLICATION"],
                           "DEL_INDEX"           => $_SESSION["INDEX"],
                           "DOC_UID"             => $doc->getOutDocUid(),
                           "DOC_VERSION"         => $lastDocVersion,
                           "USR_UID"             => $_SESSION["USER_LOGGED"],
                           "APP_DOC_TYPE"        => "OUTPUT",
                           "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                           "APP_DOC_FILENAME"    => $sFilename,
                           "FOLDER_UID"          => $folderId,
                           "APP_DOC_TAGS"        => $fileTags);

          $oAppDocument = new AppDocument();
          $oAppDocument->update($aFields);

          $sDocUID = $aRow["APP_DOC_UID"];
        }
        else { //create
          if($lastDocVersion == 0) {
            $lastDocVersion++;
          }

          $aFields = array("APP_UID"             => $_SESSION["APPLICATION"],
                           "DEL_INDEX"           => $_SESSION["INDEX"],
                           "DOC_UID"             => $doc->getOutDocUid(),
                           "DOC_VERSION"         => $lastDocVersion,
                           "USR_UID"             => $_SESSION["USER_LOGGED"],
                           "APP_DOC_TYPE"        => "OUTPUT",
                           "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                           "APP_DOC_FILENAME"    => $sFilename,
                           "FOLDER_UID"          => $folderId,
                           "APP_DOC_TAGS"        => $fileTags);

          $oAppDocument = new AppDocument();
          $sDocUID = $oAppDocument->create($aFields);
        }
      }

      //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
      $oPluginRegistry = &PMPluginRegistry::getSingleton();

      if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists("uploadDocumentData")) {
        $sPathName = PATH_DOCUMENT . $_SESSION["APPLICATION"] . PATH_SEP;

        $oData["APP_UID"] = $_SESSION["APPLICATION"];
        $oData["ATTACHMENT_FOLDER"] = true;

        switch ($aOD["OUT_DOC_GENERATE"]) {
          case "BOTH": $documentData = new uploadDocumentData(
                         $_SESSION["APPLICATION"],
                         $_SESSION["USER_LOGGED"],
                         $pathOutput . $sFilename . ".pdf",
                         $sFilename . ".pdf",
                         $sDocUID,
                         $oAppDocument->getDocVersion()
                       );

                       $documentData->sFileType = "PDF";
                       $documentData->bUseOutputFolder = true;
                       $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                       unlink($pathOutput . $sFilename. ".pdf");

                       $documentData = new uploadDocumentData(
                         $_SESSION["APPLICATION"],
                         $_SESSION["USER_LOGGED"],
                         $pathOutput . $sFilename . ".doc",
                         $sFilename . ".doc",
                         $sDocUID,
                         $oAppDocument->getDocVersion()
                       );

                       $documentData->sFileType = "DOC";
                       $documentData->bUseOutputFolder = true;
                       $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                       unlink($pathOutput . $sFilename . ".doc");
                       break;

          case "PDF": $documentData = new uploadDocumentData(
                        $_SESSION["APPLICATION"],
                        $_SESSION["USER_LOGGED"],
                        $pathOutput . $sFilename . ".pdf",
                        $sFilename . ".pdf",
                        $sDocUID,
                        $oAppDocument->getDocVersion()
                      );

                      $documentData->sFileType = "PDF";
                      $documentData->bUseOutputFolder = true;
                      $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT , $documentData);
                      unlink($pathOutput . $sFilename . ".pdf");
                      break;

          case "DOC": $documentData = new uploadDocumentData(
                        $_SESSION["APPLICATION"],
                        $_SESSION["USER_LOGGED"],
                        $pathOutput . $sFilename . ".doc",
                        $sFilename . ".doc",
                        $sDocUID,
                        $oAppDocument->getDocVersion()
                      );

                      $documentData->sFileType = "DOC";
                      $documentData->bUseOutputFolder = true;
                      $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT , $documentData);
                      unlink($pathOutput . $sFilename . ".doc");
                      break;
        }
      }
      //********** Hugo's code to versioning a document **********(end)**********
   }    
}
?>
