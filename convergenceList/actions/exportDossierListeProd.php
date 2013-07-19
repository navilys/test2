<?php 
G::loadClass ( 'pmFunctions' );
$array = Array();

$items = json_decode($_POST['items'],1);
if (count($items) > 0) {
    
    foreach ($items as $item)
        $uidIn[] = '"'.$item['APP_UID'].'"';
    
    if (count($uidIn) > 0) {
        $query = 'SELECT * FROM PMT_LISTE_PROD WHERE APP_UID IN ('.implode(',',$uidIn).')';
        $lesProd = executeQuery($query);
        
        if (count($lesProd) > 0) {
            
            foreach($lesProd as $prod) {
                $listeDossier[] = $prod['LISTE_DOSSIER'];
            }
            
            if (count($listeDossier) > 0) {
                $queryDossier = 'SELECT NUM_TITRE,ANNULE,REPRODUCTION,PMT_DEMANDES.NUM_DOSSIER,NOM,PRENOM,THEMATIQUE_LABEL,NUMVOIE,TYPEVOIE,NOMVOIE,ZIP,VILLE FROM PMT_DEMANDES JOIN PMT_CHEQUES ON (PMT_CHEQUES.NUM_DOSSIER = PMT_DEMANDES.NUM_DOSSIER) INNER JOIN PMT_VILLE ON ( PMT_VILLE.UID = PMT_DEMANDES.CODEPOSTAL ) WHERE PMT_DEMANDES.NUM_DOSSIER IN ('.implode(',',$listeDossier).') AND PMT_DEMANDES.STATUT != 0';
                $line = executeQuery($queryDossier);
            
                $csv = array();
                $entete = array('Numéro de chèque','Annulé','Nombre de reproduction','Numéro de dossier','Nom','Prénom','Thématique','Voie','Type voie','Rue','Code postal','Ville');

                $file = '/var/tmp/export_listeproduction_'.time().'.csv';
                
                if (count($line) > 0) {
                              
                    //export CSV
                    $fp = fopen($file, 'w');
                    fputcsv($fp, $entete);
                    foreach ($line as $oneline) {
                        fputcsv($fp, $oneline);
                    }
                    fclose($fp);
                    

                    $csv = file_get_contents($file);
                    unlink($file);

                    //OUPUT HEADERS
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false);

                    header('Content-Disposition: attachment; filename="export_listeproduction_'.time().'.csv";' );
                    header("Content-Transfer-Encoding: binary");

                    echo $csv;

                    header("Content-Type: text/plain");
                }
            }
            
        } 
    }

}


?>