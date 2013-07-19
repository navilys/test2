<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/GlobalFieldsPeer.php';

/**
 * Base class that represents a row from the 'GLOBAL_FIELDS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseGlobalFields extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        GlobalFieldsPeer
    */
    protected static $peer;

    /**
     * The value for the gf_name field.
     * @var        string
     */
    protected $gf_name = '';

    /**
     * The value for the gf_value field.
     * @var        string
     */
    protected $gf_value;

    /**
     * The value for the gf_type field.
     * @var        string
     */
    protected $gf_type = '';

    /**
     * The value for the gf_query field.
     * @var        string
     */
    protected $gf_query = '';

    /**
     * The value for the dbs_uid field.
     * @var        string
     */
    protected $dbs_uid = '';

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Get the [gf_name] column value.
     * 
     * @return     string
     */
    public function getGfName()
    {

        return $this->gf_name;
    }

    /**
     * Get the [gf_value] column value.
     * 
     * @return     string
     */
    public function getGfValue()
    {

        return $this->gf_value;
    }

    /**
     * Get the [gf_type] column value.
     * 
     * @return     string
     */
    public function getGfType()
    {

        return $this->gf_type;
    }

    /**
     * Get the [gf_query] column value.
     * 
     * @return     string
     */
    public function getGfQuery()
    {

        return $this->gf_query;
    }

    /**
     * Get the [dbs_uid] column value.
     * 
     * @return     string
     */
    public function getDbsUid()
    {

        return $this->dbs_uid;
    }

    /**
     * Set the value of [gf_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGfName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gf_name !== $v || $v === '') {
            $this->gf_name = $v;
            $this->modifiedColumns[] = GlobalFieldsPeer::GF_NAME;
        }

    } // setGfName()

    /**
     * Set the value of [gf_value] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGfValue($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gf_value !== $v) {
            $this->gf_value = $v;
            $this->modifiedColumns[] = GlobalFieldsPeer::GF_VALUE;
        }

    } // setGfValue()

    /**
     * Set the value of [gf_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGfType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gf_type !== $v || $v === '') {
            $this->gf_type = $v;
            $this->modifiedColumns[] = GlobalFieldsPeer::GF_TYPE;
        }

    } // setGfType()

    /**
     * Set the value of [gf_query] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGfQuery($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gf_query !== $v || $v === '') {
            $this->gf_query = $v;
            $this->modifiedColumns[] = GlobalFieldsPeer::GF_QUERY;
        }

    } // setGfQuery()

    /**
     * Set the value of [dbs_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_uid !== $v || $v === '') {
            $this->dbs_uid = $v;
            $this->modifiedColumns[] = GlobalFieldsPeer::DBS_UID;
        }

    } // setDbsUid()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (1-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
     * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
     * @return     int next starting column
     * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(ResultSet $rs, $startcol = 1)
    {
        try {

            $this->gf_name = $rs->getString($startcol + 0);

            $this->gf_value = $rs->getString($startcol + 1);

            $this->gf_type = $rs->getString($startcol + 2);

            $this->gf_query = $rs->getString($startcol + 3);

            $this->dbs_uid = $rs->getString($startcol + 4);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 5; // 5 = GlobalFieldsPeer::NUM_COLUMNS - GlobalFieldsPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating GlobalFields object", $e);
        }
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      Connection $con
     * @return     void
     * @throws     PropelException
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(GlobalFieldsPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            GlobalFieldsPeer::doDelete($this, $con);
            $this->setDeleted(true);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.  If the object is new,
     * it inserts it; otherwise an update is performed.  This method
     * wraps the doSave() worker method in a transaction.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update
     * @throws     PropelException
     * @see        doSave()
     */
    public function save($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(GlobalFieldsPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            $affectedRows = $this->doSave($con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update and any referring
     * @throws     PropelException
     * @see        save()
     */
    protected function doSave($con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;


            // If this object has been modified, then save it to the database.
            if ($this->isModified()) {
                if ($this->isNew()) {
                    $pk = GlobalFieldsPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += GlobalFieldsPeer::doUpdate($this, $con);
                }
                $this->resetModified(); // [HL] After being saved an object is no longer 'modified'
            }

            $this->alreadyInSave = false;
        }
        return $affectedRows;
    } // doSave()

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return     array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return     boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();
            return true;
        } else {
            $this->validationFailures = $res;
            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return     mixed <code>true</code> if all validations pass; 
                   array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = GlobalFieldsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = GlobalFieldsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->getByPosition($pos);
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return     mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch($pos) {
            case 0:
                return $this->getGfName();
                break;
            case 1:
                return $this->getGfValue();
                break;
            case 2:
                return $this->getGfType();
                break;
            case 3:
                return $this->getGfQuery();
                break;
            case 4:
                return $this->getDbsUid();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param      string $keyType One of the class type constants TYPE_PHPNAME,
     *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = GlobalFieldsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getGfName(),
            $keys[1] => $this->getGfValue(),
            $keys[2] => $this->getGfType(),
            $keys[3] => $this->getGfQuery(),
            $keys[4] => $this->getDbsUid(),
        );
        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = GlobalFieldsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return     void
     */
    public function setByPosition($pos, $value)
    {
        switch($pos) {
            case 0:
                $this->setGfName($value);
                break;
            case 1:
                $this->setGfValue($value);
                break;
            case 2:
                $this->setGfType($value);
                break;
            case 3:
                $this->setGfQuery($value);
                break;
            case 4:
                $this->setDbsUid($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
     * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = GlobalFieldsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setGfName($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setGfValue($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setGfType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setGfQuery($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDbsUid($arr[$keys[4]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(GlobalFieldsPeer::DATABASE_NAME);

        if ($this->isColumnModified(GlobalFieldsPeer::GF_NAME)) {
            $criteria->add(GlobalFieldsPeer::GF_NAME, $this->gf_name);
        }

        if ($this->isColumnModified(GlobalFieldsPeer::GF_VALUE)) {
            $criteria->add(GlobalFieldsPeer::GF_VALUE, $this->gf_value);
        }

        if ($this->isColumnModified(GlobalFieldsPeer::GF_TYPE)) {
            $criteria->add(GlobalFieldsPeer::GF_TYPE, $this->gf_type);
        }

        if ($this->isColumnModified(GlobalFieldsPeer::GF_QUERY)) {
            $criteria->add(GlobalFieldsPeer::GF_QUERY, $this->gf_query);
        }

        if ($this->isColumnModified(GlobalFieldsPeer::DBS_UID)) {
            $criteria->add(GlobalFieldsPeer::DBS_UID, $this->dbs_uid);
        }


        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return     Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(GlobalFieldsPeer::DATABASE_NAME);

        $criteria->add(GlobalFieldsPeer::GF_NAME, $this->gf_name);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getGfName();
    }

    /**
     * Generic method to set the primary key (gf_name column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setGfName($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of GlobalFields (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setGfValue($this->gf_value);

        $copyObj->setGfType($this->gf_type);

        $copyObj->setGfQuery($this->gf_query);

        $copyObj->setDbsUid($this->dbs_uid);


        $copyObj->setNew(true);

        $copyObj->setGfName(''); // this is a pkey column, so set to default value

    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return     GlobalFields Clone of current object.
     * @throws     PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);
        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return     GlobalFieldsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new GlobalFieldsPeer();
        }
        return self::$peer;
    }
}

