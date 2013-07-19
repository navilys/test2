<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by SlaPeer::getOMClass()
include_once 'classes/model/Sla.php';

/**
 * Base static class for performing query and update operations on the 'SLA' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSlaPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'SLA';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.Sla';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the SLA_UID field */
    const SLA_UID = 'SLA.SLA_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'SLA.PRO_UID';

    /** the column name for the SLA_NAME field */
    const SLA_NAME = 'SLA.SLA_NAME';

    /** the column name for the SLA_DESCRIPTION field */
    const SLA_DESCRIPTION = 'SLA.SLA_DESCRIPTION';

    /** the column name for the SLA_TYPE field */
    const SLA_TYPE = 'SLA.SLA_TYPE';

    /** the column name for the SLA_TAS_START field */
    const SLA_TAS_START = 'SLA.SLA_TAS_START';

    /** the column name for the SLA_TAS_END field */
    const SLA_TAS_END = 'SLA.SLA_TAS_END';

    /** the column name for the SLA_TIME_DURATION field */
    const SLA_TIME_DURATION = 'SLA.SLA_TIME_DURATION';

    /** the column name for the SLA_TIME_DURATION_MODE field */
    const SLA_TIME_DURATION_MODE = 'SLA.SLA_TIME_DURATION_MODE';

    /** the column name for the SLA_CONDITIONS field */
    const SLA_CONDITIONS = 'SLA.SLA_CONDITIONS';

    /** the column name for the SLA_PEN_ENABLED field */
    const SLA_PEN_ENABLED = 'SLA.SLA_PEN_ENABLED';

    /** the column name for the SLA_PEN_TIME field */
    const SLA_PEN_TIME = 'SLA.SLA_PEN_TIME';

    /** the column name for the SLA_PEN_TIME_MODE field */
    const SLA_PEN_TIME_MODE = 'SLA.SLA_PEN_TIME_MODE';

    /** the column name for the SLA_PEN_VALUE field */
    const SLA_PEN_VALUE = 'SLA.SLA_PEN_VALUE';

    /** the column name for the SLA_PEN_VALUE_UNIT field */
    const SLA_PEN_VALUE_UNIT = 'SLA.SLA_PEN_VALUE_UNIT';

    /** the column name for the SLA_STATUS field */
    const SLA_STATUS = 'SLA.SLA_STATUS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('SlaUid', 'ProUid', 'SlaName', 'SlaDescription', 'SlaType', 'SlaTasStart', 'SlaTasEnd', 'SlaTimeDuration', 'SlaTimeDurationMode', 'SlaConditions', 'SlaPenEnabled', 'SlaPenTime', 'SlaPenTimeMode', 'SlaPenValue', 'SlaPenValueUnit', 'SlaStatus', ),
        BasePeer::TYPE_COLNAME => array (SlaPeer::SLA_UID, SlaPeer::PRO_UID, SlaPeer::SLA_NAME, SlaPeer::SLA_DESCRIPTION, SlaPeer::SLA_TYPE, SlaPeer::SLA_TAS_START, SlaPeer::SLA_TAS_END, SlaPeer::SLA_TIME_DURATION, SlaPeer::SLA_TIME_DURATION_MODE, SlaPeer::SLA_CONDITIONS, SlaPeer::SLA_PEN_ENABLED, SlaPeer::SLA_PEN_TIME, SlaPeer::SLA_PEN_TIME_MODE, SlaPeer::SLA_PEN_VALUE, SlaPeer::SLA_PEN_VALUE_UNIT, SlaPeer::SLA_STATUS, ),
        BasePeer::TYPE_FIELDNAME => array ('SLA_UID', 'PRO_UID', 'SLA_NAME', 'SLA_DESCRIPTION', 'SLA_TYPE', 'SLA_TAS_START', 'SLA_TAS_END', 'SLA_TIME_DURATION', 'SLA_TIME_DURATION_MODE', 'SLA_CONDITIONS', 'SLA_PEN_ENABLED', 'SLA_PEN_TIME', 'SLA_PEN_TIME_MODE', 'SLA_PEN_VALUE', 'SLA_PEN_VALUE_UNIT', 'SLA_STATUS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('SlaUid' => 0, 'ProUid' => 1, 'SlaName' => 2, 'SlaDescription' => 3, 'SlaType' => 4, 'SlaTasStart' => 5, 'SlaTasEnd' => 6, 'SlaTimeDuration' => 7, 'SlaTimeDurationMode' => 8, 'SlaConditions' => 9, 'SlaPenEnabled' => 10, 'SlaPenTime' => 11, 'SlaPenTimeMode' => 12, 'SlaPenValue' => 13, 'SlaPenValueUnit' => 14, 'SlaStatus' => 15, ),
        BasePeer::TYPE_COLNAME => array (SlaPeer::SLA_UID => 0, SlaPeer::PRO_UID => 1, SlaPeer::SLA_NAME => 2, SlaPeer::SLA_DESCRIPTION => 3, SlaPeer::SLA_TYPE => 4, SlaPeer::SLA_TAS_START => 5, SlaPeer::SLA_TAS_END => 6, SlaPeer::SLA_TIME_DURATION => 7, SlaPeer::SLA_TIME_DURATION_MODE => 8, SlaPeer::SLA_CONDITIONS => 9, SlaPeer::SLA_PEN_ENABLED => 10, SlaPeer::SLA_PEN_TIME => 11, SlaPeer::SLA_PEN_TIME_MODE => 12, SlaPeer::SLA_PEN_VALUE => 13, SlaPeer::SLA_PEN_VALUE_UNIT => 14, SlaPeer::SLA_STATUS => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('SLA_UID' => 0, 'PRO_UID' => 1, 'SLA_NAME' => 2, 'SLA_DESCRIPTION' => 3, 'SLA_TYPE' => 4, 'SLA_TAS_START' => 5, 'SLA_TAS_END' => 6, 'SLA_TIME_DURATION' => 7, 'SLA_TIME_DURATION_MODE' => 8, 'SLA_CONDITIONS' => 9, 'SLA_PEN_ENABLED' => 10, 'SLA_PEN_TIME' => 11, 'SLA_PEN_TIME_MODE' => 12, 'SLA_PEN_VALUE' => 13, 'SLA_PEN_VALUE_UNIT' => 14, 'SLA_STATUS' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/SlaMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.SlaMapBuilder');
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
            $map = SlaPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. SlaPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SlaPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(SlaPeer::SLA_UID);

        $criteria->addSelectColumn(SlaPeer::PRO_UID);

        $criteria->addSelectColumn(SlaPeer::SLA_NAME);

        $criteria->addSelectColumn(SlaPeer::SLA_DESCRIPTION);

        $criteria->addSelectColumn(SlaPeer::SLA_TYPE);

        $criteria->addSelectColumn(SlaPeer::SLA_TAS_START);

        $criteria->addSelectColumn(SlaPeer::SLA_TAS_END);

        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION);

        $criteria->addSelectColumn(SlaPeer::SLA_TIME_DURATION_MODE);

        $criteria->addSelectColumn(SlaPeer::SLA_CONDITIONS);

        $criteria->addSelectColumn(SlaPeer::SLA_PEN_ENABLED);

        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME);

        $criteria->addSelectColumn(SlaPeer::SLA_PEN_TIME_MODE);

        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE);

        $criteria->addSelectColumn(SlaPeer::SLA_PEN_VALUE_UNIT);

        $criteria->addSelectColumn(SlaPeer::SLA_STATUS);

    }

    const COUNT = 'COUNT(SLA.SLA_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT SLA.SLA_UID)';

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
            $criteria->addSelectColumn(SlaPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(SlaPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = SlaPeer::doSelectRS($criteria, $con);
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
     * @return     Sla
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SlaPeer::doSelect($critcopy, $con);
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
        return SlaPeer::populateObjects(SlaPeer::doSelectRS($criteria, $con));
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
            SlaPeer::addSelectColumns($criteria);
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
        $cls = SlaPeer::getOMClass();
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
        return SlaPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a Sla or Criteria object.
     *
     * @param      mixed $values Criteria or Sla object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from Sla object
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
     * Method perform an UPDATE on the database, given a Sla or Criteria object.
     *
     * @param      mixed $values Criteria or Sla object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(SlaPeer::SLA_UID);
            $selectCriteria->add(SlaPeer::SLA_UID, $criteria->remove(SlaPeer::SLA_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the SLA table.
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
            $affectedRows += BasePeer::doDeleteAll(SlaPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a Sla or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Sla object or primary key or array of primary keys
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
            $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof Sla) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(SlaPeer::SLA_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given Sla object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Sla $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(Sla $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SlaPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SlaPeer::TABLE_NAME);

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

        return BasePeer::doValidate(SlaPeer::DATABASE_NAME, SlaPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     Sla
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(SlaPeer::DATABASE_NAME);

        $criteria->add(SlaPeer::SLA_UID, $pk);


        $v = SlaPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      Connection $con the connection to use
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
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
            $criteria->add(SlaPeer::SLA_UID, $pks, Criteria::IN);
            $objs = SlaPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseSlaPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/SlaMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.SlaMapBuilder');
}

