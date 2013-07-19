<?php
require_once ( "classes/model/PmReportPermissionsPeer.php" );
require_once ( "classes/model/PmReportPermissionsPeer.php" );
require_once ( 'classes/model/Content.php');
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/Users.php';
require_once 'classes/model/GroupUserPeer.php';
$G_PUBLISH = new Publisher;

$sPmrUid = $_POST['sPmrUid'];

$aUIDS1 = array();
$aUIDS2 = array();

$aGroups   = array();
$oCriteria = new Criteria('workflow');
$oCriteria->addJoin(GroupwfPeer::GRP_UID, PmReportPermissionsPeer::USR_UID, Criteria::LEFT_JOIN);
$oCriteria->add(PmReportPermissionsPeer::PMR_UID,     $sPmrUid);
$oCriteria->add(PmReportPermissionsPeer::PMRP_TYPE, 2);
$oCriteria->add(GroupwfPeer::GRP_STATUS,   'ACTIVE');
$oDataset = GroupwfPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
  $aGroups[] = $aRow;
  $oDataset->next();
}
foreach ($aGroups as $aGroup) {
  $aUIDS1 [] = $aGroup ['GRP_UID'];
}
$aUsers    = array();
$oCriteria = new Criteria('workflow');
$oCriteria->addJoin(UsersPeer::USR_UID, PmReportPermissionsPeer::USR_UID, Criteria::LEFT_JOIN);
$oCriteria->add(PmReportPermissionsPeer::PMR_UID,     $sPmrUid);
$oCriteria->add(PmReportPermissionsPeer::PMRP_TYPE, 1);
$oDataset = UsersPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
  $aUsers[] = $aRow;
  $oDataset->next();
}
foreach ($aUsers as $aUser) {
  $aUIDS2 [] = $aUser ['USR_UID'];
}
$aUsers = array();
$aUsers [] = array('LABEL' => 'char', 'PMR_UID' => 'char', 'USR_UID' => 'char', 'PMRP_TYPE' => 'integer');
$sDelimiter = DBAdapter::getStringDelimiter ();
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(GroupwfPeer::GRP_UID);
$oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');
$oCriteria->addAlias('C', 'CONTENT');
$aConditions = array();
$aConditions [] = array(GroupwfPeer::GRP_UID, 'C.CON_ID');
$aConditions [] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter);
$aConditions [] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
$oCriteria->add(GroupwfPeer::GRP_STATUS, 'ACTIVE');
$oCriteria->add(GroupwfPeer::GRP_UID, $aUIDS1, Criteria::NOT_IN);
//$oCriteria->add(GroupwfPeer::GRP_UID, '', Criteria::NOT_EQUAL);
$oDataset = GroupwfPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$c = 0;
while ($aRow = $oDataset->getRow()) {
  $c++;
  $oCriteria = new Criteria('workflow');
  $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');
  $oCriteria->add(GroupUserPeer::GRP_UID, $aRow ['GRP_UID']);
  $oDataset2 = GroupUserPeer::doSelectRS($oCriteria);
  $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset2->next();
  $aRow2 = $oDataset2->getRow();
  $aUsers [] = array('LABEL' => $aRow ['GRP_TITLE'] . ' <a href="#" onclick="usersGroup(\'' . $aRow ['GRP_UID'] . '\', \'' . $c . '\');return false;"><font color="green"><strong>(' . $aRow2 ['MEMBERS_NUMBER'] . ' ' . ((int) $aRow2 ['MEMBERS_NUMBER'] == 1 ? G::LoadTranslation('ID_USER') : G::LoadTranslation('ID_USERS')) . ')</strong></font></a> <br /><div id="users' . $c . '" style="display: none"></div>', 'PMR_UID' => $sPmrUid, 'USR_UID' => $aRow ['GRP_UID'], 'PMRP_TYPE' => 2);
  $oDataset->next();
}
$sDelimiter = DBAdapter::getStringDelimiter ();
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(UsersPeer::USR_UID);
$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
$oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
$oCriteria->add(UsersPeer::USR_UID, $aUIDS2, Criteria::NOT_IN);
$oDataset = UsersPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
  $aUsers [] = array('LABEL' => $aRow ['USR_FIRSTNAME'] . ' ' . $aRow ['USR_LASTNAME'], 'PMR_UID' => $sPmrUid, 'USR_UID' => $aRow ['USR_UID'], 'PMRP_TYPE' => 1);
  $oDataset->next();
}
global $_DBArray;
$_DBArray = (isset($_SESSION ['_DBArray']) ? $_SESSION ['_DBArray'] : '');
$_DBArray ['availableUsers'] = $aUsers;
$_SESSION ['_DBArray'] = $_DBArray;
G::LoadClass('ArrayPeer');
$oCriteria = new Criteria('dbarray');
$oCriteria->setDBArrayTable('availableUsers');
$oCriteria->addDescendingOrderByColumn(PmReportPermissionsPeer::PMRP_TYPE);
$oCriteria->addAscendingOrderByColumn('LABEL');

$G_PUBLISH->AddContent('propeltable', 'paged-table', 'pmReports/rptAddPermissions', $oCriteria);

G::RenderPage( "publish", "raw" );
?>
<script language="javascript">
var ajax = WebResource("../pmReports/rptAjax");
var addPermissionToPmReport=function(sPmrUid, sUsrUid, iPmrpType){
	ajax.add_permission_to_pm_report(sPmrUid, sUsrUid, iPmrpType);
	//goToPMReportPermissionsPanel(sPmrUid);
	oPanelAddPermissions.remove();
};
</script>