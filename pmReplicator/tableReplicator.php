<?php
/**
 * Loading Replicator UI
 */
$paths["{stylePath}"]=PATH_SEP.'plugin'.PATH_SEP.'pmReplicator'.PATH_SEP;
$paths["{scriptPath}"]=$paths["{stylePath}"].'javaScripts'.PATH_SEP;
//loading html template
ob_start();
include_once 'tableReplicator.html';
echo str_replace(array_keys($paths), $paths, ob_get_clean());
?>