<?php
require_once ( "classes/model/PmReportPeer.php" );
require_once ( "classes/model/PmReport.php" );
require_once ( 'classes/model/Content.php' );
require_once ( "classes/model/PmReportPermissionsPeer.php" );
require_once ( "classes/model/PmReportPermissions.php" );
require_once 'classes/model/Users.php';
require_once 'classes/model/Groupwf.php';
G::LoadClass('groups');
$G_PUBLISH = new Publisher;
$sPmrUid = $_POST['sPmrUid'];

$aUsers = array();
$aUsers [] = array('LABEL' => 'char', 'PMR_UID' => 'char', 'USR_UID' => 'char', 'PMRP_TYPE' => 'integer');
$sDelimiter = DBAdapter::getStringDelimiter ();
$oCriteria = new Criteria('workflow');
$oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');
$oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_UID);
$oCriteria->addSelectColumn(PmReportPermissionsPeer::USR_UID);
$oCriteria->addSelectColumn(PmReportPermissionsPeer::PMRP_TYPE);
//$oCriteria->addSelectColumn(PmReportPermissionsPeer::TU_RELATION);
$oCriteria->addAlias('C', 'CONTENT');
$aConditions = array();
$aConditions [] = array(PmReportPermissionsPeer::USR_UID, 'C.CON_ID');
$aConditions [] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter);
$aConditions [] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
$oCriteria->add(PmReportPermissionsPeer::PMR_UID, $sPmrUid);
$oCriteria->add(PmReportPermissionsPeer::PMRP_TYPE, 2);
//$oCriteria->add(PmReportPermissionsPeer::TU_RELATION, 2);
$oDataset = PmReportPermissionsPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$c = 0;
while ($aRow = $oDataset->getRow()) {
  $c++;
  $oGroup = new Groupwf ( );
  $aFields = $oGroup->load($aRow ['USR_UID']);
  if ($aFields ['GRP_STATUS'] == 'ACTIVE') {
    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');
    $oCriteria->add(GroupUserPeer::GRP_UID, $aRow ['USR_UID']);
    $oDataset2 = GroupUserPeer::doSelectRS($oCriteria);
    $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset2->next();
    $aRow2 = $oDataset2->getRow();
  } else {
    $aRow2 ['GROUP_INACTIVE'] = '<strong>(' . G::LoadTranslation('ID_GROUP_INACTIVE') . ')</strong>';
  }
  $aUsers [] = array('LABEL' => (!isset($aRow2 ['GROUP_INACTIVE']) ? $aRow ['GRP_TITLE'] . ' <a href="#" onclick="usersGroup(\'' . $aRow ['USR_UID'] . '\', \'' . $c . '\');return false;"><font color="green"><strong>(' . $aRow2 ['MEMBERS_NUMBER'] . ' ' . ((int) $aRow2 ['MEMBERS_NUMBER'] == 1 ? G::LoadTranslation('ID_USER') : G::LoadTranslation('ID_USERS')) . ')</strong></font></a> <br /><div id="users' . $c . '" style="display: none"></div>' : $aRow ['GRP_TITLE'] . ' ' . $aRow2 ['GROUP_INACTIVE']), 'PMR_UID' => $aRow ['PMR_UID'], 'USR_UID' => $aRow ['USR_UID'], 'PMRP_TYPE' => $aRow ['PMRP_TYPE'], 'OF_TO_ASSIGN' => G::LoadTranslation('ID_DE_ASSIGN'));
  $oDataset->next();
}
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
$oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_UID);
$oCriteria->addSelectColumn(PmReportPermissionsPeer::USR_UID);
$oCriteria->addSelectColumn(PmReportPermissionsPeer::PMRP_TYPE);
//$oCriteria->addSelectColumn(PmReportPermissionsPeer::TU_RELATION);
$oCriteria->addJoin(PmReportPermissionsPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
$oCriteria->add(PmReportPermissionsPeer::PMR_UID, $sPmrUid);
$oCriteria->add(PmReportPermissionsPeer::PMRP_TYPE, 1);
//$oCriteria->addAsColumn('OF_TO_ASSIGN',"'".G::LoadTranslation('ID_DE_ASSIGN')."'");
//$oCriteria->add(PmReportPermissionsPeer::TU_RELATION, 1);
$oDataset = PmReportPermissionsPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
  $aUsers [] = array('LABEL' => $aRow ['USR_FIRSTNAME'] . ' ' . $aRow ['USR_LASTNAME'], 'PMR_UID' => $aRow ['PMR_UID'], 'USR_UID' => $aRow ['USR_UID'], 'PMRP_TYPE' => $aRow ['PMRP_TYPE'], 'OF_TO_ASSIGN' => G::LoadTranslation('ID_DE_ASSIGN'));
  $oDataset->next();
}
global $_DBArray;
$_DBArray = (isset($_SESSION ['_DBArray']) ? $_SESSION ['_DBArray'] : '');
$_DBArray ['taskUsers'] = $aUsers;
$_SESSION ['_DBArray'] = $_DBArray;
G::LoadClass('ArrayPeer');
$oCriteria = new Criteria('dbarray');
$oCriteria->setDBArrayTable('taskUsers');
$oCriteria->addDescendingOrderByColumn(PmReportPermissionsPeer::PMRP_TYPE);
$oCriteria->addAscendingOrderByColumn('LABEL');

$aFields['PMR_UID'] = $sPmrUid;

$G_PUBLISH->AddContent('propeltable', 'paged-table', 'pmReports/rptPermissions', $oCriteria, $aFields);

G::RenderPage('publish', 'raw');

?>

<script language="javascript">
var ajax = WebResource("../pmReports/rptAjax");
var removePermissionToPmReport=function(sPmrUid, sUsrUid, iPmrpType){
	ajax.remove_permission_to_pm_report(sPmrUid, sUsrUid, iPmrpType);
	refreshPMReportPermissionsPanel(sPmrUid);
}
var usersGroup = function (GRP_UID, c) {
	var div = document.getElementById("users" + c);
	div.style.display = div.style.display == "none" ? "block" : "none";
	var oRPC = new (leimnud.module.rpc.xmlhttp)({url: "../users/users_Ajax", async: false, method: "POST", args: "function=usersGroup&GRP_UID=" + GRP_UID});
	oRPC.make();
	div.innerHTML = oRPC.xmlhttp.responseText;
}
</script>