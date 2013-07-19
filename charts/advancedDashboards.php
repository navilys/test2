<?php
/**
 * The Advanced Dashboards add new type of Dashlets which can be used on the
 * Dashboard module
 *
 * @author Victor Saisa <victor at colosa dot com>
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.advancedDashboards
 * @copyright Copyright (C) 2004 - 2012 Colosa Inc.
 */

// Verify that the plugin "enterprisePlugin" is installed
if(!class_exists('enterprisePlugin')) {
  return;
}

// Load dependences
G::LoadClass('plugin');

class advancedDashboardsPlugin extends enterprisePlugin {

  var $dashletsUids;

  public function __construct($namespace, $filename = null) {
    $result              = parent::PMPlugin($namespace, $filename);
    $config              = parse_ini_file(PATH_PLUGINS . 'advancedDashboards' . PATH_SEP . 'pluginConfig.ini');
    $this->sFriendlyName = $config['name'];
    $this->sDescription  = $config['description'];
    $this->sPluginFolder = $config['pluginFolder'];
    $this->sSetupPage    = $config['setupPage'];
    $this->iVersion      = self::getPluginVersion($namespace);
    $this->aWorkspaces   = null;
    $this->aDependences  = array(array('sClassName' => 'enterprise'), array('sClassName' => 'pmLicenseManager'));
    $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
    $this->dashletsUids  = array(array('DAS_UID' => '00000000000000000000000000010001',
                                       'DAS_CLASS' => 'dashletCasesDrillDown',
                                       'DAS_TITLE' => 'Cases Drill Down',
                                       'DAS_DESCRIPTION' => 'Cases Drill Down',
                                       'DAS_VERSION' => '1.0',
                                       'DAS_CREATE_DATE' => date('Y-m-d'),
                                       'DAS_UPDATE_DATE' => date('Y-m-d')), // Cases Drill Down
                                 array('DAS_UID' => '00000000000000000000000000010002',
                                       'DAS_CLASS' => 'dashletCasesByDrillDown',
                                       'DAS_TITLE' => 'Cases Drill Down By Category',
                                       'DAS_DESCRIPTION' => 'Cases Drill Down By Category',
                                       'DAS_VERSION' => '1.0',
                                       'DAS_CREATE_DATE' => date('Y-m-d'),
                                       'DAS_UPDATE_DATE' => date('Y-m-d')), // Cases Drill Down Filtered By
                                 array('DAS_UID' => '00000000000000000000000000010003',
                                       'DAS_CLASS' => 'dashletCustomDrillDown',
                                       'DAS_TITLE' => 'Custom Drill Down',
                                       'DAS_DESCRIPTION' => 'Custom Drill Down',
                                       'DAS_VERSION' => '1.0',
                                       'DAS_CREATE_DATE' => date('Y-m-d'),
                                       'DAS_UPDATE_DATE' => date('Y-m-d')), // Custom Drill Down
                                 array('DAS_UID' => '00000000000000000000000000010004',
                                       'DAS_CLASS' => 'dashletPmTables',
                                       'DAS_TITLE' => 'PM Table',
                                       'DAS_DESCRIPTION' => 'PM Table',
                                       'DAS_VERSION' => '1.0',
                                       'DAS_CREATE_DATE' => date('Y-m-d'),
                                       'DAS_UPDATE_DATE' => date('Y-m-d')) // PM Tables
                                );
    return $result;
  }

  public function setup() {
    $this->registerDashlets();
  }

  public function install() {
    // Nothing to do
  }

  public function enable() {
    $this->checkRecords();
  }

  public function disable() {
    $this->deleteRecords();
  }

  /**
   * This method get the version of this plugin, when the plugin is packaged in the tar.gz
   * the file "version" in the plugin folder has this information for development purposes,
   * we calculate the version using git commands, because the repository is in GIT
   *
   * @param String $namespace The namespace of the plugin
   * @return String $version
   */
  private static function getPluginVersion($namespace) {
    $pathPluginTrunk = PATH_PLUGINS . PATH_SEP . $namespace;
    if (file_exists($pathPluginTrunk . PATH_SEP . 'VERSION')) {
      $version = trim(file_get_contents($pathPluginTrunk . PATH_SEP . 'VERSION'));
    }
    else {
      $cmd = sprintf("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
      if (exec($cmd , $target)) {
        $cmd = sprintf("cd %s && git log --decorate | grep 'tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
        $commit = exec($cmd , $dummyTarget);
        $version = $target;
        if ($commit != '') {
          $cmd = sprintf("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit);
          exec($cmd , $target) ;
          $version = implode(' ', $target);
        }
        else
          $version = implode(' ', $target);
      }
      else{
        $version = 'Development Version';
      }
    }
    return $version;
  }

  private function checkRecords()
  {
    require_once ("classes/model/Dashlet.php");
    
    $connection = Propel::getConnection(DashletPeer::DATABASE_NAME);
    
    foreach ($this->dashletsUids as $dashletData) {
      $dashlet = new Dashlet();
      
      if (is_null($dashlet->load($dashletData["DAS_UID"]))) {
        $dashlet->fromArray($dashletData, BasePeer::TYPE_FIELDNAME);
        $connection->begin();
        $dashlet->save();
        $connection->commit();
      }
    }
  }

  private function deleteRecords()
  {
    require_once ("classes/model/Dashlet.php");
    require_once ("classes/model/DashletInstance.php");
    
    foreach ($this->dashletsUids as $dashletData) {
      $criteria = new Criteria("workflow");
      $criteria->add(DashletPeer::DAS_UID, $dashletData["DAS_UID"]);
      DashletPeer::doDelete($criteria);
      
      //$criteria = new Criteria("workflow");
      //$criteria->add(DashletInstancePeer::DAS_UID, $dashletData["DAS_UID"]);
      //DashletInstancePeer::doDelete($criteria);
    }
  }

}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('advancedDashboards', __FILE__);
