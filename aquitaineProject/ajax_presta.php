<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors',0);

class ajax_presta extends WebResource {

        function search_presta($uid) {
                $res = '';
                $query = 'SELECT NUM_DOSSIER,RAISONSOCIALE,ADRESSE1,TELEPHONE,TELECOPIE,MAIL,VILLE FROM PMT_PRESTATAIRE where STATUT=1 AND SIRET="'.$uid.'"';
                $result = executeQuery($query);
                if (isset($result))
                        $res = json_encode($result[1]);

                return $res;
        }

}

$o = new ajax_presta($_SERVER['REQUEST_URI'], $_POST);

?>

