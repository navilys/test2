<?php
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_PLUGIN );
}
require_once PATH_PM_SLA . 'classes/class.pmCalendar.php';

class pmCalendarTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new pmCalendar();
    }

    /** Not necesary delete data in tables...
     * protected function getTearDownOperation()
     * {
     *     return PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
     * }
     */

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;
    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    public function getConnection()
    {
        if ($this->conn === null) {
            $dsn = 'mysql:dbname=' . $_SERVER['PLUGIN_SLA_DB_NAME'] . ';host='. $_SERVER['PLUGIN_SLA_DB_HOST'];
            if (self::$pdo == null) {
                self::$pdo = new PDO(
                    $dsn,
                    $_SERVER['PLUGIN_SLA_DB_USER'],
                    $_SERVER['PLUGIN_SLA_DB_PASS'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $_SERVER['PLUGIN_SLA_DB_NAME']);
        }
        return $this->conn;
    }

    /**
     *@return PHPUnit_Extensions_Database_DataSet_IDataSet
     */

    public function getDataSet()
    {
        return $this->createXMLDataSet('pmSLA/tests/db.xml');
    }

    public function testGetCalendar()
    {
        $userId = '00000000000000000000000000000001';
        $proId = '359728002502a792a568a54012179002';
        $tasId = '851412306502a794cd11b72090707444';

        $Calendar = new pmCalendar();
        $resp = $Calendar->getCalendar($userId, $proId, $tasId);
        $this->assertEquals($resp, '9426635155057479305aa11012946220', "Error getting Process Calendar");

        $resp2 = $Calendar->getCalendar($proId, $userId, $tasId);
        $this->assertEquals($resp2, '9426635155057479305aa11012946220', "Error getting User Calendar");

        $resp2 = $Calendar->getCalendar($userId, $tasId, $proId);
        $this->assertEquals($resp2, '9426635155057479305aa11012946220', "Error getting Task Calendar");
    }

    public function testGetCalendarData()
    {
        $CalendarId = '00000000000000000000000000000001';
        $CalendarId2 = '00000000000000000000000000000002';

        $Calendar = new pmCalendar();
        $resp = $Calendar->getCalendarData($CalendarId);

        $Data = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_UPDATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                          'HOURS_FOR_DAY' => 8,
                          'BUSINESS_DAY' => array(
                                                  '1' => array(
                                                               'CALENDAR_UID' => '00000000000000000000000000000001',
                                                               'CALENDAR_BUSINESS_DAY' => '7',
                                                               'CALENDAR_BUSINESS_START' => '09:00',
                                                               'CALENDAR_BUSINESS_END' => '17:00',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_DAY)' => '7',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_START)' => '09:00',
                                                               'DIFF_HOURS' => 8.0)),
                          'HOLIDAY' => array());

        $Data1 = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => date("Y-m-d"),
                          'CALENDAR_UPDATE_DATE' => date("Y-m-d"),
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                          'HOURS_FOR_DAY' => '8',
                          'BUSINESS_DAY' => array(
                                                  '1' => array(
                                                               'CALENDAR_BUSINESS_DAY' => 7,
                                                               'CALENDAR_BUSINESS_START' => '09:00',
                                                               'CALENDAR_BUSINESS_END' => '17:00',
                                                               'DIFF_HOURS' => '8')),
                          'HOLIDAY' => array());

        $this->assertEquals($resp, $Data, "Error getting Data Calendar");

        $resp1 = $Calendar->getCalendarData($CalendarId2);
        $this->assertEquals($resp1, $Data1, "Error getting Data Calendar2");
    }

    public function testGetCalendarBusinessHours()
    {
        $CalendarId = '00000000000000000000000000000001';

        $Calendar = new pmCalendar();
        $resp = $Calendar->getCalendarBusinessHours($CalendarId);

        $Data = array(
                      '1' => array(
                                   'CALENDAR_UID' => '00000000000000000000000000000001',
                                   'CALENDAR_BUSINESS_DAY' => '7',
                                   'CALENDAR_BUSINESS_START' => '09:00',
                                   'CALENDAR_BUSINESS_END' => '17:00',
                                   'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_DAY)' => '7',
                                   'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_START)' => '09:00',
                                   'DIFF_HOURS' => 8.0));

        $this->assertEquals($resp, $Data, "Error getting Data Calendar");
    }

    public function testGetCalendarHolidays()
    {
        $CalendarId = '00000000000000000000000000000001';

        $Calendar = new pmCalendar();
        $resp = $Calendar->getCalendarHolidays($CalendarId);

        $Data = array();

        $this->assertEquals($resp, $Data, "Error getting Data Calendar");
    }

    public function testValidateCalendarInfo()
    {
        $defaultCalendar ['CALENDAR_UID'] = '00000000000000000000000000000001';
        $defaultCalendar ['CALENDAR_NAME'] = 'Default';
        $defaultCalendar ['CALENDAR_CREATE_DATE'] = date ( 'Y-m-d' );
        $defaultCalendar ['CALENDAR_UPDATE_DATE'] = date ( 'Y-m-d' );
        $defaultCalendar ['CALENDAR_DESCRIPTION'] = 'Default';
        $defaultCalendar ['CALENDAR_STATUS'] = 'ACTIVE';
        $defaultCalendar ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
        $defaultCalendar ['CALENDAR_WORK_DAYS'] = explode ( '|', '1|2|3|4|5' );
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_DAY'] = 7;
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_START'] = '09:00';
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_END'] = '17:00';
        $defaultCalendar ['HOLIDAY'] = array ();

        $fields = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_UPDATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                          'BUSINESS_DAY' => array(
                                                  '1' => array(
                                                               'CALENDAR_UID' => '00000000000000000000000000000001',
                                                               'CALENDAR_BUSINESS_DAY' => '7',
                                                               'CALENDAR_BUSINESS_START' => '09:00',
                                                               'CALENDAR_BUSINESS_END' => '17:00',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_DAY)' => '7',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_START)' => '09:00')),
                          'HOLIDAY' => array());

        $f2 = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_UPDATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2'),
                          'BUSINESS_DAY' => array(
                                                  '1' => array(
                                                               'CALENDAR_UID' => '00000000000000000000000000000001',
                                                               'CALENDAR_BUSINESS_DAY' => '7',
                                                               'CALENDAR_BUSINESS_START' => '09:00',
                                                               'CALENDAR_BUSINESS_END' => '17:00',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_DAY)' => '7',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_START)' => '09:00')),
                          'HOLIDAY' => array());

        $Calendar = new pmCalendar();

        $resp = $Calendar->validateCalendarInfo($fields, $defaultCalendar);
        $this->assertEquals($resp, $fields, "Error validating calendar info");

        try {
            $Calendar2 = new pmCalendar();
            $Calendar2->validateCalendarInfo($f2, $defaultCalendar);
        } catch (Exception $e) {
            $this->assertEquals('You must define at least 3 Working Days!', $e->getMessage(), 'Error validating calendar info2');
        }

        $f3 = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_UPDATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                          'BUSINESS_DAY' => array(),
                          'HOLIDAY' => array());

        try {
            $Calendar->validateCalendarInfo($f3, $defaultCalendar);
        } catch (Exception $er) {
            $this->assertEquals('You must define at least one Business Day for all days', $er->getMessage(), 'Error validating calendar info2');
        }

        $f4 = array(
                          'CALENDAR_UID' => '00000000000000000000000000000001',
                          'CALENDAR_NAME' => 'Default',
                          'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_UPDATE_DATE' => '2012-09-04 09:53:47',
                          'CALENDAR_DESCRIPTION' => 'Default',
                          'CALENDAR_STATUS' => 'ACTIVE',
                          'CALENDAR_WORK_DAYS' => '1|2',
                          'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2'),
                          'BUSINESS_DAY' => array(
                                                  '1' => array(
                                                               'CALENDAR_UID' => '00000000000000000000000000000001',
                                                               'CALENDAR_BUSINESS_DAY' => '5',
                                                               'CALENDAR_BUSINESS_START' => '09:00',
                                                               'CALENDAR_BUSINESS_END' => '17:00',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_DAY)' => '5',
                                                               'UPPER(CALENDAR_BUSINESS_HOURS.CALENDAR_BUSINESS_START)' => '09:00')),
                          'HOLIDAY' => array());

        $re = $Calendar->validateCalendarInfo($f4, $defaultCalendar);
        $this->assertEquals($resp, $fields, "Error validating calendar info");
    }

    public function testCalculateDate()
    {
        $ainiDate = '2012-09-04 09:53:47';
        $aduration = '2';
        $aformatDuration = 'DAYS';
        $acalendarData = array(
                               'CALENDAR_UID' => '00000000000000000000000000000001',
                               'CALENDAR_NAME' => 'Default',
                               'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                               'CALENDAR_UPDATE_DATE' => '2012-09-05 09:00:47',
                               'CALENDAR_DESCRIPTION' => 'Default',
                               'CALENDAR_STATUS' => 'ACTIVE',
                               'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                               'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                               'BUSINESS_DAY' => array(
                                                       '0' => array(
                                                                    'CALENDAR_BUSINESS_DAY' => 7,
                                                                    'CALENDAR_BUSINESS_START' => '09:00',
                                                                    'CALENDAR_BUSINESS_END' => '17:00',
                                                                    'DIFF_HOURS' => '8')),
                               'HOURS_FOR_DAY' => '8',
                               'HOLIDAY' => array());


        $CalendarId = '9426635155057479305aa11012946220';

        $Calendar = new pmCalendar();
        $Calendar->getCalendarData($CalendarId);

        $resp1 = $Calendar->calculateDate($ainiDate, $aduration, $aformatDuration);
        $this->assertEquals($resp1, '2012-09-06 09:53:47', "Error calculating date");

        $resp2 = $Calendar->calculateDate($ainiDate, 8, 'HOURS', $acalendarData);
        $this->assertEquals($resp2, '2012-09-05 09:53:47', "Error calculating date");
    }

    public function testCalculateDuration()
    {
        $ainiDate = '2012-09-04 09:53:47';
        $afinDate = '2012-09-06 09:53:47';
        $acalendarData = array(
                               'CALENDAR_UID' => '00000000000000000000000000000001',
                               'CALENDAR_NAME' => 'Default',
                               'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                               'CALENDAR_UPDATE_DATE' => '2012-09-05 09:00:47',
                               'CALENDAR_DESCRIPTION' => 'Default',
                               'CALENDAR_STATUS' => 'ACTIVE',
                               'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                               'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                               'BUSINESS_DAY' => array(
                                                       '0' => array(
                                                                    'CALENDAR_BUSINESS_DAY' => 7,
                                                                    'CALENDAR_BUSINESS_START' => '09:00',
                                                                    'CALENDAR_BUSINESS_END' => '17:00',
                                                                    'DIFF_HOURS' => '8')),
                               'HOURS_FOR_DAY' => '8',
                               'HOLIDAY' => array());


        $CalendarId = '9426635155057479305aa11012946220';

        $Calendar = new pmCalendar();
        $Calendar->getCalendarData($CalendarId);

        $resp1 = $Calendar->calculateDuration($ainiDate, $afinDate);
        $this->assertEquals($resp1, '57600.0', "Error calculating duration1");

        $resp2 = $Calendar->calculateDuration($afinDate, $ainiDate, $acalendarData);
        $this->assertEquals($resp2, '57600.0', "Error calculating duration2");

        $resp3 = $Calendar->calculateDuration($ainiDate, $ainiDate, $acalendarData);
        $this->assertEquals($resp3, '0.0', "Error calculating duration3");

        $resp4 = $Calendar->calculateDuration($ainiDate, "", $acalendarData);
        //$this->assertEquals($resp4, '431565.0', "Error calculating duration4");
    }

    public function testGetRangeWorkHours()
    {
        $adate = '2012-09-04 09:53:47';
        $aworkHours = array(
                            '0' => array(
                                         'CALENDAR_BUSINESS_DAY' => 7,
                                         'CALENDAR_BUSINESS_START' => '09:00',
                                         'CALENDAR_BUSINESS_END' => '17:00',
                                         'DIFF_HOURS' => '8'));

        $bworkHours = array(
                            '0' => array(
                                         'CALENDAR_BUSINESS_DAY' => 2,
                                         'CALENDAR_BUSINESS_START' => '09:00',
                                         'CALENDAR_BUSINESS_END' => '17:00',
                                         'DIFF_HOURS' => '8'));

        $cworkHours = array();

        $range = array(
                       'START' => '09:00:00',
                       'END' => '17:00:00',
                       'TOTAL' => 8.0);

        $Calendar = new pmCalendar();
        $resp = $Calendar->getRangeWorkHours($adate, $aworkHours);
        $this->assertEquals($resp, $range, "Error getting the range of work hours1");

        $resp2 = $Calendar->getRangeWorkHours($adate, $bworkHours);
        $this->assertEquals($resp2, $range, "Error getting the range of work hours2");

        $resp3 = $Calendar->getRangeWorkHours($adate, $cworkHours);
        $this->assertFalse($resp3, "Error getting the range of work hours3");
    }

    public function testGetIniDate()
    {
        $ainiDate = '2012-09-04 06:53:47';
        $acalendarData = array(
                               'CALENDAR_UID' => '00000000000000000000000000000001',
                               'CALENDAR_NAME' => 'Default',
                               'CALENDAR_CREATE_DATE' => '2012-09-04 09:53:47',
                               'CALENDAR_UPDATE_DATE' => '2012-09-05 09:00:47',
                               'CALENDAR_DESCRIPTION' => 'Default',
                               'CALENDAR_STATUS' => 'ACTIVE',
                               'CALENDAR_WORK_DAYS' => '1|2|3|4|5',
                               'CALENDAR_WORK_DAYS_A' => array(
                                                          '0' => '1',
                                                          '1' => '2',
                                                          '2' => '3',
                                                          '3' => '4',
                                                          '4' => '5'),
                               'BUSINESS_DAY' => array(
                                                       '0' => array(
                                                                    'CALENDAR_BUSINESS_DAY' => 7,
                                                                    'CALENDAR_BUSINESS_START' => '09:00',
                                                                    'CALENDAR_BUSINESS_END' => '17:00',
                                                                    'DIFF_HOURS' => '8')),
                               'HOURS_FOR_DAY' => '8',
                               'HOLIDAY' => array(
                                                  '0' => array(
                                                               'CALENDAR_UID' => '9426635155057479305aa11012946220',
                                                               'CALENDAR_HOLIDAY_NAME' => 'TTT',
                                                               'CALENDAR_HOLIDAY_START' => '2012-09-25 09:00:00',
                                                               'CALENDAR_HOLIDAY_END' => '2012-09-26 17:00:00')));


        $CalendarId = '9426635155057479305aa11012946220';

        $Calendar = new pmCalendar();
        $Calendar->getCalendarData($CalendarId);

        $resp1 = $Calendar->getIniDate($ainiDate);
        $this->assertEquals($resp1, '2012-09-04 09:00:00', "Error getting initial date1");

        $resp2 = $Calendar->getIniDate('2012-09-09 09:53:47', $acalendarData);
        $this->assertEquals($resp2, '2012-09-10 09:00:00', "Error getting initial date2");

        $resp3 = $Calendar->getIniDate('2012-09-26 10:00:00', $acalendarData);
        $this->assertEquals($resp3, '2012-09-27 09:00:00', "Error getting initial date3");
    }

    public function testNextWorkHours()
    {
        $date = '2012-09-04 06:53:47';
        $weekDay = 5;
        $aworkHours = array(
                            '0' => array(
                                         'CALENDAR_BUSINESS_DAY' => 5,
                                         'CALENDAR_BUSINESS_START' => '09:00',
                                         'CALENDAR_BUSINESS_END' => '17:00',
                                         'DIFF_HOURS' => '8'),
                            '1' => array(
                                         'CALENDAR_BUSINESS_DAY' => 3,
                                         'CALENDAR_BUSINESS_START' => '09:00',
                                         'CALENDAR_BUSINESS_END' => '17:00',
                                         'DIFF_HOURS' => '8'));

        $WorkHours = array(
                           'STATUS' => true,
                           'DATE' => '2012-09-04 09:00:00');

        $Calendar = new pmCalendar();

        $resp = $Calendar->nextWorkHours($date, $weekDay, $aworkHours);
        $this->assertEquals($resp, $WorkHours, "Error getting next work hour");
    }

    public function testIs_holiday()
    {
        $aDate = '2012-09-04 06:53:47';
        $aholidays = array(
                           '0' => array(
                                        'CALENDAR_UID' => '9426635155057479305aa11012946220',
                                        'CALENDAR_HOLIDAY_NAME' => 'TTT',
                                        'CALENDAR_HOLIDAY_START' => '2012-09-25 09:00:00',
                                        'CALENDAR_HOLIDAY_END' => '2012-09-27 09:00:00'));

        $Calendar = new pmCalendar();

        $resp1 = $Calendar->is_holiday($aDate, $aholidays);
        $this->assertFalse($resp1, "Error 1");

        $resp2 = $Calendar->is_holiday('2012-09-25 09:00:00', $aholidays);
        $this->assertTrue($resp2, "Error 2");
    }

    /*public function testShowLog()
    {
    }*/
}

