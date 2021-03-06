<?php
G::LoadClass("plugin");

class ProductionAS400Plugin extends PMPlugin
{
    
  public function ProductionAS400Plugin($sNamespace, $sFilename = null)
  {
    $res = parent::PMPlugin($sNamespace, $sFilename);
    $this->sFriendlyName = "ProductionAS400 Plugin";
    $this->sDescription  = "Autogenerated plugin for class ProductionAS400";
    $this->sPluginFolder = "ProductionAS400";
    $this->sSetupPage    = "setup";
    $this->iVersion      = 1.8;
    //$this->iPMVersion    = 2425;
    $this->aWorkspaces   = null;
    //$this->aWorkspaces = array("os");
    
 	if(method_exists($this,'registerCss'))
    {
        $this->registerCss("/plugin/ProductionAS400/productionCss");
    }
    else
    {
    	die('yesss');
    }
    
    return $res;
  }

  public function setup()
  {
    $this->registerPmFunction();
    $this->registerMenu("processmaker", "menuProductionAS.php");
    
  }

  public function install()
  {
  }
  
  public function enable()
  {
    
  }

  public function disable()
  {
    
  }
  
}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin("ProductionAS400", __FILE__);
