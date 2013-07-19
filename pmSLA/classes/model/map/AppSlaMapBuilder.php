<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_SLA' table to 'workflow' DatabaseMap object.
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
class AppSlaMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppSlaMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_SLA');
        $tMap->setPhpName('AppSla');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('SLA_UID', 'SlaUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APP_SLA_INIT_DATE', 'AppSlaInitDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_SLA_DUE_DATE', 'AppSlaDueDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_SLA_FINISH_DATE', 'AppSlaFinishDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('APP_SLA_DURATION', 'AppSlaDuration', 'double', CreoleTypes::DOUBLE, true, null);

        $tMap->addColumn('APP_SLA_REMAINING', 'AppSlaRemaining', 'double', CreoleTypes::DOUBLE, true, null);

        $tMap->addColumn('APP_SLA_EXCEEDED', 'AppSlaExceeded', 'double', CreoleTypes::DOUBLE, true, null);

        $tMap->addColumn('APP_SLA_PEN_VALUE', 'AppSlaPenValue', 'double', CreoleTypes::DOUBLE, true, null);

        $tMap->addColumn('APP_SLA_STATUS', 'AppSlaStatus', 'string', CreoleTypes::VARCHAR, true, 20);

    } // doBuild()
}
// AppSlaMapBuilder

