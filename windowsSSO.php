<?php
/**
 * The Windows SSO plugin brings ability to use SSO with Active Directory users
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

// Verify that the plugin "enterprisePlugin" is installed
if(!class_exists('enterprisePlugin')) {
  return;
}

// Load dependences
G::LoadClass('plugin');

class windowsSSOPlugin extends enterprisePlugin {

  /**
   * This method initializes the plugin attributes
   *
   * @param String $namespace The namespace of the plugin
   * @param String $filename The filename of the plugin
   * @return String $result
   */
  public function __construct($namespace, $filename = null) {
    $version = self::getPluginVersion($namespace);
    // Setting the attributes
    $result = parent::PMPlugin($namespace, $filename);
    $config = parse_ini_file(PATH_PLUGINS . 'windowsSSO' . PATH_SEP . 'pluginConfig.ini');
    $this->sFriendlyName = $config['name'];
    $this->sDescription  = $config['description'];
    $this->sPluginFolder = $config['pluginFolder'];
    $this->sSetupPage    = $config['setupPage'];
    $this->iVersion      = $version;
    $this->aWorkspaces   = null;
    $this->aDependences  = array(array('sClassName' => 'enterprise'), array('sClassName' => 'pmLicenseManager'));
    $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
    return $result;
  }

  /**
   * The setup function that handles the registration of the menu also
   * checks the current version of PM and register the menu according to that
   *
   * @return void
   */
  public function setup() {
    if (!defined('PM_SINGLE_SIGN_ON')) {
      define('PM_SINGLE_SIGN_ON', 'PM_SINGLE_SIGN_ON');
    }
    $this->registerTrigger(PM_SINGLE_SIGN_ON, 'singleSignOn');
    $this->registerJavascript('authSources/authSourcesList', 'windowsSSO/authSourcesList');
    $this->copyFiles();
  }

  /**
   * The default install method that is called whenever the plugin is installed in ProcessMaker
   * internally calls the method copyInstallFiles since is the only action that is executed
   * @return void
   */
  public function install() {
  }

  /**
   * The default enable method that is called whenever the plugin is enabled in ProcessMaker
   * internally calls the method copyInstallFiles since is the only action that is executed
   *
   * @return void
   */
  public function enable() {
    $this->copyFiles();
  }

  /**
   * The default disable method that is called whenever the plugin is disabled in ProcessMaker
   * internally deletes the copied files so these don't trigger errors about dependencies with these
   *
   * @return void
   */
  public function disable() {
    $binFile  = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'windowsSSO.php';
  	$rbacFile = PATH_RBAC . 'plugins' . PATH_SEP . 'class.windowsSSO.php';
    $this->delete($rbacFile, true);
    $this->delete($binFile, true);
  }

  /**
   * This method get the version of this plugin, when the plugin is packaged in the tar.gz
   * the file "version" in the plugin folder has this information for development purposes,
   * we calculate the version using git commands, because the repository is in GIT
   *
   * @param String $namespace The namespace of the plugin
   * @return String
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

  /**
   * Copy the files in data folder to the specific folders in ProcessMaker core
   *
   * @return void
   */
  private function copyFiles() {
  	$binFile  = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'windowsSSO.php';
  	$rbacFile = PATH_RBAC . 'plugins' . PATH_SEP .'class.windowsSSO.php';
    $this->copy('bin' . PATH_SEP . 'windowsSSO.php',        $binFile,  false, true);
    $this->copy('data' . PATH_SEP . 'class.windowsSSO.php', $rbacFile, false, true);
  }

}

// Register this plugin in the Plugins Singleton

$oPluginRegistry =& PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('windowsSSO', __FILE__);