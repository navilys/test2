<?php
//require_once 'bootstrap.php';
if (!defined('PATH_PM_SLA')) {
    //define('PATH_PM_SLA', PATH_CORE . 'plugins' . PATH_SEP . 'pmSLA' . PATH_SEP );
    define('PATH_PM_SLA', PATH_PLUGIN );
}

require_once PATH_PM_SLA . 'classes/class.dashletpmSLA.php';

class dashletpmSLATest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new dashletpmSLA();
    }

    protected function tearDown()
    {
    }

    public function testGetAdditionalFields()
    {
        $additionalFields = array();
        $this->assertEquals(dashletpmSLA::getAdditionalFields(1), $additionalFields);
    }

    public function testGetXTemplate()
    {
        $path = "<iframe src='pmSLA/slaDashlet.php' width='{width}' height='207' frameborder='0'></iframe>";
        $this->assertEquals(dashletpmSLA::getXTemplate(1), $path);
    }

    public function testSetup()
    {
        $this->assertTrue(dashletpmSLA::setup(1));
    }

    public function testRender ($width = 300)
    {
        $this->assertTrue(dashletpmSLA::render(1));
    }
}

