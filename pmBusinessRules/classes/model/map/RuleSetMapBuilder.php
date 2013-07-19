<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'RULE_SET' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    workflow.classes.model.map
 */
class RuleSetMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.RuleSetMapBuilder';

    /**
     * The database map.
     */
    private $dbMap;

    /**
     * Tells us if this DatabaseMapBuilder is built so that we
     * don't have to re-build it every time.
     *
     * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
     */
    public function isBuilt()
    {
        return ($this->dbMap !== null);
    }

    /**
     * Gets the databasemap this map builder built.
     *
     * @return     the databasemap
     */
    public function getDatabaseMap()
    {
        return $this->dbMap;
    }

    /**
     * The doBuild() method builds the DatabaseMap
     *
     * @return     void
     * @throws     PropelException
     */
    public function doBuild()
    {
        $this->dbMap = Propel::getDatabaseMap('workflow');

        $tMap = $this->dbMap->addTable('RULE_SET');
        $tMap->setPhpName('RuleSet');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('RST_UID', 'RstUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('RST_NAME', 'RstName', 'string', CreoleTypes::VARCHAR, true, 64);

        $tMap->addColumn('RST_DESCRIPTION', 'RstDescription', 'string', CreoleTypes::VARCHAR, false, 256);

        $tMap->addColumn('RST_TYPE', 'RstType', 'string', CreoleTypes::VARCHAR, false, 10);

        $tMap->addColumn('RST_STRUCT', 'RstStruct', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('RST_SOURCE', 'RstSource', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('RST_CREATE_DATE', 'RstCreateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('RST_UPDATE_DATE', 'RstUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('RST_CHECKSUM', 'RstChecksum', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('RST_DELETED', 'RstDeleted', 'boolean', CreoleTypes::BOOLEAN, false, null);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

    } // doBuild()

} // RuleSetMapBuilder
