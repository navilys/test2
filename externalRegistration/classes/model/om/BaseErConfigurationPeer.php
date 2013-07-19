<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ErConfigurationPeer::getOMClass()
include_once 'classes/model/ErConfiguration.php';

/**
 * Base static class for performing query and update operations on the 'ER_CONFIGURATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseErConfigurationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'workflow';

	/** the table name for this class */
	const TABLE_NAME = 'ER_CONFIGURATION';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'classes.model.ErConfiguration';

	/** The total number of columns. */
	const NUM_COLUMNS = 14;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ER_UID field */
	const ER_UID = 'ER_CONFIGURATION.ER_UID';

	/** the column name for the ER_TITLE field */
	const ER_TITLE = 'ER_CONFIGURATION.ER_TITLE';

	/** the column name for the PRO_UID field */
	const PRO_UID = 'ER_CONFIGURATION.PRO_UID';

	/** the column name for the ER_TEMPLATE field */
	const ER_TEMPLATE = 'ER_CONFIGURATION.ER_TEMPLATE';

	/** the column name for the DYN_UID field */
	const DYN_UID = 'ER_CONFIGURATION.DYN_UID';

	/** the column name for the ER_VALID_DAYS field */
	const ER_VALID_DAYS = 'ER_CONFIGURATION.ER_VALID_DAYS';

	/** the column name for the ER_ACTION_ASSIGN field */
	const ER_ACTION_ASSIGN = 'ER_CONFIGURATION.ER_ACTION_ASSIGN';

	/** the column name for the ER_OBJECT_UID field */
	const ER_OBJECT_UID = 'ER_CONFIGURATION.ER_OBJECT_UID';

	/** the column name for the ER_ACTION_START_CASE field */
	const ER_ACTION_START_CASE = 'ER_CONFIGURATION.ER_ACTION_START_CASE';

	/** the column name for the TAS_UID field */
	const TAS_UID = 'ER_CONFIGURATION.TAS_UID';

	/** the column name for the ER_ACTION_EXECUTE_TRIGGER field */
	const ER_ACTION_EXECUTE_TRIGGER = 'ER_CONFIGURATION.ER_ACTION_EXECUTE_TRIGGER';

	/** the column name for the TRI_UID field */
	const TRI_UID = 'ER_CONFIGURATION.TRI_UID';

	/** the column name for the ER_CREATE_DATE field */
	const ER_CREATE_DATE = 'ER_CONFIGURATION.ER_CREATE_DATE';

	/** the column name for the ER_UPDATE_DATE field */
	const ER_UPDATE_DATE = 'ER_CONFIGURATION.ER_UPDATE_DATE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ErUid', 'ErTitle', 'ProUid', 'ErTemplate', 'DynUid', 'ErValidDays', 'ErActionAssign', 'ErObjectUid', 'ErActionStartCase', 'TasUid', 'ErActionExecuteTrigger', 'TriUid', 'ErCreateDate', 'ErUpdateDate', ),
		BasePeer::TYPE_COLNAME => array (ErConfigurationPeer::ER_UID, ErConfigurationPeer::ER_TITLE, ErConfigurationPeer::PRO_UID, ErConfigurationPeer::ER_TEMPLATE, ErConfigurationPeer::DYN_UID, ErConfigurationPeer::ER_VALID_DAYS, ErConfigurationPeer::ER_ACTION_ASSIGN, ErConfigurationPeer::ER_OBJECT_UID, ErConfigurationPeer::ER_ACTION_START_CASE, ErConfigurationPeer::TAS_UID, ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER, ErConfigurationPeer::TRI_UID, ErConfigurationPeer::ER_CREATE_DATE, ErConfigurationPeer::ER_UPDATE_DATE, ),
		BasePeer::TYPE_FIELDNAME => array ('ER_UID', 'ER_TITLE', 'PRO_UID', 'ER_TEMPLATE', 'DYN_UID', 'ER_VALID_DAYS', 'ER_ACTION_ASSIGN', 'ER_OBJECT_UID', 'ER_ACTION_START_CASE', 'TAS_UID', 'ER_ACTION_EXECUTE_TRIGGER', 'TRI_UID', 'ER_CREATE_DATE', 'ER_UPDATE_DATE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ErUid' => 0, 'ErTitle' => 1, 'ProUid' => 2, 'ErTemplate' => 3, 'DynUid' => 4, 'ErValidDays' => 5, 'ErActionAssign' => 6, 'ErObjectUid' => 7, 'ErActionStartCase' => 8, 'TasUid' => 9, 'ErActionExecuteTrigger' => 10, 'TriUid' => 11, 'ErCreateDate' => 12, 'ErUpdateDate' => 13, ),
		BasePeer::TYPE_COLNAME => array (ErConfigurationPeer::ER_UID => 0, ErConfigurationPeer::ER_TITLE => 1, ErConfigurationPeer::PRO_UID => 2, ErConfigurationPeer::ER_TEMPLATE => 3, ErConfigurationPeer::DYN_UID => 4, ErConfigurationPeer::ER_VALID_DAYS => 5, ErConfigurationPeer::ER_ACTION_ASSIGN => 6, ErConfigurationPeer::ER_OBJECT_UID => 7, ErConfigurationPeer::ER_ACTION_START_CASE => 8, ErConfigurationPeer::TAS_UID => 9, ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER => 10, ErConfigurationPeer::TRI_UID => 11, ErConfigurationPeer::ER_CREATE_DATE => 12, ErConfigurationPeer::ER_UPDATE_DATE => 13, ),
		BasePeer::TYPE_FIELDNAME => array ('ER_UID' => 0, 'ER_TITLE' => 1, 'PRO_UID' => 2, 'ER_TEMPLATE' => 3, 'DYN_UID' => 4, 'ER_VALID_DAYS' => 5, 'ER_ACTION_ASSIGN' => 6, 'ER_OBJECT_UID' => 7, 'ER_ACTION_START_CASE' => 8, 'TAS_UID' => 9, 'ER_ACTION_EXECUTE_TRIGGER' => 10, 'TRI_UID' => 11, 'ER_CREATE_DATE' => 12, 'ER_UPDATE_DATE' => 13, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'classes/model/map/ErConfigurationMapBuilder.php';
		return BasePeer::getMapBuilder('classes.model.map.ErConfigurationMapBuilder');
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
			$map = ErConfigurationPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. ErConfigurationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ErConfigurationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(ErConfigurationPeer::ER_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_TITLE);

		$criteria->addSelectColumn(ErConfigurationPeer::PRO_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_TEMPLATE);

		$criteria->addSelectColumn(ErConfigurationPeer::DYN_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_VALID_DAYS);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_ASSIGN);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_OBJECT_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_START_CASE);

		$criteria->addSelectColumn(ErConfigurationPeer::TAS_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER);

		$criteria->addSelectColumn(ErConfigurationPeer::TRI_UID);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_CREATE_DATE);

		$criteria->addSelectColumn(ErConfigurationPeer::ER_UPDATE_DATE);

	}

	const COUNT = 'COUNT(ER_CONFIGURATION.ER_UID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT ER_CONFIGURATION.ER_UID)';

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
			$criteria->addSelectColumn(ErConfigurationPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(ErConfigurationPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = ErConfigurationPeer::doSelectRS($criteria, $con);
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
	 * @return     ErConfiguration
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ErConfigurationPeer::doSelect($critcopy, $con);
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
		return ErConfigurationPeer::populateObjects(ErConfigurationPeer::doSelectRS($criteria, $con));
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
			ErConfigurationPeer::addSelectColumns($criteria);
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
		$cls = ErConfigurationPeer::getOMClass();
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
		return ErConfigurationPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a ErConfiguration or Criteria object.
	 *
	 * @param      mixed $values Criteria or ErConfiguration object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from ErConfiguration object
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
	 * Method perform an UPDATE on the database, given a ErConfiguration or Criteria object.
	 *
	 * @param      mixed $values Criteria or ErConfiguration object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(ErConfigurationPeer::ER_UID);
			$selectCriteria->add(ErConfigurationPeer::ER_UID, $criteria->remove(ErConfigurationPeer::ER_UID), $comparison);

		} else { // $values is ErConfiguration object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the ER_CONFIGURATION table.
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
			$affectedRows += BasePeer::doDeleteAll(ErConfigurationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a ErConfiguration or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or ErConfiguration object or primary key or array of primary keys
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
			$con = Propel::getConnection(ErConfigurationPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof ErConfiguration) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ErConfigurationPeer::ER_UID, (array) $values, Criteria::IN);
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
	 * Validates all modified columns of given ErConfiguration object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      ErConfiguration $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(ErConfiguration $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ErConfigurationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ErConfigurationPeer::TABLE_NAME);

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

		return BasePeer::doValidate(ErConfigurationPeer::DATABASE_NAME, ErConfigurationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     ErConfiguration
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(ErConfigurationPeer::DATABASE_NAME);

		$criteria->add(ErConfigurationPeer::ER_UID, $pk);


		$v = ErConfigurationPeer::doSelect($criteria, $con);

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
			$criteria->add(ErConfigurationPeer::ER_UID, $pks, Criteria::IN);
			$objs = ErConfigurationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseErConfigurationPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseErConfigurationPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'classes/model/map/ErConfigurationMapBuilder.php';
	Propel::registerMapBuilder('classes.model.map.ErConfigurationMapBuilder');
}
