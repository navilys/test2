<?php
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclFormReplicator.php');
$oFormReplicator = new tclFormReplicator();

$bResult = $oFormReplicator->copyDynaform("formshare", "workflow", "6146426994dc2dc8e446fc1004165971", "6223792654e86e2440d0e02043593487");
if($bResult)
	print "done";
else
	print "handled error: ".$oFormReplicator->sErrorMessage;

?>