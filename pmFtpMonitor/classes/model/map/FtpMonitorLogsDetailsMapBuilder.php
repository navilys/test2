<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'FTP_MONITOR_LOGS_DETAILS' table to 'workflow' DatabaseMap object.
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
class FtpMonitorLogsDetailsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.FtpMonitorLogsDetailsMapBuilder';

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

		$tMap = $this->dbMap->addTable('FTP_MONITOR_LOGS_DETAILS');
		$tMap->setPhpName('FtpMonitorLogsDetails');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('FTP_LOG_DET_UID', 'FtpLogDetUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('FTP_LOG_UID', 'FtpLogUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('EXECUTION_DATETIME', 'ExecutionDatetime', 'string', CreoleTypes::VARCHAR, true, 19);

		$tMap->addColumn('FULL_PATH', 'FullPath', 'string', CreoleTypes::VARCHAR, true, 256);

		$tMap->addColumn('HAVE_XML', 'HaveXml', 'string', CreoleTypes::CHAR, true, 5);

		$tMap->addColumn('VARIABLES', 'Variables', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, true, 8);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, true, 256);

	} // doBuild()

} // FtpMonitorLogsDetailsMapBuilder
