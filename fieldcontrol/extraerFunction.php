<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

$ruta = "opt/processmaker/plugins/";
$archivo = file($ruta);
$lineas = count($archivo); //contamos los elementos del array, es decir el total de lineas
for($i=0; $i < $lineas; $i++){
echo $archivo[$i];
}

?>