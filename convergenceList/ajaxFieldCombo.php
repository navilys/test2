<?php
/////   INICIALIZACIONES    /////
//ini_set ( 'error_reporting', E_ALL );
//ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$start    = isset($_POST['start']) ? $_POST['start'] : 0;
$limit    = isset($_POST['limit']) ? $_POST['limit'] : 2000000;
$USER_UID = $_SESSION ['USER_LOGGED'];
$Where    ='';
$array    = Array ();
$total    = ''; 
$rolID    = isset($_GET['rolID'])?$_GET['rolID']:'';
$_GET['rolID'] =isset( $_GET['rolID'])? $_GET['rolID']:'';
$_POST['idTable'] = isset($_POST['idTable'])?$_POST['idTable']:'';
if(isset($_REQUEST) && $_REQUEST['TYPE'] == 'FieldCombo')
{

	if($_POST['idTable'])
	{
		$sQuery = "SHOW COLUMNS FROM ".$_POST['idTable']." ";
		
		$aDara = executeQuery ($sQuery);
	
		foreach ( $aDara as $value )
		{
			$value['ID'] = $value['Field'];
	    	$value['NAME'] = $value['Field'];
			$array [] = $value;
		}
			
		$total = count ( $aDara );
	}
}
$_REQUEST['idField'] = isset($_REQUEST['idField'])?$_REQUEST['idField']:'';
if(isset($_REQUEST) && $_REQUEST['idField'] != '')
{
  	$query = " SHOW COLUMNS FROM ".$_POST['idTable']." WHERE FIELD = '".$_REQUEST['idField']."' ";
    $fields = executeQuery($query);
    $i = 1;
    foreach($fields as $index)
    {
    	$index['ID'] = $index['Field'];
    	$index['NAME'] = $index['Field'];
		$array[] = $index;
    }
    $total = count ( $fields );
  	
}
if (isset ( $_REQUEST ['idTable'] ) && $_REQUEST ['idTable'] != '') 
{
	$array    = Array ();
  	 $query = "  SHOW COLUMNS FROM ".$_REQUEST['idTable']." ";
    $fields = executeQuery($query);
    
    $i = 1;
    foreach($fields as $index)
    {
    	$index['ID'] = $index['Field'];
    	$index['NAME'] = $index['Field'];
		$array[] = $index;
    }
    $total = count ( $fields );
}
header("Content-Type: text/plain");    
    $paging = array(
        'success'=> true,
        'total'=> $total,
        'data'=> array_splice($array,$start,$limit)
    );  
        
echo json_encode($paging);

?>
