<?php
require_once ("classes/model/Configuration.php");
G::LoadClass("plugin");





if (!defined("PATH_PM_ENTERPRISE")) {
    define("PATH_PM_ENTERPRISE", PATH_CORE . "/plugins/enterprise/");
}

if (!defined("PATH_DATA_SITE")) {
    define("PATH_DATA_SITE", PATH_DATA . "sites/" . SYS_SYS . "/");
}

set_include_path(PATH_PM_ENTERPRISE . PATH_SEPARATOR . get_include_path());





class enterprisePlugin extends PMPlugin
{
    public function enterprisePlugin($sNamespace, $sFilename=null)
    {
      $pathPluginTrunk = PATH_PLUGINS . "enterprise";
      if (file_exists($pathPluginTrunk . PATH_SEP . "VERSION")) {
        $VERSION = trim(file_get_contents($pathPluginTrunk . PATH_SEP . "VERSION"));
      }
      else {
        $cmd = sprintf("cd %s && git describe  --long", $pathPluginTrunk);
        if (exec($cmd, $target)) {
          $VERSION = trim (implode(" ", $target));
        }
        else {
          $VERSION = "Development Version";
        }

        //file_put_contents( $pathPluginTrunk . PATH_SEP . "VERSION", $VERSION );
      }

      $res = parent::PMPlugin($sNamespace, $sFilename);
      $this->sFriendlyName = "ProcessMaker Enterprise Edition";
      $this->sDescription  = "ProcessMaker Enterprise Edition $VERSION";
      $this->sPluginFolder = "enterprise";
      $this->sSetupPage    = "../enterprise/pluginsList.php";
      $this->iVersion      = $VERSION;
      $this->iPMVersion    = "2.0.31";
      $this->aDependences  = null;
      $this->aWorkspaces   = null;

      $this->database = "workflow";
      $this->table    = array("ADDONS_STORE", "ADDONS_MANAGER", "LICENSE_MANAGER");

      ///////
      if (!isset($_SESSION["__EE_INSTALLATION__"])) {
        $_SESSION["__EE_INSTALLATION__"] = 0;
      }

      if (!isset($_SESSION["__EE_SW_PMLICENSEMANAGER__"])) {
        $_SESSION["__EE_SW_PMLICENSEMANAGER__"] = 1;
      }

      ///////
      $sw = 1;
      $msgf = null;
      $msgd = null;

      if (file_exists(PATH_CORE . "plugins" . PATH_SEP . "pmLicenseManager.php")) {
        $_SESSION["__EE_INSTALLATION__"] = 1;
        $_SESSION["__EE_SW_PMLICENSEMANAGER__"] = 0;

        $plugin = "pmLicenseManager";
        $this->pluginUninstall($plugin);

        if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin . ".php") || file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin)) {
          $msgf = $msgf . (($msgf != null)? ", " : null) . $plugin . ".php";
          $msgd = $msgd . (($msgd != null)? ", " : null) . $plugin;
          $sw = 0;
        }

        $plugin = "enterprise";
        $this->pluginUninstall($plugin);

        if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin . ".php") || file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin)) {
          $msgf = $msgf . (($msgf != null)? ", " : null) . $plugin . ".php";
          $msgd = $msgd . (($msgd != null)? ", " : null) . $plugin;
          $sw = 0;
        }

        $this->uninstall();
      }
      else {
        $_SESSION["__EE_INSTALLATION__"] = $_SESSION["__EE_INSTALLATION__"] + 1;
      }

      if ($sw == 0) {
        unset($_SESSION["__EE_INSTALLATION__"]);
        unset($_SESSION["__EE_SW_PMLICENSEMANAGER__"]);

        ///////
        $js = "window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main?s=PLUGINS\", \"_top\", \"\");";

        if (substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs") {
          //$js = "parent.window.location.href = \"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main_init?s=PLUGINS\";";
          //$js = "window.location.href = \"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/pluginsImport\";";
          $js = "window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/main\", \"_top\", \"\");";
        }

        ///////
        G::SendMessageText("ProcessMaker Enterprise plug-in can't delete the files \"$msgf\" and directories \"$msgd\" of \"" . (PATH_CORE . "plugins") . "\". Before proceeding with the installation of the plug-in must remove them.", "INFO");

        echo "<script type=\"text/javascript\">" . $js . "</script>";
        exit(0);
      }

      if ($_SESSION["__EE_SW_PMLICENSEMANAGER__"] == 0 && $_SESSION["__EE_INSTALLATION__"] == 2) {
        unset($_SESSION["__EE_INSTALLATION__"]);
        unset($_SESSION["__EE_SW_PMLICENSEMANAGER__"]);

        $this->install();
      }

      ///////
      return $res;
    }

    public function install()
    {
      try {
        G::LoadThirdParty("pear/Archive", "Tar");

        $this->copy("advancedTools" . PATH_SEP . "casesListSetup.js", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js", false, true);
        $this->copy("advancedTools" . PATH_SEP . "casesListSetup.html", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html", false, true);

        ///////
        $tableBackup = $this->table;
        $this->tableBackup($tableBackup);

        $this->sqlExecute(PATH_PLUGINS . "enterprise" . PATH_SEP . "data" . PATH_SEP . "mysql" . PATH_SEP . "schema.sql");

        $this->tableBackupRestore($tableBackup);

        ///////
        $pluginRegistry = &PMPluginRegistry::getSingleton();

        $autoPlugins = glob(PATH_PLUGINS . "enterprise/data/*.tar");
        $autoPluginsA = array();
        foreach ($autoPlugins as $filePath) {
          $plName = basename($filePath);
          //if (!(in_array($plName, $def))) {
            $autoPluginsA[]["sFilename"] = $plName;
          //}
        }

        require_once (PATH_PLUGINS . "enterprise.php");

        $pluginDetail = $pluginRegistry->getPluginDetails("enterprise.php");
        $pluginRegistry->enablePlugin($pluginDetail->sNamespace);

        file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());

        //$_SESSION["___PMEE_INSTALLED___"] = $installedPlugins;
        if (!isset($_SESSION["__ENTERPRISE_INSTALL__"]) && count($autoPluginsA) > 0) {
          $_SESSION["___PMEE_INSTALLED___"] = 1;
        }
        else {
          unset($_SESSION["__ENTERPRISE_INSTALL__"]);
        }
      }
      catch (Exception $oError) {
        throw $oError;
      }
    }

    public function uninstall()
    {
      G::LoadClass("system");
      G::LoadClass("wsTools");

      ///////
      $ee = array();

      $ee[] = "pmLicenseManager";
      $ee[] = "enterprise";

      ///////
      $workspace = System::listWorkspaces();

      foreach ($workspace as $indexWS => $ws) {
        $wsPathDataSite = PATH_DATA . "sites" . PATH_SEP . $ws->name . PATH_SEP;

        if (file_exists($wsPathDataSite . "ee")) {
          $arrayEE = unserialize(file_get_contents($wsPathDataSite . "ee"));

          foreach ($arrayEE as $index => $value) {
            $ee[] = $index;
          }
        }
      }

      $ee = array_unique($ee);

      ///////
      $pluginRegistry = &PMPluginRegistry::getSingleton();

      $pluginRegistry->uninstallPluginWorkspaces($ee);

      $pluginRegistry->unSerializeInstance(file_get_contents(PATH_DATA_SITE . "plugin.singleton"));
    }

    public function setup()
    {
      $urlPart = substr(SYS_SKIN, 0, 2) == 'ux' && SYS_SKIN != 'uxs' ? 'main/login' : 'login/login';

      if (isset($_SESSION["___PMEE_INSTALLED___"])) {
        G::LoadThirdParty("pear/Archive", "Tar");

        unset($_SESSION["___PMEE_INSTALLED___"]);

        $sPath = PATH_DOCUMENT . "input" . PATH_SEP;

        $pluginRegistry = &PMPluginRegistry::getSingleton();

        $autoPlugins = glob(PATH_PLUGINS . "enterprise/data/*.tar");

        $autoPluginsA = array();
        foreach ($autoPlugins as $filePath) {
          $plName = basename($filePath);
          //if (!(in_array($plName, $def))) {
            $autoPluginsA[]["sFilename"] = $plName;
          //}
        }

        $aPlugins = $autoPluginsA;

        foreach ($aPlugins as $aPlugin) {
          $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));

          if ($sClassName == "enterprise") break;

          $oTar = new Archive_Tar(PATH_PLUGINS . "enterprise/data/" . $aPlugin["sFilename"]);
          $oTar->extract(PATH_PLUGINS);

          if (!(class_exists($sClassName))) {
            require_once (PATH_PLUGINS . $sClassName . ".php");
          }

          $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");
          $pluginRegistry->installPlugin($pluginDetail->sNamespace); //error
        }

        $message = "Enterprise Plugin has been correctly installed. Please login again to apply the changes";
        G::SendMessageText($message, "INFO");

        echo "<script type=\"text/javascript\">window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $urlPart . "\", \"_top\", \"\");</script>";
        exit(0);
      }

      if (isset($_SESSION["___PMEE_INSTALLED_LIC___"])) {
        unset($_SESSION["___PMEE_INSTALLED_LIC___"]);

        $message = "A license has been correctly installed. Please login again to complete apply the changes";
        G::SendMessageText($message, "INFO");

        echo "<script type=\"text/javascript\">window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $urlPart . "\", \"_top\", \"\");</script>";
        exit(0);
      }

      //$this->setCompanyLogo("/plugin/enterprise/enterprise.png");
      if (!file_exists(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js")) {
        $this->copy("advancedTools" . PATH_SEP . "casesListSetup.js", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js", false, true);
      }
      if (!file_exists(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html")) {
        $this->copy("advancedTools" . PATH_SEP . "casesListSetup.html", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html", false, true);
      }

      $this->registerMenu("setup", "menuEnterprise.php");

      if (method_exists($this, "registerCss")) {
        $this->registerCss("/plugin/enterprise/enterprise_css");
      } else {
        //Nothing
      }

      //if (!$this->tableIsInstalled()) {
      //  $tableBackup = $this->table;
      //  $this->tableBackup($tableBackup);
      //
      //  $this->sqlExecute(PATH_PLUGINS . "enterprise" . PATH_SEP . "data" . PATH_SEP . "mysql" . PATH_SEP . "schema.sql");
      //
      //  $this->tableBackupRestore($tableBackup);
      //}

      //setup for pmLicenseManager plugin
      //since we are placing pmLicenseManager and EE together.. after register EE, we need setup pmLicenseManager
      //if( !$this->isInstalled()){
      //  $this->install();
      //}
      //$this->registerMenu( 'processmaker', 'menupmLicenseManager.php');
      //$this->registerMenu( 'setup', 'menupmLicenseManagerList.php');
      //$this->registerPmFunction();
      //if(method_exists($this,'registerCss')){
      //  $this->registerCss("/plugin/pmLicenseManager/licmgr_css");
      //}
      include_once ("class.pmLicenseManager.php");  //including the file inside the enterprise folder

      $this->registerTrigger(PM_LOGIN, "enterpriseSystemUpdate");
    }

    public function enable()
    {
      $this->setConfiguration();

      ///////
      $this->copy("advancedTools" . PATH_SEP . "casesListSetup.js", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js", false, true);
      $this->copy("advancedTools" . PATH_SEP . "casesListSetup.html", PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html", false, true);

      ///////
      $tableBackup = $this->table;
      $this->tableBackup($tableBackup);

      $this->sqlExecute(PATH_PLUGINS . "enterprise" . PATH_SEP . "data" . PATH_SEP . "mysql" . PATH_SEP . "schema.sql");

      $this->tableBackupRestore($tableBackup);

      ///////
      $pluginRegistry = &PMPluginRegistry::getSingleton();
      $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . "enterprise/data/default")));
      foreach ($aPlugins as $aPlugin) {
        if ($aPlugin["bActive"]) {
          $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));
          require_once (PATH_PLUGINS . $sClassName . ".php");
          $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");
          if (($pluginDetail) && (isset($pluginDetail->sNamespace))) {
            $pluginRegistry->enablePlugin($pluginDetail->sNamespace);
          }
        }
      }

      file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());
      $licfile = glob(PATH_PLUGINS . "*.dat");

      if ((isset($licfile[0])) && ( is_file($licfile[0]) )) {
        $licfilename = basename($licfile[0]);
        @copy($licfile[0], PATH_DATA_SITE . $licfilename);
        @unlink($licfile[0]);
      }
    }

    public function disable()
    {
      $this->delete(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.js", true);
      $this->delete(PATH_HOME . "engine" . PATH_SEP . "templates" . PATH_SEP . "cases" . PATH_SEP . "casesListSetup.html", true);
      $pluginRegistry = & PMPluginRegistry::getSingleton();
      $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . "enterprise/data/default")));
      $aPluginsE = unserialize(trim(file_get_contents(PATH_DATA_SITE . "ee")));
      $aPlugins = array_merge($aPlugins, $aPluginsE);
      foreach ($aPlugins as $aPlugin) {
        $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));
        require_once (PATH_PLUGINS . $sClassName . ".php");
        $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");
        if ($pluginDetail) {
          $pluginRegistry->disablePlugin($pluginDetail->sNamespace);
        }
      }
      file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());
    }





    public function setConfiguration()
    {
        $confEeUid = "enterpriseConfiguration";

        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);
        $criteria->add(ConfigurationPeer::CFG_UID, "EE");
        $criteria->add(ConfigurationPeer::OBJ_UID, $confEeUid);

        $rsCriteria = ConfigurationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            $conf = new Configuration();

            $data = array("internetConnection" => 1);

            $conf->create(
                array(
                    "CFG_UID"   => "EE",
                    "OBJ_UID"   => $confEeUid,
                    "CFG_VALUE" => serialize($data),
                    "PRO_UID"   => "",
                    "USR_UID"   => "",
                    "APP_UID"   => ""
                )
            );
        }
    }

    public function pluginUninstall($pluginName)
    {
        //define("PATH_PLUGINS", PATH_CORE . "plugins" . PATH_SEP);

        if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php")) {
            require_once (PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php");

            $pluginRegistry = &PMPluginRegistry::getSingleton();

            $pluginDetail = $pluginRegistry->getPluginDetails($pluginName . ".php");

            if ($pluginDetail) {
                $pluginRegistry->enablePlugin($pluginDetail->sNamespace);
                $pluginRegistry->disablePlugin($pluginDetail->sNamespace);

                ///////
                $plugin = new $pluginDetail->sClassName($pluginDetail->sNamespace, $pluginDetail->sFilename);
                //$this->_aPlugins[$pluginDetail->sNamespace] = $plugin;

                if (method_exists($plugin, "uninstall")) {
                    $plugin->uninstall();
                }

                ///////
                file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());
            }

            ///////
            unlink(PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php");

            if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $pluginName)) {
                G::rm_dir(PATH_CORE . "plugins" . PATH_SEP . $pluginName);
            }
        }
    }

    public function registerEE($pluginFile, $pluginVersion)
    {
        if (file_exists(PATH_DATA_SITE . "ee")) {
          $this->systemAvailable = unserialize(trim(file_get_contents(PATH_DATA_SITE . "ee")));
        }

        $this->systemAvailable[$pluginFile]["sFilename"] = $pluginFile . "-" . $pluginVersion . ".tar";
        file_put_contents(PATH_DATA_SITE . "ee", serialize($this->systemAvailable));

        return true;
    }

    public function checkDependencies()
    {
    }

    public function tableBackup($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
    {
      //Database Connections
      $cnn = Propel::getConnection($this->database);
      $stmt = $cnn->createStatement();

      foreach ($tableBackup as $key => $table) {
        $tablebak = $backupPrefix . $table . $backupSuffix;

        //First Search if the Table exists
        $sqlTable = "SHOW TABLES LIKE '$table'";
        $rsTable = $stmt->executeQuery($sqlTable, ResultSet::FETCHMODE_ASSOC);
        if ($rsTable->getRecordCount() > 0) { //Table $table exists, so we can Backup
          //If there are records in $table Backup
          $sqlSelectTable = "SELECT * FROM $table";
          $rsSelectTable = $stmt->executeQuery($sqlSelectTable, ResultSet::FETCHMODE_ASSOC);
          if ($rsSelectTable->getRecordCount() > 0) { //There are records in $table!! Backup!
            //Delete a previous Backup if exists
            $sql = "DROP TABLE IF EXISTS $tablebak;";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            //Create a COPY of $table in $tablebak :: Backup
            $sql = "CREATE TABLE $tablebak SELECT * FROM $table";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            //Delete a previous $table if exists
            $sql = "DROP TABLE IF EXISTS $table;";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          }
        }
      }
    }

    public function tableBackupRestore($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
    {
      //Database Connections
      $cnn = Propel::getConnection($this->database);
      $stmt = $cnn->createStatement();

      foreach ($tableBackup as $key => $table) {
        $tablebak = $backupPrefix . $table . $backupSuffix;

        //First Search if the $tablebak exists
        $sqlTablebak = "SHOW TABLES LIKE '$tablebak'";
        $rsTablebak = $stmt->executeQuery($sqlTablebak, ResultSet::FETCHMODE_ASSOC);
        if ($rsTablebak->getRecordCount() > 0) { //Table $tablebak exists, so we can Restore
          $sqlSelectTablebak = "SELECT * FROM $tablebak";
          $rsSelectTablebak = $stmt->executeQuery($sqlSelectTablebak, ResultSet::FETCHMODE_ASSOC);
          if ($rsSelectTablebak->getRecordCount() > 0) {
            $strTable = str_replace("_", " ", strtolower($table));
            $strTable = str_replace(" ", null, ucwords($strTable));

            require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "$strTable.php");

            while ($rsSelectTablebak->next()) {
              $row = $rsSelectTablebak->getRow();

              //INSERT INTO TABLEN(FIELD1, FIELD2) VALUES('VALUE1', 'VALUE2')
              $oTable = new $strTable();
              $oTable->fromArray($row, BasePeer::TYPE_FIELDNAME); //Fill an object from of the array //Fill attributes
              $oTable->save();
            }
          }

          //Delete Backup
          $sql = "DROP TABLE IF EXISTS $tablebak;";
          $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        }
      }
    }

    /*
    public function tableIsInstalled()
    {
      G::LoadSystem("database_" . DB_ADAPTER);
      $database = new database(DB_ADAPTER, DB_HOST, DB_USER, DB_PASS, DB_NAME);

      $cnn = Propel::getConnection($this->database);
      $stmt = $cnn->createStatement();

      $sw = true;

      foreach ($this->table as $key => $table) {
        $rs = $stmt->executeQuery($database->generateShowTablesLikeSQL($table), ResultSet::FETCHMODE_ASSOC);

        if ($rs->getRecordCount() == 0) {
          $sw = false;
        }
      }

      return ($sw);
    }
    */

    public function sqlExecute($sqlFile)
    {
      $file = fopen($sqlFile, "r");

      if ($file) {
        $line = null;

        while (!feof($file)) {
          $buffer = trim(fgets($file, 4096)); //Read a line.

          if (strlen($buffer) > 0 && $buffer[0] != "#") { //Check for valid lines
            $line = $line . $buffer;

            if ($buffer[strlen($buffer) - 1] == ";") {
              $cnn = Propel::getConnection($this->database);
              $stmt = $cnn->createStatement();
              $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
              $line = null;
            }
          }
        }

        fclose($file);
      }
    }
}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('enterprise', __FILE__); //<- enterprise string must be in single quote, otherwise generate error

//since we are placing pmLicenseManager and EE together.. after register EE, we need to require_once the pmLicenseManager
//if( !defined("PATH_PM_LICENSE_MANAGER") ) {
//  define("PATH_PM_LICENSE_MANAGER", PATH_CORE . "/plugins/pmLicenseManager/");
//}
//set_include_path(
//  PATH_PM_LICENSE_MANAGER.PATH_SEPARATOR.
//  get_include_path()
//);

