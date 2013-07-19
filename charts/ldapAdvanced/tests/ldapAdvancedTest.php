<?php
require_once 'ldapAdvanced/class.ldapAdvanced.php';

if (!defined('PATH_LDAP_ADV')) {
    define('PATH_LDAP_ADV', PATH_PLUGIN );
}

class ldapAdvancedTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new ldapAdvanced();
    }

    protected function tearDown()
    {
    }

    public function testSearchUsers()
    {
        $keyword = 'cochalo';

        $answer1 = array();
        $answer = array(
                        '0' => array (
                                      'sUsername' => 'cochalo@colosa.com',
                                      'sFullname' => 'Cochalo Test',
                                      'sFirstname' => 'Cochalito',
                                      'sLastname' => 'PEreyra',
                                      'sEmail' => 'cochalo@colosa.com',
                                      'sCategory' => 'CN=Person,CN=Schema,CN=Configuration,DC=colosa,DC=net',
                                      'sDN' => 'CN=Cochalo Test,OU=Desarrollo,OU=Bolivia,DC=colosa,DC=net',
                                      'sManagerDN' => ''));

        $Ldap = new ldapAdvanced();
        $Ldap->sAuthSource = '354277856509d478b6a49a4074997710';

        $resp = $Ldap->searchUsers('cochalo');
        $resp1 = $Ldap->searchUsers('cochalo123');

        $this->assertEquals($resp, $answer, "Error");
        $this->assertEquals($resp1, $answer1, "Error2");
    }
}

