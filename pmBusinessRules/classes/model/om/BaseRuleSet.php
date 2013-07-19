<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/RuleSetPeer.php';

/**
 * Base class that represents a row from the 'RULE_SET' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseRuleSet extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        RuleSetPeer
    */
    protected static $peer;

    /**
     * The value for the rst_uid field.
     * @var        string
     */
    protected $rst_uid = '';

    /**
     * The value for the rst_name field.
     * @var        string
     */
    protected $rst_name = '';

    /**
     * The value for the rst_description field.
     * @var        string
     */
    protected $rst_description = '';

    /**
     * The value for the rst_type field.
     * @var        string
     */
    protected $rst_type = '';

    /**
     * The value for the rst_struct field.
     * @var        string
     */
    protected $rst_struct;

    /**
     * The value for the rst_source field.
     * @var        string
     */
    protected $rst_source;

    /**
     * The value for the rst_create_date field.
     * @var        int
     */
    protected $rst_create_date;

    /**
     * The value for the rst_update_date field.
     * @var        int
     */
    protected $rst_update_date;

    /**
     * The value for the rst_checksum field.
     * @var        string
     */
    protected $rst_checksum;

    /**
     * The value for the rst_deleted field.
     * @var        boolean
     */
    protected $rst_deleted = false;

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid;

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
     * Get the [rst_uid] column value.
     * 
     * @return     string
     */
    public function getRstUid()
    {

        return $this->rst_uid;
    }

    /**
     * Get the [rst_name] column value.
     * 
     * @return     string
     */
    public function getRstName()
    {

        return $this->rst_name;
    }

    /**
     * Get the [rst_description] column value.
     * 
     * @return     string
     */
    public function getRstDescription()
    {

        return $this->rst_description;
    }

    /**
     * Get the [rst_type] column value.
     * 
     * @return     string
     */
    public function getRstType()
    {

        return $this->rst_type;
    }

    /**
     * Get the [rst_struct] column value.
     * 
     * @return     string
     */
    public function getRstStruct()
    {

        return $this->rst_struct;
    }

    /**
     * Get the [rst_source] column value.
     * 
     * @return     string
     */
    public function getRstSource()
    {

        return $this->rst_source;
    }

    /**
     * Get the [optionally formatted] [rst_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getRstCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->rst_create_date === null || $this->rst_create_date === '') {
            return null;
        } elseif (!is_int($this->rst_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->rst_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [rst_create_date] as date/time value: " .
                    var_export($this->rst_create_date, true));
            }
        } else {
            $ts = $this->rst_create_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [rst_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getRstUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->rst_update_date === null || $this->rst_update_date === '') {
            return null;
        } elseif (!is_int($this->rst_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->rst_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [rst_update_date] as date/time value: " .
                    var_export($this->rst_update_date, true));
            }
        } else {
            $ts = $this->rst_update_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [rst_checksum] column value.
     * 
     * @return     string
     */
    public function getRstChecksum()
    {

        return $this->rst_checksum;
    }

    /**
     * Get the [rst_deleted] column value.
     * 
     * @return     boolean
     */
    public function getRstDeleted()
    {

        return $this->rst_deleted;
    }

    /**
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
    }

    /**
     * Set the value of [rst_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_uid !== $v || $v === '') {
            $this->rst_uid = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_UID;
        }

    } // setRstUid()

    /**
     * Set the value of [rst_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_name !== $v || $v === '') {
            $this->rst_name = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_NAME;
        }

    } // setRstName()

    /**
     * Set the value of [rst_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_description !== $v || $v === '') {
            $this->rst_description = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_DESCRIPTION;
        }

    } // setRstDescription()

    /**
     * Set the value of [rst_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_type !== $v || $v === '') {
            $this->rst_type = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_TYPE;
        }

    } // setRstType()

    /**
     * Set the value of [rst_struct] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstStruct($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_struct !== $v) {
            $this->rst_struct = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_STRUCT;
        }

    } // setRstStruct()

    /**
     * Set the value of [rst_source] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstSource($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_source !== $v) {
            $this->rst_source = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_SOURCE;
        }

    } // setRstSource()

    /**
     * Set the value of [rst_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRstCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [rst_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->rst_create_date !== $ts) {
            $this->rst_create_date = $ts;
            $this->modifiedColumns[] = RuleSetPeer::RST_CREATE_DATE;
        }

    } // setRstCreateDate()

    /**
     * Set the value of [rst_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRstUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [rst_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->rst_update_date !== $ts) {
            $this->rst_update_date = $ts;
            $this->modifiedColumns[] = RuleSetPeer::RST_UPDATE_DATE;
        }

    } // setRstUpdateDate()

    /**
     * Set the value of [rst_checksum] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRstChecksum($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rst_checksum !== $v) {
            $this->rst_checksum = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_CHECKSUM;
        }

    } // setRstChecksum()

    /**
     * Set the value of [rst_deleted] column.
     * 
     * @param      boolean $v new value
     * @return     void
     */
    public function setRstDeleted($v)
    {

        if ($this->rst_deleted !== $v || $v === false) {
            $this->rst_deleted = $v;
            $this->modifiedColumns[] = RuleSetPeer::RST_DELETED;
        }

    } // setRstDeleted()

    /**
     * Set the value of [pro_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_uid !== $v) {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = RuleSetPeer::PRO_UID;
        }

    } // setProUid()

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

            $this->rst_uid = $rs->getString($startcol + 0);

            $this->rst_name = $rs->getString($startcol + 1);

            $this->rst_description = $rs->getString($startcol + 2);

            $this->rst_type = $rs->getString($startcol + 3);

            $this->rst_struct = $rs->getString($startcol + 4);

            $this->rst_source = $rs->getString($startcol + 5);

            $this->rst_create_date = $rs->getTimestamp($startcol + 6, null);

            $this->rst_update_date = $rs->getTimestamp($startcol + 7, null);

            $this->rst_checksum = $rs->getString($startcol + 8);

            $this->rst_deleted = $rs->getBoolean($startcol + 9);

            $this->pro_uid = $rs->getString($startcol + 10);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 11; // 11 = RuleSetPeer::NUM_COLUMNS - RuleSetPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating RuleSet object", $e);
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
            $con = Propel::getConnection(RuleSetPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            RuleSetPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(RuleSetPeer::DATABASE_NAME);
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
                    $pk = RuleSetPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += RuleSetPeer::doUpdate($this, $con);
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


            if (($retval = RuleSetPeer::doValidate($this, $columns)) !== true) {
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
        $pos = RuleSetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRstUid();
                break;
            case 1:
                return $this->getRstName();
                break;
            case 2:
                return $this->getRstDescription();
                break;
            case 3:
                return $this->getRstType();
                break;
            case 4:
                return $this->getRstStruct();
                break;
            case 5:
                return $this->getRstSource();
                break;
            case 6:
                return $this->getRstCreateDate();
                break;
            case 7:
                return $this->getRstUpdateDate();
                break;
            case 8:
                return $this->getRstChecksum();
                break;
            case 9:
                return $this->getRstDeleted();
                break;
            case 10:
                return $this->getProUid();
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
        $keys = RuleSetPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getRstUid(),
            $keys[1] => $this->getRstName(),
            $keys[2] => $this->getRstDescription(),
            $keys[3] => $this->getRstType(),
            $keys[4] => $this->getRstStruct(),
            $keys[5] => $this->getRstSource(),
            $keys[6] => $this->getRstCreateDate(),
            $keys[7] => $this->getRstUpdateDate(),
            $keys[8] => $this->getRstChecksum(),
            $keys[9] => $this->getRstDeleted(),
            $keys[10] => $this->getProUid(),
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
        $pos = RuleSetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setRstUid($value);
                break;
            case 1:
                $this->setRstName($value);
                break;
            case 2:
                $this->setRstDescription($value);
                break;
            case 3:
                $this->setRstType($value);
                break;
            case 4:
                $this->setRstStruct($value);
                break;
            case 5:
                $this->setRstSource($value);
                break;
            case 6:
                $this->setRstCreateDate($value);
                break;
            case 7:
                $this->setRstUpdateDate($value);
                break;
            case 8:
                $this->setRstChecksum($value);
                break;
            case 9:
                $this->setRstDeleted($value);
                break;
            case 10:
                $this->setProUid($value);
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
        $keys = RuleSetPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setRstUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setRstName($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setRstDescription($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setRstType($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setRstStruct($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setRstSource($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setRstCreateDate($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setRstUpdateDate($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setRstChecksum($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setRstDeleted($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setProUid($arr[$keys[10]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RuleSetPeer::DATABASE_NAME);

        if ($this->isColumnModified(RuleSetPeer::RST_UID)) {
            $criteria->add(RuleSetPeer::RST_UID, $this->rst_uid);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_NAME)) {
            $criteria->add(RuleSetPeer::RST_NAME, $this->rst_name);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_DESCRIPTION)) {
            $criteria->add(RuleSetPeer::RST_DESCRIPTION, $this->rst_description);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_TYPE)) {
            $criteria->add(RuleSetPeer::RST_TYPE, $this->rst_type);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_STRUCT)) {
            $criteria->add(RuleSetPeer::RST_STRUCT, $this->rst_struct);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_SOURCE)) {
            $criteria->add(RuleSetPeer::RST_SOURCE, $this->rst_source);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_CREATE_DATE)) {
            $criteria->add(RuleSetPeer::RST_CREATE_DATE, $this->rst_create_date);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_UPDATE_DATE)) {
            $criteria->add(RuleSetPeer::RST_UPDATE_DATE, $this->rst_update_date);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_CHECKSUM)) {
            $criteria->add(RuleSetPeer::RST_CHECKSUM, $this->rst_checksum);
        }

        if ($this->isColumnModified(RuleSetPeer::RST_DELETED)) {
            $criteria->add(RuleSetPeer::RST_DELETED, $this->rst_deleted);
        }

        if ($this->isColumnModified(RuleSetPeer::PRO_UID)) {
            $criteria->add(RuleSetPeer::PRO_UID, $this->pro_uid);
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
        $criteria = new Criteria(RuleSetPeer::DATABASE_NAME);

        $criteria->add(RuleSetPeer::RST_UID, $this->rst_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getRstUid();
    }

    /**
     * Generic method to set the primary key (rst_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setRstUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of RuleSet (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setRstName($this->rst_name);

        $copyObj->setRstDescription($this->rst_description);

        $copyObj->setRstType($this->rst_type);

        $copyObj->setRstStruct($this->rst_struct);

        $copyObj->setRstSource($this->rst_source);

        $copyObj->setRstCreateDate($this->rst_create_date);

        $copyObj->setRstUpdateDate($this->rst_update_date);

        $copyObj->setRstChecksum($this->rst_checksum);

        $copyObj->setRstDeleted($this->rst_deleted);

        $copyObj->setProUid($this->pro_uid);


        $copyObj->setNew(true);

        $copyObj->setRstUid(''); // this is a pkey column, so set to default value

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
     * @return     RuleSet Clone of current object.
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
     * @return     RuleSetPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new RuleSetPeer();
        }
        return self::$peer;
    }
}

