<?php
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_PLUGIN );
}
require_once PATH_PM_SLA . 'classes/model/Sla.php';

class SlaTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new Sla();
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

    public function testCreate()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('SLA'), "Pre-Condition");

        $data = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");
        $appSlaUpdate = new Sla();
        $appSlaUpdate->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Post-Condition");

        $queryTable = $this->getConnection()->createQueryTable(
            'SLA', 'SELECT * FROM SLA'
        );

        $expectedTable = $this->createFlatXmlDataSet('pmSLA/tests/fixtures/insertAppSLA.xml')
                              ->getTable("SLA");

        $this->assertTablesEqual($expectedTable, $queryTable, "ERROR inserting...");
    }

    public function testSlaExists()
    {
        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Post-Insertion");

        $Uid = "dbUnit";
        $aUid = "db";

        $slaExist = new Sla();
        $res = $slaExist->slaExists($Uid);
        $resp = $slaExist->slaExists($aUid);

        $this->assertFalse($resp, "Error");
        $this->assertTrue($res, "Error");
    }

    public function testLoad()
    {
        $data = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Post-Insertion");

        $SlaUid = "dbUnit";

        $slaLoad = new Sla();
        $resp = $slaLoad->load($SlaUid);

        $this->assertEquals($resp, $data, "Error");

        //If the row dosn't exist
        try {
            $SlaUida = "dbUnita";
            $resp1 = $slaLoad->load($SlaUida);
        } catch (Exception $e) {
            $this->assertEquals( "The row '" . $SlaUida . "' in table SLA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }
    }

    public function testLoadDetails()
    {
        $data = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Post-Insertion");

        $SlaUid = "dbUnit";

        $slaLoad = new Sla();
        $resp = $slaLoad->loadDetails($SlaUid);

        $this->assertEquals($resp, $data, "Error");

        //If the row dosn't exist
        try {
            $SlaUida = "dbUnita";
            $resp1 = $slaLoad->loadDetails($SlaUida);
        } catch (Exception $e) {
            $this->assertEquals( "The row '" . $SlaUida . "' in table SLA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }
    }

    public function testUpdate()
    {
        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Inserting failed");

        $update = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "zzzz",
                      'SLA_DESCRIPTION' => "xxxx",
                      'SLA_TYPE' => "yyyy",
                      'SLA_TAS_START' => "jjjj",
                      'SLA_TAS_END' => "pppp",
                      'SLA_TIME_DURATION' => 3,
                      'SLA_TIME_DURATION_MODE' => "HOURS",
                      'SLA_CONDITIONS' => "oooo",
                      'SLA_PEN_ENABLED' => 3,
                      'SLA_PEN_TIME' => 3,
                      'SLA_PEN_TIME_MODE' => "HOURS",
                      'SLA_PEN_VALUE' => 3,
                      'SLA_PEN_VALUE_UNIT' => "qqqq",
                      'SLA_STATUS' => "mmmm");
        $SlaUpdate = new Sla();
        $SlaUpdate->update($update);

        $updatedTable = $this->getConnection()->createQueryTable(
            'SLA', 'SELECT * FROM SLA'
        );

        $expectedTable = $this->createFlatXmlDataSet("pmSLA/tests/fixtures/updateAppSLA.xml")
                              ->getTable("SLA");
        $this->assertTablesEqual($expectedTable, $updatedTable, "Datos ingresados incorrectamente");

        //If the row dosn't exist
        try {
            $fields = array(
                      'SLA_UID' => "dbUnita",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "zzzz",
                      'SLA_DESCRIPTION' => "xxxx",
                      'SLA_TYPE' => "yyyy",
                      'SLA_TAS_START' => "jjjj",
                      'SLA_TAS_END' => "pppp",
                      'SLA_TIME_DURATION' => 3,
                      'SLA_TIME_DURATION_MODE' => "HOURS",
                      'SLA_CONDITIONS' => "oooo",
                      'SLA_PEN_ENABLED' => 3,
                      'SLA_PEN_TIME' => 3,
                      'SLA_PEN_TIME_MODE' => "HOURS",
                      'SLA_PEN_VALUE' => 3,
                      'SLA_PEN_VALUE_UNIT' => "qqqq",
                      'SLA_STATUS' => "mmmm");

            $resp1 = $SlaUpdate->update($fields);
        } catch (Exception $e) {
            $this->assertEquals( "The row 'dbUnita' in table SLA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }
    }

    public function testRemove()
    {
        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Inserting failed");

        $SlaUid = "dbUnit";

        $SlaRemove = new Sla();
        $SlaRemove->remove($SlaUid);

        $this->assertEquals(0, $this->getConnection()->getRowCount('SLA'), "Removing failed");

        //If the row dosn't exist
        try {
            $SlaUida = "dbUnita";
            $resp1 = $SlaRemove->remove($SlaUida);
        } catch (Exception $e) {
            $this->assertEquals( 'This object has already been deleted.', $e->getMessage(), 'Doesnt exist!');
        }
    }

    //Trying to brake exceptions in Create()

    public function testingCreate()
    {
        $res = $this->object->slaExists('0001');
        $this->assertEquals( false, $res ,'slaExists');

        $aData = array(
                      'SLA_UID' => "0001-SLA_UID",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");

        $res = $this->object->create($aData);
        $this->assertEquals( true, $res ,'create');

        $res = $this->object->slaExists('0001-SLA_UID');
        $this->assertEquals( true, $res ,'slaExists');

        //creating a duplicate row
        try {
            $obj = new Sla();
            $res = $obj->create($aData);
        } catch (Exception $e) {
            $this->assertEquals( 'Unable to execute INSERT statement.', substr($e->getMessage(),0,35) ,'duplicate');
        }

        //remove
        $res = $this->object->remove('0001-SLA_UID');
        $res = $this->object->slaExists('0001-SLA_UID');
        $this->assertEquals( false, $res ,'remove');
    }

    public function testGetSlaNameExist()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('SLA'), "Pre-Condition");

        $Inidata = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");

        $sla = new Sla();
        $sla->create($Inidata);

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Inserting failed");

        $SlaName = "aaaa";

        $existName = $sla->getSlaNameExist($SlaName);

        $this->assertTrue($existName, "Error");

        //If the row dosn't exist
        $SlaNames = "dbUnita";
        $existName1 = $sla->getSlaNameExist($SlaNames);
        $this->assertFalse($existName1, 'Doesnt exist!');
    }

    public function testLoadByAppSlaName()
    {
        /*$data = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => 2,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => 2,
                      'SLA_PEN_TIME' => 2,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 2,
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj");

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "Inserting failed");

        $SlaName = "aaaa";
        $Criteria = " ";

        $slaLoadAppSlaName = new Sla();
        $crit = $slaLoadAppSlaName->loadByAppSlaName($SlaName, $Criteria);

        $this->assertEquals($crit, $data, "Error");*/
    }

    public function testLoadBySlaNameInArray()
    {
        $data = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => '2',
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => '2',
                      'SLA_PEN_TIME' => '2',
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => '2',
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj",
                      'SLA_TASKS' => "cccc",
                      'PRO_NAME' => null);

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");

        $Name = "aaaa";

        $SlaName = new Sla();
        $exist = $SlaName->loadBySlaNameInArray($Name);

        $this->assertEquals($exist, $data, "Error Loading by SLAName in array");
        //$this->assertEquals($exist, $Inidata, "Error");
    }

    public function testGetListSla()
    {
        $data = array(
                      '0' => array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => '2',
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => '2',
                      'SLA_PEN_TIME' => '2',
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => '2',
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj",
                      'SLA_TASKS' => "cccc",
                      'PRO_NAME' => null,
                      'APP_UID' => null,
                      'APP_SLA_DURATION' => null,
                      'APP_SLA_DUE_DATE' => null,
                      'APP_SLA_PEN_VALUE' => null,
                      'APP_SLA_STATUS' => null));

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");

        $name = "aaaa";

        //Testing when Criteria = All, means that we gonna get APP_SLA data to.
        $SlaName = new Sla();
        $exis = $SlaName->getListSla($name, "All");

        $this->assertEquals($data, $exis, "Error getting list SLA and APP_SLA");

        //Testing when Criteria = "", means that wont get APP_SLA data.

        $datas = array(
                      '0' => array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa",
                      'SLA_DESCRIPTION' => "bbbb",
                      'SLA_TYPE' => "cccc",
                      'SLA_TAS_START' => "dddd",
                      'SLA_TAS_END' => "eeee",
                      'SLA_TIME_DURATION' => '2',
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "gggg",
                      'SLA_PEN_ENABLED' => '2',
                      'SLA_PEN_TIME' => '2',
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => '2',
                      'SLA_PEN_VALUE_UNIT' => "iiii",
                      'SLA_STATUS' => "jjjj",
                      'SLA_TASKS' => "cccc",
                      'PRO_NAME' => null));

        $exiss = $SlaName->getListSla($name, " ");

        $this->assertEquals($datas, $exiss, "Error getting list SLA");
    }

    public function testGetListSlaName()
    {
        $data = array(
                      '0' => array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa"
                      ),

                      '1' => array(
                      'SLA_UID' => "dbUnit",
                      'SLA_NAME' => "aaaa"
                      ));

        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");

        $value = array(
                      'SLA_UID' => "dbUnit",
                      'PRO_UID' => "abcd",
                      'SLA_NAME' => "aaaa");

        $SlaName = new Sla();
        $list = $SlaName->getListSlaName($value);

        $this->assertEquals($data, $list, "Error getting list");
    }

    public function testGetSelectSlaUid()
    {
        $this->assertEquals(1, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");

        $Inidata = array(
                      'SLA_UID' => "0002",
                      'PRO_UID' => "daj",
                      'SLA_NAME' => "a",
                      'SLA_DESCRIPTION' => "b",
                      'SLA_TYPE' => "c",
                      'SLA_TAS_START' => "d",
                      'SLA_TAS_END' => "e",
                      'SLA_TIME_DURATION' => 5,
                      'SLA_TIME_DURATION_MODE' => "MINUTES",
                      'SLA_CONDITIONS' => "g",
                      'SLA_PEN_ENABLED' => 5,
                      'SLA_PEN_TIME' => 5,
                      'SLA_PEN_TIME_MODE' => "MINUTES",
                      'SLA_PEN_VALUE' => 5,
                      'SLA_PEN_VALUE_UNIT' => "i",
                      'SLA_STATUS' => "j");

        $SlaUid = new Sla();
        $SlaUid->create($Inidata);

        $this->assertEquals(2, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");

        $SlaUidget = "0002";
        $AppNumber = "0";

        $get = $SlaUid->getSelectSlaUid($SlaUidget, 2);

        $data = array();

        /**
         *Delete all data in table SLA, to end test... this has to be in
         *the last function 'cause we are not using getTearDownOperation()
         */
        $SlaUid = "dbUnit";
        $SecSla = "0002";
        $SlaRemove = new Sla();
        $SlaRemove->remove($SlaUid);
        $slaRem = new Sla();
        $slaRem->remove($SecSla);

        $this->assertEquals($data, $get, "Error getting SLA by SLA_UID");
        $this->assertEquals(0, $this->getConnection()->getRowCount('SLA'), "The table SLA is empty...");
    }

    public function testGetProcessList()
    {
        $processList = new Sla();
        $proc = $processList->getProcessList();

        $this->assertEquals(1, $this->getConnection()->getRowCount('PROCESS'), "The table is empty");

        $expectedTable = array(
                               '0' => array(
                             'PRO_UID' => "359728002502a792a568a54012179002",
                             'PRO_PARENT' => "359728002502a792a568a54012179002",
                             'PRO_STATUS' => "ACTIVE",
                             'PRO_CATEGORY' => '',
                             'PRO_CREATE_DATE' => "2012-08-14 12:13:30",
                             'PRO_CREATE_USER' => "00000000000000000000000000000001",
                             'PRO_DEBUG' => '0',
                             'PRO_DESCRIPTION' => '',
                             'PRO_TITLE' => 'Solicitud e instalacion TVcable'));
        $this->assertEquals($expectedTable, $proc, "Datos obtenidos incorrectamente");
    }
}

