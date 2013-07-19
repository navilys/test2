<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by KtDocumentPeer::getOMClass()
include_once 'classes/model/KtDocument.php';

/**
 * Base static class for performing query and update operations on the 'KT_DOCUMENT' table.
 *
 * 
 *
 * @package    model.om
 */
abstract class BaseKtDocumentPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'workflow';

	/** the table name for this class */
	const TABLE_NAME = 'KT_DOCUMENT';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'model.KtDocument';

	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the DOC_UID field */
	const DOC_UID = 'KT_DOCUMENT.DOC_UID';

	/** the column name for the DOC_TYPE field */
	const DOC_TYPE = 'KT_DOCUMENT.DOC_TYPE';

	/** the column name for the DOC_PMTYPE field */
	const DOC_PMTYPE = 'KT_DOCUMENT.DOC_PMTYPE';

	/** the column name for the PRO_UID field */
	const PRO_UID = 'KT_DOCUMENT.PRO_UID';

	/** the column name for the APP_UID field */
	const APP_UID = 'KT_DOCUMENT.APP_UID';

	/** the column name for the KT_DOCUMENT_ID field */
	const KT_DOCUMENT_ID = 'KT_DOCUMENT.KT_DOCUMENT_ID';

	/** the column name for the KT_STATUS field */
	const KT_STATUS = 'KT_DOCUMENT.KT_STATUS';

	/** the column name for the KT_DOCUMENT_TITLE field */
	const KT_DOCUMENT_TITLE = 'KT_DOCUMENT.KT_DOCUMENT_TITLE';

	/** the column name for the KT_FULL_PATH field */
	const KT_FULL_PATH = 'KT_DOCUMENT.KT_FULL_PATH';

	/** the column name for the KT_CREATE_USER field */
	const KT_CREATE_USER = 'KT_DOCUMENT.KT_CREATE_USER';

	/** the column name for the KT_CREATE_DATE field */
	const KT_CREATE_DATE = 'KT_DOCUMENT.KT_CREATE_DATE';

	/** the column name for the KT_UPDATE_DATE field */
	const KT_UPDATE_DATE = 'KT_DOCUMENT.KT_UPDATE_DATE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DocUid', 'DocType', 'DocPmtype', 'ProUid', 'AppUid', 'KtDocumentId', 'KtStatus', 'KtDocumentTitle', 'KtFullPath', 'KtCreateUser', 'KtCreateDate', 'KtUpdateDate', ),
		BasePeer::TYPE_COLNAME => array (KtDocumentPeer::DOC_UID, KtDocumentPeer::DOC_TYPE, KtDocumentPeer::DOC_PMTYPE, KtDocumentPeer::PRO_UID, KtDocumentPeer::APP_UID, KtDocumentPeer::KT_DOCUMENT_ID, KtDocumentPeer::KT_STATUS, KtDocumentPeer::KT_DOCUMENT_TITLE, KtDocumentPeer::KT_FULL_PATH, KtDocumentPeer::KT_CREATE_USER, KtDocumentPeer::KT_CREATE_DATE, KtDocumentPeer::KT_UPDATE_DATE, ),
		BasePeer::TYPE_FIELDNAME => array ('DOC_UID', 'DOC_TYPE', 'DOC_PMTYPE', 'PRO_UID', 'APP_UID', 'KT_DOCUMENT_ID', 'KT_STATUS', 'KT_DOCUMENT_TITLE', 'KT_FULL_PATH', 'KT_CREATE_USER', 'KT_CREATE_DATE', 'KT_UPDATE_DATE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DocUid' => 0, 'DocType' => 1, 'DocPmtype' => 2, 'ProUid' => 3, 'AppUid' => 4, 'KtDocumentId' => 5, 'KtStatus' => 6, 'KtDocumentTitle' => 7, 'KtFullPath' => 8, 'KtCreateUser' => 9, 'KtCreateDate' => 10, 'KtUpdateDate' => 11, ),
		BasePeer::TYPE_COLNAME => array (KtDocumentPeer::DOC_UID => 0, KtDocumentPeer::DOC_TYPE => 1, KtDocumentPeer::DOC_PMTYPE => 2, KtDocumentPeer::PRO_UID => 3, KtDocumentPeer::APP_UID => 4, KtDocumentPeer::KT_DOCUMENT_ID => 5, KtDocumentPeer::KT_STATUS => 6, KtDocumentPeer::KT_DOCUMENT_TITLE => 7, KtDocumentPeer::KT_FULL_PATH => 8, KtDocumentPeer::KT_CREATE_USER => 9, KtDocumentPeer::KT_CREATE_DATE => 10, KtDocumentPeer::KT_UPDATE_DATE => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('DOC_UID' => 0, 'DOC_TYPE' => 1, 'DOC_PMTYPE' => 2, 'PRO_UID' => 3, 'APP_UID' => 4, 'KT_DOCUMENT_ID' => 5, 'KT_STATUS' => 6, 'KT_DOCUMENT_TITLE' => 7, 'KT_FULL_PATH' => 8, 'KT_CREATE_USER' => 9, 'KT_CREATE_DATE' => 10, 'KT_UPDATE_DATE' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'classes/model/map/KtDocumentMapBuilder.php';
		return BasePeer::getMapBuilder('model.map.KtDocumentMapBuilder');
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
			$map = KtDocumentPeer::getTableMap();
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
	 * @param      string $column The column name for current table. (i.e. KtDocumentPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(KtDocumentPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(KtDocumentPeer::DOC_UID);

		$criteria->addSelectColumn(KtDocumentPeer::DOC_TYPE);

		$criteria->addSelectColumn(KtDocumentPeer::DOC_PMTYPE);

		$criteria->addSelectColumn(KtDocumentPeer::PRO_UID);

		$criteria->addSelectColumn(KtDocumentPeer::APP_UID);

		$criteria->addSelectColumn(KtDocumentPeer::KT_DOCUMENT_ID);

		$criteria->addSelectColumn(KtDocumentPeer::KT_STATUS);

		$criteria->addSelectColumn(KtDocumentPeer::KT_DOCUMENT_TITLE);

		$criteria->addSelectColumn(KtDocumentPeer::KT_FULL_PATH);

		$criteria->addSelectColumn(KtDocumentPeer::KT_CREATE_USER);

		$criteria->addSelectColumn(KtDocumentPeer::KT_CREATE_DATE);

		$criteria->addSelectColumn(KtDocumentPeer::KT_UPDATE_DATE);

	}

	const COUNT = 'COUNT(KT_DOCUMENT.DOC_UID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT KT_DOCUMENT.DOC_UID)';

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
			$criteria->addSelectColumn(KtDocumentPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(KtDocumentPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = KtDocumentPeer::doSelectRS($criteria, $con);
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
	 * @return     KtDocument
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = KtDocumentPeer::doSelect($critcopy, $con);
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
		return KtDocumentPeer::populateObjects(KtDocumentPeer::doSelectRS($criteria, $con));
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
			KtDocumentPeer::addSelectColumns($criteria);
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
		$cls = KtDocumentPeer::getOMClass();
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
		return KtDocumentPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a KtDocument or Criteria object.
	 *
	 * @param      mixed $values Criteria or KtDocument object containing data that is used to create the INSERT statement.
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
			$criteria = $values->buildCriteria(); // build Criteria from KtDocument object
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
	 * Method perform an UPDATE on the database, given a KtDocument or Criteria object.
	 *
	 * @param      mixed $values Criteria or KtDocument object containing data that is used to create the UPDATE statement.
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

			$comparison = $criteria->getComparison(KtDocumentPeer::DOC_UID);
			$selectCriteria->add(KtDocumentPeer::DOC_UID, $criteria->remove(KtDocumentPeer::DOC_UID), $comparison);

			$comparison = $criteria->getComparison(KtDocumentPeer::DOC_TYPE);
			$selectCriteria->add(KtDocumentPeer::DOC_TYPE, $criteria->remove(KtDocumentPeer::DOC_TYPE), $comparison);

		} else { // $values is KtDocument object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the KT_DOCUMENT table.
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
			$affectedRows += BasePeer::doDeleteAll(KtDocumentPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a KtDocument or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or KtDocument object or primary key or array of primary keys
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
			$con = Propel::getConnection(KtDocumentPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof KtDocument) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			// primary key is composite; we therefore, expect
			// the primary key passed to be an array of pkey
			// values
			if(count($values) == count($values, COUNT_RECURSIVE))
			{
				// array is not multi-dimensional
				$values = array($values);
			}
			$vals = array();
			foreach($values as $value)
			{

				$vals[0][] = $value[0];
				$vals[1][] = $value[1];
			}

			$criteria->add(KtDocumentPeer::DOC_UID, $vals[0], Criteria::IN);
			$criteria->add(KtDocumentPeer::DOC_TYPE, $vals[1], Criteria::IN);
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
	 * Validates all modified columns of given KtDocument object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      KtDocument $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(KtDocument $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(KtDocumentPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(KtDocumentPeer::TABLE_NAME);

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

		return BasePeer::doValidate(KtDocumentPeer::DATABASE_NAME, KtDocumentPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param string $doc_uid
	   @param string $doc_type
	   
	 * @param      Connection $con
	 * @return     KtDocument
	 */
	public static function retrieveByPK( $doc_uid, $doc_type, $con = null) {
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$criteria = new Criteria();
		$criteria->add(KtDocumentPeer::DOC_UID, $doc_uid);
		$criteria->add(KtDocumentPeer::DOC_TYPE, $doc_type);
		$v = KtDocumentPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseKtDocumentPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseKtDocumentPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'classes/model/map/KtDocumentMapBuilder.php';
	Propel::registerMapBuilder('model.map.KtDocumentMapBuilder');
}
