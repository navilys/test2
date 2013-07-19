<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors',0);

class ajax_ville extends WebResource {

        function search_ville($codePostal) {
                $res = '';
                $query = 'SELECT * FROM PMT_VILLE where ZIP LIKE "%'.$codePostal.'%"';
                $result = executeQuery($query);
                /*var_dump($result);
                die();*/
                if (isset($result))
                        $res = $result[1]['UID'];

                return $res;
        }

}

$o = new ajax_ville($_SERVER['REQUEST_URI'], $_POST);

?>

