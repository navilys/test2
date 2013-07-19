<?php


$paths["{stylePath}"] = PATH_SEP.'plugin'.PATH_SEP.SYS_COLLECTION.PATH_SEP.'style'.PATH_SEP;
$paths["{scriptPath}"]= PATH_SEP.'plugin'.PATH_SEP.SYS_COLLECTION.PATH_SEP.'javascript'.PATH_SEP;
$paths["{imagePath}"]= PATH_SEP.'plugin'.PATH_SEP.SYS_COLLECTION.PATH_SEP.'images'.PATH_SEP;
$paths["{newWksPath}"] = PATH_SEP.'sys'.SYS_SYS.PATH_SEP.SYS_LANG.PATH_SEP.SYS_SKIN.PATH_SEP.'install'.PATH_SEP.'newSite?type=blank';

// loading html template
ob_start();
include_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'templates'.PATH_SEP.'BaseWorkspaceList.html');
echo str_replace(array_keys($paths), $paths, ob_get_clean());

?>