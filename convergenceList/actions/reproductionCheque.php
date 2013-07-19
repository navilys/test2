<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");

$items = json_decode($_POST['array'],1);
$todoAnnule = $_POST['todo'];
$flag = 0; 
$oCase = new Cases ();
foreach($items as $item){
	if(isset($item['APP_UID']) && $item['APP_UID'] != ''){
                //on met a jour le flag NPAI
                $Fields = $oCase->loadCase ($item['APP_UID']);

                if ($todoAnnule == 1) {
                    $Fields['APP_DATA']['REPRODUCTION_CHQ'] = 'N';
                    insertHistoryLogPlugin($item['APP_UID'],$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','',"Annulation de la reproduction",$Fields['APP_DATA']['STATUT']);
                    $flag = -1; 
                }
                else {
                    $Fields['APP_DATA']['REPRODUCTION_CHQ'] = 'O';
                    insertHistoryLogPlugin($item['APP_UID'],$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','',"Demande de reproduction",$Fields['APP_DATA']['STATUT']);
                    $flag = 1; 
                }
                $oCase->updateCase($item['APP_UID'], $Fields);

                

        }

}

if ($flag == -1)
    $messageInfo = 'Annulation de reproduction de cheques correctement enregistrees.';    
if ($flag == 1)
    $messageInfo = 'Reproduction de cheques correctement enregistrees.';    
if ($flag == 0)
    $messageInfo = 'Aucun traitement effectue.';    


$paging = array ('success' => true, 'messageinfo' => $messageInfo);
echo G::json_encode ( $paging );


?>