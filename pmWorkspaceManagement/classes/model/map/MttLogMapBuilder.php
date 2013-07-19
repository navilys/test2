<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MttLog' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    workflow.classes.map
 */
class MttLogMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.map.MttLogMapBuilder';

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
        $this->dbMap = Propel::getDatabaseMap('multitenant');

        $tMap = $this->dbMap->addTable('MTT_LOG');
        $tMap->setPhpName('MttLog');

        $tMap->setUseIdGenerator(true);

        $tMap->addPrimaryKey('LOG_ID', 'LogId', 'int', CreoleTypes::INTEGER, true, 10);

        $tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('LOG_IP', 'LogIp', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('LOG_DATETIME', 'LogDatetime', 'int', CreoleTypes::DATE, true, null);

        $tMap->addColumn('LOG_ACTION', 'LogAction', 'string', CreoleTypes::VARCHAR, true, 50);

        $tMap->addColumn('LOG_DESCRIPTION', 'LogDescription', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('LOG_TYPE', 'LogType', 'string', CreoleTypes::VARCHAR, true, 20);

        $tMap->addColumn('LOG_ADDITIONAL_DETAILS', 'LogAdditionalDetails', 'string', CreoleTypes::LONGVARCHAR, true, null);

    } // doBuild()

} // LogMapBuilder
