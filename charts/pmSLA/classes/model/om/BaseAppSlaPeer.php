<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by AppSlaPeer::getOMClass()
include_once 'classes/model/AppSla.php';

/**
 * Base static class for performing query and update operations on the 'APP_SLA' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppSlaPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'APP_SLA';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.AppSla';

    /** The total number of columns. */
    const NUM_COLUMNS = 10;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the APP_UID field */
    const APP_UID = 'APP_SLA.APP_UID';

    /** the column name for the SLA_UID field */
    const SLA_UID = 'APP_SLA.SLA_UID';

    /** the column name for the APP_SLA_INIT_DATE field */
    const APP_SLA_INIT_DATE = 'APP_SLA.APP_SLA_INIT_DATE';

    /** the column name for the APP_SLA_DUE_DATE field */
    const APP_SLA_DUE_DATE = 'APP_SLA.APP_SLA_DUE_DATE';

    /** the column name for the APP_SLA_FINISH_DATE field */
    const APP_SLA_FINISH_DATE = 'APP_SLA.APP_SLA_FINISH_DATE';

    /** the column name for the APP_SLA_DURATION field */
    const APP_SLA_DURATION = 'APP_SLA.APP_SLA_DURATION';

    /** the column name for the APP_SLA_REMAINING field */
    const APP_SLA_REMAINING = 'APP_SLA.APP_SLA_REMAINING';

    /** the column name for the APP_SLA_EXCEEDED field */
    const APP_SLA_EXCEEDED = 'APP_SLA.APP_SLA_EXCEEDED';

    /** the column name for the APP_SLA_PEN_VALUE field */
    const APP_SLA_PEN_VALUE = 'APP_SLA.APP_SLA_PEN_VALUE';

    /** the column name for the APP_SLA_STATUS field */
    const APP_SLA_STATUS = 'APP_SLA.APP_SLA_STATUS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid', 'SlaUid', 'AppSlaInitDate', 'AppSlaDueDate', 'AppSlaFinishDate', 'AppSlaDuration', 'AppSlaRemaining', 'AppSlaExceeded', 'AppSlaPenValue', 'AppSlaStatus', ),
        BasePeer::TYPE_COLNAME => array (AppSlaPeer::APP_UID, AppSlaPeer::SLA_UID, AppSlaPeer::APP_SLA_INIT_DATE, AppSlaPeer::APP_SLA_DUE_DATE, AppSlaPeer::APP_SLA_FINISH_DATE, AppSlaPeer::APP_SLA_DURATION, AppSlaPeer::APP_SLA_REMAINING, AppSlaPeer::APP_SLA_EXCEEDED, AppSlaPeer::APP_SLA_PEN_VALUE, AppSlaPeer::APP_SLA_STATUS, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID', 'SLA_UID', 'APP_SLA_INIT_DATE', 'APP_SLA_DUE_DATE', 'APP_SLA_FINISH_DATE', 'APP_SLA_DURATION', 'APP_SLA_REMAINING', 'APP_SLA_EXCEEDED', 'APP_SLA_PEN_VALUE', 'APP_SLA_STATUS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AppUid' => 0, 'SlaUid' => 1, 'AppSlaInitDate' => 2, 'AppSlaDueDate' => 3, 'AppSlaFinishDate' => 4, 'AppSlaDuration' => 5, 'AppSlaRemaining' => 6, 'AppSlaExceeded' => 7, 'AppSlaPenValue' => 8, 'AppSlaStatus' => 9, ),
        BasePeer::TYPE_COLNAME => array (AppSlaPeer::APP_UID => 0, AppSlaPeer::SLA_UID => 1, AppSlaPeer::APP_SLA_INIT_DATE => 2, AppSlaPeer::APP_SLA_DUE_DATE => 3, AppSlaPeer::APP_SLA_FINISH_DATE => 4, AppSlaPeer::APP_SLA_DURATION => 5, AppSlaPeer::APP_SLA_REMAINING => 6, AppSlaPeer::APP_SLA_EXCEEDED => 7, AppSlaPeer::APP_SLA_PEN_VALUE => 8, AppSlaPeer::APP_SLA_STATUS => 9, ),
        BasePeer::TYPE_FIELDNAME => array ('APP_UID' => 0, 'SLA_UID' => 1, 'APP_SLA_INIT_DATE' => 2, 'APP_SLA_DUE_DATE' => 3, 'APP_SLA_FINISH_DATE' => 4, 'APP_SLA_DURATION' => 5, 'APP_SLA_REMAINING' => 6, 'APP_SLA_EXCEEDED' => 7, 'APP_SLA_PEN_VALUE' => 8, 'APP_SLA_STATUS' => 9, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/AppSlaMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.AppSlaMapBuilder');
    }
    /**
     * Gets a map (hash) of PHP names to DB column names.
     *
     * @return     array The PHP to DB name map for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
     */
    public static function getPhpNameMap()
    {
        if (self::$phpNameMap === null) {
            $map = AppSlaPeer::getTableMap();
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
     *      $c->addAlias("alias1", TablePeer::TABLE_NAME);
     *      $c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. AppSlaPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AppSlaPeer::TABLE_NAME.'.', $alias.'.', $column);
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
     *       rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria)
    {

        $criteria->addSelectColumn(AppSlaPeer::APP_UID);

        $criteria->addSelectColumn(AppSlaPeer::SLA_UID);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_INIT_DATE);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DUE_DATE);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_FINISH_DATE);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_DURATION);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_REMAINING);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_EXCEEDED);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_PEN_VALUE);

        $criteria->addSelectColumn(AppSlaPeer::APP_SLA_STATUS);

    }

    const COUNT = 'COUNT(APP_SLA.APP_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT APP_SLA.APP_UID)';

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
            $criteria->addSelectColumn(AppSlaPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(AppSlaPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = AppSlaPeer::doSelectRS($criteria, $con);
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
     * @return     AppSla
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AppSlaPeer::doSelect($critcopy, $con);
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
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, $con = null)
    {
        return AppSlaPeer::populateObjects(AppSlaPeer::doSelectRS($criteria, $con));
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
     *       rethrown wrapped into a PropelException.
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
            AppSlaPeer::addSelectColumns($criteria);
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
     *       rethrown wrapped into a PropelException.
     */
    public static function populateObjects(ResultSet $rs)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = AppSlaPeer::getOMClass();
        $cls = Propel::import($cls);
        // populate the object(s)
        while ($rs->next()) {

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
     *       rethrown wrapped into a PropelException.
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
        return AppSlaPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a AppSla or Criteria object.
     *
     * @param      mixed $values Criteria or AppSla object containing data that is used to create the INSERT statement.
     * @param      Connection $con the connection to use
     * @return     mixed The new primary key.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from AppSla object
        }


        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->begin();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }

        return $pk;
    }

    /**
     * Method perform an UPDATE on the database, given a AppSla or Criteria object.
     *
     * @param      mixed $values Criteria or AppSla object containing data create the UPDATE statement.
     * @param      Connection $con The connection to use (specify Connection exert more control over transactions).
     * @return     int The number of affected rows (if supported by underlying database driver).
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $selectCriteria = new Criteria(self::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(AppSlaPeer::APP_UID);
            $selectCriteria->add(AppSlaPeer::APP_UID, $criteria->remove(AppSlaPeer::APP_UID), $comparison);

            $comparison = $criteria->getComparison(AppSlaPeer::SLA_UID);
            $selectCriteria->add(AppSlaPeer::SLA_UID, $criteria->remove(AppSlaPeer::SLA_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the APP_SLA table.
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
            $affectedRows += BasePeer::doDeleteAll(AppSlaPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a AppSla or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AppSla object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      Connection $con the connection to use
     * @return     int  The number of affected rows (if supported by underlying database driver).
     *             This includes CASCADE-related rows
     *              if supported by native driver or if emulated using Propel.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
    */
    public static function doDelete($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AppSlaPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof AppSla) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey
            // values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            $vals = array();
            foreach ($values as $value) {

                $vals[0][] = $value[0];
                $vals[1][] = $value[1];
            }

            $criteria->add(AppSlaPeer::APP_UID, $vals[0], Criteria::IN);
            $criteria->add(AppSlaPeer::SLA_UID, $vals[1], Criteria::IN);
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
     * Validates all modified columns of given AppSla object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      AppSla $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(AppSla $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AppSlaPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AppSlaPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->containsColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(AppSlaPeer::DATABASE_NAME, AppSlaPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $app_uid
       * @param string $sla_uid
        * @param      Connection $con
     * @return     AppSla
     */
    public static function retrieveByPK($app_uid, $sla_uid, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(AppSlaPeer::APP_UID, $app_uid);
        $criteria->add(AppSlaPeer::SLA_UID, $sla_uid);
        $v = AppSlaPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseAppSlaPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/AppSlaMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.AppSlaMapBuilder');
}

