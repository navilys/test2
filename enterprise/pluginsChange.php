<?php
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "class.enterpriseUtils.php");





$oPluginRegistry =& PMPluginRegistry::getSingleton();

if ($oHandle = opendir(PATH_PLUGINS)) {
  while (false !== ($file = readdir($oHandle))) {
   if (strpos($file, ".php", 1)) {
      if (($file == $_GET['id']) && ($_GET['status'] == '1')) {
        $oDetails = $oPluginRegistry->getPluginDetails($_GET['id']);
        $oPluginRegistry->disablePlugin($oDetails->sNamespace);
        file_put_contents(PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance());
      }
      if (($file == $_GET['id']) && ($_GET['status'] == '')) {
        require_once PATH_PLUGINS . $_GET['id'];
        $oDetails = $oPluginRegistry->getPluginDetails($_GET['id']);
        $oPluginRegistry->enablePlugin($oDetails->sNamespace);
        $oPluginRegistry->setupPlugins();
        file_put_contents(PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance());
      }
    }
  }
  closedir($oHandle);
}

$jsParentWindow = (EnterpriseUtils::skinIsUx() == 1)? "parent.window" : "parent.parent.window";

echo "<script type=\"text/javascript\">" . $jsParentWindow . ".location.href = \"../" . EnterpriseUtils::getUrlPartSetup() . "?s=\" + parent._NODE_SELECTED;</script>";
exit(0);

//$oPluginRegistry->showArrays();
//G::header('location: pluginsSetup?id=enterprise.php');