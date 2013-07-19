<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by FtpMonitorSettingPeer::getOMClass()
include_once 'classes/model/FtpMonitorSetting.php';

/**
 * Base static class for performing query and update operations on the 'FTP_MONITOR_SETTING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFtpMonitorSettingPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'workflow';

	/** the table name for this class */
	const TABLE_NAME = 'FTP_MONITOR_SETTING';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'classes.model.FtpMonitorSetting';

	/** The total number of columns. */
	const NUM_COLUMNS = 14;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the FTP_UID field */
	const FTP_UID = 'FTP_MONITOR_SETTING.FTP_UID';

	/** the column name for the CONNECTION_TYPE field */
	const CONNECTION_TYPE = 'FTP_MONITOR_SETTING.CONNECTION_TYPE';

	/** the column name for the HOST field */
	const HOST = 'FTP_MONITOR_SETTING.HOST';

	/** the column name for the PORT field */
	const PORT = 'FTP_MONITOR_SETTING.PORT';

	/** the column name for the USER field */
	const USER = 'FTP_MONITOR_SETTING.USER';

	/** the column name for the PASS field */
	const PASS = 'FTP_MONITOR_SETTING.PASS';

	/** the column name for the SEARCH_PATTERN field */
	const SEARCH_PATTERN = 'FTP_MONITOR_SETTING.SEARCH_PATTERN';

	/** the column name for the FTP_PATH field */
	const FTP_PATH = 'FTP_MONITOR_SETTING.FTP_PATH';

	/** the column name for the INPUT_DOCUMENT_UID field */
	const INPUT_DOCUMENT_UID = 'FTP_MONITOR_SETTING.INPUT_DOCUMENT_UID';

	/** the column name for the XML_SEARCH field */
	const XML_SEARCH = 'FTP_MONITOR_SETTING.XML_SEARCH';

	/** the column name for the PRO_UID field */
	const PRO_UID = 'FTP_MONITOR_SETTING.PRO_UID';

	/** the column name for the TAS_UID field */
	const TAS_UID = 'FTP_MONITOR_SETTING.TAS_UID';

	/** the column name for the DEL_USER_UID field */
	const DEL_USER_UID = 'FTP_MONITOR_SETTING.DEL_USER_UID';

	/** the column name for the FTP_STATUS field */
	const FTP_STATUS = 'FTP_MONITOR_SETTING.FTP_STATUS';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('FtpUid', 'ConnectionType', 'Host', 'Port', 'User', 'Pass', 'SearchPattern', 'FtpPath', 'InputDocumentUid', 'XmlSearch', 'ProUid', 'TasUid', 'DelUserUid', 'FtpStatus', ),
		BasePeer::TYPE_COLNAME => array (FtpMonitorSettingPeer::FTP_UID, FtpMonitorSettingPeer::CONNECTION_TYPE, FtpMonitorSettingPeer::HOST, FtpMonitorSettingPeer::PORT, FtpMonitorSettingPeer::USER, FtpMonitorSettingPeer::PASS, FtpMonitorSettingPeer::SEARCH_PATTERN, FtpMonitorSettingPeer::FTP_PATH, FtpMonitorSettingPeer::INPUT_DOCUMENT_UID, FtpMonitorSettingPeer::XML_SEARCH, FtpMonitorSettingPeer::PRO_UID, FtpMonitorSettingPeer::TAS_UID, FtpMonitorSettingPeer::DEL_USER_UID, FtpMonitorSettingPeer::FTP_STATUS, ),
		BasePeer::TYPE_FIELDNAME => array ('FTP_UID', 'CONNECTION_TYPE', 'HOST', 'PORT', 'USER', 'PASS', 'SEARCH_PATTERN', 'FTP_PATH', 'INPUT_DOCUMENT_UID', 'XML_SEARCH', 'PRO_UID', 'TAS_UID', 'DEL_USER_UID', 'FTP_STATUS', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('FtpUid' => 0, 'ConnectionType' => 1, 'Host' => 2, 'Port' => 3, 'User' => 4, 'Pass' => 5, 'SearchPattern' => 6, 'FtpPath' => 7, 'InputDocumentUid' => 8, 'XmlSearch' => 9, 'ProUid' => 10, 'TasUid' => 11, 'DelUserUid' => 12, 'FtpStatus' => 13, ),
		BasePeer::TYPE_COLNAME => array (FtpMonitorSettingPeer::FTP_UID => 0, FtpMonitorSettingPeer::CONNECTION_TYPE => 1, FtpMonitorSettingPeer::HOST => 2, FtpMonitorSettingPeer::PORT => 3, FtpMonitorSettingPeer::USER => 4, FtpMonitorSettingPeer::PASS => 5, FtpMonitorSettingPeer::SEARCH_PATTERN => 6, FtpMonitorSettingPeer::FTP_PATH => 7, FtpMonitorSettingPeer::INPUT_DOCUMENT_UID => 8, FtpMonitorSettingPeer::XML_SEARCH => 9, FtpMonitorSettingPeer::PRO_UID => 10, FtpMonitorSettingPeer::TAS_UID => 11, FtpMonitorSettingPeer::DEL_USER_UID => 12, FtpMonitorSettingPeer::FTP_STATUS => 13, ),
		BasePeer::TYPE_FIELDNAME => array ('FTP_UID' => 0, 'CONNECTION_TYPE' => 1, 'HOST' => 2, 'PORT' => 3, 'USER' => 4, 'PASS' => 5, 'SEARCH_PATTERN' => 6, 'FTP_PATH' => 7, 'INPUT_DOCUMENT_UID' => 8, 'XML_SEARCH' => 9, 'PRO_UID' => 10, 'TAS_UID' => 11, 'DEL_USER_UID' => 12, 'FTP_STATUS' => 13, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'classes/model/map/FtpMonitorSettingMapBuilder.php';
		return BasePeer::getMapBuilder('classes.model.map.FtpMonitorSettingMapBuilder');
	}
	/**
	 * Gets a map (hash) of PHP names to DB column names.
	 *
	 * @return     array The PHP to DB name map for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
	 */
	public static function getPhpNameMap()
	{
		if (self::$phpNameMap === null) {
			$map = FtpMonitorSettingPeer::getTableMap();
			$columns = $map->getColumns();
			$nameMap = array();
			foreach ($columns as $column) {
				$nameMap[$column->getPhpName()] = $column->getColumnName();
			}
			self::$phpNameMap = $nameMap;
		}
		return self::$phpNameMap;
	}
	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants TYPE_PHPNAME,
	 *                         TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants TYPE_PHPNAME,
	 *                      TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. FtpMonitorSettingPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(FtpMonitorSettingPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{

		$criteria->addSelectColumn(FtpMonitorSettingPeer::FTP_UID);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::CONNECTION_TYPE);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::HOST);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::PORT);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::USER);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::PASS);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::SEARCH_PATTERN);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::FTP_PATH);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::INPUT_DOCUMENT_UID);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::XML_SEARCH);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::PRO_UID);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::TAS_UID);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::DEL_USER_UID);

		$criteria->addSelectColumn(FtpMonitorSettingPeer::FTP_STATUS);

	}

	const COUNT = 'COUNT(FTP_MONITOR_SETTING.FTP_UID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT FTP_MONITOR_SETTING.FTP_UID)';

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(FtpMonitorSettingPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(FtpMonitorSettingPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = FtpMonitorSettingPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      Connection $con
	 * @return     FtpMonitorSetting
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = FtpMonitorSettingPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, $con = null)
	{
		return FtpMonitorSettingPeer::populateObjects(FtpMonitorSettingPeer::doSelectRS($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect()
	 * method to get a ResultSet.
	 *
	 * Use this method directly if you want to just get the resultset
	 * (instead of an array of objects).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     ResultSet The resultset object with numerically-indexed fields.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectRS(Criteria $criteria, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if (!$criteria->getSelectColumns()) {
			$criteria = clone $criteria;
			FtpMonitorSettingPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a Creole ResultSet, set to return
		// rows indexed numerically.
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(ResultSet $rs)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = FtpMonitorSettingPeer::getOMClass();
		$cls = Propel::import($cls);
		// populate the object(s)
		while($rs->next()) {
		
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * This uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass()
	{
		return FtpMonitorSettingPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a FtpMonitorSetting or Criteria object.
	 *
	 * @param      mixed $values Criteria or FtpMonitorSetting object containing data that is used to create the INSERT statement.
	 * @param      Connection $con the connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from FtpMonitorSetting object
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->begin();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollback();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a FtpMonitorSetting or Criteria object.
	 *
	 * @param      mixed $values Criteria or FtpMonitorSetting object containing data that is used to create the UPDATE statement.
	 * @param      Connection $con The connection to use (specify Connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(FtpMonitorSettingPeer::FTP_UID);
			$selectCriteria->add(FtpMonitorSettingPeer::FTP_UID, $criteria->remove(FtpMonitorSettingPeer::FTP_UID), $comparison);

		} else { // $values is FtpMonitorSetting object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the FTP_MONITOR_SETTING table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			$affectedRows += BasePeer::doDeleteAll(FtpMonitorSettingPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a FtpMonitorSetting or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or FtpMonitorSetting object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      Connection $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(FtpMonitorSettingPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof FtpMonitorSetting) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(FtpMonitorSettingPeer::FTP_UID, (array) $values, Criteria::IN);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given FtpMonitorSetting object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      FtpMonitorSetting $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(FtpMonitorSetting $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(FtpMonitorSettingPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(FtpMonitorSettingPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(FtpMonitorSettingPeer::DATABASE_NAME, FtpMonitorSettingPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     FtpMonitorSetting
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(FtpMonitorSettingPeer::DATABASE_NAME);

		$criteria->add(FtpMonitorSettingPeer::FTP_UID, $pk);


		$v = FtpMonitorSettingPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria();
			$criteria->add(FtpMonitorSettingPeer::FTP_UID, $pks, Criteria::IN);
			$objs = FtpMonitorSettingPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseFtpMonitorSettingPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseFtpMonitorSettingPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'classes/model/map/FtpMonitorSettingMapBuilder.php';
	Propel::registerMapBuilder('classes.model.map.FtpMonitorSettingMapBuilder');
}
