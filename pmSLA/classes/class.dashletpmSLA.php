<?php
require_once ("classes/interfaces/dashletInterface.php");

class dashletpmSLA implements DashletInterface
{
    const VERSION = '1.0';

    private $role;
    private $note;

    public static function getAdditionalFields($className)
    {
        $additionalFields = array();
        return ($additionalFields);
    }

    public static function getXTemplate($className)
    {
        return "<iframe src='pmSLA/slaDashlet.php' width='{" . "width" . "}' height='207' frameborder='0'></iframe>";
    }

    public function setup($config)
    {
        return true;
    }

    public function render($width = 300)
    {
        return true;
    }
}

