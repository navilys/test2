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

if(isset($_REQUEST) && $_REQUEST['TYPE'] == 'reportTableCombo')
{
	$array    = Array ();
  	  
	$sQuery = "SELECT A.ADD_TAB_NAME as ID, 
					  A.ADD_TAB_NAME as NAME, 
					  A.ADD_TAB_DESCRIPTION 
               FROM ADDITIONAL_TABLES AS A          
               -- WHERE  PRO_UID <> '' 
               ";	    
	$aDara = executeQuery ($sQuery);
	
	foreach ( $aDara as $value ) 
	{
		$query = "SELECT JOIN_CONFIG FROM PMT_AS400_CONFIG WHERE JOIN_CONFIG != '' 
		AND PROCESS_UID = '".$_POST['idProcess']."' AND TABLENAME = '".$value['ID']."'";
		
  		$newOptions = executeQuery ( $query );
		$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
		$value['INNER_JOIN'] = $innerJoin;
		$array [] = $value;
	}
	$total = count ( $aDara );
}$_REQUEST['idTable'] = isset($_REQUEST['idTable'])?$_REQUEST['idTable']:'';
if(isset($_REQUEST) && $_REQUEST['idTable'] != '')
{
	$array    = Array ();
  	$query = " SELECT F.FLD_UID AS ID, F.FLD_DESCRIPTION AS NAME 
  				FROM FIELDS F, PMT_COLUMN_AS400 PC
    		   	WHERE F.ADD_TAB_UID = '".$_REQUEST['idTable']."' AND F.FLD_UID != PC.FIELD_NAME
    		    GROUP BY F.FLD_UID
    		";
  	 $fields = executeQuery($query);
    $i = 1;
    foreach($fields as $index)
    {
    	$query = "SELECT JOIN_CONFIG FROM PMT_AS400_CONFIG WHERE JOIN_CONFIG != '' 
		AND PROCESS_UID = '".$_POST['idProcess']."' AND TABLENAME = '".$index['ID']."'";
		
  		$newOptions = executeQuery ( $query );
		$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
		$index['INNER_JOIN'] = $innerJoin;
		
		$array[] = $index;
    }
    $total = count ( $fields );
  	
}

if (isset ( $_REQUEST ['idProcess'] ) && $_REQUEST ['idProcess'] != '') 
{
	$array    = Array ();
 
	$query = "SELECT  
			  ADD_TAB_NAME AS ID, ADD_TAB_NAME AS NAME
			  FROM ADDITIONAL_TABLES
			  -- WHERE PRO_UID =  '" .$_POST['idProcess']. "' 
			  ";
	
	$fields = executeQuery($query);
    
    $i = 1;
    foreach($fields as $index)
    {
    	$query = "SELECT JOIN_CONFIG FROM PMT_AS400_CONFIG WHERE  
		PROCESS_UID = '".$_POST['idProcess']."' AND TABLENAME = '".$index['ID']."'";
		
  		$newOptions = executeQuery ( $query );
		
  		$innerJoin = isset ( $newOptions [1]['JOIN_CONFIG'] ) ? $newOptions [1]['JOIN_CONFIG'] : '';
		$index['INNER_JOIN'] = $innerJoin;
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
