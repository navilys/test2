<?php
$tiempo_inicio = microtime(true);

G::LoadClass("dates");
$dates = new dates();

$tiempo_inicio = microtime(true);
$iniDate = '2012-09-01 10:15:00';
$duration = 1;
$formatDuration = 'HOURS';

$iDueDate = $dates->calculateDate($iniDate, $duration,
    $formatDuration,
    "working",
    null,
    '359728002502a792a568a54012179002'
);

G::pr($iDueDate['DUE_DATE']);

$tiempo_fin = microtime(true);
echo "HUGO Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

/*
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
}

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());

require_once 'classes/model/Application.php';
require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Sla.php';

require_once PATH_PM_SLA . 'class.pmSLA.php';

$iniDate = '2012-09-17 08:30:00';
$finDate = '2013-09-17 23:00:00';
$proUid = '359728002502a792a568a54012179002';
$res = pmSLAClass::calculateDurationDates($iniDate, $finDate, $proUid);

G::pr($res);

$tiempo_fin = microtime(true);
echo "HUGO Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

*/












/*
public static function calculateDurationDates($dateStart, $dateEnd, $proUid = null)
{
    G::LoadClass("dates");
    $dates = new dates();

    $dateStart = date('Y-m-d H:i:',strtotime($dateStart)) . '00';
    $dateEnd = date('Y-m-d H:i:',strtotime($dateEnd)) . '00';

    $dateEndTimestamp = strtotime($dateEnd);
    $iDueDateTimestamp = $startDateTimestamp = strtotime($dateStart);

    $secondDiff = strtotime($dateEnd) - strtotime($dateStart);
    $minDiff  = $secondDiff / 3600;

    $hoursFinal = $minDiff/24;

    $durationFinal=0;
    $duration=8;

    while ($iDueDateTimestamp < $dateEndTimestamp) {
        $secondDiff = strtotime($dateEnd) - strtotime($dateStart);
        $minDiff = $secondDiff / 3600;

        if (($minDiff<=24)&&($minDiff>=1)) {
            $duration = 1;
        }

        if ($minDiff<1) {
            $duration = 1/60;
        }

        $durationFinal += $duration;

        $iDueDate = $dates->calculateDate($dateStart, $duration,
            "hours",
            "working",
            null,
            $proUid
        );

        $dateStart= $iDueDate['DUE_DATE'];
        $iDueDateTimestamp=$iDueDate['DUE_DATE_SECONDS'];
    }

    return $durationFinal;
}

 */

