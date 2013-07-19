<?php

G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );
G::LoadClass ( 'configuration' );

$c = new Configurations();
$configPage = $c->getConfiguration('additionalTablesList', 'pageSize','',$_SESSION['USER_LOGGED']);
$Config['pageSize'] = isset($configPage['pageSize']) ? $configPage['pageSize'] : 20;

$oHeadPublisher = & headPublisher::getSingleton ();

if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
}

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
require_once PATH_PM_SLA . 'class.pmSLA.php';

$flagExecute = true;
$filReview   = true;
$executeCron = 'Never';

// Comment temporary by warning message
if (file_exists(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log')) {
    $text = file_get_contents(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log');    
    $words = explode(' ',$text);
    if (count($words) > 2) {
        $executeCron = $words['0'] . ' ' . $words['1'];
    }
}

// $oHeadPublisher->assign("aStoreFields", $aStoreFields);
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/labelsplugin', false, true );
//$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportCase', false, true );
//$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaFilters', false, true );
//$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReport', false, true );


$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportFilter', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportFirstLevel', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportSecondLevel', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportThirdLevel', false, true );
$oHeadPublisher->addExtJsScript (PATH_PLUGINS . SYS_COLLECTION . '/js/slaReportForm', false, true );

$oHeadPublisher->assign('FORMATS', $c->getFormats());
$oHeadPublisher->assign('CONFIG', $Config);
$oHeadPublisher->assign('timeCron', $executeCron);
G::RenderPage ('publish', 'extJs');

// <link rel="stylesheet" type="text/css" href="/plugin/pmSLA/pmSla.css" />

?>

<iframe id="exportReport" height="0px" width="0px"></iframe>


