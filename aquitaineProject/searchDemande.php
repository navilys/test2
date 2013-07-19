<?php
G::LoadClass ('pmFunctions');
$POST['PRESTA']='';
$POST['BENEF']='';
$POST['CHEQUE']='';
$sFrom = 'PMT_DEMANDES AS D, PMT_CHEQUES AS C';
$sWhere = ' WHERE D.APP_UID = C.ID_DEMANDE AND C.ANNULE=0 ';

if(isset($_POST['presta']) && $_POST['presta'] !=""){
	$sWhere .= "AND D.IDPRESTA = P.UID AND P.RAISONSOCIALE LIKE '%".$_POST['presta']."%' ";
	$POST['PRESTA']=$_POST['presta'];
        $sFrom .=',PMT_PARTENAIRE AS P';
}
if(isset($_POST['benef']) && $_POST['benef'] !=""){
	$sWhere .= "AND D.ID_USER = U.USR_UID AND (U.USR_LASTNAME LIKE '".$_POST['benef']."%' OR U.USR_FIRSTNAME LIKE '".$_POST['benef']."%')";
	$POST['BENEF']=$_POST['benef'];
        $sFrom .=',USERS AS U';
}
if(isset($_POST['cheque']) && $_POST['cheque'] !=""){
	$sWhere .= "AND C.NUM LIKE '".$_POST['cheque']."%' ";
	$POST['CHEQUE']=$_POST['cheque'];
}

$sSQL = "SELECT D.APP_UID, D.DATESIGNATURE, D.ID_USER, D.IDPRESTA, C.NUM
	FROM $sFrom
        $sWhere 
        ORDER BY DATESIGNATURE";
$aResult = executeQuery ($sSQL);

$aRows = array('DATE' => 'char', 'USER' => 'char','PRESTA' => 'char', 'CHEQUE' => 'char','SELECT_DEMANDE' => 'char'); 

$aDatas[] = $aRows;
foreach($aResult as $row){
	$sLink='<span class="RowLink"><a class="tableOption" href="#" onClick="setDemandeUid(\''.$row['APP_UID'].'\');">OK</a></span>';
	$aRows = array('DATE' => $row['DATESIGNATURE'], 'USER' => convergence_getNameUser($row['ID_USER']),'PRESTA' => convergence_getNamePresta($row['IDPRESTA']), 'CHEQUE' => $row['NUM'], 'SELECT_DEMANDE' =>$sLink ); 
	$aDatas[] = $aRows;
}	
global $_DBArray;			
$_DBArray['LIST']     = $aDatas;
$_SESSION['_DBArray'] = $_DBArray;
$criteria = new Criteria('dbarray');
$criteria->setDBArrayTable('LIST');  
$G_PUBLISH = new Publisher;  
$G_PUBLISH->AddContent('xmlform', 'xmlform', SYS_COLLECTION.'/demande_filters','',$POST);
$G_PUBLISH->AddContent('propeltable', SYS_COLLECTION.'/paged-table_demande', SYS_COLLECTION.'/searchDemande', $criteria);
G::RenderPage('publish',"raw");
?>




