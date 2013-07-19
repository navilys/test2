<?php
G::LoadClass ('pmFunctions');
$POST['NAME']='';
$POST['VILLE']='';
$sWhere = ' WHERE TRUE ';

if(isset($_POST['searchEtablissement']) && $_POST['searchEtablissement'] !=""){
	$sWhere .= "AND NAME LIKE '".$_POST['searchEtablissement']."%' ";
	$POST['NAME']=$_POST['raison'];
}
if(isset($_POST['searchVille']) && $_POST['searchVille'] !=""){
	$sWhere .= "AND VILLE LIKE '".$_POST['searchVille']."%' ";
	$POST['VILLE']=$_POST['ville'];
}
$sSQL = "SELECT UID, ADR1, ADR2, NAME, RNE, CP, VILLE
			FROM
			  PMT_ETABLISSEMENT $sWhere ORDER BY NAME";
$aResult = executeQuery ($sSQL);

$aRows = array('ADR1' => 'char','ADR2' => 'char', 'NAME' => 'char','RNE' => 'char', 'VILLE' => 'char','CP' => 'char','SELECT_ETABLISSEMENT' => 'char'); 

$aDatas[] = $aRows;
foreach($aResult as $row){
	$sLink='<span class="RowLink"><a class="tableOption" href="#" onClick="setEtabUid(\''.$row['UID'].'\');">OK</a></span>';
	$aRows = array('ADR1' => $row['ADR1'], 'ADR2' => $row['ADR2'],'RNE' => $row['RNE'], 'NAME' => $row['NAME'], 'CP' => $row['CP'],'VILLE' => $row['VILLE'], 'SELECT_PRESTA' =>$sLink ); 
	$aDatas[] = $aRows;
}	
global $_DBArray;			
$_DBArray['LIST']     = $aDatas;
$_SESSION['_DBArray'] = $_DBArray;
$criteria = new Criteria('dbarray');
$criteria->setDBArrayTable('LIST');  
$G_PUBLISH = new Publisher;  
$G_PUBLISH->AddContent('xmlform', 'xmlform', SYS_COLLECTION.'/etablissement_filters','',$POST);
$G_PUBLISH->AddContent('propeltable', SYS_COLLECTION.'/paged-table', SYS_COLLECTION.'/searchEtablissement', $criteria);
G::RenderPage('publish',"raw");
?>




