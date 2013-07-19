<?php
/*
set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
G::loadClass ( 'pmFunctions' );

for ($i = 1; $i <= 1500 ; $i++) {
    PMFNewCase('359728002502a792a568a54012179002',
        '00000000000000000000000000000001',
        '782953492502a7930d71d77015756878',
        array());
}


die;*/
$tiempo_inicio = microtime(true);


/*
set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
require_once ( 'classes/class.pmCalendar.php' );
$oCalen = new pmCalendar();
$calendarUid = $oCalen->getCalendar(null, '359728002502a792a568a54012179002', null);
$calendarData = $oCalen->getCalendarData();

$iniDate = '2012-09-01 11:15:00';
$duration = 1;
$formatDuration = 'HOURS';

$res = $oCalen->calculateDate($iniDate, $duration, $formatDuration);

G::pr($res);

$tiempo_fin = microtime(true);
echo "BRAYAN Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

 */

set_include_path(PATH_PLUGINS . 'pmSLA' . PATH_SEPARATOR . get_include_path());
require_once ( 'classes/class.pmCalendar.php' );
$oCalen = new pmCalendar();
$calendarUid = $oCalen->getCalendar(null, '359728002502a792a568a54012179002', null);
$calendarData = $oCalen->getCalendarData();

$iniDate = '2012-09-03 08:43:02';
$finDate = '2012-09-24 10:27:55';

$res = $oCalen->calculateDuration($iniDate, $finDate);

G::pr(($res/60));

$tiempo_fin = microtime(true);
echo "BRAYAN Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);

