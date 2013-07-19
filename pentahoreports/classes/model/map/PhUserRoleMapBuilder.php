<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PH_USER_ROLE' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package plugins.pentahoreports.classes.model.map
 */
class PhUserRoleMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.PhUserRoleMapBuilder';

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

		$tMap = $this->dbMap->addTable('PH_USER_ROLE');
		$tMap->setPhpName('PhUserRole');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ROL_OBJ_UID', 'RolObjUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ROL_UID', 'RolUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('OBJ_UID', 'ObjUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('OBJ_TYPE', 'ObjType', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('OBJ_DASHBOARD', 'ObjDashboard', 'string', CreoleTypes::VARCHAR, true, 32);

	} // doBuild()

} // PhUserRoleMapBuilder
