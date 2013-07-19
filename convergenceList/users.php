<?php
G::LoadClass ('pmFunctions');
$POST['FIRST_NAME']='';
$POST['LAST_NAME']='';
$sWhere = ' WHERE TRUE ';

if(isset($_POST['fname']) && $_POST['fname'] !=""){
	$sWhere .= "AND USR_FIRSTNAME LIKE '".$_POST['fname']."%' ";
	$POST['FIRST_NAME']=$_POST['fname'];
}
if(isset($_POST['lname']) && $_POST['lname'] !=""){
	$sWhere .= "AND USR_LASTNAME LIKE '".$_POST['lname']."%' ";
	$POST['LAST_NAME']=$_POST['lname'];
}
$sSQL = "SELECT
			  USR_UID,
			  USR_USERNAME,
			  USR_FIRSTNAME,
			  USR_LASTNAME,
			  USR_EMAIL
			FROM
			  USERS $sWhere ORDER BY USR_FIRSTNAME, USR_LASTNAME";
$aResult = executeQuery ($sSQL);
$aRows = array('USR_USERNAME' => 'char', 'USR_FIRSTNAME' => 'char','USR_LASTNAME' => 'char', 'USR_EMAIL' => 'char','SELECT_USER' => 'char'); 
$aDatas[] = $aRows;
foreach($aResult as $row){
	$sLink='<span class="RowLink"><a class="tableOption" href="#" onClick="setUserUid(\''.$row['USR_UID'].'\');">Select</a></span>';
	$aRows = array('USR_USERNAME' => $row['USR_USERNAME'], 'USR_FIRSTNAME' => $row['USR_FIRSTNAME'],'USR_LASTNAME' => $row['USR_LASTNAME'], 'USR_EMAIL' => $row['USR_EMAIL'], 'SELECT_USER' =>$sLink ); 
	$aDatas[] = $aRows;
}	
global $_DBArray;			
$_DBArray['LIST']     = $aDatas;
$_SESSION['_DBArray'] = $_DBArray;
$criteria = new Criteria('dbarray');
$criteria->setDBArrayTable('LIST');  
$G_PUBLISH = new Publisher;  
$G_PUBLISH->AddContent('xmlform', 'xmlform', SYS_COLLECTION.'/users_filters','',$POST);
$G_PUBLISH->AddContent('propeltable', 'convergenceList/paged-table', SYS_COLLECTION.'/users', $criteria);
G::RenderPage('publish',"raw");
?>




