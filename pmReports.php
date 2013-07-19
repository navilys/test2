<?php
if (class_exists("enterprisePlugin")) {
  G::LoadClass("plugin");
  if (!defined("PATH_PLUGIN_PM_SIMPLE_REPORTS")) {
    define("PATH_PLUGIN_PM_SIMPLE_REPORTS", PATH_CORE . "/plugins/pmReports/");
    set_include_path(
    PATH_PLUGIN_PM_SIMPLE_REPORTS.PATH_SEPARATOR.
    get_include_path()
    );
  }

  class pmReportsPlugin extends enterprisePlugin
  {  function pmReportsPlugin($sNamespace, $sFilename = null)
     {  $pathPluginTrunk = PATH_PLUGINS . "pmReports";

        if (file_exists($pathPluginTrunk . PATH_SEP . "VERSION")) {
          $version = trim(file_get_contents($pathPluginTrunk . PATH_SEP . "VERSION"));
        }
        else {
          $cmd = sprintf("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
          if (exec($cmd, $target)) {
            $cmd = sprintf("cd %s && git log --decorate | grep '(tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
            $commit = exec ( $cmd , $dummyTarget);
            $cmd = sprintf("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit );
            exec($cmd, $target) ;
            $version = implode(" ", $target) ;
          }
          else{
            $version = "Development Version";
          }
        }

        $res = parent::PMPlugin($sNamespace, $sFilename);
        $config = parse_ini_file(PATH_PLUGINS . "pmReports" . PATH_SEP . "pluginConfig.ini");
        $this->sFriendlyName = $config["name"];
        $this->sDescription  = $config["description"];
        $this->sPluginFolder = $config["pluginFolder"];
        $this->sSetupPage    = $config["setupPage"];
        $this->iVersion      = $version;
        $this->aWorkspaces   = null;
        $this->aDependences  = array(array("sClassName" => "enterprise"), array("sClassName" => "pmLicenseManager"));
        $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
        
        $this->database = "workflow";
        $this->table    = array("PM_REPORT", "PM_REPORT_PERMISSIONS");
        
        return $res;
     }

     function setup()
     {  if (!$this->isInstalled()) {
          $this->install();
        }

        //$this->registerMenu("processes", "rptMenu.php");
        $this->registerMenu("cases", "rptCasesMenu.php");
        $this->registerMenu("setup", "setReports.php");
        if (method_exists($this, "registerCss")) {
          $this->registerCss("/plugin/pmReports/pmReports_css");
        }
        //$this->redirectLogin("PROCESSMAKER_PMREPORTS", "users/users_List");
     }

     function enable()
     {
     }
      
     function disable()
     {
     }

     function install()
     {  //$RBAC = RBAC::getSingleton() ;
        //$RBAC->initRBAC();
        //$roleData = array();
        //$roleData['ROL_UID'] = G::GenerateUniqueId();
        //$roleData['ROL_PARENT'] = '';
        //$roleData['ROL_SYSTEM'] = '00000000000000000000000000000002';
        //$roleData['ROL_CODE'] = 'PROCESSMAKER_PMREPORTS';
        //$roleData['ROL_CREATE_DATE'] = date('Y-m-d H:i:s');
        //$roleData['ROL_UPDATE_DATE'] = date('Y-m-d H:i:s');
        //$roleData['ROL_STATUS'] = '1';
        //$RBAC->createRole ( $roleData );
        //$RBAC->createPermision ('PM_PMREPORTS' );

        $tableBackup = $this->table;
        $this->tableBackup($tableBackup);

        $sqlFile = PATH_PLUGINS . "pmReports" . PATH_SEP . "data" . PATH_SEP . "mysql" . PATH_SEP . "schema.sql";
        $file = fopen($sqlFile, "r");
        
        if ($file) {
          $line = null;

          while (!feof($file)) {
            $buffer = trim(fgets($file, 4096)); //Read a line.
            
            if (strlen($buffer) > 0 && substr($buffer, 0, 2) != "--") { // Check for valid lines
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

        $this->tableBackupRestore($tableBackup);
        //$this->tableUpdate();
     }

     function tableBackup($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
     {  //Database Connections
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
              //Delete a Prev. Backup if exists
              $sql = "DROP TABLE IF EXISTS $tablebak;";
              $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            
              //Create a COPY of $table in $tablebak :: Backup
              $sql = "CREATE TABLE $tablebak SELECT * FROM $table";
              $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            }
          }
        }
     }

     function tableBackupRestore($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
     {  //Database Connections
        $cnn = Propel::getConnection($this->database);
        $stmt = $cnn->createStatement();

        foreach ($tableBackup as $key => $table) {
          $tablebak = $backupPrefix . $table . $backupSuffix;
          //First Search if the $tablebak exists
          $sqlTablebak = "SHOW TABLES LIKE '$tablebak'";
          $rsTablebak = $stmt->executeQuery($sqlTablebak, ResultSet::FETCHMODE_ASSOC);
          if ($rsTablebak->getRecordCount() > 0) { //Table $tablebak exists, so we can Restore
            //Delete a Prev. $table if exists
            $sql = "DROP TABLE IF EXISTS $table;";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            //Rename $tablebak to $table in $tablebak :: Restore
            $sql = "RENAME TABLE '$tablebak' TO '$table'";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          }
        }
     }

     function isInstalled()
     {  $cnn = Propel::getConnection($this->database);
        $stmt = $cnn->createStatement();
      
        $sw = true;
      
        foreach ($this->table as $key => $table) {
          //First Search if the Table exists
          $sql = "SHOW TABLES LIKE '$table'";
          $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
          if ($rs->getRecordCount() == 0) {
            $sw = false;
          }
        }
      
        return ($sw);
     }
  }

  $oPluginRegistry = &PMPluginRegistry::getSingleton();
  $oPluginRegistry->registerPlugin("pmReports", __FILE__);

  if (class_exists("headPublisher")) {
    $oHeadPublisher = &headPublisher::getSingleton();
    $oHeadPublisher->addScriptFile("/plugin/pmReports/js/pmReports.js", 1);
  }
}
?>