<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$paths["{stylePath}"]=PATH_SEP.'plugin'.PATH_SEP.'pmReplicator'.PATH_SEP;
$paths["{scriptPath}"]=$paths["{stylePath}"].'javaScripts'.PATH_SEP;
//loading html template
ob_start();
include_once 'dynReplicator.html';
echo str_replace(array_keys($paths), $paths, ob_get_clean());
?>
