<?php
G::LoadClass ('pmFunctions');
$POST['CONVENTION']='';
$POST['PRESTA_NAME']='';
$POST['VILLE']='';
$POST['CODE_OPER']='';
$sWhere = ' WHERE STATUT=1 ';


/*if(isset($_POST['codeOper']) && $_POST['codeOper'] !=""){
    $sWhere .= 'AND CODE_OPER_ELIGIBLE = '.$_POST['codeOper'].' ';
    $POST['CODE_OPER']=$_POST['codeOper'];
}*/
if(isset($_POST['convention']) && $_POST['convention'] !=""){
    $sWhere .= "AND CONVENTION LIKE '%".$_POST['convention']."%' ";
    $POST['CONVENTION']=$_POST['convention'];
}
if(isset($_POST['raisonsociale']) && $_POST['raisonsociale'] !=""){
    $sWhere .= "AND RAISONSOCIALE LIKE '%".mysql_escape_string($_POST['raisonsociale'])."%' ";
    $POST['PRESTA_NAME']=$_POST['raisonsociale'];
}
if(isset($_POST['ville']) && $_POST['ville'] !=""){
    $sWhere .= "AND VILLE LIKE '%".$_POST['ville']."%' ";
    $POST['VILLE']=$_POST['ville'];
}
$sSQL = "SELECT CONVENTION, RAISONSOCIALE, VILLE
            FROM
              PMT_PRESTATAIRE $sWhere ORDER BY RAISONSOCIALE";
$aResult = executeQuery ($sSQL);
$aRows = array('CONVENTION' => 'char', 'RAISONSOCIALE' => 'char', 'VILLE' => 'char', 'SELECT_ETAB' => 'char');
$aDatas[] = $aRows;
foreach($aResult as $row){
    $sLink='<span class="RowLink"><a class="tableOption" href="#" onClick="setPrestaUid(\''.$row['CONVENTION'].'\');">OK</a></span>';
    $aRows = array('CONVENTION' => $row['CONVENTION'], 'PRESTA_NAME' => $row['RAISONSOCIALE'], 'VILLE' => $row['VILLE'], 'SELECT_ETAB' =>$sLink );
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



