<?php
//ini_set ( 'error_reporting', E_ALL );
//ini_set ( 'display_errors', True );
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
	require_once PATH_CONTROLLERS . 'pmTablesProxy.php';
    G::LoadClass('reportTables');
    
	$oReportTables = new pmTablesProxy();
    $aFields['FIELDS'] = array();
    $aFields['PRO_UID'] = $_REQUEST ['idProcess'];
    $dynFields = array();
    $dynFields = $oReportTables->_getDynafields($aFields['PRO_UID'], 'xmlform', 0,100000, null);
  	$newArrayInner = Array ();
  	
    foreach ($dynFields['rows'] as $row) {
    	
		$arrayAux = Array(
						//"FLD_UID" => $row['FIELD_UID'],
						"FLD_UID" => $row['FIELD_NAME'],
    					"FIELD_NAME" => $row['FIELD_NAME'],
						"FIELD_DESCRIPTION" => strtoupper($row['FIELD_NAME'])
						);
						
			$newArrayInner[] = $arrayAux;
	}
			
    $totalInner = 0;
		
 	//G::pr($newArrayInner);
  	$valor = Array();
	$swPos = 0;
	$iTotal = 1;
	$queryTot = "SELECT CA.CD_FIELDNAME AS FLD_UID
				 FROM PMT_COLUMN_DEDOUBLONAGE CA, PMT_CONFIG_DEDOUBLONAGE AC 
				 WHERE 
				 CA.CD_UID_CONFIG_AS = AC.CD_UID 
				 AND AC.CD_PROCESS_UID = '".$_REQUEST['idProcess']."' 
				  ";

	$execTot= executeQuery ( $queryTot );
	$totalFields = sizeof($execTot);
	$posField = $totalFields;
	$total = sizeof($newArrayInner);
	$array = '';
	
	$queryConfig = "SELECT CD_UID FROM PMT_CONFIG_DEDOUBLONAGE
				  WHERE CD_PROCESS_UID = '".$_POST['idProcess']."' ";
	
	$qidConfig = executeQuery($queryConfig);
	$idConfigAS = 0 ; 
	if(sizeof($qidConfig))
	{
		$idConfigAS = $qidConfig[1]['CD_UID'];
		
	$qRows = executeQuery ("SELECT CA.CD_FIELDNAME AS FLD_UID, 
				CA.CD_FIELD_DESCRIPTION AS FIELD_DESCRIPTION FROM PMT_COLUMN_DEDOUBLONAGE CA  
				 WHERE CA.CD_UID_CONFIG_AS = '". $idConfigAS."' ORDER BY CA.CD_ORDER_FIELD");
		
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
	if(is_array($newArrayInner))
	{
		foreach($newArrayInner as $index )
		{
			$query = "SELECT CA.* ,
					  CD.CD_PROCESS_UID AS PROCESS_UID
					  FROM PMT_COLUMN_DEDOUBLONAGE CA, PMT_CONFIG_DEDOUBLONAGE CD
					  WHERE 
					  CA.CD_UID_CONFIG_AS = CD.CD_UID 
					  AND CA.CD_FIELDNAME = '" . $index['FLD_UID'] . "' 
					  AND CA.CD_UID_CONFIG_AS = '". $idConfigAS."'
					  AND CA.CD_FIELD_DESCRIPTION = '" . mysql_escape_string($index['FIELD_DESCRIPTION']). "'";
			
			$newOptions = executeQuery ( $query );
			$i=0;
			if (sizeof ( $newOptions )) 
			{
				$i++;
				$valor ['CD_INCLUDE_OPTION'] = true;
				
				if (isset ( $newOptions [1] ['CD_FIELDNAME'] ) && $newOptions [1] ['CD_FIELDNAME'] != '')
					$valor['FIELD_NAME'] = $newOptions [1] ['CD_FIELDNAME'];
					
				if (isset ( $newOptions [1] ['CD_FIELD_DESCRIPTION'] ) && $newOptions [1] ['CD_FIELD_DESCRIPTION'] != '')
					$valor ['FLD_DESCRIPTION'] = strtoupper($newOptions [1] ['CD_FIELD_DESCRIPTION']);
					
				if (isset ( $newOptions [1] ['CD_ORDER_FIELD'] ) && $newOptions [1] ['CD_ORDER_FIELD'] != '')	
					$valor ['POSITION'] = $newOptions [1] ['CD_ORDER_FIELD'];	
					
				if(isset ( $newOptions [1] ['PROCESS_UID'] ) && $newOptions [1] ['PROCESS_UID'] != '')
					$valor['ID_PROCESS'] = $newOptions[1]['PROCESS_UID'];	
				
				if(isset ( $newOptions [1] ['CD_ID_TABLE'] ) && $newOptions [1] ['CD_ID_TABLE'] != '')
					$valor['CD_ID_TABLE'] = $newOptions[1]['CD_ID_TABLE'];
					
				if(isset( $newOptions [1] ['CD_RATIO'] ) && $newOptions [1] ['CD_RATIO'] != '')
					$valor['RATIO_FIELD'] = $newOptions[1]['CD_RATIO'];
				else 
					$valor['RATIO_FIELD'] = 0;
					
				$swPos = 1;		
			} 
			else 
			{
				$posField++;
				$valor ['CD_INCLUDE_OPTION'] = false;
				$valor ['FIELD_NAME'] = $index['FLD_UID'];
				$valor ['FLD_DESCRIPTION'] = strtoupper($index['FIELD_DESCRIPTION']);
				$valor ['POSITION'] = $posField;
				$valor ['ID_PROCESS'] = $_POST ['idProcess'];
				$valor ['ID_TABLE'] = '';
				$valor ['RATIO_FIELD'] = 0;
				$valor ['ORDER_FIELD'] = 0;
			}
			$valor ['FIELD_NAME'] = $index['FLD_UID'];
			$valor ['FLD_UID'] = $index['FLD_UID'];
			
			if($iTotal != 1)
				$indexAux = next($newArrayInner);
			else
				$indexAux = current($newArrayInner);
			
			//G::pr($index['FLD_UID'].' '.$indexAux['FLD_UID'].' '. ($iTotal).'  '. $total.'  '.$indexAux['ADD_TAB_UID'].'  '.$index['ADD_TAB_UID'].' next ');

			$array [] = $valor;
			$valor ['CD_INCLUDE_OPTION'] = false;
			$valor ['FIELD_NAME'] = '';
			$valor ['FLD_DESCRIPTION'] = '';
			$valor ['INNER_JOIN'] = '';
			$valor ['POSITION'] = '';
			$valor ['ID_PROCESS'] = '';
			$valor ['ID_TABLE'] = '';
			$valor ['RATIO'] = '';
			$valor ['RATIO_FIELD'] = 0;
			$valor ['ORDER_FIELD'] = '';
			$valor ['ADD_TAB_NAME'] = '';
			$valor ['ALIAS_TABLE'] = '';
			$valor++;
			$iTotal++;
			
		}		
		$field = 'POSITION';
		
		$array = orderMultiDimensionalArray($array, $field, '');
	}	
}

$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ), 'response' => 'OK' );
echo json_encode ( $paging );


function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) 
{  
    $position = array();  
    $newRow = array();  
    $returnArray = array(); 
    //G::pr($toOrderArray);
    if(is_array($toOrderArray))
    {
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
	    foreach ($position as $key => $pos) {       
	        $returnArray[] = $newRow[$key];  
	    }
    }   
     
     
      
    return $returnArray;  
}  
?>
    