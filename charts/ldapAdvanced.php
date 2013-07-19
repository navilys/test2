<?php
/**
 * The main plugin class that handles the install, enabling, disabling, setup page call
 *
 * @package plugins.ldapAdvanced
 */

 //since this plugins is part of enterprise plugins, we need the enterprise previously loaded,


if(class_exists("enterprisePlugin")) {

  G::LoadClass('plugin');

  class ldapAdvancedPlugin extends enterprisePlugin {
   /**
    * This method initializes the plugin attributes with the data contained in the VERSION
    * file and returns the server response
    * @param String The namespace of the plugin
    * @param String The filename of the plugin
    * @return String
    */
    function __construct($sNamespace, $sFilename = null) {
      $version = self::getPluginVersion($sNamespace);
      // setting the attributes
      $res = parent::PMPlugin($sNamespace, $sFilename);
      $config = parse_ini_file(PATH_PLUGINS .'ldapAdvanced'.PATH_SEP .'pluginConfig.ini');
      $this->sFriendlyName = $config['name'];
      $this->sDescription  = $config['description'];
      $this->sPluginFolder = $config['pluginFolder'];
      $this->sSetupPage    = $config['setupPage'];
      $this->iVersion      = $version;
      $this->aWorkspaces = null;
      $this->aDependences  = array(array("sClassName"=>"enterprise"),array("sClassName"=>"pmLicenseManager"));
      $this->bPrivate=parent::registerEE($this->sPluginFolder,$this->iVersion);
      $this->backupTables=array("KT_APPLICATION","KT_PROCESS","KT_DOCUMENT","KT_DOC_TYPE","KT_FIELDS_MAP","KT_CONFIG");
      return $res;
    }

    /**
     * The default install method that is called whenever the plugin is installed in ProcessMaker
     * internally calls the method copyInstallFiles since is the only action that is executed
     * @return void
     */
    function install() {
       $this->copyInstallFiles();
    }

    /**
     * The setup function that handles the registration of the menu also
     * checks the current version of PM and register the menu according to that
     * @return void
     */
    function setup() {
      // overwriting with the predefined plugin methods
      $this->copyInstallFiles();
      $this->registerJavascript('authSources/authSourcesList', 'ldapAdvanced/authSourcesList');
    }

    /**
     * The default enable method that is called whenever the plugin is enabled in ProcessMaker
     * internally calls the method copyInstallFiles since is the only action that is executed
     * @return void
     */
    function enable() {
      // overwriting the predefined functions
      $this->copyInstallFiles();
    }

    /**
     * The default disable method that is called whenever the plugin is disabled in ProcessMaker
     * internally deletes the copied files so these don't trigger errors about dependencies with these
     * @return void
     */
    function disable() {
      // erasing the created files
    	$rbacFile = PATH_RBAC . 'plugins' . PATH_SEP .'class.ldapAdvanced.php';
    	$binFile  = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'ldapadvanced.php' ;
      $this->delete( $rbacFile, true);
      $this->delete( $binFile,  true);
    }

   /**
    * copy the files in data folder to the specific folders in ProcessMaker
    * @return void
    */
    function copyInstallFiles() {
    	$dataPath = 'data' . PATH_SEP ;
    	$servicesPath = 'services' . PATH_SEP ;
    	$rbacFile = PATH_RBAC . 'plugins' . PATH_SEP .'class.ldapAdvanced.php';
    	$binFile  = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'ldapadvanced.php' ;

      $this->copy( 'class.ldapAdvanced.php',           $rbacFile, false, true);
      $this->copy( 'services/ldapadvanced.php',        $binFile,  false, true);
    }

    function getPluginVersion($sNamespace) {

      $pathPluginTrunk= PATH_PLUGINS . PATH_SEP . $sNamespace;
      if ( file_exists ( $pathPluginTrunk . PATH_SEP . 'VERSION' ) ){
        $version = trim(file_get_contents ( $pathPluginTrunk .PATH_SEP .'VERSION' ));
      }
      else {
        $cmd = sprintf	("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
        if ( exec ( $cmd , $target) ) {
          $cmd = sprintf	("cd %s && git log --decorate | grep 'tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
          $commit = exec ( $cmd , $dummyTarget);
          $version = $target;
          if ( $commit != '' ) {
            $cmd = sprintf	("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit );
            exec ( $cmd , $target) ;
            $version = implode(' ', $target) ;
          }
        }
        else{
          $version = 'Development Version' ;
        }
      }
      return $version;
    }

  }//end of class

  /**
   * this script besides defining the plugin class also register this inside ProcessMaker
   */

  $oPluginRegistry =& PMPluginRegistry::getSingleton();
  $oPluginRegistry->registerPlugin('ldapAdvanced', __FILE__);

}
