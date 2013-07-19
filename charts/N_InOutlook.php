<?php
/*
 * Class N_InOutlookPlugin by Nightlies.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 2.0 (2011-11-01)
 * @link http://plugins.nightlies.com
 */

// Verify that the plugin "enterprisePlugin" is installed
if(!class_exists('enterprisePlugin')) {
  return;
}

G::LoadClass('plugin');

class N_InOutlookPlugin extends enterprisePlugin {
  public function __construct($namespace, $fileName = null) {
    $version = self::getPluginVersion($namespace);
    // Setting the attributes
    $results = parent::PMPlugin($namespace, $fileName);
    $config = parse_ini_file(PATH_PLUGINS . 'N_InOutlook' . PATH_SEP . 'pluginConfig.ini');
    $this->sFriendlyName = $config['name'];
    $this->sDescription  = $config['description'];
    $this->sPluginFolder = $config['pluginFolder'];
    $this->sSetupPage    = $config['setupPage'];
    $this->iVersion      = $version;
    $this->aWorkspaces   = null;
    $this->aDependences  = array(array('sClassName' => 'enterprise'), array('sClassName' => 'pmLicenseManager'));
    $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
    return $results;
  }

  public function install() {
  }

  public function setup() {
    if (isset($_SERVER['REQUEST_URI'])) {
      if (isset($_SESSION['__OUTLOOK_CONNECTOR__'])) {
        if ($_SESSION['__OUTLOOK_CONNECTOR__']) {
          if (strpos($_SERVER['REQUEST_URI'], 'cases/casesListExtJsRedirector') !== false) {
            G::header('Location: ../N_InOutlook/casesList?action=todo');
            die();
          }
          /*if (!isset($_SESSION['USER_LOGGED']) &&
              strpos($_SERVER['REQUEST_URI'], 'login/login') === false &&
              strpos($_SERVER['REQUEST_URI'], 'login/authentication') === false) {
            die('Session time out. Please click in other folder to continue.');
          }*/
          if (strpos($_SERVER['REQUEST_URI'], 'cases/cases_Step') !== false) {
            echo '<script type="text/javascript">var parent = {};parent.showCaseNavigatorPanel = function(){};</script>';
          }
        }
      }
    }
  }

  public function enable() {
  }

  public function disable() {
  }

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
}

$oPluginRegistry =& PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('N_InOutlook', __FILE__);
