<?php
//ini_set ( 'error_reporting', E_ALL );
//ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'pmFunctions' );

function validate_field($newInner)
{
    $newInner = str_replace('  ', ' ', $newInner);
    return $newInner;
}

$res = false;
$idConfigAS=0;

if ( isset( $_POST ['idProcess']) ) {
	
	$positionField = 0;
	$qSelect = "SELECT AC.CD_PROCESS_UID AS PROCESS, AC.CD_UID AS ID_CONFIG_AS FROM PMT_CONFIG_DEDOUBLONAGE AC 
	WHERE AC.CD_PROCESS_UID = '". $_POST['idProcess'] ."'";

	$qProduction = executeQuery($qSelect);
	
	$idConfigAS = isset($qProduction[1]['ID_CONFIG_AS'])?$qProduction[1]['ID_CONFIG_AS']:'';
	
	$delete = executeQuery ( "DELETE FROM PMT_COLUMN_DEDOUBLONAGE WHERE CD_UID_CONFIG_AS = '".$idConfigAS."' " );  
		
	//$deleteConfig = executeQuery ( "DELETE FROM PMT_AS400_CONFIG WHERE PROCESS_UID = '" . $_POST ['idProcess'] . "' " ); 
	
	if(!isset($positionField))
	{
		$qPos = "SELECT max(CD_ORDER_FIELD) AS POSITION FROM PMT_COLUMN_DEDOUBLONAGE
		WHERE CD_UID_CONFIG_AS  = '" . $idConfigAS . "' ";
		$position = executeQuery ( $qPos );
		$positionField = $position [1] ['POSITION'];
		$positionField = $positionField + 1;
	}			
	
	//insert / update AS400_CONFIG	$_POST ['joinConfig'] = isset($_POST ['joinConfig'])?$_POST ['joinConfig']:'';	$_POST ['tokenCsv'] = isset($_POST ['tokenCsv'])?$_POST ['tokenCsv']:'';	$_POST ['configWhere'] = isset($_POST ['configWhere'])?$_POST ['configWhere']:'';
	if($idConfigAS != 0)
	{		$updateConfig = "UPDATE  PMT_CONFIG_DEDOUBLONAGE  SET
				   CD_TABLENAME = '" . $_POST ['tableName'] . "',
				   CD_JOIN_CONFIG = '" . $_POST ['joinConfig'] . "',
				   CD_TOKEN_CSV = '" . mysql_real_escape_string($_POST ['tokenCsv']) . "',
				   CD_PROCESS_UID = '" . $_POST ['idProcess'] . "',
				   CD_CONFIG_WHERE = '" . mysql_real_escape_string($_POST ['configWhere']) . "'
				   WHERE CD_UID = '" . $idConfigAS . "' ";
		executeQuery($updateConfig);
		
	}	else	{
		
		$insertConfig = "INSERT INTO PMT_CONFIG_DEDOUBLONAGE (  
						CD_TABLENAME,
						CD_JOIN_CONFIG,
						CD_TOKEN_CSV,
						CD_PROCESS_UID,
						CD_CONFIG_WHERE)
					VALUES (
					'" . $_POST ['tableName'] . "',
					'" . $_POST ['joinConfig'] . "',
					'" . mysql_real_escape_string($_POST ['tokenCsv']) . "',
					'" . $_POST ['idProcess'] . "',
					'" . mysql_real_escape_string($_POST ['configWhere']) . "'
					)
				  ";
		executeQuery ( $insertConfig );
	}
	
		
}
$data = json_decode ( $_POST ['myArray'] );
//G::pr($data);die;
$positionField = 0;

 // idConfig

	$qSelect = executeQuery("SELECT AC.CD_PROCESS_UID AS PROCESS, AC.CD_UID AS ID_CONFIG_AS FROM PMT_CONFIG_DEDOUBLONAGE AC 
	WHERE AC.CD_PROCESS_UID = '". $_POST ['idProcess'] ."'");

	$idConfigAS = $qSelect[1]['ID_CONFIG_AS'];
	
	if(count($data)){
	foreach ( $data as $name => $value ) 	{	
		$idTable = $value->idTable;	
		$innerJoin = isset($value->innerJoin)?$value->innerJoin:'';	
		$idField = $value->idField;	
		$descripField = $value->descripField;	
		$filterField = isset($value->filterField)?$value->filterField:'';	
		$aliasTable = isset($value->aliasTable)?$value->aliasTable:'';	
		$ratioField = $value->ratio;	
		$fieldName = $value->fieldName;	
		$positionField = $positionField + 1;	
		if($innerJoin != '')	
			$innerJoin = validate_field($innerJoin);	
		$insert = "INSERT INTO PMT_COLUMN_DEDOUBLONAGE (	
						CD_FIELDNAME,	
						CD_ID_TABLE,	
						CD_INCLUDE_OPTION,	
						CD_RATIO,	
						CD_ORDER_FIELD,	
						CD_UID_CONFIG_AS,	
						CD_FIELD_DESCRIPTION	
					)	
					VALUES (	
					'" . $fieldName . "',	
					'" . $_POST ['tableName'] . "',	
					'1',	
					'" . $ratioField . "',	
					'" . $positionField ."',	
					'" . $idConfigAS . "',	
					'" . $descripField. "')	
				  ";	
		executeQuery ( $insert );	
		$res = true;	
	}}

$save = array ('success' => $res );
echo json_encode ( $save );

?>
