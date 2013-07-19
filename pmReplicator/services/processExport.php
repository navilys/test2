<?php
//http://192.168.1.8/sysformshare/en/classic/customizedApprovals/services/processExport?sProUid=7332262014ddd22f62d5a55094652067
//http://192.168.1.8/sysworkflow/en/classic/customizedApprovals/services/processExport?sProUid=6223792654e86e2440d0e02043593487
G::LoadClass('processes');
$sProUid = $_REQUEST["DATA_UID"];
$sProcessDone=true;
$oProcess  = new Processes();
$proFields = $oProcess->serializeProcess( $sProUid );
$Fields = $oProcess->saveSerializedProcess ( $proFields );
$sFilename = $Fields['FILENAME'];
//paths
$path = PATH_DOCUMENT.'output'.PATH_SEP;
//rename file
rename($path.$sFilename.'tpm', $path.$sFilename);
//copy
echo "$sFilename";
?>