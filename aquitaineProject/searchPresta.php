<?php
G::LoadClass ('pmFunctions');
$POST['NUM']='';
$POST['RAISON']='';
$POST['VILLE']='';
$sWhere = ' WHERE STATUT=1 ';

if(isset($_POST['num']) && $_POST['num'] !=""){
	$sWhere .= "AND NUM_DOSSIER LIKE '".$_POST['num']."%' ";
	$POST['NUM']=$_POST['num'];
}
if(isset($_POST['raison']) && $_POST['raison'] !=""){
	$sWhere .= "AND RAISONSOCIALE LIKE '".mysql_escape_string($_POST['raison'])."%' ";
	$POST['RAISON']=$_POST['raison'];
}
if(isset($_POST['ville']) && $_POST['ville'] !=""){
	$sWhere .= "AND VILLE LIKE '".$_POST['ville']."%' ";
	$POST['VILLE']=$_POST['ville'];
}
$sSQL = "SELECT SIRET, NUM_DOSSIER, RAISONSOCIALE, ADRESSE1, VILLE
			FROM
			  PMT_PRESTATAIRE $sWhere ORDER BY RAISONSOCIALE";
$aResult = executeQuery ($sSQL);

$aRows = array('NUM_DOSSIER' => 'char', 'RAISON' => 'char','ADRESSE' => 'char', 'VILLE' => 'char','SELECT_PRESTA' => 'char');

$aDatas[] = $aRows;
foreach($aResult as $row){
	$sLink='<span class="RowLink"><a class="tableOption" href="#" onClick="setPrestaUid(\''.$row['SIRET'].'\');">OK</a></span>';
	$aRows = array('NUM_DOSSIER' => $row['NUM_DOSSIER'], 'RAISON' => $row['RAISONSOCIALE'],'ADRESSE' => $row['ADRESSE1'], 'VILLE' => $row['VILLE'], 'SELECT_PRESTA' =>$sLink );
	$aDatas[] = $aRows;
}	
global $_DBArray;			
$_DBArray['LIST']     = $aDatas;
$_SESSION['_DBArray'] = $_DBArray;
$criteria = new Criteria('dbarray');
$criteria->setDBArrayTable('LIST');  
$G_PUBLISH = new Publisher;  
$G_PUBLISH->AddContent('xmlform', 'xmlform', SYS_COLLECTION.'/presta_filters','',$POST);
$G_PUBLISH->AddContent('propeltable', SYS_COLLECTION.'/paged-table', SYS_COLLECTION.'/searchPresta', $criteria);
G::RenderPage('publish',"raw");
?>




