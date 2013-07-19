<?php
// ProcessMaker Test Unit Bootstrap

// Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
define('PATH_SEP', '/');

if (!defined('__DIR__')) {
    define ('__DIR__', dirname(__FILE__));
}

//getting the Environment variables
if ( isset($_SERVER['PROCESSMAKER_PATH_TRUNK'])) {
    $relativePath = getenv('PROCESSMAKER_PATH_TRUNK');
} else {
    $relativePath = '/../../../processmaker';
}

if ( isset($_SERVER['PLUGIN_SLA_DB_HOST'])) {
    $pluginSlaDbHost = getenv('PLUGIN_SLA_DB_HOST');
} else {
    $pluginSlaDbHost = 'localhost';
    $_SERVER['PLUGIN_SLA_DB_HOST'] = $pluginSlaDbHost;
}

if ( isset($_SERVER['PLUGIN_SLA_DB_NAME'])) {
    $pluginSlaDbName = getenv('PLUGIN_SLA_DB_NAME');
} else {
    $pluginSlaDbName = 'wf_os';
    $_SERVER['PLUGIN_SLA_DB_NAME'] = $pluginSlaDbName;
}

if ( isset($_SERVER['PLUGIN_SLA_DB_USER'])) {
    $pluginSlaDbUser = getenv('PLUGIN_SLA_DB_USER');
} else {
    $pluginSlaDbUser = 'wf_user';
    $_SERVER['PLUGIN_SLA_DB_USER'] = $pluginSlaDbUser;
}

if ( isset($_SERVER['PLUGIN_SLA_DB_PASS'])) {
    $pluginSlaDbPass = getenv('PLUGIN_SLA_DB_PASS');
} else {
    $pluginSlaDbPass = 'wf_pass';
    $_SERVER['PLUGIN_SLA_DB_PASS'] = $pluginSlaDbPass;
}

// Defining the Home Directory
define('PATH_TRUNK', realpath(__DIR__ . $relativePath) . PATH_SEP);

if (PATH_TRUNK == '/') {
    throw ( new Exception('error variable PATH_TRUNK was not defined, impossible to continue'));
}

define('PATH_PLUGIN', realpath(__DIR__ . '/../') . PATH_SEP);
define('PATH_HOME',  PATH_TRUNK . 'workflow' . PATH_SEP);

//print 'workflow' . "\n";

require  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';

  // Call Gulliver Classes
G::LoadThirdParty('pear/json','class.json');
G::LoadThirdParty('smarty/libs','Smarty.class');


if (!defined ('SYS_LANG')) {
    define ('SYS_LANG', 'en');
}

//initializing Propel
require_once PATH_TRUNK . 'gulliver/thirdparty/propel/Propel.php';
$dsn = 'mysql://' . $pluginSlaDbUser . ':' . $pluginSlaDbPass . '@' . $pluginSlaDbHost . '/' . $pluginSlaDbName .
    '?encoding=utf8';
$dsnRbac = $dsn;
$dsnRp   = $dsn;

global $pro;
$pro ['datasources'] ['workflow'] ['connection'] = $dsn;
$pro ['datasources'] ['workflow'] ['adapter'] = 'mysql';
$pro ['datasources'] ['rbac'] ['connection'] = $dsnRbac;
$pro ['datasources'] ['rbac'] ['adapter'] = 'mysql';
$pro ['datasources'] ['rp'] ['connection'] = $dsnRp;
$pro ['datasources'] ['rp'] ['adapter'] = 'mysql';

$oFile = fopen ( PATH_TRUNK . 'databases.php', 'w');
fwrite ($oFile, '<?php global $pro;return $pro; ?>');
fclose ($oFile);
Propel::init (PATH_TRUNK . 'databases.php');

require_once "PHPUnit/Extensions/Database/TestCase.php";

//adding the plugin path to the PHP include path
set_include_path( PATH_PLUGIN . '/' . PATH_SEPARATOR . get_include_path());

print "Database Connection:" . 'mysql://' . $pluginSlaDbUser .'@' . $pluginSlaDbHost . '/' . $pluginSlaDbName . "\n";

