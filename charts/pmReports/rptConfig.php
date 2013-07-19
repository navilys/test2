<?php
require_once ( "classes/model/PmReportPeer.php" );
require_once ( "classes/model/PmReport.php" );
require_once ('classes/model/Content.php');
$G_PUBLISH = new Publisher;

$sPmrUid=0;
//if Edit, then sPmrUid will be different from 0
if(isset($_POST['sPmrUid']))
	$sPmrUid=$_POST['sPmrUid'];

//$aFields['SYS_LANG'] = SYS_LANG;
$aFields['PRO_UID'] = $_SESSION['PROCESS'];
//if different from 0, then retrieve information related
if($sPmrUid){
	$oPmReport = PmReportPeer::retrieveByPK($sPmrUid);
	if(is_object($oPmReport) && get_class($oPmReport) == 'PmReport'){
		
		$aFields['REP_TAB_UID'] = $oPmReport->getRepTabUid();
		$aFields['PMR_UID'    ] = $oPmReport->getPmrUid();
		$aFields['PMR_STATUS' ] = $oPmReport->getPmrStatus();
		$aFields['DYN_UID'    ] = $oPmReport->getDynUid();
		$oContent = new Content();
		$aFields['PMR_TITLE'      ] = $oContent->load('PMR_TITLE', '', $oPmReport->getPmrUid(), SYS_LANG);
		$aFields['PMR_DESCRIPTION'] = $oContent->load('PMR_DESCRIPTION', '', $oPmReport->getPmrUid(), SYS_LANG);
	}
}


$G_PUBLISH->AddContent('xmlform', 'xmlform', 'pmReports/rptConfig','',$aFields);

G::RenderPage( "publish", "blank" );