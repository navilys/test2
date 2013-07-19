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
	$qSelect = "SELECT AC.PROCESS_UID AS PROCESS, AC.ID AS ID_CONFIG_AS FROM PMT_AS400_CONFIG AC 
	WHERE AC.PROCESS_UID = '". $_POST['idProcess'] ."'";

	$qProduction = executeQuery($qSelect);
	
	$idConfigAS = isset($qProduction[1]['ID_CONFIG_AS'])?$qProduction[1]['ID_CONFIG_AS']:'';
	
	$delete = executeQuery ( "DELETE FROM PMT_COLUMN_AS400 WHERE ID_CONFIG_AS = '".$idConfigAS."' " );  
	
	
	//$deleteConfig = executeQuery ( "DELETE FROM PMT_AS400_CONFIG WHERE PROCESS_UID = '" . $_POST ['idProcess'] . "' " ); 
	
	if(!isset($positionField))
	{
		$qPos = "SELECT max(ORDER_FIELD) AS POSITION FROM PMT_COLUMN_AS400
		WHERE ID_CONFIG_AS  = '" . $idConfigAS . "' ";
		$position = executeQuery ( $qPos );
		$positionField = isset($position [1] ['POSITION'])?$position [1] ['POSITION']:'';
		$positionField = $positionField + 1;
	}			
	
	//insert / update AS400_CONFIG
	$_POST ['joinConfig'] = isset($_POST ['joinConfig'])?$_POST ['joinConfig']:'';	$_POST ['tokenCsv'] = isset($_POST ['tokenCsv'])?$_POST ['tokenCsv']:'';	$_POST ['configWhere'] = isset($_POST ['configWhere'])?$_POST ['configWhere']:'';
	if($idConfigAS != 0)
	{
		$updateConfig = "UPDATE  PMT_AS400_CONFIG  SET
				   TABLENAME = '" . $_POST ['tableName'] . "',
				   JOIN_CONFIG = '" . $_POST ['joinConfig'] . "',
				   TOKEN_CSV = '" . mysql_real_escape_string($_POST ['tokenCsv']) . "',
				   PROCESS_UID = '" . $_POST ['idProcess'] . "',
				   CONFIG_WHERE = '" . mysql_real_escape_string($_POST ['configWhere']) . "',
				   TASK_UID = '" .$_POST['idTask']. "'
				   WHERE ID = '" . $idConfigAS . "' ";
		executeQuery($updateConfig);
		
	}	else	{
		$insertConfig = "INSERT INTO PMT_AS400_CONFIG (  
						TABLENAME,
						JOIN_CONFIG,
						TOKEN_CSV,
						PROCESS_UID,
						CONFIG_WHERE,
						TASK_UID )
					VALUES (
					'" . $_POST ['tableName'] . "',
					'" . $_POST ['joinConfig'] . "',
					'" . mysql_real_escape_string($_POST ['tokenCsv']) . "',
					'" . $_POST ['idProcess'] . "',
					'" . mysql_real_escape_string($_POST ['configWhere']) . "',
					'" . $_POST ['idTask'] . "'
					)
				  ";
		executeQuery ( $insertConfig );
	}
	
		
}
$data = json_decode ( $_POST ['myArray'] );
//G::pr($data);die;
$positionField = 0;

 // idConfig

	$qSelect = executeQuery("SELECT AC.PROCESS_UID AS PROCESS, AC.ID AS ID_CONFIG_AS FROM PMT_AS400_CONFIG AC 
	WHERE AC.PROCESS_UID = '". $_POST ['idProcess'] ."'");

	$idConfigAS = $qSelect[1]['ID_CONFIG_AS'];
	
	
foreach ( $data as $name => $value ) 
{
	$idTable = $value->idTable;
	$innerJoin = isset($value->innerJoin)?$value->innerJoin:'';
	$idField = $value->idField;
	$descripField = $value->descripField;
	$filterField = isset($value->filterField)?$value->filterField:'';	$aliasTable = isset($value->aliasTable)?$value->aliasTable:'';		
	$lengthField = $value->length;
	$fieldName = $value->fieldName;
	$as400Type = $value->as400Type;
	$required = $value->required;
	$constant = $value->constant;
	
	$positionField = $positionField + 1;
		
	if($innerJoin != '')
		$innerJoin = validate_field($innerJoin);
					
	$insert = "INSERT INTO PMT_COLUMN_AS400 (
					FIELD_NAME,
					ID_TABLE,
					LENGTH,
					INCLUDE_OPTION,
					ORDER_FIELD,
					AS400_TYPE,
					REQUIRED,
					ID_CONFIG_AS,
					FIELD_DESCRIPTION,
					CONSTANT
				)
				VALUES (
				'" . $fieldName . "',
				'" . $idTable . "',
				'" . $lengthField . "',
				'1',
				'" . $positionField ."',
				'" . $as400Type . "',
				'" . $required . "',
				'" . $idConfigAS . "',
				'" . $descripField. "',
				'" . $constant . "')
			  ";
	executeQuery ( $insert );
	$res = true;
}
$save = array ('success' => $res );
echo json_encode ( $save );

?>
