<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PM_REPORT_PERMISSIONS' table to 'workflow' DatabaseMap object.
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
class PmReportPermissionsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.PmReportPermissionsMapBuilder';

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

		$tMap = $this->dbMap->addTable('PM_REPORT_PERMISSIONS');
		$tMap->setPhpName('PmReportPermissions');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('PMR_UID', 'PmrUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addPrimaryKey('ADD_TAB_UID', 'AddTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PMR_TYPE', 'PmrType', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('PMR_OWNER_UID', 'PmrOwnerUid', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('PMR_CREATE_DATE', 'PmrCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('PMR_UPDATE_DATE', 'PmrUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('PMR_STATUS', 'PmrStatus', 'int', CreoleTypes::TINYINT, true, null);

	} // doBuild()

} // PmReportPermissionsMapBuilder
