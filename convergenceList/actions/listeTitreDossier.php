<?php
G::loadClass ( 'pmFunctions' );

function getData($dossier) {

    $query = 'SELECT P.APP_UID AS PRESTAID,D.THEMATIQUE_LABEL,C.UID,D.COMPLEMENT_CHQ,C.BCONSTANTE,C.NUM_TITRE,C.VN_TITRE,C.DEBUT_VALIDITE,C.FIN_VALIDITE,C.ANNULE,C.REPRODUCTION,P.RAISONSOCIALE,P.VILLE,C.DATE_RMB FROM PMT_CHEQUES AS C INNER JOIN PMT_DEMANDES AS D ON (D.NUM_DOSSIER = C.NUM_DOSSIER) LEFT JOIN PMT_PRESTATAIRE AS P ON (C.CODE_PRESTA = P.CONVENTION) WHERE D.NUM_DOSSIER = '.$dossier.' OR D.NUM_DOSSIER_COMPLEMENT='.$dossier;
    $result = executeQuery($query);
    
    
    if (is_array($result) && count($result) > 0) {
        
        foreach($result as $value) $array[] = $value;
        
        return $array;
    }


    
}


$data = getData($_REQUEST["num_dossier"]);

header("Content-Type: text/plain");

$paging = array(
    'success'=> true,
    'total'=> count($data),
    'data'=> $data
    );

echo G::json_encode($paging);

?>
