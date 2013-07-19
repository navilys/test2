<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', True); 
G::LoadClass ( 'case' );
G::LoadClass ( 'pmFunctions' );
$form=$_POST;
$res=false;

function removeSelectQueryInbox($id){
	$ret = array();
	
	$query = "SELECT CFG_USR_FIELD_NAME, CFG_USR_TYPE FROM PMT_CONFIG_USERS WHERE CFG_USR_ID = " . $id;
	$data = executeQuery($query);
	$type = isset($data[1]['CFG_USR_TYPE'])?$data[1]['CFG_USR_TYPE']:'' ;
	$fieldName = isset($data[1]['CFG_USR_FIELD_NAME'])?$data[1]['CFG_USR_FIELD_NAME']:'' ;
	if($type == 'DROPDOWN')
	{
		$fieldName = 'id_'.$fieldName ;
	}
	else
	{
		$fieldName = 'name_'.$fieldName;
	}
	
	$sql = "DELETE FROM PMT_CONFIG_LIST_USERS WHERE CFG_LIST_USR_FIELD_NAME = '".$fieldName."' " ;
	executeQuery($sql);
	
	$sql = "DELETE FROM PMT_CONFIG_USERS WHERE CFG_USR_ID = " . $id;
	executeQuery($sql);
	
	header("Content-Type: text/html");
	$returnStatus = array('success' => true,'Msg'=>'Succesfully');

	echo G::json_encode($returnStatus);

}

function editSelectQueryInbox($dataParameters,$dataFields,$dataDescription, $dataType, $dataTypeAction, $idConfigUsers, $dataStatus)
{
	if ($dataParameters!='' && $dataTypeAction == 'QUERY')
	{
		$dataParameters= str_replace('from' , 'FROM' , $dataParameters);
		$queryParameters = explode('FROM',$dataParameters);
		$selectParameter = explode(',',$queryParameters[0]);
		$i = 0;
		$dataSelect = '';
		foreach($selectParameter as $row)
		{	
			$i++;
			$select = explode(' ', trim($row));
			if(sizeof($select) > 2)
			{
				if($i == 1)
					$dataSelect = $select[0].' '.$select[1].' AS ID,';
				else 
					$dataSelect = $dataSelect.' '.$select[0].' AS NAME';
			}
			else 
			{
				if($i == 1)
					$dataSelect = $row.' AS ID,';
				else 
					$dataSelect = $dataSelect.$row.' AS NAME';
			}
		
		}
		if($dataSelect !='')
			$dataParameters = $dataSelect.' FROM '.$queryParameters[1];
	}
	
	$updateSelect="UPDATE PMT_CONFIG_USERS SET
			CFG_USR_FIELD_NAME  = '$dataFields',
			CFG_USR_DESCRIPTION  = '".mysql_real_escape_string($dataDescription)."',
			CFG_USR_TYPE  = '$dataType',
			CFG_USR_TYPE_ACTION = '$dataTypeAction',
			CFG_USR_PARAMETERS  = '".mysql_real_escape_string($dataParameters)."',
			CFG_USR_STATUS = '$dataStatus'
			WHERE CFG_USR_ID = '$idConfigUsers' 
			";

	executeQuery($updateSelect);
	
	$update = "UPDATE PMT_CONFIG_USERS_OPTIONS SET
				   CFG_USR_ID =  '" . $idConfigUsers ."' 
				   WHERE CFG_USR_FIELD_NAME = '" . $dataFields . "' ";
	executeQuery ( $update );	
		
	$res=true;	    
		    
	header("Content-Type: text/html");
	$returnStatus = array('success' => $res);

	echo G::json_encode($returnStatus);	
}


function addConfigUsers($dataParameters,$dataFields, $dataDescription,  $dataType,  $dataTypeAction, $dataStatus)
{
	$queryId = "SELECT max(CFG_USR_ID) AS MAX_ID FROM  PMT_CONFIG_USERS  ";
	$maxId = executeQuery ( $queryId );
	$sgtIdIn = $maxId[1]['MAX_ID'] + 1;
	$queryPos = "SELECT max(CFG_USR_POSITION) AS POSITION FROM PMT_CONFIG_USERS ";
	$execPosition = executeQuery ( $queryPos );
	$position = $execPosition [1] ['POSITION'];
	$position = $position + 1;
	
	if ($dataParameters!='' && $dataTypeAction == 'QUERY')
	{
		
		$dataParameters= str_replace('from' , 'FROM' , $dataParameters);
		$queryParameters = explode('FROM',$dataParameters);
		$selectParameter = explode(',',$queryParameters[0]);
		$i = 0;
		$dataSelect = '';
		foreach($selectParameter as $row)
		{	
			$i++;
			$select = explode(' ', trim($row));
			if(sizeof($select) > 2)
			{
				if($i == 1)
					$dataSelect = $select[0].' '.$select[1].' AS ID,';
				else 
					$dataSelect = $dataSelect.' '.$select[0].' AS NAME';
			}
			else 
			{
				if($i == 1)
					$dataSelect = $row.' AS ID,';
				else 
					$dataSelect = $dataSelect.$row.' AS NAME';
			}
		
		}
		$dataParameters = $dataSelect.' FROM '.$queryParameters[1];
	}
	if($dataFields != '')
	{
		$insert = "INSERT INTO PMT_CONFIG_USERS (  
					CFG_USR_ID,
					CFG_USR_FIELD_NAME,
					CFG_USR_DESCRIPTION,
					CFG_USR_TYPE,
					CFG_USR_TYPE_ACTION,
					CFG_USR_PARAMETERS, 
					CFG_USR_POSITION,
					CFG_USR_STATUS
				)
				VALUES (
				'" . $sgtIdIn ."',
				'" . $dataFields . "',
				'" . mysql_real_escape_string($dataDescription) . "',
				'" . $dataType . "',
				'" . $dataTypeAction . "',
				'" . mysql_real_escape_string($dataParameters) . "',
				'" . $position . "',
				'" . $dataStatus . "'
				)
    	
			";
		executeQuery ( $insert );
		
		$update = "UPDATE PMT_CONFIG_USERS_OPTIONS SET
				   CFG_USR_ID =  '" . $sgtIdIn ."' 
				   WHERE CFG_USR_FIELD_NAME = '" . $dataFields . "' ";
		executeQuery ( $update );		
	}
	
	
	$res = true;
	$save = array ('success' => $res );
	echo json_encode ( $save );
}

function addConfigOptions($dataParameters,$fieldName)
{
	foreach ( $dataParameters as $name => $value ) 
		{
			$idInbox = $value->idOption;
			$description = $value->description;
			
			$query = "SELECT max(CFG_USR_OPT_ID) AS CFG_USR_OPT_ID FROM PMT_CONFIG_USERS_OPTIONS ";
			$execQuery = executeQuery ( $query );
			$idConfig = $execQuery [1] ['CFG_USR_OPT_ID'];
			$idConfig = $idConfig + 1;

			$queryItemFile="INSERT INTO PMT_CONFIG_USERS_OPTIONS (CFG_USR_OPT_ID,CFG_USR_OPT_ID_OPTION,CFG_USR_OPT_DESCRIPTION,CFG_USR_FIELD_NAME)
			                                              VALUES ('$idConfig'   ,	'$idInbox'        ,	'$description'        ,	'$fieldName'	)";
		    executeQuery($queryItemFile);
		    		
			$res = true;
		
		}
		$save = array ('success' => $res );
		echo json_encode ( $save );
}

function listConfigOptions($fieldName)
{
	$start = isset($_POST['start']) ? $_POST['start'] : 0;
    $limit = isset($_POST['limit']) ? $_POST['limit'] : 20;
	$array = Array();
	$query = "SELECT CFG_USR_OPT_ID_OPTION AS ID_OPTION, CFG_USR_OPT_DESCRIPTION AS DESCRIPTION
			  FROM PMT_CONFIG_USERS_OPTIONS WHERE CFG_USR_FIELD_NAME = '$fieldName' ";
	$data = executeQuery($query);
	
	$total = count($data); 

	foreach($data as $valor)
	{       	
		$array[] = $valor;
	}
	header("Content-Type: text/plain");


	$paging = array(
	    'success'=> true,
	    'total'=> $total,
	    'data'=> array_splice($array,$start,$limit)
	);


	echo json_encode($paging);    
}

function addDragDropConfigUsers($data)
{
	$queryId = "SELECT max(CFG_USR_ID) AS MAX_ID FROM  PMT_CONFIG_USERS  ";
	$maxId = executeQuery ( $queryId );
	$sgtIdIn = $maxId[1]['MAX_ID'] + 1;
	$queryPos = "SELECT max(CFG_USR_POSITION) AS POSITION FROM PMT_CONFIG_USERS ";
	$execPosition = executeQuery ( $queryPos );
	$position = $execPosition [1] ['POSITION'];
	$position = $position + 1;
	foreach ( $data as $name => $value ) 
	{
		$fieldName = $value->fieldName;
		$description = $value->description;
		$type = $value->type;
		$typeAction = $value->typeAction;
		$parameterField = $value->parameterField;
			
		$insert = "INSERT INTO PMT_CONFIG_USERS (  
					CFG_USR_ID,
					CFG_USR_FIELD_NAME,
					CFG_USR_DESCRIPTION,
					CFG_USR_TYPE,
					CFG_USR_TYPE_ACTION,
					CFG_USR_PARAMETERS, 
					CFG_USR_POSITION
				)
				VALUES (
				'" . $sgtIdIn ."',
				'" . $fieldName . "',
				'" . $description . "',
				'" . $type . "',
				'" . $typeAction . "',
				'" . $parameterField . "',
				'" . $position . "'
				)
    	
			";
		executeQuery ( $insert );
		$sgtIdIn++;
		$position++;
		
	}
		   		
	$res = true;
	$save = array ('success' => $res );
	echo json_encode ( $save );
}

function addDragDropConfigListUsers($data)
{
	$queryId = "SELECT max(CFG_LIST_USR_ID) AS MAX_ID FROM PMT_CONFIG_LIST_USERS  ";
	$maxId = executeQuery ( $queryId );
	$sgtIdIn = $maxId[1]['MAX_ID'] + 1;
	$queryPos = "SELECT max(CFG_LIST_USR_POSITION) AS POSITION FROM PMT_CONFIG_LIST_USERS ";
	$execPosition = executeQuery ( $queryPos );
	$position = $execPosition [1] ['POSITION'];
	$position = $position + 1;
	foreach ( $data as $name => $value ) 
	{
		$fieldName = $value->fieldName;
		$description = $value->description;
		$hiddenField = $value->hiddenField;
		$table = $value->table;
			
		$insert = "INSERT INTO PMT_CONFIG_LIST_USERS (  
					CFG_LIST_USR_ID,
					CFG_LIST_USR_FIELD_NAME,
					CFG_LIST_USR_DESCRIPTION,
					CFG_LIST_USR_INCLUDE,
					CFG_LIST_USR_HIDDEN,
					CFG_LIST_USR_POSITION, 
					CFG_LIST_USR_TABLE
				)
				VALUES (
				'" . $sgtIdIn ."',
				'" . $fieldName . "',
				'" . $description . "',
				'1',
				'" . $hiddenField . "',
				'" . $position . "',
				'" . $table . "'
				)
    	
			";
		executeQuery ( $insert );
		$sgtIdIn++;
		$position++;
		
	}
		   		
	$res = true;
	$save = array ('success' => $res );
	echo json_encode ( $save );
}

header('Content-type:text/javascript;charset=UTF-8');
$method = $_GET["method"];
switch ($method) {
	case "remove":
		$ret = removeSelectQueryInbox($_POST["ID"]);
	break;
	
	case "add":
		
		$delQuery = "DELETE FROM PMT_CONFIG_USERS WHERE CFG_USR_FIELD_NAME = '" . $_POST ['fieldName'] . "' AND CFG_USR_DESCRIPTION = '" . mysql_real_escape_string($_POST ['description']) ."' ";
		$delete = executeQuery ($delQuery);
		$dataParameters = $_POST ['parameterField'] ;
		//$dataParameters = str_replace("'", '"', $dataParameters);
		$dataFields =  $_POST ['fieldName'];
		$dataDescription =  $_POST ['description'];
		$dataType =  $_POST ['type'];
		$dataTypeAction =  $_POST ['typeAction'];
		$dataStatus =  $_POST ['status'];
		$ret = addConfigUsers($dataParameters,$dataFields, $dataDescription,  $dataType,  $dataTypeAction, $dataStatus);
		break;
		
	case "edit":
		$dataParameters = $_POST ['parameterField'] ;
		//$dataParameters = str_replace("'", '"', $dataParameters);
		$dataFields =  $_POST ['fieldName'];
		$dataDescription =  $_POST ['description'];
		$dataType =  $_POST ['type'];
		$dataTypeAction =  $_POST ['typeAction'];
		$idConfigUsers =  $_POST ['idConfigUsers'];
		$dataStatus =  $_POST ['status'];
		$ret = editSelectQueryInbox($dataParameters,$dataFields,$dataDescription, $dataType, $dataTypeAction,$idConfigUsers,$dataStatus);
	
		break; 
	
	case "options":				
		$dataParameters = json_decode( $_POST ['myArray']) ;
		$fieldName = $_POST ['fieldName'];
		$query = "DELETE FROM PMT_CONFIG_USERS_OPTIONS WHERE CFG_USR_FIELD_NAME = '$fieldName' ";
		$execQuery = executeQuery ( $query );
		$ret = addConfigOptions($dataParameters,$fieldName);
	
		break;
		
	case "listOptions":				
		
		$fieldName = $_GET['fieldName'];
		$ret = listConfigOptions($fieldName);
	
		break;
		
	case "dragdrop":
		
		$delQuery = "DELETE FROM PMT_CONFIG_USERS ";
		$delete = executeQuery ($delQuery);
	
		$data = json_decode ( $_POST ['arrayConfigUsers'] );
		
		$ret = addDragDropConfigUsers($data);
		break;
	
	case "dragdropListUsers":
		
		$delQuery = "DELETE FROM PMT_CONFIG_LIST_USERS ";
		$delete = executeQuery ($delQuery);
	
		$data = json_decode ( $_POST ['arrayConfigListUsers'] );
		
		$ret = addDragDropConfigListUsers($data);
		break;
	
}
?>
