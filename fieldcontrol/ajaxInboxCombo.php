<?php
/////   Starting    /////
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$start    = isset($_POST['start']) ? $_POST['start'] : 0;
$limit    = isset($_POST['limit']) ? $_POST['limit'] : 2000000;
$USER_UID = $_SESSION ['USER_LOGGED'];
$Where    ='';
$array    = Array ();
$total    = '';
$rolID    = $_GET['rolID'];
	  
	$sQuery = "SELECT INBOX  AS ID, DESCRIPTION AS NAME
               FROM PMT_INBOX  
              ";	    
	$aDatos = executeQuery ($sQuery);
	
	foreach ( $aDatos as $valor ) 
	{
  		$array [] = $valor;
	}
	$total = count ( $aDatos );

header("Content-Type: text/plain");    
    $paging = array(
        'success'=> true,
        'total'=> $total,
        'data'=> array_splice($array,$start,$limit)
    );  
        
echo json_encode($paging);

?>
