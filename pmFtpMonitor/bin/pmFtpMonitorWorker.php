<?php

/**
 * pmFtpMonitorWorker.php
 * the ProcessMaker FtpMonitor worker for Gearman infraestructure
 *
 * @package workflow-engine-bin
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
$rootDir = dirname(__FILE__);
$pwd = explode('/', $_SERVER['PWD']);

array_pop($pwd); //bin
array_pop($pwd); //pmFtpMonitor
array_pop($pwd); //plugins
array_pop($pwd); //engine
$pathhome = implode('/', $pwd) . '/';
array_pop($pwd);
$pathTrunk = implode('/', $pwd) . '/';
array_pop($pwd);
$pathOutTrunk = implode('/', $pwd) . '/';

// define constants
if (!defined('PATH_SEP'))
    define('PATH_SEP', '/');
if (!defined('PATH_HOME'))
    define('PATH_HOME', $pathhome);
if (!defined('PATH_TRUNK'))
    define('PATH_TRUNK', $pathTrunk);
if (!defined('PATH_OUTTRUNK'))
    define('PATH_OUTTRUNK', $pathOutTrunk);

// load paths
if (!file_exists(PATH_HOME . 'engine/config/paths.php')) {
    echo "Configuration file '" . PATH_HOME . "engine/config/paths.php' has not been found";
    return false;
}
require_once (PATH_HOME . 'engine/config/paths.php');

G::LoadSystem('templatePower');
require_once ( "propel/Propel.php" );
require_once ( "creole/Creole.php" );

// start Gearman server
echo "Starting pmFtpMonitor Worker\n";
$gmworker = new GearmanWorker();
$gmworker->addServer('localhost');
$gmworker->addFunction("pmFtpMonitor", "pmFtpMonitor");

print "Waiting for job...\n";
while ($gmworker->work()) {
    if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}

function pmFtpMonitor($job) {
    $workload = $job->workload();
    $workspace = $workload;
    $result = "";

    $msg = "";
    if (!@is_dir(PATH_DB . $workspace)) {
        printf("[%s] Workspace $workspace has not been found\n", $job->handle());
        return false;
    } else {
        printf("[%s] pmFtpMonitor in workspace $workspace\n", $job->handle());
        $SYS_SYS = $workspace;
        $msg = generateDatabasesFile(PATH_DB . $SYS_SYS, '/db.php', '/databases.php');
        if (strlen($msg) != 0) {
            printf("[%s] $msg\n", $job->handle());
            return false;
        }
        if (!defined('PATH_DATA_SITE'))
            define('PATH_DATA_SITE', PATH_DB . $SYS_SYS . '/');
        if (!defined('SYS_SYS'))
            define('SYS_SYS', $SYS_SYS);
        if (!defined('SERVER_NAME'))
            define('SERVER_NAME', 'vera.pmos.colosa.net');
        try {
            Propel::init(PATH_DB . $SYS_SYS . '/databases.php');
            $SYS_LANG = 'en';
            G::LoadTranslationObject($SYS_LANG);
            require_once PATH_HOME . 'engine/classes/class.plugin.php';
            require_once PATH_PLUGINS . 'pmFtpMonitor/class.pmFtpMonitor.php';
            $plugin = new pmFtpMonitorClass();
            $plugin->executeSchedulerJob();
            if ($result != -1) {
                printf("[%s] executed pmFtpMonitorClass in %5.3f s.\n", $job->handle(), $result);
            }
        } catch (Exception $oError) {
            printf("[%s] Error: %s\n", $job->handle(), $oError->getMessage());
            return -1;
        }
    }
    return $result;
}

function generateDatabasesFile($path, $dbF, $databasesF) {
    $msg = "n";
    if (!@file_exists($path . $dbF)) { // check if db.php file exists
        $msg = "Database configuration file '$path$dbF' has not been found\n";
    } else {

        if (@file_exists($path . $databasesF)) {
            if (@filemtime($path . $dbF) > @filemtime($path . $databasesF))
                if (is_writable($path))
                    if (is_writable($path . $databasesF))
                        $msg = "y";
                    else
                        $msg = "There are no permissions to rewrite '$path$databasesF'";
                else
                    $msg = "There are no permissions to write in '$path'";
        }
        else {
            if (is_writable($path))
                $msg = "y";
            else
                $msg = "There are no permissions to write in '$path'";
        }
    }
    if ($msg === "n")
        $msg = "";
    else if ($msg === "y") {
        $fp = @fopen($path . $databasesF, 'w');
        if ($fp) {
            require_once $path . $dbF;
            if (defined('DB_ADAPTER') && defined('DB_USER') && defined('DB_PASS') && defined('DB_HOST') && defined('DB_NAME') && defined('DB_RBAC_HOST') && defined('DB_RBAC_NAME') && defined('DB_RBAC_USER') && defined('DB_RBAC_PASS') && defined('DB_REPORT_HOST') && defined('DB_REPORT_NAME') && defined('DB_REPORT_USER') && defined('DB_REPORT_PASS')) {
                $content = "<?php\n\n\$pro = array();\n\n";
                $content .= "\$pro ['datasources']['workflow']['connection'] = '" . DB_ADAPTER . "://" . DB_USER . ":" . DB_PASS . "@" . DB_HOST . "/" . DB_NAME . "?encoding=utf8';\n";
                $content .= "\$pro ['datasources']['workflow']['adapter'] = '" . DB_ADAPTER . "';\n\n";
                $content .= "\$pro ['datasources']['rbac']['connection'] = '" . DB_ADAPTER . "://" . DB_RBAC_USER . ":" . DB_RBAC_PASS . "@" . DB_RBAC_HOST . "/" . DB_RBAC_NAME . "?encoding=utf8';\n";
                $content .= "\$pro ['datasources']['rbac']['adapter'] = '" . DB_ADAPTER . "';\n\n";
                $content .= "\$pro ['datasources']['rp']['connection'] = '" . DB_ADAPTER . "://" . DB_REPORT_USER . ":" . DB_REPORT_PASS . "@" . DB_REPORT_HOST . "/" . DB_REPORT_NAME . "?encoding=utf8';\n";
                $content .= "\$pro ['datasources']['rp']['adapter'] = '" . DB_ADAPTER . "';\n\n";
                $content .= "\$pro ['datasources']['dbarray']['connection'] = 'dbarray://user:pass@localhost/pm_os';\n";
                $content .= "\$pro ['datasources']['dbarray']['adapter']    = 'dbarray';\n\n";
                $content .= "return \$pro;";
                if (!fwrite($fp, $content))
                    $msg = "There was a problem while writing to the file '$path$databasesF'";
                fclose($fp);
            } else
                $msg = "Database configurations aint found in the '$path$dbF' or are incomplete";
        }
        else
            $msg = "Can not open the file '$path$databasesF' for writing";
    }
    return $msg;
}
