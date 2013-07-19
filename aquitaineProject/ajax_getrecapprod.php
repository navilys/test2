<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors',0);

class getrecapprod extends WebResource {

        function get_recapprod() {
                    
                $result = convergence_countCase(2);
                
                $res = json_encode($result);

                return $res;
        }
        function get_recaprmb() {

                $result = convergence_countCaseRmb(2);

                $res = json_encode($result);

                return $res;
        }
}

$o = new getrecapprod($_SERVER['REQUEST_URI'], $_POST);

?>

