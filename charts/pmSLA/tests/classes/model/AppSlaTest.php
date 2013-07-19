<?php
if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_PLUGIN );
}
require_once PATH_PM_SLA . 'classes/model/AppSla.php';

class AppSlaTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new AppSla();
    }

    /** Not necesary delete data in tables...*/
    protected function getTearDownOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
    }

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

    /**
     * This is the default method to test, if the class still having
     * the same number of methods.
    */
    public function testNumberOfMethodsInThisClass()
    {
        $methods = get_class_methods('AppSla');
        $this->assertEquals( 81, count($methods), 'number of methods in this class has changed');

    }

    public function testcreate()
    {
        $methods = get_class_methods($this->object);
        $this->assertTrue( in_array('create', $methods ), 'exists method create' );
        $r = new ReflectionMethod('AppSla', 'create');
        $params = $r->getParameters();
        $this->assertEquals( 'aData', $params[0]->name ,'params has changed');
    }

    public function testslaExists()
    {
        $methods = get_class_methods($this->object);
        $this->assertTrue( in_array('slaExists', $methods ), 'exists method slaExists' );
        $r = new ReflectionMethod('AppSla', 'slaExists');
        $params = $r->getParameters();
        $this->assertEquals( 'AppUid', $params[0]->name ,'params has changed');
        $this->assertEquals( 'SlaUid', $params[1]->name ,'params has changed');
    }

    public function testupdate()
    {
        $methods = get_class_methods($this->object);
        $this->assertTrue( in_array('update', $methods ), 'exists method update' );
        $r = new ReflectionMethod('AppSla', 'update');
        $params = $r->getParameters();
        $this->assertEquals( 'fields', $params[0]->name ,'params has changed');
    }

    public function testremove()
    {
        $methods = get_class_methods($this->object);
        $this->assertTrue( in_array('remove', $methods ), 'exists method remove' );
        $r = new ReflectionMethod('AppSla', 'remove');
        $params = $r->getParameters();
        $this->assertEquals( 'AppUid', $params[0]->name ,'params has changed');
        $this->assertEquals( 'SlaUid', $params[1]->name ,'params has changed');
    }

    public function testing()
    {
        $res = $this->object->slaExists( '0001', '001');
        $this->assertEquals( false, $res ,'slaExists1');

        $aData = array();
        $aData['APP_UID']             = "0001-APP_UID";
        $aData['SLA_UID']             = "0001-SLA_UID";
        $aData['APP_SLA_INIT_DATE']   = date("Y-m-d H:i:s");
        $aData['APP_SLA_DUE_DATE']    = date("Y-m-d H:i:s");
        $aData['APP_SLA_FINISH_DATE'] = date("Y-m-d H:i:s");
        $aData['APP_SLA_DURATION']    = 10;
        $aData['APP_SLA_REMAINING']   = 1;
        $aData['APP_SLA_EXCEEDED']    = 2;
        $aData['APP_SLA_PEN_VALUE']   = 3;
        $aData['APP_SLA_STATUS']      = "OPEN";

        $res = $this->object->create($aData);
        $this->assertEquals( true, $res ,'create');

        $res = $this->object->slaExists( '0001-APP_UID', '0001-SLA_UID');
        $this->assertEquals( true, $res ,'slaExists2');

        //creating a duplicate row
        try {
            $obj = new AppSla();
            $res = $obj->create($aData);
        } catch (Exception $e) {
            $this->assertEquals( 'Unable to execute INSERT statement.', substr($e->getMessage(),0,35) ,'duplicate');
        }

        //exist
        $apUid = "...";
        $slUid = "***";

        $slaExist = new AppSla();

        $resp = $slaExist->slaExists($apUid, $slUid);
        $this->assertFalse($resp, "Error");

        $resp1 = $slaExist->slaExists($apUid, 4);
        $this->assertFalse($resp1, "Error2");

        //update
        //If the row dosn't exist
        try {
            $fields = array(
                      'APP_UID' => "0055",
                      'SLA_UID' => "0005",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
            $SlaUpdate = new AppSla();
            $resp1 = $SlaUpdate->update($fields);
        } catch (Exception $e) {
            $this->assertEquals( "The rows '0055/0005' in table APP_SLA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }

        //remove

        $res = $this->object->remove( '0001-APP_UID', '0001-SLA_UID');
        $res = $this->object->slaExists( '0001-APP_UID', '0001-SLA_UID');
        $this->assertEquals( false, $res ,'remove');

        //If the row dosn't exist
        try {
            $this->object->remove('0024', '00895');
        } catch (Exception $e) {
            $this->assertEquals( 'This object has already been deleted.', $e->getMessage(), 'Doesnt exist!!!');
        }
    }

    public function testLoad()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "Pre-Insertion");

        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "Post-Insertion");

        $AppUid = "phpUnit";
        $SlaUid = "abc";

        $resp = $AppSla->load($AppUid, $SlaUid);

        $this->assertEquals($resp, $data, "Error Loading");

        //If the row dosn't exist
        try {
            $aAppUid = "002";
            $aSlaUid = "003";
            $resp1 = $AppSla->load($aAppUid, $aSlaUid);
        } catch (Exception $e) {
            $this->assertEquals( "The rows '002/003' in table APP_SLA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }
    }

    public function testLoadDetails()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1.0,
                      //'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1.0,
                      'APP_SLA_PEN_VALUE' => 1.0,
                      'APP_SLA_STATUS' => 'bcd');
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "The table SLA is empty...");

        $AppUid = "phpUnit";
        $SlaUid = "abc";

        $resp = $AppSla->loadDetails($AppUid, $SlaUid);

        $this->assertEquals($resp, $data, "Error Loading Details");

        //If the row dosn't exist
        try {
            $aAppUid = "002";
            $aSlaUid = "003";
            $resp1 = $AppSla->loadDetails($aAppUid, $aSlaUid);
        } catch (Exception $e) {
            $this->assertEquals( "The rows '002/003' in table APP_LSA doesn't exist!", $e->getMessage(), 'Doesnt exist!');
        }
    }

    //LoadByAppSla returns a large propel object, so this function is gonna be tested indirectly by the other functions
    public function testLoadByAppSla()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => 'ACTIVE');
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "The table SLA is empty...");

        $aslaUid = "abc";
        $adateStart = "2012-09-03 08:10:00";
        $adateEnd = "2012-09-04 09:10:00";
        $astatus = "ACTIVE";
        $bstatus = "INACTIVE";
        $astart = 0;
        $alimit = 20;

        $AppSla->setSlaUidRep($aslaUid);
        $AppSla->setDateStart($adateStart);
        $AppSla->setDateEnd($adateEnd);
        $AppSla->setStatus($astatus);
        $AppSla->setStart($astart);
        $AppSla->setLimit($alimit);

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('>');
        $AppSla->setSort('APP_NUMBER');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('EXCEEDED');
        $AppSla->setTypeDate('>=');
        $AppSla->setSort('TOTAL_EXCEEDED');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp1 = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('EXCEEDED_LESS');
        $AppSla->setTypeDate('<');
        $AppSla->setSort('APP_SLA_INIT_DATE');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp2 = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED_LESS', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('EXCEEDED_MORE');
        $AppSla->setTypeDate('<=');
        $AppSla->setSort('APP_SLA_DUE_DATE');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp2 = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED_LESS', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('');
        $AppSla->setTypeDate('between');
        $AppSla->setSort('APP_SLA_FINISH_DATE');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp3 = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, '', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('TEST');
        $AppSla->setSort('APP_SLA_PEN_VALUE');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('>');
        $AppSla->setSort('APP_SLA_STATUS');
        $AppSla->setDir('ASC');
        $AppSla->loadByAppSla();
        //$RepResp = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $AppSla->setDir('DSC');
        $AppSla->setStatus('COMPLETED');
        $AppSla->loadByAppSla();

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('>');
        $AppSla->setSort('TEST');
        $AppSla->setStatus('OPEN');
        //$RepResp = $AppSla->loadByAppSla($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $AppSla->loadByAppSla();

        ///
        $rows = $AppSla->getTotalRows();
    }

    public function testGetReportFirstLevel()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => 'ACTIVE');
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "The table SLA is empty...");

        $rep = array(
                      '0' => array(
                                   'SLA_UID' => 'abc',
                                   'SLA_NAME' => null,
                                   'SLA_PEN_VALUE_UNIT' => null,
                                   'SUM_DURATION' => '1',
                                   'SUM_EXCEEDED' => '1',
                                   'AVG_SLA' => '1',
                                   'SUM_PEN_VALUE' => '1',
                                   'UPPER(APP_SLA.SLA_UID)' => 'ABC'));
        $r = array();

        $aslaUid = "abc";
        $adateStart = "2012-09-03 08:10:00";
        $adateEnd = "2012-09-05 10:10:00";
        $astatus = "ACTIVE";
        $bstatus = "INACTIVE";
        $typeExceeded = "";
        $astart = 0;
        $alimit = 20;

        $AppSla->setSlaUidRep($aslaUid);
        $AppSla->setDateStart($adateStart);
        $AppSla->setDateEnd($adateEnd);
        $AppSla->setStatus($astatus);
        $AppSla->setStart($astart);
        $AppSla->setLimit($alimit);

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('>');
        //$RepResp = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $RepResp = $AppSla->getReportFirstLevel();
        $this->assertEquals($r, $RepResp, "Error Getting Report1");

        $AppSla->setTypeExceeded('EXCEEDED');
        $AppSla->setTypeDate('>=');
        //$RepResp1 = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED', $astart, $alimit);
        $RepResp1 = $AppSla->getReportFirstLevel();
        $this->assertEquals($r, $RepResp1, "Error Getting Report2");

        $AppSla->setTypeExceeded('EXCEEDED_LESS');
        $AppSla->setTypeDate('<');
        //$RepResp2 = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED_LESS', $astart, $alimit);
        $RepResp2 = $AppSla->getReportFirstLevel();
        $this->assertEquals($r, $RepResp2, "Error Getting Report3");

        $AppSla->setTypeExceeded('EXCEEDED_MORE');
        $AppSla->setTypeDate('<=');
        //$RepResp3 = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, 'EXCEEDED_MORE', $astart, $alimit);
        $RepResp3 = $AppSla->getReportFirstLevel();
        $this->assertEquals($RepResp3, $r, "Error Getting Report4");

        $AppSla->setTypeExceeded('TEST');
        $AppSla->setTypeDate('between');
        $AppSla->setStatus('COMPLETED');
        //$RepResp4 = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, '', $astart, $alimit);
        $RepResp4 = $AppSla->getReportFirstLevel();
        //$this->assertEquals($RepResp4, $r, "Error Getting Report5");

        $AppSla->setTypeExceeded('NO_EXCEEDED');
        $AppSla->setTypeDate('TEST');
        $AppSla->setStatus('OPEN');
        //$RepResp = $AppSla->getReportFirstLevel($aslaUid, $adateStart, $adateEnd, $astatus, 'NO_EXCEEDED', $astart, $alimit);
        $RepResp = $AppSla->getReportFirstLevel();
        //$this->assertEquals($r, $RepResp, "Error Getting Report5");
    }

    public function testLoadDashlet()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is not clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "The table APP_SLA is empty, can not work without data");

        $da = array(
                    '0' => array(
                      'SLA_UID' => "abc",
                      'SLA_NAME' => null,
                      'SLA_PEN_VALUE_UNIT' => null,
                      'SUM_DURATION' => '1',
                      'SUM_EXCEEDED' => '1',
                      'AVG_SLA' => '1',
                      'SUM_PEN_VALUE' => '1'));

        $res = $AppSla->loadDashlet();

        $this->assertEquals($res, $da, "Error Loading Dashlet");
    }

    public function testLoadDetailReportSel()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is not clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
        $data2 = array(
                      'APP_UID' => "0002",
                      'SLA_UID' => "001",
                      'APP_SLA_INIT_DATE' => "2013-09-03 18:10:00",
                      'APP_SLA_DUE_DATE' => "2013-09-04 19:10:00",
                      'APP_SLA_FINISH_DATE' => "2013-09-05 12:10:00",
                      'APP_SLA_DURATION' => 2,
                      'APP_SLA_REMAINING' => 2,
                      'APP_SLA_EXCEEDED' => 2,
                      'APP_SLA_PEN_VALUE' => 2,
                      'APP_SLA_STATUS' => "EXCEEDED");
        $AppSla = new AppSla();
        $AppSla->create($data);
        $AppSla2 = new AppSla();
        $AppSla2->create($data2);

        $this->assertEquals(2, $this->getConnection()->getRowCount('APP_SLA'), "The table APP_SLA is empty, can not work without data");

        $resp = $AppSla->loadDetailReportSel("001", 0);

        $d = array();

        $this->assertEquals($resp, $d, "Error Loading Dashlet");
    }

    public function testLoadBySlaNameInArray()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is not clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
        $AppSla = new AppSla();
        $AppSla->create($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "The table APP_SLA is empty, can't work without data");

        $SlaUid = "abc";

        $exi = $AppSla->loadBySlaNameInArray($SlaUid);

        $this->assertFalse($exi, "SLA_UID not found");
    }

    public function testGetReportAppSla()
    {
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "APP_SLA is not clean");
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 1,
                      'APP_SLA_REMAINING' => 1,
                      'APP_SLA_EXCEEDED' => 1,
                      'APP_SLA_PEN_VALUE' => 1,
                      'APP_SLA_STATUS' => "bcd");
        $data2 = array(
                      'APP_UID' => "0002",
                      'SLA_UID' => "001",
                      'APP_SLA_INIT_DATE' => "2013-09-03 18:10:00",
                      'APP_SLA_DUE_DATE' => "2013-09-04 19:10:00",
                      'APP_SLA_FINISH_DATE' => "2013-09-05 12:10:00",
                      'APP_SLA_DURATION' => 2,
                      'APP_SLA_REMAINING' => 2,
                      'APP_SLA_EXCEEDED' => 2,
                      'APP_SLA_PEN_VALUE' => 2,
                      'APP_SLA_STATUS' => "EXCEEDED");
        $AppSla = new AppSla();
        $AppSla->create($data);
        $AppSla2 = new AppSla();
        $AppSla2->create($data2);

        $this->assertEquals(2, $this->getConnection()->getRowCount('APP_SLA'), "The table APP_SLA is empty, can't work without data");

        $SlaUid = "001";

        $re = $AppSla->getReportAppSla($SlaUid);

        $d = array();
        $this->assertEquals($re, $re, "Error Loading Dashlet");
    }
}

