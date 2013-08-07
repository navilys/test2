<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors',0);

class getrecapprod extends WebResource {

        function get_recapprod($codeOper, $detailChequier = 1) {

        $result = convergence_countCaseToProduct(2, $codeOper, $detailChequier);

        $res = json_encode($result);

                return $res;
        }        
}

$o = new getrecapprod($_SERVER['REQUEST_URI'], $_POST);

?>

