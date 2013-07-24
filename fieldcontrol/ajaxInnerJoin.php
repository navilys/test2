<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 1000;
   
$array = Array ();

if (isset ( $_REQUEST ['idTable'] ) && $_REQUEST ['idTable'] != '' && isset($_REQUEST ['inner']) && $_REQUEST ['inner'] != '') 
{
	$table =  $_REQUEST ['idTable'];
	
	$sQueryT = " SELECT * 
  				FROM ".$table." 
  				 " . $_REQUEST ['inner'] . " 
    		   	";
    		   	
	$selectT = executeQuery($sQueryT);
	 
	$inner = explode(' ',$_REQUEST ['inner']);
	$inner = $inner[2];
	$sQuery = " SHOW COLUMNS
  				FROM ".$table."
    		   ";
	
	$aDatos = executeQuery ( $sQuery );

	foreach ( $selectT as $valor )
	{
    	$newArray =  array_keys($valor);
		break;
	}
	 
	
	foreach($newArray as $index => $value)
	{ 
		$i = 0;
		$swF = 0;
		foreach ( $aDatos as $valor ) 
		{
			if($valor['Field'] == $value)
			{
				$swF = 1;
				$i++;
			}
		}
		if($swF == 0 || $i > 1)
		{
			$arrayAux = Array(
					"ID" => $value,
   	 				"NAME" => $value
				);
			$array []= $arrayAux;
		}
		
	}
	//G::pr($array);
	
	$total = count ( $aDatos );
	
	$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ) );
	
	echo json_encode ( $paging );
}


?>
    