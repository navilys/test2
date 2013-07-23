<?php 

G::loadClass ( 'pmFunctions' );

function getData($app_uid) {

    $queryListeDmd = 'SELECT LISTE_DOSSIER FROM PMT_LISTE_PROD WHERE APP_UID = "'.$app_uid.'"';
    $resultListeDmd = executeQuery($queryListeDmd);
    
    $lst = explode(',',$resultListeDmd[1]['LISTE_DOSSIER']);
    
    foreach ($lst as $val) {
        if ($val != '')
            $tempLst[] = $val;
    }
    
    $query = 'SELECT D.APP_UID, D.NUM_DOSSIER, D.NUM_DOSSIER_COMPLEMENT, D.PRENOM, D.NOM, D.REPRODUCTION_CHQ, D.NPAI, TC.LABEL, C.BCONSTANTE
        FROM PMT_DEMANDES AS D INNER JOIN PMT_TYPE_CHEQUIER AS TC ON (TC.CODE_CD = D.CODE_CHEQUIER) INNER JOIN PMT_CHEQUES AS C ON (C.NUM_DOSSIER = D.NUM_DOSSIER)
        WHERE D.NUM_DOSSIER IN (
            '.implode(',',$tempLst).'
        ) AND D.STATUT=6 GROUP BY C.BCONSTANTE';

    $result = executeQuery($query);
    
    
    if (is_array($result) && count($result) > 0) {
        
        foreach($result as $value) $array[] = $value;
        
        return $array;
    }
    
}


$data = getData($_REQUEST["app_uid"]);

header("Content-Type: text/plain");

$paging = array(
    'success'=> true,
    'total'=> count($data),
    'data'=> $data
    );

echo G::json_encode($paging);


?>