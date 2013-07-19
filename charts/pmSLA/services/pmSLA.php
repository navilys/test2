<?php

ini_set('memory_limit', '128M');

set_include_path( PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path() );
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA ', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
}

//error_reporting(0);
if (!defined('SYS_LANG')) {
    define('SYS_LANG', 'en');
}

if (!defined('PATH_HOME')) {
    if ( !defined('PATH_SEP') ) {
        define('PATH_SEP', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? '\\' : '/');
    }
    $docuroot = explode(PATH_SEP, str_replace('engine' . PATH_SEP . 'methods' . PATH_SEP . 'services', '',
        dirname(__FILE__)));
    array_pop($docuroot);
    array_pop($docuroot);
    $pathhome = implode(PATH_SEP, $docuroot) . PATH_SEP;
    //try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
    //in a normal installation you don't need to change it.
    array_pop($docuroot);
    $pathTrunk = implode(PATH_SEP, $docuroot) . PATH_SEP ;
    array_pop($docuroot);
    $pathOutTrunk = implode( PATH_SEP, $docuroot) . PATH_SEP ;
    // to do: check previous algorith for Windows  $pathTrunk = "c:/home/";

    define('PATH_HOME',     $pathhome);
    define('PATH_TRUNK',    $pathTrunk);
    define('PATH_OUTTRUNK', $pathOutTrunk);

    //***************** In this file we cant to get the PM paths , RBAC Paths and Gulliver Paths  *********************
    require_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
    //***************** In this file we cant to get the PM definitions  ***********************************************
    require_once (PATH_HOME . PATH_SEP . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php');
    //require_once (PATH_THIRDPARTY . 'krumo' . PATH_SEP . 'class.krumo.php');
    //***************** Call Gulliver Classes **************************
    //G::LoadThirdParty('pear/json','class.json');
    //G::LoadThirdParty('smarty/libs','Smarty.class');

    G::LoadThirdParty('pear/json','class.json');
    G::LoadThirdParty('smarty/libs','Smarty.class');
    G::LoadSystem('error');
    G::LoadSystem('dbconnection');
    G::LoadSystem('dbsession');
    G::LoadSystem('dbrecordset');
    G::LoadSystem('dbtable');
    G::LoadSystem('rbac' );
    G::LoadSystem('publisher');
    G::LoadSystem('templatePower');
    G::LoadSystem('xmlDocument');
    G::LoadSystem('xmlform');
    G::LoadSystem('xmlformExtension');
    G::LoadSystem('form');
    G::LoadSystem('menu');
    G::LoadSystem("xmlMenu");
    G::LoadSystem('dvEditor');
    G::LoadSystem('table');
    G::LoadSystem('pagedTable');
    require_once ( "propel/Propel.php" );
    require_once ( "creole/Creole.php" );
}

//******* main program ************************************************************************************************

require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Event.php';
require_once 'classes/model/AppEvent.php';
require_once 'classes/model/CaseScheduler.php';
//G::loadClass('pmScript');

//default values
$bCronIsRunning = false;
$sLastExecution = '';
if ( file_exists(PATH_DATA . 'cron') ) {
    $aAux = unserialize( trim( @file_get_contents(PATH_DATA . 'cron')) );
    $bCronIsRunning = (boolean)$aAux['bCronIsRunning'];
    $sLastExecution = $aAux['sLastExecution'];
} else {
    //if not exists the file, just create a new one with current date
    @file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' => '1',
        'sLastExecution' => date('Y-m-d H:i:s'))));
}

if (!defined('SYS_SYS')) {
    $sObject = $argv[1];
    $sNow    = $argv[2];
    $sFilter = '';

    for ($i=3; $i<count($argv); $i++) {
        $sFilter .= ' '.$argv[$i];
    }

    $oDirectory = dir(PATH_DB);

    if (is_dir(PATH_DB . $sObject)) {
        //saveLog ( 'main', 'action', "checking folder " . PATH_DB . $sObject );
        if (file_exists(PATH_DB . $sObject . PATH_SEP . 'db.php')) {

            define('SYS_SYS', $sObject);

            include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths_installed.php');
            include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths.php');

            //***************** PM Paths DATA **************************
            define( 'PATH_DATA_SITE',                 PATH_DATA      . 'sites/' . SYS_SYS . '/');
            define( 'PATH_DOCUMENT',                  PATH_DATA_SITE . 'files/' );
            define( 'PATH_DATA_MAILTEMPLATES',        PATH_DATA_SITE . 'mailTemplates/' );
            define( 'PATH_DATA_PUBLIC',               PATH_DATA_SITE . 'public/' );
            define( 'PATH_DATA_REPORTS',              PATH_DATA_SITE . 'reports/' );
            define( 'PATH_DYNAFORM',                  PATH_DATA_SITE . 'xmlForms/' );
            define( 'PATH_IMAGES_ENVIRONMENT_FILES',  PATH_DATA_SITE . 'usersFiles'.PATH_SEP);
            define( 'PATH_IMAGES_ENVIRONMENT_USERS',  PATH_DATA_SITE . 'usersPhotographies'.PATH_SEP);

            if (is_file(PATH_DATA_SITE.PATH_SEP.'.server_info')) {
                $SERVER_INFO = file_get_contents(PATH_DATA_SITE.PATH_SEP.'.server_info');
                $SERVER_INFO = unserialize($SERVER_INFO);
                define( 'SERVER_NAME',  $SERVER_INFO ['SERVER_NAME']);
                define( 'SERVER_PORT',  $SERVER_INFO ['SERVER_PORT']);
            } else {
                eprintln("WARNING! No server info found!", 'red');
            }

            $sContent = file_get_contents(PATH_DB . $sObject . PATH_SEP . 'db.php');

            $sContent = str_replace('<?php', '', $sContent);
            $sContent = str_replace('<?', '', $sContent);
            $sContent = str_replace('?>', '', $sContent);
            $sContent = str_replace('define', '', $sContent);
            $sContent = str_replace("('", "$", $sContent);
            $sContent = str_replace("',", '=', $sContent);
            $sContent = str_replace(");", ';', $sContent);

            eval($sContent);
            $dsn = $DB_ADAPTER . '://' . $DB_USER . ':' . $DB_PASS . '@' . $DB_HOST . '/' . $DB_NAME;
            $dsnRbac = $DB_ADAPTER . '://' . $DB_RBAC_USER . ':' . $DB_RBAC_PASS . '@' . $DB_RBAC_HOST . '/' .
                $DB_RBAC_NAME;
            $dsnRp = $DB_ADAPTER . '://' . $DB_REPORT_USER . ':' . $DB_REPORT_PASS . '@' . $DB_REPORT_HOST . '/' .
                $DB_REPORT_NAME;
            switch ($DB_ADAPTER) {
                case 'mysql':
                    $dsn     .= '?encoding=utf8';
                    $dsnRbac .= '?encoding=utf8';
                    break;
                case 'mssql':
                    break;
                default:
                    break;
            }
            $pro['datasources']['workflow']['connection'] = $dsn;
            $pro['datasources']['workflow']['adapter'] = $DB_ADAPTER;
            $pro['datasources']['rbac']['connection'] = $dsnRbac;
            $pro['datasources']['rbac']['adapter'] = $DB_ADAPTER;
            $pro['datasources']['rp']['connection'] = $dsnRp;
            $pro['datasources']['rp']['adapter'] = $DB_ADAPTER;
            $oFile = fopen(PATH_CORE . 'config/_databases_.php', 'w');
            fwrite($oFile, '<?php global $pro;return $pro; ?>');
            fclose($oFile);
            Propel::init(PATH_CORE . 'config/_databases_.php');


            eprintln("Processing workspace: " . $sObject, 'green');
            try {
            } catch (Exception $e) {
                echo  $e->getMessage();
                eprintln("Probelm in workspace: " . $sObject.' it was ommited.', 'red');
            }
            eprintln();
        }
    }
    unlink(PATH_CORE . 'config/_databases_.php');
}

require_once 'classes/class.pmSlaCron.php';

