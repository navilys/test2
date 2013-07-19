<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
header ( "Content-Type: text/plain" );
$array=array();
$array = $_REQUEST['array'];
$items = json_decode($array,true);
$array=array();
$oCase = new Cases ();
$messageInfo = "OK";
foreach($items as $item){
	if(isset($item['TITLE']) && $item['TITLE'] != 'Validé')
	{
		if(isset($item['APP_UID']) && $item['APP_UID'] != ''){
                    convergence_changeStatut($item['APP_UID'], 2, 'Demande Forcée');     
		}
		else
			$messageInfo = "Une erreur a empêché le forçage";
	}	
}

if(count($items)>0){	
	$messageInfo = "Forçage effectué";
}
else
	$messageInfo = "Une erreur a empêché le forçage";

	
$paging = array ('success' => true, 'messageinfo' => $messageInfo);
echo G::json_encode ( $paging );
?>
