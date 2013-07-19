<?php

G::loadClass('pmFunctions');
G::LoadClass("case");
header("Content-Type: text/plain");
$array = array();
$array = $_REQUEST['array'];
$value = $_REQUEST['statut'];
$sSQL = "SELECT TITLE FROM PMT_STATUT WHERE UID ='$value'";
$aResult = executeQuery($sSQL);
$title = $aResult[1]['TITLE'];
$items = json_decode($array, true);
$array = array();
$oCase = new Cases ();
$messageInfo = "OK";
foreach ($items as $item)
{

    if (isset($item['APP_UID']) && $item['APP_UID'] != '')
    {
        convergence_changeStatut($item['APP_UID'], $value, 'Etat Modifié en ' . $title);
    }
    else
        $messageInfo = "NO";
}

if (count($items) > 0)
{
    $messageInfo = "Etat des dossier modifiés en " . $title;
}
else
    $messageInfo = "NO";


$paging = array('success' => true, 'messageinfo' => $messageInfo);
echo G::json_encode($paging);
?>