<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 50;

if(isset($_GET['type']) && $_GET['type'] == 'list')
{   
	$array = Array ();
	
	
	try{
		
		
		$query = "SELECT CFG_USR_FIELD_NAME AS ID_FIELD, CFG_USR_DESCRIPTION AS DESCRIPTION, CFG_USR_FIELD_NAME AS FIELD_NAME, CFG_USR_TYPE
				  FROM PMT_CONFIG_USERS 
		  		  GROUP BY ID_FIELD";
		$newOptions = executeQuery($query);
		foreach($newOptions as $index)
		{
			if($index['CFG_USR_TYPE'] == 'DROPDOWN')
			{
				$index['ID_FIELD'] = 'id_'.$index['ID_FIELD'];
			}
			else
			{
				$index['ID_FIELD'] = 'name_'.$index['ID_FIELD'];
			}
			$newArray[] = $index;
		}
		$queryTot = "SELECT CFG_LIST_USR_FIELD_NAME FROM PMT_CONFIG_LIST_USERS ";
		$execTot= executeQuery ( $queryTot );
		$totalFields = sizeof($execTot);
		$posField = $totalFields;
		
		$total = sizeof($newArray);
			
		foreach($newArray as $index)
		{
			$query = "SELECT * FROM PMT_CONFIG_LIST_USERS 
				 	 WHERE CFG_LIST_USR_FIELD_NAME = '" . $index['ID_FIELD'] . "' ";
			$newOptions = executeQuery ( $query );
			if (sizeof ( $newOptions )) 
			{
				$index ['INCLUDE_OPTION'] = true;
				if (isset ( $newOptions [1] ['CFG_LIST_USR_HIDDEN'] ) && $newOptions [1] ['CFG_LIST_USR_HIDDEN'] == 1)
					$index['HIDDEN_FIELD'] = true;
				else 
					$index['HIDDEN_FIELD'] = false;
				if (isset ( $newOptions [1] ['CFG_LIST_USR_POSITION'] ) && $newOptions [1] ['CFG_LIST_USR_POSITION'] != '')	
					$index ['POSITION'] = $newOptions [1] ['CFG_LIST_USR_POSITION'];
				if (isset ( $newOptions [1] ['CFG_LIST_USR_DESCRIPTION'] ) && $newOptions [1] ['CFG_LIST_USR_DESCRIPTION'] != '')	
					$index ['DESCRIPTION'] = $newOptions [1] ['CFG_LIST_USR_DESCRIPTION'];
			}
			else 
			{
				$posField++;
				$index ['INCLUDE_OPTION'] = false;
				$index ['HIDDEN_FIELD'] = false;
				$index ['POSITION'] = $posField;
			}
			$array [] = $index;
			$index ['POSITION'] = '';
			$index ['INCLUDE_OPTION'] = '';
			$index ['HIDDEN_FIELD'] = '';
			$index ['DESCRIPTION'] = '';
		}
		$field = 'POSITION';
		$array = orderMultiDimensionalArray($array, $field, '');
		
	}	
	catch (Exception $e)
	{
		$error = $e->getMessage();
		$error = preg_replace("[\n|\r|\n\r]", ' ', $error);
		$paging = array ('success' => false, 'response' => $error);
		echo json_encode ( $paging );
		die;

	}
	$total = count ( $newArray );
	
	$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ) );
	
	echo json_encode ( $paging );
}

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

?>
    