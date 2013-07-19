<?php

/**
 * The main plugin class that handles the install, enabling, disabling, setup page call
 *
 * @package plugins.pmFtpMonitor
 */
//since this plugins is part of enterprise plugins, we need the enterprise previously loaded,

if (class_exists("enterprisePlugin")) {
    G::LoadClass('plugin');

    define("PATH_PLUGIN_PMFTPMONITOR", PATH_CORE . "/plugins/pmFtpMonitor/");
    set_include_path(
    PATH_PLUGIN_PMFTPMONITOR . PATH_SEPARATOR .
    get_include_path()
    );

    class pmFtpMonitorPlugin extends enterprisePlugin {

        /**
         * This method initializes the plugin attributes with the data contained in the VERSION
         * file and returns the server response
         * @param String The namespace of the plugin
         * @param String The filename of the plugin
         * @return String
         */
        function pmFtpMonitorPlugin($sNamespace, $sFilename = null) {
            $pathPluginTrunk = PATH_PLUGINS . PATH_SEP . 'pmFtpMonitor';
            if (file_exists($pathPluginTrunk . PATH_SEP . 'VERSION')) {
                $version = trim(file_get_contents($pathPluginTrunk . PATH_SEP . 'VERSION'));
            } else {
                $cmd = sprintf("cd %s && git status | grep 'On branch' | awk '{print $3 $4} ' && git log --decorate | grep '(tag:' | head -1  | awk '{print $3$4} ' ", $pathPluginTrunk);
                if (exec($cmd, $target)) {
                    $cmd = sprintf("cd %s && git log --decorate | grep '(tag:' | head -1  | awk '{print $2} ' ", $pathPluginTrunk);
                    $commit = exec($cmd, $dummyTarget);
                    $cmd = sprintf("echo ' +' && cd %s && git log %s.. --oneline | wc -l && echo ' commits.'", $pathPluginTrunk, $commit);
                    exec($cmd, $target);
                    $version = implode(' ', $target);
                } else {
                    $version = 'Development Version';
                }
            }
            $res = parent::PMPlugin($sNamespace, $sFilename);
            $config = parse_ini_file(PATH_PLUGINS . 'pmFtpMonitor' . PATH_SEP . 'pluginConfig.ini');
            $this->sFriendlyName = $config['name'];
            $this->sDescription = $config['description'];
            $this->sPluginFolder = $config['pluginFolder'];
            $this->sSetupPage = $config['setupPage'];
            $this->iVersion = $version;
            $this->aWorkspaces = null;
            $this->aDependences = array(array("sClassName" => "enterprise"), array("sClassName" => "pmLicenseManager"));
            $this->bPrivate = parent::registerEE($this->sPluginFolder, $this->iVersion);
            return $res;
        }

        /**
         * The setup function that handles the registration of the menu also
         * checks the current version of PM and register the menu according to that
         * @return void
         */
        function setup() {
            $this->registerMenu('setup', 'menuFTPSetup.php');
        }

        /**
         * The default install method that is called whenever the plugin is installed in ProcessMaker
         * internally calls the method copyInstallFiles since is the only action that is executed
         * @return void
         */
        function install() {
            $this->executeSchemaSql();
            $this->copyInstallFiles();
        }

        function executeSchemaSql() {
            $sqlFile = PATH_PLUGINS . 'pmFtpMonitor' . PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql';
            $sqlContents = file_get_contents(PATH_PLUGINS . 'pmFtpMonitor' . PATH_SEP . 'data' . PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql');

            $handle = @fopen($sqlFile, "r"); // Open file form read.
            $line = '';
            if ($handle) {
                while (!feof($handle)) { // Loop til end of file.
                    $buffer = fgets($handle, 4096); // Read a line.
                    if ($buffer[0] != "#" && strlen(trim($buffer)) > 0) { // Check for valid lines
                        $line .= $buffer;
                        $buffer = trim($buffer);
                        if ($buffer [strlen($buffer) - 1] == ';') {
                            //echo "$line <hr>";
                            $con = Propel::getConnection('workflow');
                            $stmt = $con->createStatement();
                            $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
                            $line = '';
                        }
                    }
                }
                fclose($handle); // Close the file.
            }
        }

        /**
         * The default enable method that is called whenever the plugin is enabled in ProcessMaker
         * internally calls the method copyInstallFiles since is the only action that is executed
         * @return void
         */
        function enable() {
            $this->executeSchemaSql();
            $this->copyInstallFiles();
        }

        /**
         * The default disable method that is called whenever the plugin is disabled in ProcessMaker
         * internally deletes the copied files so these don't trigger errors about dependencies with these
         * @return void
         */
        function disable() {
            $this->deleteInstallFiles();
        }

        /**
         * copy the files in data folder to the specific folders in ProcessMaker
         * @return void
         */
        function copyInstallFiles() {
            $fileName = 'pmFtpMonitor.php';
            $dataPath = PATH_PLUGIN_PMFTPMONITOR . 'bin' . PATH_SEP;
            $binPath = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP;
            $this->copy($dataPath . $fileName, $binPath . $fileName, true, true);
        }

        /**
         * delete the files from the specific folders in ProcessMaker in order to disable the plugin
         * @return void
         */
        function deleteInstallFiles() {
            $fileName = 'pmFtpMonitor.php';
            $binPath = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP;
            $this->delete($binPath . $fileName, true);
        }

    }

    //end of class

    /**
     * this script besides defining the plugin class also register this inside ProcessMaker
     */
    $oPluginRegistry = & PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerPlugin('pmFtpMonitor', __FILE__);
}