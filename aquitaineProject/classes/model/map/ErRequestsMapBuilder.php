<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ER_REQUESTS' table to 'workflow' DatabaseMap object.
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
class ErRequestsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.ErRequestsMapBuilder';

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

		$tMap = $this->dbMap->addTable('ER_REQUESTS');
		$tMap->setPhpName('ErRequests');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ER_REQ_UID', 'ErReqUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ER_UID', 'ErUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ER_REQ_DATA', 'ErReqData', 'string', CreoleTypes::LONGVARCHAR, true, null);

		$tMap->addColumn('ER_REQ_DATE', 'ErReqDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('ER_REQ_COMPLETED', 'ErReqCompleted', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('ER_REQ_COMPLETED_DATE', 'ErReqCompletedDate', 'int', CreoleTypes::TIMESTAMP, false, null);

	} // doBuild()

} // ErRequestsMapBuilder
