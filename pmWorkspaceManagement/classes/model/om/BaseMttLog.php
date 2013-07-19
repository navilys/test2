<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'model'.PATH_SEP.'MttLogPeer.php';

/**
 * Base class that represents a row from the 'MttLog' table.
 *
 * 
 *
 * @package    workflow.classes.om
 */
abstract class BaseMttLog extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MttLogPeer
    */
    protected static $peer;

    /**
     * The value for the log_id field.
     * @var        int
     */
    protected $log_id;

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid;

    /**
     * The value for the log_ip field.
     * @var        string
     */
    protected $log_ip;

    /**
     * The value for the log_datetime field.
     * @var        int
     */
    protected $log_datetime;

    /**
     * The value for the log_action field.
     * @var        string
     */
    protected $log_action;

    /**
     * The value for the log_description field.
     * @var        string
     */
    protected $log_description;

    /**
     * The value for the log_type field.
     * @var        string
     */
    protected $log_type;

    /**
     * The value for the log_additional_details field.
     * @var        string
     */
    protected $log_additional_details;

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
     * Get the [log_id] column value.
     * 
     * @return     int
     */
    public function getLogId()
    {

        return $this->log_id;
    }

    /**
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Get the [log_ip] column value.
     * 
     * @return     string
     */
    public function getLogIp()
    {

        return $this->log_ip;
    }

    /**
     * Get the [optionally formatted] [log_datetime] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getLogDatetime($format = 'Y-m-d')
    {

        if ($this->log_datetime === null || $this->log_datetime === '') {
            return null;
        } elseif (!is_int($this->log_datetime)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->log_datetime);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [log_datetime] as date/time value: " .
                    var_export($this->log_datetime, true));
            }
        } else {
            $ts = $this->log_datetime;
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
     * Get the [log_action] column value.
     * 
     * @return     string
     */
    public function getLogAction()
    {

        return $this->log_action;
    }

    /**
     * Get the [log_description] column value.
     * 
     * @return     string
     */
    public function getLogDescription()
    {

        return $this->log_description;
    }

    /**
     * Get the [log_type] column value.
     * 
     * @return     string
     */
    public function getLogType()
    {

        return $this->log_type;
    }

    /**
     * Get the [log_additional_details] column value.
     * 
     * @return     string
     */
    public function getLogAdditionalDetails()
    {

        return $this->log_additional_details;
    }

    /**
     * Set the value of [log_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->log_id !== $v) {
            $this->log_id = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_ID;
        }

    } // setLogId()

    /**
     * Set the value of [usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_uid !== $v) {
            $this->usr_uid = $v;
            $this->modifiedColumns[] = MttLogPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [log_ip] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogIp($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_ip !== $v) {
            $this->log_ip = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_IP;
        }

    } // setLogIp()

    /**
     * Set the value of [log_datetime] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogDatetime($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [log_datetime] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->log_datetime !== $ts) {
            $this->log_datetime = $ts;
            $this->modifiedColumns[] = MttLogPeer::LOG_DATETIME;
        }

    } // setLogDatetime()

    /**
     * Set the value of [log_action] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogAction($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_action !== $v) {
            $this->log_action = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_ACTION;
        }

    } // setLogAction()

    /**
     * Set the value of [log_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_description !== $v) {
            $this->log_description = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_DESCRIPTION;
        }

    } // setLogDescription()

    /**
     * Set the value of [log_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_type !== $v) {
            $this->log_type = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_TYPE;
        }

    } // setLogType()

    /**
     * Set the value of [log_additional_details] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogAdditionalDetails($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_additional_details !== $v) {
            $this->log_additional_details = $v;
            $this->modifiedColumns[] = MttLogPeer::LOG_ADDITIONAL_DETAILS;
        }

    } // setLogAdditionalDetails()

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

            $this->log_id = $rs->getInt($startcol + 0);

            $this->usr_uid = $rs->getString($startcol + 1);

            $this->log_ip = $rs->getString($startcol + 2);

            $this->log_datetime = $rs->getDate($startcol + 3, null);

            $this->log_action = $rs->getString($startcol + 4);

            $this->log_description = $rs->getString($startcol + 5);

            $this->log_type = $rs->getString($startcol + 6);

            $this->log_additional_details = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = MttLogPeer::NUM_COLUMNS - MttLogPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating MttLog object", $e);
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
            $con = Propel::getConnection(MttLogPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            MttLogPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(MttLogPeer::DATABASE_NAME);
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
                    $pk = MttLogPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setLogId($pk);  //[IMV] update autoincrement primary key

                    $this->setNew(false);
                } else {
                    $affectedRows += MttLogPeer::doUpdate($this, $con);
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


            if (($retval = MttLogPeer::doValidate($this, $columns)) !== true) {
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
        $pos = MttLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLogId();
                break;
            case 1:
                return $this->getUsrUid();
                break;
            case 2:
                return $this->getLogIp();
                break;
            case 3:
                return $this->getLogDatetime();
                break;
            case 4:
                return $this->getLogAction();
                break;
            case 5:
                return $this->getLogDescription();
                break;
            case 6:
                return $this->getLogType();
                break;
            case 7:
                return $this->getLogAdditionalDetails();
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
        $keys = MttLogPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLogId(),
            $keys[1] => $this->getUsrUid(),
            $keys[2] => $this->getLogIp(),
            $keys[3] => $this->getLogDatetime(),
            $keys[4] => $this->getLogAction(),
            $keys[5] => $this->getLogDescription(),
            $keys[6] => $this->getLogType(),
            $keys[7] => $this->getLogAdditionalDetails(),
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
        $pos = MttLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setLogId($value);
                break;
            case 1:
                $this->setUsrUid($value);
                break;
            case 2:
                $this->setLogIp($value);
                break;
            case 3:
                $this->setLogDatetime($value);
                break;
            case 4:
                $this->setLogAction($value);
                break;
            case 5:
                $this->setLogDescription($value);
                break;
            case 6:
                $this->setLogType($value);
                break;
            case 7:
                $this->setLogAdditionalDetails($value);
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
        $keys = MttLogPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setLogId($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setUsrUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setLogIp($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setLogDatetime($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setLogAction($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setLogDescription($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setLogType($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setLogAdditionalDetails($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MttLogPeer::DATABASE_NAME);

        if ($this->isColumnModified(MttLogPeer::LOG_ID)) {
            $criteria->add(MttLogPeer::LOG_ID, $this->log_id);
        }

        if ($this->isColumnModified(MttLogPeer::USR_UID)) {
            $criteria->add(MttLogPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_IP)) {
            $criteria->add(MttLogPeer::LOG_IP, $this->log_ip);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_DATETIME)) {
            $criteria->add(MttLogPeer::LOG_DATETIME, $this->log_datetime);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_ACTION)) {
            $criteria->add(MttLogPeer::LOG_ACTION, $this->log_action);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_DESCRIPTION)) {
            $criteria->add(MttLogPeer::LOG_DESCRIPTION, $this->log_description);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_TYPE)) {
            $criteria->add(MttLogPeer::LOG_TYPE, $this->log_type);
        }

        if ($this->isColumnModified(MttLogPeer::LOG_ADDITIONAL_DETAILS)) {
            $criteria->add(MttLogPeer::LOG_ADDITIONAL_DETAILS, $this->log_additional_details);
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
        $criteria = new Criteria(MttLogPeer::DATABASE_NAME);

        $criteria->add(MttLogPeer::LOG_ID, $this->log_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     int
     */
    public function getPrimaryKey()
    {
        return $this->getLogId();
    }

    /**
     * Generic method to set the primary key (log_id column).
     *
     * @param      int $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setLogId($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of log (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setLogIp($this->log_ip);

        $copyObj->setLogDatetime($this->log_datetime);

        $copyObj->setLogAction($this->log_action);

        $copyObj->setLogDescription($this->log_description);

        $copyObj->setLogType($this->log_type);

        $copyObj->setLogAdditionalDetails($this->log_additional_details);


        $copyObj->setNew(true);

        $copyObj->setLogId(NULL); // this is a pkey column, so set to default value

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
     * @return     log Clone of current object.
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
     * @return     logPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MttLogPeer();
        }
        return self::$peer;
    }
}

