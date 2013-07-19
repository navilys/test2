<?php

$tiempo_inicio = microtime(true);

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
}

require_once PATH_PM_SLA . 'classes/class.pmSlaCron.php';

$oPmSlaCron = new pmSLAClassCron();
G::pr($oPmSlaCron->executeCron());

$tiempo_fin = microtime(true);
echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

