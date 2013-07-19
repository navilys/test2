<?php
// Verify that the plugin "enterprisePlugin" is installed
if(!class_exists("enterprisePlugin")) {
  return;
}

G::LoadClass("plugin");

class sigplusPlugin extends enterprisePlugin
{  function sigplusPlugin($namespace, $filename = null)
   {  $version = self::getPluginVersion($namespace);
      
      $res = parent::PMPlugin($namespace, $filename);
      $config = parse_ini_file(PATH_PLUGINS . "sigplus" . PATH_SEP . "pluginConfig.ini");
      $this->sFriendlyName = $config["name"];
      $this->sDescription  = $config["description"];
      $this->sPluginFolder = $config["pluginFolder"];
      $this->sSetupPage    = $config["setupPage"];
      $this->iVersion      = $version;
      $this->aWorkspaces   = null;
      $this->aDependences  = array(array("sClassName" => "enterprise"), array("sClassName" => "pmLicenseManager"));
      $this->bPrivate      = parent::registerEE($this->sPluginFolder, $this->iVersion);
      
      $this->database = "workflow";
      $this->table    = array("SIGPLUS_SIGNERS");

      return $res;
   }

   function setup()
   {  if (!$this->isInstalled()) {
        $this->install();
      }

      $this->registerStep("8604949424b13e60108f772041214042", "stepsigplus", "Digital Sign for Output Documents", "../sigplus/sigplusSigners/sigplusSignersEdit");
      $this->registerPmFunction();
   }

   function enable() {
   }

   function disable() {
   }

   function install()
   {  $tableBackup = $this->table;
      $this->tableBackup($tableBackup);

      $sqlFile = PATH_PLUGINS . "sigplus". PATH_SEP . "data" . PATH_SEP . "mysql" . PATH_SEP . "schema.sql";
      $file = fopen($sqlFile, "r");

      if ($file) {
        $line = null;

        while (!feof($file)) {
          $buffer = trim(fgets($file, 4096)); //Read a line.

          if (strlen($buffer) > 0 && substr($buffer, 0, 2) != "--") { //Check for valid lines
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
}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin("sigplus", __FILE__);
?>