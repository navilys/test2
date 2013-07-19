<?php

G::LoadClass("plugin");

if (!defined('PATH_PM_BUSINESS_RULES')) {
    define('PATH_PM_BUSINESS_RULES', PATH_CORE . 'plugins' . PATH_SEP . 'pmBusinessRules' . PATH_SEP );
}

class pmBusinessRulesPlugin extends PMPlugin
{
    public function pmBusinessRulesPlugin($sNamespace, $sFilename = null)
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = "pmBusinessRules Plugin";
        $this->sDescription  = "Plugin to execute and manage Business Rules engine in ProcessMaker";
        $this->sPluginFolder = "pmBusinessRules";
        $this->sSetupPage    = "setup";
        $this->iVersion      = self::getPluginVersion($sNamespace);
        $this->aWorkspaces   = null;
        return $res;
    }

    public function setup()
    {
        set_include_path(dirname(__FILE__) . PATH_SEP . 'pmBusinessRules' . PATH_SEPARATOR . get_include_path());
        require_once 'classes/model/RuleSet.php';
        require_once 'classes/model/GlobalFields.php';


        $this->registerMenu("setup", "menuConfig.php");
        $this->registerPmFunction();
    }

    public function install()
    {


    }

    public function enable()
    {
        $this->createTableGlobalFields();
    }

    public function disable()
    {
    }

    private static function createTableGlobalFields()
    {
        $sqlFile = PATH_PM_BUSINESS_RULES . 'data'. PATH_SEP . 'mysql' . PATH_SEP . 'schema.sql';
        $handle = @fopen( $sqlFile, "r"); // Open file form read.
        $line = '';

        // to verify if business rules tables exist or not
        $con = Propel::getConnection('workflow');
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery('show tables like "RULE_SET";', ResultSet::FETCHMODE_ASSOC);

        if ($rs->getRecordCount()) {
            return false;
        }

        if ($handle) {
            // Loop til end of file.
            while (!feof($handle)) {
                // Read a line.
                $buffer = fgets($handle, 4096);
                // Check for valid lines
                if ($buffer[0] != "#" && strlen(trim($buffer)) >0) {
                    $buffer = trim( $buffer);
                    $line .= $buffer;
                    if ( $buffer[strlen( $buffer)-1] == ';' ) {

                        $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
                        $line = '';
                    }
                }
            }
            // Close the file.
            fclose($handle);
        }
    }

    private static function getPluginVersion($namespace)
    {
        $pathPluginTrunk = PATH_PLUGINS . PATH_SEP . $namespace;
        if (file_exists($pathPluginTrunk . PATH_SEP . 'VERSION')) {
            $version = trim(file_get_contents($pathPluginTrunk . PATH_SEP . 'VERSION'));
        } else {
            $version = 'Development Version';
        }
        return $version;
    }
}

$oPluginRegistry = &PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin("pmBusinessRules", __FILE__);
