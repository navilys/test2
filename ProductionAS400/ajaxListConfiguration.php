<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 500;
$OrderBy = "ORDER BY FLD_DESCRIPTION ASC";
if (isset ( $_POST ['sort'] )) 
{
	if ($_POST ['sort'] != '') {
		$typeOrder = "";
		if (isset ( $_POST ['dir'] ) && $_POST ['dir'] != '') {
			$typeOrder = $_POST ['dir'];
		}
		$OrderBy = " ORDER BY " . $_POST ['sort'] . "  " . $typeOrder . " ";
	}
}
$array = Array ();
$innerJoin = '';
$j = 0;

if (isset ( $_POST ['idProcess'] ) && $_POST ['idProcess'] != '') 
{
  	 $query = " SELECT
    		    ADD_TAB_NAME AS ID,
    		    ADD_TAB_NAME AS NAME,
    		    ID AS ID_CONFIG,
    		    TASK_UID AS TASK_UID
    		    FROM PMT_AS400_CONFIG
				INNER JOIN ADDITIONAL_TABLES ON PMT_AS400_CONFIG.TABLENAME = ADDITIONAL_TABLES.ADD_TAB_NAME
				WHERE PMT_AS400_CONFIG.PROCESS_UID = '".$_POST['idProcess']."' 
				--  AND ALIAS_TABLE = ID_TABLE 
				GROUP BY ADD_TAB_UID";
 
    $fields = executeQuery($query);
      
    foreach($fields as $index)
    {
		$query = "SELECT * FROM PMT_AS400_CONFIG WHERE PROCESS_UID = '".$_POST['idProcess']."' AND TABLENAME = '".$index['ID']."'   ";
		$newOptions = executeQuery ( $query );
		 
    	$qTask = executeQuery("SELECT CON_ID as ID_TASK, CON_VALUE AS NAME_TASK
   					FROM CONTENT WHERE CON_ID = '". $newOptions [1]['TASK_UID'] ."' AND CON_CATEGORY = 'TAS_TITLE'       
   					GROUP BY CON_ID ");
    
		$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
		$whereConfig = isset( $newOptions [1]['CONFIG_WHERE'] ) ? $newOptions [1]['CONFIG_WHERE'] : '';
		$tokenCsv = isset( $newOptions [1]['TOKEN_CSV'] ) ? $newOptions [1]['TOKEN_CSV'] : '';
		$taskUid = isset( $newOptions [1]['TASK_UID']) ? $newOptions [1]['TASK_UID']: '';
		$taskName = isset($qTask[1]['NAME_TASK']) ? $qTask[1]['NAME_TASK'] : 'Select a Task';
		$index['INNER_JOIN'] = $innerJoin; 
		$index['CONFIG_WHERE'] = $whereConfig;
		$index['TOKEN_CSV'] = $tokenCsv;
		$index['TASK_UID'] = $taskUid;
		$index['TASK_NAME'] = $taskName;
		$index['SW'] = 1;
		$array[] = $index;
    	
	}
	if(sizeof($fields) == 0)
	{
		$query = " SELECT
    		    ADD_TAB_NAME AS ID,
    		    ADD_TAB_NAME AS NAME
    		    FROM ADDITIONAL_TABLES 
				WHERE PRO_UID = '".$_POST['idProcess']."' 
				GROUP BY ADD_TAB_UID";
    	$fields = executeQuery($query);
		foreach($fields as $index)
    	{
    		$index['SW'] = 0;
    		$query = "SELECT JOIN_CONFIG FROM PMT_AS400_CONFIG WHERE JOIN_CONFIG != '' 
			AND PROCESS_UID = '".$_POST['idProcess']."' AND TABLENAME = '".$index['ID']."'";
		
	  		$newOptions = executeQuery ( $query );
			$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
			$index['INNER_JOIN'] = $innerJoin;
			
			$array[] = $index;
		}
	}
    $total = count ( $fields );
 
}
$total = 0;

if (isset ( $_REQUEST ['idTable'] ) && $_REQUEST ['idTable'] != '') 
{
	
	if( (isset($_REQUEST ['inner']) && $_REQUEST ['inner'] != '') || (isset($_REQUEST ['swinner']) && $_REQUEST ['swinner'] == 1) )
		$innerJoin = $_REQUEST ['inner'];
	else 
	{
		$query = "SELECT JOIN_CONFIG FROM PMT_AS400_CONFIG 
				  WHERE JOIN_CONFIG != '' 
				  AND PROCESS_UID = '".$_POST['idProcess']."' 
				  AND TABLENAME = '".$_POST['idTable']."' ";

		$newOptions = executeQuery ( $query );
		$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
	}
	$innerJoin= str_replace(' AS ' , ' ' , $innerJoin);
	$innerJoin= str_replace(' as ' , ' ' , $innerJoin);
	$innerJoin= str_replace(' inner ' , ' left ' , $innerJoin);
	$innerJoin= str_replace(' INNER ' , ' LEFT ' , $innerJoin);
	$innerJoin= str_replace(' join ' , ' JOIN ' , $innerJoin);
	$table =  $_REQUEST ['idTable'];
	
	$sQueryT = " SELECT * 
  						 FROM ".$table." 
  						 " . $innerJoin . " 
    		   	";
	try{
    	$selectT = executeQuery($sQueryT);
	}
	catch (Exception $e)
	{
		$error = $e->getMessage();
		$error = preg_replace("[\n|\r|\n\r]", ' ', $error);
		$paging = array ('success' => false, 'response' => $error);
		echo json_encode ( $paging );
		die;
	}
	$newArray = Array ();
	$newArrayInner = Array ();
	## Get Names of Tables of Query
    $queryExplain = "EXPLAIN $sQueryT";
    $infoTables = executeQuery ( $queryExplain );
    
    $aTables = array();
    foreach ( $infoTables as $key => $data ) 
    {
        $aTables[] = trim($data['table']);
    }
    //G::pr($aTables);
	$tableNames=array();
    $tableNames[]=  array('OLD_NAME' => $table , 'ORIG_NAME' => $table );
    $tableOldLast = $table;
    for ($i=0;$i< count($aTables); $i++) 
    {
    	$partQuery1= explode(' '.$aTables[$i].' ',$sQueryT);
    	//G::pr($partQuery1);
        $partQuery2 = explode(' ',$partQuery1[0]);
        //G::pr($partQuery2);
        $origTableName = trim($partQuery2[count($partQuery2)-1]);
        if($aTables[$i] != $table)
        	$tableNames[]=  array('OLD_NAME' => $aTables[$i] , 'ORIG_NAME' => $origTableName);
      
    }
    $arrayTotalFields = Array();
	
	foreach ( $selectT as $valorInner )
	{
    	$newArray =  array_keys($valorInner);
		break;
	}
	
	$totalInner = 0;
	$iColor = 1;
	$swColor = 0;
	//G::pr($newArray);
	foreach($newArray as $index => $value)
	{ 
		$swField = 0;
		foreach ($tableNames as $row) 
		{
			
      		if($row['ORIG_NAME'] != 'JOIN' && $row['ORIG_NAME'] != 'FROM')
      			$tableShow = $row['ORIG_NAME'];
      		else
      			$tableShow = $row['OLD_NAME'];
      			
			$queryVerification = "SHOW COLUMNS FROM ".$tableShow." ";
      		
			$result = executeQuery ($queryVerification);
			
      		$rowAux = $row;
			$rowAux++; 
      		foreach($result as $field)
      		{
      			if($field['Field'] == $value && $swField == 0)
      			{
      				/*$arrayAux = Array(
								"FLD_UID" => $value,
   	 							"ADD_TAB_UID" => $row['ORIG_NAME'],
      							"ALIAS_TABLE" => $row['OLD_NAME']
								);*/
					
					$arrayAux = Array(
								"FLD_UID" => $value,
   	 							"ADD_TAB_UID" => $tableShow,
      							"ALIAS_TABLE" => $row['OLD_NAME'],
								"FIELD_DESCRIPTION" => mysql_escape_string($value)
								);
								
					$newArrayInner[] = $arrayAux;
					$swField = 1;
					break;
      			}
      		}
      		
      		
      	}
 	}
 	//G::pr($newArrayInner);
 	$queryColor = "SELECT COLOR_CODE FROM PMT_INBOX_FIELDS_COLOR WHERE COLOR_UID = ". $iColor ." ";
	$color = executeQuery($queryColor);	
	$valor = Array();
	$swPos = 0;
	$iTotal = 1;
	$swColorCon = 0;
	$queryTot = "SELECT CA.FIELD_NAME AS FLD_UID
				 FROM PMT_COLUMN_AS400 CA, PMT_AS400_CONFIG AC 
				  WHERE 
				  CA.ID_CONFIG_AS = AC.ID 
				  AND AC.PROCESS_UID = '".$_REQUEST['idProcess']."' 
				  ";

	$execTot= executeQuery ( $queryTot );
	$totalFields = sizeof($execTot);
	$posField = $totalFields;
	$total = sizeof($newArrayInner);
	$array = '';
	
	$queryConfig = "SELECT ID , TABLENAME FROM PMT_AS400_CONFIG 
				  WHERE PROCESS_UID = '".$_POST['idProcess']."' ";
	
	$qidConfig = executeQuery($queryConfig);
	$idConfigAS = isset($qidConfig[1]['ID'])?$qidConfig[1]['ID']:'';
	$qidConfig[1]['TABLENAME'] = isset($qidConfig[1]['TABLENAME'])?$qidConfig[1]['TABLENAME']:'';
	if($table == $qidConfig[1]['TABLENAME'])
	{
		$qRows = executeQuery ("SELECT CA.FIELD_NAME AS FLD_UID, 
				CA.FIELD_DESCRIPTION AS FIELD_DESCRIPTION FROM PMT_COLUMN_AS400 CA  
				 WHERE CA.ID_CONFIG_AS = '". $idConfigAS."' ORDER BY CA.ORDER_FIELD");
		
		$compareArray = $newArrayInner;
		$newArrayInner = Array();
		$swRow = 0;
		
		foreach ($compareArray As $row)
		{	
			$swRow = 0;
			$compare = $qRows;
			foreach($compare as $rowAux)
			{
				if($rowAux['FLD_UID'] == $row['FLD_UID'])				
				{	
					$row['FIELD_DESCRIPTION'] = $rowAux['FIELD_DESCRIPTION'];
					$newArrayInner[] = $row;
					$swRow = 1;
				}
			}	
			if($swRow != 1)
			{
				$row['FIELD_DESCRIPTION'] = $row['FLD_UID'];
				$newArrayInner[] = $row;
			}
		}
	}
	
	foreach($newArrayInner as $index )
	{
		$query = "SELECT CA.* ,
				  CN.PROCESS_UID AS PROCESS,
				  CN.TASK_UID AS TASK_UID
				  FROM PMT_COLUMN_AS400 CA, PMT_AS400_CONFIG CN
				  WHERE 
				  CA.ID_CONFIG_AS = CN.ID 
				  AND FIELD_NAME = '" . $index['FLD_UID'] . "' 
				  AND ID_TABLE = '".$index['ADD_TAB_UID']."' 
				  AND ID_CONFIG_AS = '". $idConfigAS."'
				  AND TABLENAME = '".$_POST['idTable']."' 
				  AND FIELD_DESCRIPTION = '" . mysql_escape_string($index['FIELD_DESCRIPTION']). "'";
		
		//G::pr($query);
		$newOptions = executeQuery ( $query );
		
		if (sizeof ( $newOptions )) 
		{
			$valor ['INCLUDE_OPTION'] = true;
			
			if (isset ( $newOptions [1] ['FIELD_NAME'] ) && $newOptions [1] ['FIELD_NAME'] != '')
				$valor['FIELD_NAME'] = $newOptions [1] ['FIELD_NAME'];
				
			if (isset ( $newOptions [1] ['FIELD_DESCRIPTION'] ) && $newOptions [1] ['FIELD_DESCRIPTION'] != '')
				$valor ['FLD_DESCRIPTION'] = $newOptions [1] ['FIELD_DESCRIPTION'];
				
			if (isset ( $newOptions [1] ['JOIN_CONFIG'] ) && $newOptions [1] ['JOIN_CONFIG'] != '')
				$valor ['INNER_JOIN'] = $newOptions [1] ['JOIN_CONFIG'];
				
			$valor ['TASK_NAME'] = 'Select a Task ';	
			
			if (isset ( $newOptions [1] ['TASK_UID'] ) && $newOptions [1] ['TASK_UID'] != '')
			{
				$valor ['TASK_UID'] = $newOptions [1] ['TASK_UID'];
				$qTask = executeQuery("SELECT CON_ID as ID_TASK, CON_VALUE AS NAME_TASK
   					FROM CONTENT WHERE CON_ID = '". $newOptions [1]['TASK_UID'] ."' AND CON_CATEGORY = 'TAS_TITLE'       
   					GROUP BY CON_ID ");
				$taskName = $qTask[1]['NAME_TASK'];
				$valor ['TASK_NAME'] = $taskName;
			}
				
			if (isset ( $newOptions [1] ['ORDER_FIELD'] ) && $newOptions [1] ['ORDER_FIELD'] != '')	
				$valor ['POSITION'] = $newOptions [1] ['ORDER_FIELD'];	
				
			if(isset ( $newOptions [1] ['PROCESS_UID'] ) && $newOptions [1] ['PROCESS_UID'] != '')
				$valor['ID_PROCESS'] = $newOptions[1]['PROCESS_UID'];	
				
			if(isset ( $newOptions [1] ['ID_TABLE'] ) && $newOptions [1] ['ID_TABLE'] != '')
				$valor['ID_TABLE'] = $newOptions[1]['ID_TABLE'];
				
			if(isset( $newOptions [1] ['LENGTH'] ) && $newOptions [1] ['LENGTH'] != '')
				$valor['LENGTH_FIELD'] = $newOptions[1]['LENGTH'];
			else 
				$valor['LENGTH_FIELD'] = 0;
				
			if(isset ( $newOptions [1] ['AS400_TYPE']) && $newOptions [1] ['AS400_TYPE'] != '')
				$valor['AS400_TYPE'] = $newOptions [1] ['AS400_TYPE'];
			else
				$valor['AS400_TYPE'] = 'String';
			
			if(isset ( $newOptions [1] ['ORDER_FIELD'] ) && $newOptions [1] ['ORDER_FIELD'] != '')
				$valor['ORDER_FIELD'] = $newOptions[1]['ORDER_FIELD'];
				
			if(isset ( $newOptions [1] ['CONFIG_WHERE'] ) && $newOptions [1] ['CONFIG_WHERE'] != '')					
				$valor ['CONFIG_WHERE'] = $newOptions [1] ['CONFIG_WHERE'];
				
			if($newOptions [1] ['REQUIRED'] == 'yes')
				$valor['REQUIRED'] = true;
			else
				$valor['REQUIRED'] = false;
			
			if(isset ($newOptions [1] ['CONSTANT']) && $newOptions [1] ['CONSTANT'] != '')
				$valor ['CONSTANT'] = $newOptions [1] ['CONSTANT'];
			
			$valor['ADD_TAB_NAME'] = $index['ADD_TAB_UID'];
			$valor['ALIAS_TABLE'] = $index['ALIAS_TABLE'];
			//$valor['ID_TABLE'] = $index['ALIAS_TABLE'];
			$swPos = 1;		
		} 
		else 
		{
			$posField++;
			$valor ['INCLUDE_OPTION'] = false;
			$valor ['FIELD_NAME'] = $index['FLD_UID'];
			$valor ['FLD_DESCRIPTION'] = $index['FIELD_DESCRIPTION'];
			//$valor ['INNER_JOIN'] = $index['JOIN_CONFIG'];
			$valor ['POSITION'] = $posField;
			//$valor ['ID_PROCESS'] = $index['PROCESS_UID'];
			$valor ['ID_TABLE'] = $index['ADD_TAB_UID'];
			//$valor['ID_TABLE'] = $index['ALIAS_TABLE'];
			$valor ['AS400_TYPE'] = 'String';
			$valor ['LENGTH_FIELD'] = 0;
			$valor ['ORDER_FIELD'] = 0;
			$valor ['TASK_UID'] = '';
			$valor ['TASK_NAME'] = 'Select a Task ';
			//$valor['LENGHT'] = $index['LENGHT'];
			//$valor['AS400_TYPE'] = $index['AS400_TYPE'];
			//$valor['REQUIRED'] = $index['REQUIRED'];
			$valor ['REQUIRED'] = false;
			$valor ['CONSTANT'] = 0;
			$valor ['ADD_TAB_NAME'] = $index['ADD_TAB_UID'];
			$valor ['ALIAS_TABLE'] = $index['ALIAS_TABLE'];
		}
		$valor ['FIELD_NAME'] = $index['FLD_UID'];
		$valor ['FLD_UID'] = $index['FLD_UID'];
		$valor ['COLOR'] = isset ( $color[1]['COLOR_CODE'] ) ? $color[1]['COLOR_CODE'] : '';
		
		if($iTotal != 1)
			$indexAux = next($newArrayInner);
		else
			$indexAux = current($newArrayInner);
		
		//G::pr($index['FLD_UID'].' '.$indexAux['FLD_UID'].' '. ($iTotal).'  '. $total.'  '.$indexAux['ADD_TAB_UID'].'  '.$index['ADD_TAB_UID'].' next ');
		if($indexAux['ADD_TAB_UID'] != $index['ADD_TAB_UID']  && $iTotal  < $total)
		{
			$iColor++;
			$queryColor = "SELECT COLOR_CODE FROM PMT_INBOX_FIELDS_COLOR WHERE COLOR_UID = ". $iColor ." ";
			$color = executeQuery($queryColor);
			$swColor++;
			$swColorCon = 0;
			
		}
		if($swColor == 1);
			$swColorCon ++;
		if($swColorCon == 1)
			$swColor = 0 ;
		$array [] = $valor;
		$valor ['INCLUDE_OPTION'] = '';
		$valor ['FIELD_NAME'] = '';
		$valor ['FLD_DESCRIPTION'] = '';
		$valor ['INNER_JOIN'] = '';
		$valor ['POSITION'] = '';
		$valor ['ID_PROCESS'] = '';
		$valor ['ID_TABLE'] = '';
		$valor ['LENGTH'] = '';
		$valor ['LENGTH_FIELD'] = 0;
		$valor ['AS400_TYPE'] = 'String';
		$valor ['REQUIRED'] = '';
		$valor ['ORDER_FIELD'] = '';
		$valor ['ADD_TAB_NAME'] = '';
		$valor ['ALIAS_TABLE'] = '';
		$valor ['TASK_UID'] = '';
		$valor ['TASK_NAME'] = '';
		$valor ['CONSTANT'] = 0;
		$valor++;
		$iTotal++;
		
	}

		
	$field = 'POSITION';
	
	$array = orderMultiDimensionalArray($array, $field, '');
	
}
//G::pr($array);
$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ), 'response' => 'OK' );
echo json_encode ( $paging );


function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) 
{  
    $position = array();  
    $newRow = array();  
    //G::pr($toOrderArray);
    foreach ($toOrderArray as $key => $row) {  
            $position[$key]  = $row[$field];  
            $newRow[$key] = $row;  
    }  
   
    if ($inverse) {  
        arsort($position);  
    }  
    else {  
        asort($position);  
    }  
    $returnArray = array();  
    foreach ($position as $key => $pos) {       
        $returnArray[] = $newRow[$key];  
    }  
    return $returnArray;  
}  
?>
    