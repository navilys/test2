<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'FTP_MONITOR_SETTING' table to 'workflow' DatabaseMap object.
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
class FtpMonitorSettingMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.FtpMonitorSettingMapBuilder';

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

		$tMap = $this->dbMap->addTable('FTP_MONITOR_SETTING');
		$tMap->setPhpName('FtpMonitorSetting');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('FTP_UID', 'FtpUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('CONNECTION_TYPE', 'ConnectionType', 'string', CreoleTypes::VARCHAR, true, 8);

		$tMap->addColumn('HOST', 'Host', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('PORT', 'Port', 'string', CreoleTypes::VARCHAR, true, 5);

		$tMap->addColumn('USER', 'User', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('PASS', 'Pass', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('SEARCH_PATTERN', 'SearchPattern', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('FTP_PATH', 'FtpPath', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('INPUT_DOCUMENT_UID', 'InputDocumentUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('XML_SEARCH', 'XmlSearch', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEL_USER_UID', 'DelUserUid', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('FTP_STATUS', 'FtpStatus', 'string', CreoleTypes::VARCHAR, true, 8);

	} // doBuild()

} // FtpMonitorSettingMapBuilder
