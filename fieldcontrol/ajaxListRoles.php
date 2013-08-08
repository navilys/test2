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
$ROL_UID = $_GET ['rolID'];
$innerJoin = '';
$j = 0;
$total = 0;
if (isset ( $_POST ['idInbox'] ) && $_POST ['idInbox'] != '') 
{
	 $array = Array();
	 $select = "SELECT ID_TABLE FROM PMT_INBOX_PARENT_TABLE WHERE ID_INBOX = '".$_POST['idInbox']."' AND ROL_CODE  = '" . $_GET ['rolID'] . "' ";
	 $dataSelect = executeQuery($select);
	 if(sizeof($dataSelect))
	 {
  	 	$query = " SELECT
    		    ADD_TAB_NAME AS ID,
    		    ADD_TAB_NAME AS NAME
    		    FROM PMT_INBOX_FIELDS
				INNER JOIN ADDITIONAL_TABLES ON (PMT_INBOX_FIELDS.ID_TABLE = ADDITIONAL_TABLES.ADD_TAB_NAME AND ADDITIONAL_TABLES.PRO_UID != '' )
				WHERE PMT_INBOX_FIELDS.ID_INBOX = '".$_POST['idInbox']."' AND ROL_CODE  = '" . $_GET ['rolID'] . "' 
					  AND ALIAS_TABLE = ID_TABLE AND ID_TABLE = '".$dataSelect[1]['ID_TABLE']."'
				GROUP BY ADD_TAB_UID";
	 }
	 else 
	 {
	 	$query = " SELECT
    		    ADD_TAB_NAME AS ID,
    		    ADD_TAB_NAME AS NAME
    		    FROM PMT_INBOX_FIELDS
				INNER JOIN ADDITIONAL_TABLES ON (PMT_INBOX_FIELDS.ID_TABLE = ADDITIONAL_TABLES.ADD_TAB_NAME AND ADDITIONAL_TABLES.PRO_UID != '' )
				WHERE PMT_INBOX_FIELDS.ID_INBOX = '".$_POST['idInbox']."' AND ROL_CODE  = '" . $_GET ['rolID'] . "' 
					  AND ALIAS_TABLE = ID_TABLE 
				GROUP BY ADD_TAB_UID
				ORDER BY PRO_UID DESC";
	 	
	 }
    $fields = executeQuery($query);
  
    if(sizeof($fields))
    {
        foreach($fields as $index)
        {
		    $query = "SELECT JOIN_QUERY FROM PMT_INBOX_JOIN 
				  WHERE JOIN_ROL_CODE  = '" . $_GET ['rolID'] . "'  AND JOIN_ID_INBOX = '".$_POST['idInbox']."'  ";

		    $newOptions = executeQuery ( $query );
		    $innerJoin = isset ( $newOptions [1]['JOIN_QUERY'] ) ? $newOptions [1]['JOIN_QUERY'] : '';
		    $index['INNER_JOIN'] = $innerJoin; 
    	    $array[] = $index;
	    }
	    $total = count ( $fields );
    }
    else
    {
        $index['INNER_JOIN'] = '';
        $index['ID'] = '';
        $index['NAME'] = '';
        $array[] = $index;
        $total = 1;
    }

}


if (isset ( $_REQUEST ['idTable'] ) && $_REQUEST ['idTable'] != '') 
{
    $total = 0;
	if( (isset($_REQUEST ['inner']) && $_REQUEST ['inner'] != '') || (isset($_REQUEST ['swinner']) && $_REQUEST ['swinner'] == 1) )
		$innerJoin = $_REQUEST ['inner'];
	else 
	{
		$query = "SELECT JOIN_QUERY FROM PMT_INBOX_JOIN 

				  WHERE JOIN_ROL_CODE  = '" . $_GET ['rolID'] . "' AND JOIN_ID_INBOX = '".$_REQUEST['idInboxData']."' ";

		$newOptions = executeQuery ( $query );
		$innerJoin = isset ( $newOptions [1]['JOIN_QUERY'] ) ? $newOptions [1]['JOIN_QUERY'] : '';
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
		
    	$DBConnectionUID = 'workflow';
    	$ipServer = getIP();
		$con = mysql_connect(DB_HOST,DB_USER,DB_PASS); 
		mysql_select_db(DB_NAME, $con); 
		$selectT = mysql_query ($sQueryT);
		mysql_close($con);
		if(sizeof($selectT))
		{
			$newArray = Array ();
			$arrayTotalFields = Array ();
			## Get Names of Tables of Query
    		$queryExplain = "EXPLAIN $sQueryT";
   			$infoTables = executeQuery ( $queryExplain );
    		$aTables = array();
    		foreach ( $infoTables as $key => $data ) 
    		{
        		$aTables[] = trim($data['table']);
    		}

    		//G::pr($aTables); die;

			$tableNames=array();
    		$tableNames[]=  array('OLD_NAME' => $table , 'ORIG_NAME' => $table );
    		$tableOldLast = $table;
    		for ($i=0;$i< count($aTables); $i++) 
    		{
    			$partQuery1= explode(' '.$aTables[$i].' ',$sQueryT);
        		$partQuery2 = explode(' ',$partQuery1[0]);
        		$origTableName = trim($partQuery2[count($partQuery2)-1]);
        		if($aTables[$i] != $table)
        			$tableNames[]=  array('OLD_NAME' => $aTables[$i] , 'ORIG_NAME' => $origTableName);
    		}

    //G::pr($tableNames);

    		$i = 0;
			while ($i < mysql_num_fields($selectT)) 
			{
				$metaData = mysql_fetch_field($selectT, $i);
   				if (!$metaData) {
       		 		echo "No hay información disponible<br />\n";
   				}
   				$nameTable = $metaData->table;
   				foreach ($tableNames as $row) 
				{
					if($row['ORIG_NAME'] != 'JOIN' && $row['ORIG_NAME'] != 'FROM')
      					$tableShow = $row['ORIG_NAME'];
      				else
      					$tableShow = $row['OLD_NAME'];
      				//echo ('register'.$nameTable.'  '. $tableShow.'<br>');
					if($nameTable == $tableShow || $nameTable == $row['OLD_NAME'])
					{
						$arrayTotalFields[] = Array(
								"FLD_UID" => $metaData->name,
   	 							"ADD_TAB_UID" => $tableShow,
      							"ALIAS_TABLE" => $row['OLD_NAME']
								);
					}
				}
    			$newArray[] = $metaData->name;
     			$i++;
			}

			### data custom columns
			$select = "SELECT FIELD_NAME FROM PMT_INBOX_FIELDS_SELECT 
			  	 WHERE ROL_CODE = '".$_GET ['rolID']."' AND ID_INBOX = '".$_REQUEST['idInboxData']."' AND TYPE = 'Yes' 
			  ";
			$dataSelect = executeQuery($select);
			foreach($dataSelect as $row)
			{
				$sw = 0;
				foreach($arrayTotalFields as $index )
				{
					if($row['FIELD_NAME'] == $index['FLD_UID'] && $sw == 0)
					{
						$sw = 1;
					}
				}
				if($sw == 0)
				{
					$arrayTotalFields[] = Array(
								"FLD_UID" => $row['FIELD_NAME'],
   	 							"ADD_TAB_UID" => '',
      							"ALIAS_TABLE" => ''
								);
				}
			}
	
			$totalInner = 0;
			$iColor = 1;
			$swColor = 0;

			$queryColor = "SELECT COLOR_CODE FROM PMT_INBOX_FIELDS_COLOR WHERE COLOR_UID = ". $iColor ." ";
			$color = executeQuery($queryColor);	
			$valor = Array();
			$swPos = 0;
			$iTotal = 1;
			$swColorCon = 0;
			$queryTot = "SELECT FLD_UID FROM PMT_INBOX_FIELDS 
				  WHERE ROL_CODE  = '" . $_GET ['rolID'] . "'  AND ID_INBOX = '".$_REQUEST['idInboxData']."' ";
			$execTot= executeQuery ( $queryTot );

			$totalFields = sizeof($execTot);

			$posField = $totalFields;
	
			$total = sizeof($arrayTotalFields);
	
 			//G::pr($arrayTotalFields);
			foreach($arrayTotalFields as $index )
			{
				$query = "SELECT * FROM PMT_INBOX_FIELDS 
				 	 WHERE ROL_CODE  = '" . $_GET ['rolID'] . "' AND FLD_UID = '" . $index['FLD_UID'] . "' AND 
				  	 ID_INBOX = '".$_REQUEST['idInboxData']."' AND ID_TABLE = '".$index['ADD_TAB_UID']."' ";

				$newOptions = executeQuery ( $query );
				if (sizeof ( $newOptions )) 
				{
			
					$valor ['INCLUDE_OPTION'] = true;
					if (isset ( $newOptions [1] ['DESCRIPTION'] ) && $newOptions [1] ['DESCRIPTION'] != '')
						$valor ['FLD_DESCRIPTION'] = $newOptions [1] ['DESCRIPTION'];
				
					if (isset ( $newOptions [1] ['JOIN_QUERY'] ) && $newOptions [1] ['JOIN_QUERY'] != '')
						$valor ['INNER_JOIN'] = $newOptions [1] ['JOIN_QUERY'];
				
					if (isset ( $newOptions [1] ['POSITION'] ) && $newOptions [1] ['POSITION'] != '')	
						$valor ['POSITION'] = $newOptions [1] ['POSITION'];	
				
					if (isset ( $newOptions [1] ['FIELD_REPLACE'] ) && $newOptions [1] ['FIELD_REPLACE'] != '')	
						$valor ['FIELD_REPLACE'] = $newOptions [1] ['FIELD_REPLACE'];
				
					if(isset ( $newOptions [1] ['ID_INBOX'] ) && $newOptions [1] ['ID_INBOX'] != '')
						$valor['ID_INBOX'] = $newOptions[1]['ID_INBOX'];	
				
					if (isset ( $newOptions [1] ['OPTION_QUERY_FUNCTION'] ) && $newOptions [1] ['OPTION_QUERY_FUNCTION'] != '')
						$valor['SELECT_OPTION'] = $newOptions [1] ['OPTION_QUERY_FUNCTION'];
				
					if (isset ( $newOptions [1] ['FUNCTIONS'] ) && $newOptions [1] ['FUNCTIONS'] != '')
						$valor['FUNCTIONS'] = $newOptions [1] ['FUNCTIONS'];

					if (isset ( $newOptions [1] ['PARAMETERS'] ) && $newOptions [1] ['PARAMETERS'] != '')
						$valor['PARAMETERS'] = $newOptions [1] ['PARAMETERS'];

					if (isset ( $newOptions [1] ['HIDDEN_FIELD'] ) && $newOptions [1] ['HIDDEN_FIELD'] == 1)
						$valor['HIDDEN_FIELD'] = true;
					else 
						$valor['HIDDEN_FIELD'] = false;

					if (isset ( $newOptions [1] ['INCLUDE_FILTER'] ) && $newOptions [1] ['INCLUDE_FILTER'] == 1)
						$valor['INCLUDE_FILTER'] = true;
					else 
						$valor['INCLUDE_FILTER'] = false;

					if (isset ( $newOptions [1] ['ORDER_BY'] ) && $newOptions [1] ['ORDER_BY']  != '')
						$valor['ORDER_BY'] = $newOptions [1] ['ORDER_BY'];
					else 
						$valor['ORDER_BY'] = '';

					$valor['ADD_TAB_NAME'] = $index['ADD_TAB_UID'];

					$valor['ALIAS_TABLE'] = $index['ALIAS_TABLE'];

					$swPos = 1;		

				} 
				else 
				{
					$posField++;
					$valor ['INCLUDE_OPTION'] = false;
					$valor ['INCLUDE_FILTER'] = false;
					$valor ['HIDDEN_FIELD'] = false;
					$valor ['FLD_DESCRIPTION'] = $index['FLD_UID'];
					$valor ['INNER_JOIN'] = '';
					$valor ['POSITION'] = $posField;
					$valor ['FIELD_REPLACE'] = '';
					$valor ['ID_INBOX'] = '';
					$valor ['SELECT_OPTION'] = '';
					$valor ['FUNCTIONS'] = '';
					$valor ['PARAMETERS'] = '';
					$valor['ORDER_BY'] = '';
					$valor['ADD_TAB_NAME'] = $index['ADD_TAB_UID'];
					$valor['ALIAS_TABLE'] = $index['ALIAS_TABLE'];
				}

				$valor ['ROL_CODE'] = $_GET ['rolID'];
				$valor ['FIELD_NAME'] = $index['FLD_UID'];
				$valor ['FLD_UID'] = $index['FLD_UID'];
				$valor ['COLOR'] = isset ( $color[1]['COLOR_CODE'] ) ? $color[1]['COLOR_CODE'] : '';

				if($iTotal != 1)
					$indexAux = next($arrayTotalFields);
				else
					$indexAux = current($arrayTotalFields);

			//G::pr($index['FLD_UID'].' index   indexAux '.$indexAux['FLD_UID'].' '. ($iTotal).'  '. $total.'  '.$indexAux['ADD_TAB_UID'].'  '.$index['ADD_TAB_UID'].' next ');

				if($indexAux['ADD_TAB_UID'] != $index['ADD_TAB_UID']  && $iTotal  < $total)
				{
					$iColor++;
					$queryColor = "SELECT COLOR_CODE FROM PMT_INBOX_FIELDS_COLOR WHERE COLOR_UID = ". $iColor ." ";
					$color = executeQuery($queryColor);
					$swColor++;
					$swColorCon = 0;
				}
				if($indexAux['ADD_TAB_UID'] != $index['ADD_TAB_UID']  && $iTotal  < $total && $indexAux['ADD_TAB_UID'] == '')
				{
					$iColor++;
					$queryColor = "SELECT COLOR_CODE FROM PMT_INBOX_FIELDS_COLOR WHERE COLOR_UID = '5' ";
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
				$valor ['FLD_DESCRIPTION'] = '';
				$valor ['INNER_JOIN'] = '';
				$valor ['POSITION'] = '';
				$valor ['FIELD_REPLACE'] = '';
				$valor ['ID_INBOX'] = '';
				$valor ['SELECT_OPTION'] = '';
				$valor ['FUNCTIONS'] = '';
				$valor ['PARAMETERS'] = '';
				$valor ['ADD_TAB_NAME'] = '';
				$valor ['ALIAS_TABLE'] = '';
				$valor++;
				$iTotal++;

			}

			$field = 'POSITION';
			$array = orderMultiDimensionalArray($array, $field, '');
		//G::pr($array);
		
		
		}
		 
	}
	catch (Exception $e)
	{
		$error = $e->getMessage();
		$error = preg_replace("[\n|\r|\n\r]", ' ', $error);
		$paging = array ('success' => false, 'response' => $error);
		echo json_encode ( $paging );
		die;

	}
		
}

$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ), 'response' => 'OK' );
echo json_encode ( $paging );


function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) 
{  
    $position = array();  
    $newRow = array();  
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

function getIP()
{
    if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
    else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = null ;
    return $ip;
}
?>

    