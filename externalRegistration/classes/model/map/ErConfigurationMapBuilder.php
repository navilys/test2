<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ER_CONFIGURATION' table to 'workflow' DatabaseMap object.
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
class ErConfigurationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.ErConfigurationMapBuilder';

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

		$tMap = $this->dbMap->addTable('ER_CONFIGURATION');
		$tMap->setPhpName('ErConfiguration');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ER_UID', 'ErUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ER_TITLE', 'ErTitle', 'string', CreoleTypes::VARCHAR, true, 150);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ER_TEMPLATE', 'ErTemplate', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('DYN_UID', 'DynUid', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('ER_VALID_DAYS', 'ErValidDays', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('ER_ACTION_ASSIGN', 'ErActionAssign', 'string', CreoleTypes::VARCHAR, false, 10);

		$tMap->addColumn('ER_OBJECT_UID', 'ErObjectUid', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('ER_ACTION_START_CASE', 'ErActionStartCase', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('ER_ACTION_EXECUTE_TRIGGER', 'ErActionExecuteTrigger', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('TRI_UID', 'TriUid', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('ER_CREATE_DATE', 'ErCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('ER_UPDATE_DATE', 'ErUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

	} // doBuild()

} // ErConfigurationMapBuilder
