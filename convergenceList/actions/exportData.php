<?php 
G::loadClass ( 'pmFunctions' );
$array = Array();

$type = $_GET['type'];

if ($type == 'npai') {
    
    $csv = array();
    $entete = array('Numéro de dossier','Identifiant PND','nom','prenom','voie','type voie','rue','code postal','ville');
    
    $query = 'SELECT NUM_DOSSIER,PMT_CPTPND.UID,NOM,PRENOM,NUMVOIE,TYPEVOIE,NOMVOIE,ZIP,VILLE FROM PMT_DEMANDES LEFT JOIN PMT_CPTPND ON (PMT_CPTPND.DOSSIER = PMT_DEMANDES.NUM_DOSSIER) INNER JOIN PMT_VILLE ON ( PMT_VILLE.UID = PMT_DEMANDES.CODEPOSTAL ) WHERE NPAI = 1 AND STATUT != 0 AND STATUT != 999';        
    $result = executeQuery($query);
    
    $file = '/var/tmp/export_npai_'.time().'.csv';
}

if ($type == 'adrmodif') {
    
    $query = 'SELECT PMT_DEMANDES.APP_UID,NUM_TITRE,NOM,PRENOM,NUMVOIE,TYPEVOIE,NOMVOIE,ZIP,VILLE FROM PMT_DEMANDES 
        JOIN PMT_CHEQUES ON (PMT_CHEQUES.NUM_DOSSIER = PMT_DEMANDES.NUM_DOSSIER) 
        INNER JOIN PMT_VILLE ON ( PMT_VILLE.UID = PMT_DEMANDES.CODEPOSTAL ) 
        WHERE NPAI = 1 AND STATUT != 0 AND STATUT != 999';        
    $resultAllNPAI = executeQuery($query);
    
    foreach ($resultAllNPAI as $k => $npai) {
        
        $query = 'SELECT max(HLOG_DATECREATED) as HLOG_DATECREATED FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID="'.$npai['APP_UID'].'" AND HLOG_ACTION="produit"'; 
        $resultDate = executeQuery($query);
        if (isset($resultDate[1]['HLOG_DATECREATED']) && $resultDate[1]['HLOG_DATECREATED'] != '' ) {

            $query2 = 'SELECT count(*) as NB FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID="'.$npai['APP_UID'].'" AND HLOG_DATECREATED > "'.$result[1]['HLOG_DATECREATED'].'" AND HLOG_ACTION="Modification de l\'adresse"'; 
            $result2 = executeQuery($query2);

            if ($result2[1]['NB'] > 0) {
                unset($resultAllNPAI[$k]['APP_UID']);
                $result[] = $resultAllNPAI[$k];
            }

        }
        
    }

    $csv = array();
    $entete = array('Numéro de titre','nom','prenom','voie','type voie','rue','code postal','ville');
    

    $file = '/var/tmp/export_npaiAdresse_'.time().'.csv';
}

//export CSV

if(isset($result) && count($result) > 0) {
    $fp = fopen($file, 'w');
    fputcsv($fp, $entete);
    foreach ($result as $demande) {
        fputcsv($fp, $demande);
    }
    fclose($fp);
}

$csv = file_get_contents($file);
unlink($file);

//OUPUT HEADERS
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);

header('Content-Disposition: attachment; filename="export_npai_'.time().'.csv";' );
header("Content-Transfer-Encoding: binary");

echo $csv;

header("Content-Type: text/plain");

/*$paging = array(
    'success'=> true
 );

//OUTPUT CSV CONTENT
echo G::json_encode($paging);   
*/




?>