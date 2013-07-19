<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SLA' table to 'workflow' DatabaseMap object.
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
class SlaMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.SlaMapBuilder';

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

        $tMap = $this->dbMap->addTable('SLA');
        $tMap->setPhpName('Sla');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('SLA_UID', 'SlaUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SLA_NAME', 'SlaName', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('SLA_DESCRIPTION', 'SlaDescription', 'string', CreoleTypes::VARCHAR, true, 250);

        $tMap->addColumn('SLA_TYPE', 'SlaType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('SLA_TAS_START', 'SlaTasStart', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SLA_TAS_END', 'SlaTasEnd', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('SLA_TIME_DURATION', 'SlaTimeDuration', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SLA_TIME_DURATION_MODE', 'SlaTimeDurationMode', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('SLA_CONDITIONS', 'SlaConditions', 'string', CreoleTypes::VARCHAR, true, 150);

        $tMap->addColumn('SLA_PEN_ENABLED', 'SlaPenEnabled', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SLA_PEN_TIME', 'SlaPenTime', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SLA_PEN_TIME_MODE', 'SlaPenTimeMode', 'string', CreoleTypes::VARCHAR, true, 10);

        $tMap->addColumn('SLA_PEN_VALUE', 'SlaPenValue', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('SLA_PEN_VALUE_UNIT', 'SlaPenValueUnit', 'string', CreoleTypes::VARCHAR, true, 30);

        $tMap->addColumn('SLA_STATUS', 'SlaStatus', 'string', CreoleTypes::VARCHAR, true, 20);

    } // doBuild()
}
// SlaMapBuilder

