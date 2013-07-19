<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'KT_DOCUMENT' table to 'workflow' DatabaseMap object.
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
class KtDocumentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'model.map.KtDocumentMapBuilder';

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

		$tMap = $this->dbMap->addTable('KT_DOCUMENT');
		$tMap->setPhpName('KtDocument');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('DOC_UID', 'DocUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addPrimaryKey('DOC_TYPE', 'DocType', 'string', CreoleTypes::VARCHAR, true, 4);

		$tMap->addColumn('DOC_PMTYPE', 'DocPmtype', 'string', CreoleTypes::VARCHAR, true, 10);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('KT_DOCUMENT_ID', 'KtDocumentId', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('KT_STATUS', 'KtStatus', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('KT_DOCUMENT_TITLE', 'KtDocumentTitle', 'string', CreoleTypes::VARCHAR, true, 150);

		$tMap->addColumn('KT_FULL_PATH', 'KtFullPath', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('KT_CREATE_USER', 'KtCreateUser', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('KT_CREATE_DATE', 'KtCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('KT_UPDATE_DATE', 'KtUpdateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

	} // doBuild()

} // KtDocumentMapBuilder
