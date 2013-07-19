<?php
if (class_exists("enterprisePlugin")) {
  G::LoadClass("plugin");

  class pmMonitorPlugin extends enterprisePlugin
  {  function pmMonitorPlugin($sNamespace, $sFilename = null)
     {  $pathPluginTrunk = PATH_PLUGINS . "pmMonitor";

        if (file_exists($pathPluginTrunk . PATH_SEP."VERSION")) {
          $version = trim(file_get_contents ( $pathPluginTrunk.PATH_SEP.'VERSION' ));
        }
        else {
          $cmd = sprintf("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
          if (exec($cmd, $target)) {
            $cmd = sprintf("cd %s && git log --decorate | grep '(tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
            $commit = exec($cmd, $dummyTarget);
            $cmd = sprintf("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit );
            exec($cmd, $target);
            $version = implode(" ", $target);
          }
          else{
            $version = "Development Version";
          }
        }

        $res = parent::PMPlugin($sNamespace, $sFilename);
        $config = parse_ini_file(PATH_PLUGINS . "pmMonitor" . PATH_SEP . "pluginConfig.ini");
        $this->sFriendlyName = $config["name"];
        $this->sDescription  = $config["description"];
        $this->sPluginFolder = $config["pluginFolder"];
        $this->sSetupPage    = $config["setupPage"];
        $this->iVersion      = $version;
        $this->aWorkspaces   = null;
        $this->aDependences  = array(array("sClassName" => "enterprise"), array("sClassName" => "pmLicenseManager"));
        $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
        
        return $res;
     }

     function setup()
     {  //$this->registerTrigger(10000, "createCaseFolder");
        $this->registerMenu("setup", "menuMonitorSetup.php");
        
        if (method_exists($this, "registerCss")) {
          $this->registerCss("/plugin/pmMonitor/pmMonitor_css");
          //$this->registerCss("/plugin/pmMonitor/workspaceManagement_css");
        }
     }

     function install()
     {
     }
     
     function enable()
     {
     }
     
     function disable()
     {
     }
  }

  $oPluginRegistry = &PMPluginRegistry::getSingleton();
  $oPluginRegistry->registerPlugin("pmMonitor", __FILE__);
}
?>