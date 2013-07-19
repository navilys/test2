<?php
/////   Starting    /////
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True ); 
$start    = isset($_POST['start']) ? $_POST['start'] : 0;
$limit    = isset($_POST['limit']) ? $_POST['limit'] : 2000000;
$USER_UID = $_SESSION ['USER_LOGGED'];

$Where    ='';
$array    = Array ();
$total    = '';
$APP_UID    = $_POST['app_uid'];
$DOC_UID = '138507476518d6da82f0e97031493508';	  
	    
	
	$querySelect = "SELECT APP_DOC_UID FROM APP_DOCUMENT 
	WHERE APP_UID = '". $APP_UID ."'  AND DOC_UID = '". $DOC_UID ."'";
	$aDatos = executeQuery ($querySelect);
	
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
