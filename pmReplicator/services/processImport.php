<?php
//http://192.168.1.8/syssample/en/classic/customizedApprovals/services/processImport?filename=Leave_of_Absence8.pm
G::LoadClass('processes');
$path = PATH_DOCUMENT.'input'.PATH_SEP;
$filename = $_REQUEST['DATA_FILENAME'];
$oProcess = new Processes();
$oData = $oProcess->getProcessData ( $path . $filename  );
$Fields['PRO_FILENAME']  = $filename;
$Fields['IMPORT_OPTION'] = 2;
$sProUid = $oData->process['PRO_UID'];
$oData->process['PRO_UID_OLD']=$sProUid;
//groups
$groupsDuplicated = $oProcess->checkExistingGroups($oData->groupwfs);
if($groupsDuplicated>0){
	$oData->groupwfs = $oProcess->renameExistingGroups($oData->groupwfs);
}
//process
if ( $oProcess->processExists ( $sProUid ) ) {
	$oProcess->updateProcessFromData ($oData, $path . $filename );
	if (file_exists(PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid)) {
	  $oDirectory = dir(PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid);
	  while($sObjectName = $oDirectory->read()) {
	    if (($sObjectName != '.') && ($sObjectName != '..')) {
	      unlink(PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid . PATH_SEP .  $sObjectName);
	    }
	  }
	  $oDirectory->close();
	}
	$sNewProUid = $sProUid;
}else{
	$oProcess->createProcessFromData ($oData, $path . $filename );
}
echo "Done";
?>