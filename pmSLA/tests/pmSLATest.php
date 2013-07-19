<?php
require_once 'pmSLA/class.pmSLA.php';

if (!defined('PATH_PM_SLA')) {
    define('PATH_PM_SLA', PATH_PLUGIN );
}

class pmSLATest extends PHPUnit_Extensions_Database_TestCase
{
    protected $object;

    public function setup()
    {
    }

    protected function getTearDownOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
    }

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

    public function testCreateTables()
    {
        $createTables = pmSLAClass::createTables();
        //$guestbook->addEntry("suzy", "Hello world!");

        $this->assertEquals(0, $this->getConnection()->getRowCount('SLA'), "Pre-Condition");
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "Pre-Condition");
    }

    public function testSaveLogSla()
    {
        //Cadena para comparar
        $cadena = 'TestUnit of the function';

        ////////////////////////////////////////////////////////////
        //Escribo la cadena en cron.log
        pmSLAClass::saveLogSla('cronSLA', 'action', $cadena);

        /*if (!defined('PATH_PM_SLA')) {
            define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
        }*/

        //Obtengo las filas escritas en cron.log
        $filas=file(PATH_PM_SLA . 'log' . PATH_SEP . 'cron.log');

        //Cuento las filas obtenidas
        $cantidad = count($filas);
        $ultimaFila = $filas[$cantidad-1];

        //Separo la ultima fila usando los espacios
        $palabras = explode(' ', $ultimaFila);

        //Borro los dos primeros
        unset($palabras[0]);
        unset($palabras[1]);
        unset($palabras[2]);

        //Separo la cadena
        $palabra2 = trim (implode(' ', $palabras));

        //Comparo
        $this->assertEquals($palabra2, $cadena, 'errrrorrrrrr');

        ///////////////////////////////////////////////////////////
        //Escribo la cadena en cronError.log
        pmSLAClass::saveLogSla('cronSLA', 'error', $cadena);

        //Obtengo las filas escritas en cronError.log
        $filasEr=file(PATH_PM_SLA . 'log' . PATH_SEP . 'cronExecute.log');

        $cantidadEr = count($filasEr);

        $ultimaFilaEr = $filasEr[$cantidadEr-1];

        $palabrasEr = explode(' ', $ultimaFilaEr);

        unset($palabrasEr[0]);
        unset($palabrasEr[1]);
        unset($palabrasEr[2]);

        $palabra2Er = trim (implode(' ', $palabrasEr));

        //Comparo
        $this->assertEquals($palabra2Er, $cadena, 'errrrorrrrrr');
    }

    public function testMinutesToHours()
    {
        //Validar numeros enteros
        $this->assertEquals(pmSLAClass::minutesToHours(60), 1);
        //Validar numeros negativos
        $this->assertEquals(pmSLAClass::minutesToHours(-60), -1);
        //Validar Strings
        $st = "test";
        $this->assertEquals(pmSLAClass::minutesToHours($st),0);
        //Validar numeros reales
        $this->assertEquals(pmSLAClass::minutesToHours(0.60), 0.01);
    }

    public function testHoursToMinutes()
    {
        //Validar numeros enteros
        $this->assertEquals(pmSLAClass::hoursToMinutes(2), 120);
        //Validar numeros negativos
        $this->assertEquals(pmSLAClass::hoursToMinutes(-2), -120);
        //Validar Strings
        $str = "test";
        $this->assertEquals(pmSLAClass::hoursToMinutes($str), 0);
        //Validar numeros reales
        $this->assertEquals(pmSLAClass::hoursToMinutes(0.02), 1.2);
    }

    public function testCalculateDueDate()
    {
        //Hours
        $hr = '2012-09-03 08:10:00';
        //$duration = 4;
        $type = "hours";
        $process = '359728002502a792a568a54012179002';

        $this->assertEquals('2012-09-03 13:00:00', pmSLAClass::calculateDueDate($hr, 4, $type, $process), 'Pruba Hours1');
        //$this->assertEquals('2012-09-03 03:00:00', pmSLAClass::calculateDueDate($hr, -4, $type, $process), 'Prueba Hours2');

        //Days
        $type2 = "days";
        $this->assertEquals('2012-09-28 17:00:00', pmSLAClass::calculateDueDate($hr, 20, $type2, $process), 'Prueba Days1');
        //$this->assertEquals('2012-08-13 17:00:00', pmSLAClass::calculateDueDate($hr, -20, $type2, $process), 'Prueba Days2');
    }

    public function testInsertAppSla()
    {
        //Primero cuento la cantidad de datos que tiene almacenados la tabla APP_SLA, debe ser = 0.
        $this->assertEquals(0, $this->getConnection()->getRowCount('APP_SLA'), "Pre-Condition");

        //Ingreso un nuevo registro con la funcion que se testea.
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 3,
                      'APP_SLA_REMAINING' => 3,
                      'APP_SLA_EXCEEDED' => 3,
                      'APP_SLA_PEN_VALUE' => 3,
                      'APP_SLA_STATUS' => "bcd");
        //pmSLAClass::insertAppSla($data);

        //Ejemplo de como se realiza una insercion de datos a una tabla con DbUnit
        $appSla = new pmSLAClass();
        $appSla->insertAppSla($data);

        //Vuelvo a contar la cantidad de datos almacenados en la tabla APP_SLA, debiendo haberse incrementado en 1 (=1).
        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "Inserting failed");

        //Ahora verificamos si los datos almacenados en la tabla APP_SLA son los correctos.
        $queryTable = $this->getConnection()->createQueryTable(
            'APP_SLA', 'SELECT * FROM APP_SLA'
        );

        $expectedTable = $this->createFlatXmlDataSet('pmSLA/tests/fixtures/insertAppSLA.xml')
                              ->getTable("APP_SLA");

        $this->assertTablesEqual($expectedTable, $queryTable, "ERROR");
    }

    public function testUpdateAppSla()
    {
        $data = array(
                      'APP_UID' => "phpUnit",
                      'SLA_UID' => "abc",
                      'APP_SLA_INIT_DATE' => "2012-09-03 08:10:00",
                      'APP_SLA_DUE_DATE' => "2012-09-04 09:10:00",
                      'APP_SLA_FINISH_DATE' => "2012-09-05 10:10:00",
                      'APP_SLA_DURATION' => 2,
                      'APP_SLA_REMAINING' => 2,
                      'APP_SLA_EXCEEDED' => 2,
                      'APP_SLA_PEN_VALUE' => 2,
                      'APP_SLA_STATUS' => "bcd");
        //pmSLAClass::insertAppSla($data);

        //Insercion de datos a una tabla con DbUnit
        $appSla1 = new pmSLAClass();
        $appSla1->insertAppSla($data);

        $this->assertEquals(1, $this->getConnection()->getRowCount('APP_SLA'), "Inserting failed");

        //$insertedTable = $this->getConnection()->createQueryTable(
        //   'APP_SLA', 'SELECT * FROM APP_SLA'
        //);

        $update = array(
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
        $appSlaUpdate = new pmSLAClass();
        $appSlaUpdate->updateAppSla($update);

        //Obtengo los datos modificados
        $updatedTable = $this->getConnection()->createQueryTable(
            'APP_SLA', 'SELECT * FROM APP_SLA'
        );
        //Comparo el ingreso inicial con la modificacion, si son distintos, paso a comparar si lo modificado es correcto
        //$this->assertTablesEqual($insertedTable, $updatedTable, "Updating failed!!!");

        $expectedTable = $this->createFlatXmlDataSet("pmSLA/tests/fixtures/updateAppSLA.xml")
                              ->getTable("APP_SLA");
        $this->assertTablesEqual($expectedTable, $updatedTable, "Datos modificados incorrectamente");
    }

    public function testCreateXml()
    {
        //the initial array to create the xml file
        $col = array();

        $object = new stdClass();
        $object->DATAINDEX = "COL_UNO";
        $object->HEADER = "COLUMNA 1";
        $col[] = $object;

        $objecto = new stdClass();
        $objecto->DATAINDEX = "COL_DOS";
        $objecto->HEADER = "COLUMNA 2";
        $col[] = $objecto;

        $object1 = new stdClass();
        $object1->DATAINDEX = "COL_TRES";
        $object1->HEADER = "COLUMNA 3";
        $col[] = $object1;

        //sending the array $col to createXml
        $testXml = new pmSLAClass();
        $xml = $testXml->createXml($col);

        //testing if xml file exist...
        $this->assertFileExists('pmSLA/reportExcel.xml');

        //New array to compare
        //$resp[0] = 'aaaaaa';
        //$resp[1]  = 'bbbbb';
        //$resp[2] = 'cccc';

        //testing data in xml file...
        $this->assertXmlFileEqualsXmlFile('pmSLA/tests/fixtures/testCreateXml.xml', 'pmSLA/reportExcel.xml', "Error creating XML file.....");
        //$this->assertEquals($xml, $resp, "Error creating XML file.....");
    }

    public function testNumberToLabelTime()
    {
        $timeMinutes = 330;

        $pmSla = new pmSLAClass();
        $resp = $pmSla->numberToLabelTime($timeMinutes);

        $this->assertEquals($resp, '5 H, 30 min', "Error");
    }
}

