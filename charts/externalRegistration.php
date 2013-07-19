<?php
/**
 * The External Registration plugin allows to sending invitations to external users to
 * register in the System and then to participate in the processes of it.
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @author Marco Antonio Nina <marcoantonionina at colosa dot com>
 * @package plugins.externalRegistration
 * @copyright Copyright (C) 2004 - 2012 Colosa Inc.
 */

// Verify that the plugin "enterprisePlugin" is installed
if(!class_exists('enterprisePlugin')) {
  return;
}

// Load dependences
G::LoadClass('plugin');

class externalRegistrationPlugin extends enterprisePlugin {

  public function __construct($namespace, $filename = null) {
    $result              = parent::PMPlugin($namespace, $filename);
    $config              = parse_ini_file(PATH_PLUGINS . 'externalRegistration' . PATH_SEP . 'pluginConfig.ini');
    $this->sFriendlyName = $config['name'];
    $this->sDescription  = $config['description'];
    $this->sPluginFolder = $config['pluginFolder'];
    $this->sSetupPage    = $config['setupPage'];
    $this->iVersion      = self::getPluginVersion($namespace);
    $this->aWorkspaces   = null;
    $this->aDependences  = array(array('sClassName' => 'enterprise'), array('sClassName' => 'pmLicenseManager'));
    $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
    return $result;
  }

  public function setup() {
    $this->registerMenu('setup', 'externalRegistrationMenu.php');
    if (method_exists($this, "registerCss")) {
      $this->registerCss('/plugin/externalRegistration/externalRegistration_css');
    }
    $this->registerPmFunction();
  }

  public function install() {
  }

  public function enable() {
    $this->checkTables();
  }

  public function disable() {
    // Nothing to do for now
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

  public function checkTables() {
    $con = Propel::getConnection('workflow');
    $stmt = $con->createStatement();
    // setting the path of the sql schema files
    $filenameSql = PATH_PLUGINS . 'externalRegistration/data/schema.sql';
    // checking the existence of the schema file
    if (!file_exists($filenameSql)) {
      throw new Exception("File data/schema.sql doesn't exists");
    }
    // exploding the sql query in an array
    $sql = explode(';', file_get_contents($filenameSql));

    $stmt->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
    // executing each query stored in the array
    foreach ($sql as $sentence) {
      if (trim($sentence) != '') {
        $stmt->executeQuery($sentence);
      }
    }
  }
}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('externalRegistration', __FILE__);
