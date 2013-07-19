<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'KT_PROCESS' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    model.map
 */
class KtProcessMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'model.map.KtProcessMapBuilder';

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

		$tMap = $this->dbMap->addTable('KT_PROCESS');
		$tMap->setPhpName('KtProcess');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('KT_FOLDER_ID', 'KtFolderId', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('KT_PARENT_ID', 'KtParentId', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('KT_FOLDER_NAME', 'KtFolderName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('KT_FULL_PATH', 'KtFullPath', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('KT_CREATE_USER', 'KtCreateUser', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('KT_CREATE_DATE', 'KtCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('KT_UPDATE_DATE', 'KtUpdateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

	} // doBuild()

} // KtProcessMapBuilder
