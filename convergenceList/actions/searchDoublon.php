<?php 
G::loadClass ( 'pmFunctions' );

$getVar = array();

$uidRequest = $_REQUEST['idR'];

$query = 'SELECT * FROM PMT_DEMANDES WHERE APP_UID = "'.$uidRequest.'"';
$result = executeQuery($query);
if(isset($result) && count($result) > 0) {

    $debug = 1;
    
    $doubl = make_dedoublonage("87479663751a5c3a664a656077060757",$uidRequest,$debug);

}




?>