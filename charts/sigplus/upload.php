<?php

  function logx ( $text ) {
    $logfile = '/home/fernando/x.log';
    $fp = fopen ( $logfile, 'a');
    fwrite ( $fp, "$text \n" );
    fclose ( $fp );
  }
  
  $appid = $_POST['appid'];
  $tasid = $_POST['tasid'];
  $stpid = $_POST['stpid'];
		
  logx ( date ('H-m-s H:i:s ') );
  logx ( $appid );
  logx ( $tasid );
  logx ( $stpid );

die;
/*
	if (isset($_FILES) && $_FILES['ATTACH_FILE']['error'] == 0) {
		$sPathName = PATH_DOCUMENT . $_POST['APPLICATION'] . PATH_SEP;
		$sFileName = $sAppDocUid . '.' . $ext;
		print G::uploadFile($_FILES['ATTACH_FILE']['tmp_name'], $sPathName, $sFileName );
		print ("* The file {$_FILES['ATTACH_FILE']['name']} was uploaded successfully in case {$_POST['APPLICATION']} as input document..\n");
		
		//Plugin Hook PM_UPLOAD_DOCUMENT for upload document
    	$oPluginRegistry =& PMPluginRegistry::getSingleton();
        if ( $oPluginRegistry->existsTrigger ( PM_UPLOAD_DOCUMENT ) && class_exists ('uploadDocumentData' ) ) {
			$oData['APP_UID']	  = $_POST['APPLICATION'];
			$documentData = new uploadDocumentData (
	            $_POST['APPLICATION'],
	            $_POST['USR_UID'],
	            $sPathName . $sFileName,
	            $aFields['APP_DOC_FILENAME'],
	            $sAppDocUid
	        );
			
			$oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
			unlink ( $sPathName . $sFileName );
        }
      //end plugin
	}
		*/