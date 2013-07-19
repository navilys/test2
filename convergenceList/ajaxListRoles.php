<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', True);
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

/*$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 20;
$OrderBy = "ORDER BY NOM_BEN DESC";
if (isset ( $_POST ['sort'] )) 
{
	if ($_POST ['sort'] != '') 
	{
		$typeOrder = "";
		if (isset ( $_POST ['dir'] ) && $_POST ['dir'] != '') {
			$typeOrder = $_POST ['dir'];
		}
		if ($_POST ['sort'] == "UNDERWRITER") {
			$_POST ['sort'] = "A.ASSIGNED_UNDERWRITER";
		}
		if ($_POST ['sort'] == "SUBMISSION_DATE") {
			$_POST ['sort'] = "APP.APP_INIT_DATE";
		}
		$OrderBy = " ORDER BY " . $_POST ['sort'] . "  " . $typeOrder . " ";
	}
}
$array = Array();
$query = "SELECT * FROM PMT_FIELDS_INBOX WHERE ROL_UID = '".$_GET['rolID']."'";
$newOptions = executeQuery($query);
$filter = (isset($_REQUEST['textFilter']))? $_REQUEST['textFilter'] : '';
$ROL_UID = $_GET['rolID'];

$paging = array ('success' => true, 'total' => $total, 'data' => $array );

echo json_encode ( $paging );*/
$select = "SELECT * FROM PMT_INBOX_FIELDS_SELECT ORDER BY ID";
$dataSelect = executeQuery($select);
$i = 1;
foreach($dataSelect as $row)
{
	$updateInbox = "UPDATE PMT_INBOX_FIELDS_SELECT SET
				ID = '$i'
				WHERE ID = '".$row['ID']."' ";
	executeQuery($updateInbox);
	$i++;
}
?>
    