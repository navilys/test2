<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors',0);

class ajax_etab extends WebResource {

        function search_presta($uid) {
                $res = '';
                $query = 'SELECT RAISONSOCIALE,CONVENTION,VILLE FROM PMT_PRESTATAIRE where STATUT=1 AND CONVENTION ="'.$uid.'"';
                $result = executeQuery($query);
                if (isset($result))
                        $res = json_encode($result[1]);

                return $res;
        }

}

$o = new ajax_etab($_SERVER['REQUEST_URI'], $_POST);

?>

