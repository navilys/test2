<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/SlaPeer.php';

/**
 * Base class that represents a row from the 'SLA' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSla extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SlaPeer
    */
    protected static $peer;

    /**
     * The value for the sla_uid field.
     * @var        string
     */
    protected $sla_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the sla_name field.
     * @var        string
     */
    protected $sla_name = '';

    /**
     * The value for the sla_description field.
     * @var        string
     */
    protected $sla_description = '';

    /**
     * The value for the sla_type field.
     * @var        string
     */
    protected $sla_type = '';

    /**
     * The value for the sla_tas_start field.
     * @var        string
     */
    protected $sla_tas_start = '';

    /**
     * The value for the sla_tas_end field.
     * @var        string
     */
    protected $sla_tas_end = '';

    /**
     * The value for the sla_time_duration field.
     * @var        int
     */
    protected $sla_time_duration = 0;

    /**
     * The value for the sla_time_duration_mode field.
     * @var        string
     */
    protected $sla_time_duration_mode = 'HOURS';

    /**
     * The value for the sla_conditions field.
     * @var        string
     */
    protected $sla_conditions = '';

    /**
     * The value for the sla_pen_enabled field.
     * @var        int
     */
    protected $sla_pen_enabled = 0;

    /**
     * The value for the sla_pen_time field.
     * @var        int
     */
    protected $sla_pen_time = 0;

    /**
     * The value for the sla_pen_time_mode field.
     * @var        string
     */
    protected $sla_pen_time_mode = 'HOURS';

    /**
     * The value for the sla_pen_value field.
     * @var        int
     */
    protected $sla_pen_value = 0;

    /**
     * The value for the sla_pen_value_unit field.
     * @var        string
     */
    protected $sla_pen_value_unit = '';

    /**
     * The value for the sla_status field.
     * @var        string
     */
    protected $sla_status = '';

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
     * Get the [sla_uid] column value.
     * 
     * @return     string
     */
    public function getSlaUid()
    {

        return $this->sla_uid;
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
     * Get the [sla_name] column value.
     * 
     * @return     string
     */
    public function getSlaName()
    {

        return $this->sla_name;
    }

    /**
     * Get the [sla_description] column value.
     * 
     * @return     string
     */
    public function getSlaDescription()
    {

        return $this->sla_description;
    }

    /**
     * Get the [sla_type] column value.
     * 
     * @return     string
     */
    public function getSlaType()
    {

        return $this->sla_type;
    }

    /**
     * Get the [sla_tas_start] column value.
     * 
     * @return     string
     */
    public function getSlaTasStart()
    {

        return $this->sla_tas_start;
    }

    /**
     * Get the [sla_tas_end] column value.
     * 
     * @return     string
     */
    public function getSlaTasEnd()
    {

        return $this->sla_tas_end;
    }

    /**
     * Get the [sla_time_duration] column value.
     * 
     * @return     int
     */
    public function getSlaTimeDuration()
    {

        return $this->sla_time_duration;
    }

    /**
     * Get the [sla_time_duration_mode] column value.
     * 
     * @return     string
     */
    public function getSlaTimeDurationMode()
    {

        return $this->sla_time_duration_mode;
    }

    /**
     * Get the [sla_conditions] column value.
     * 
     * @return     string
     */
    public function getSlaConditions()
    {

        return $this->sla_conditions;
    }

    /**
     * Get the [sla_pen_enabled] column value.
     * 
     * @return     int
     */
    public function getSlaPenEnabled()
    {

        return $this->sla_pen_enabled;
    }

    /**
     * Get the [sla_pen_time] column value.
     * 
     * @return     int
     */
    public function getSlaPenTime()
    {

        return $this->sla_pen_time;
    }

    /**
     * Get the [sla_pen_time_mode] column value.
     * 
     * @return     string
     */
    public function getSlaPenTimeMode()
    {

        return $this->sla_pen_time_mode;
    }

    /**
     * Get the [sla_pen_value] column value.
     * 
     * @return     int
     */
    public function getSlaPenValue()
    {

        return $this->sla_pen_value;
    }

    /**
     * Get the [sla_pen_value_unit] column value.
     * 
     * @return     string
     */
    public function getSlaPenValueUnit()
    {

        return $this->sla_pen_value_unit;
    }

    /**
     * Get the [sla_status] column value.
     * 
     * @return     string
     */
    public function getSlaStatus()
    {

        return $this->sla_status;
    }

    /**
     * Set the value of [sla_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_uid !== $v || $v === '') {
            $this->sla_uid = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_UID;
        }

    } // setSlaUid()

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

        if ($this->pro_uid !== $v || $v === '') {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = SlaPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [sla_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_name !== $v || $v === '') {
            $this->sla_name = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_NAME;
        }

    } // setSlaName()

    /**
     * Set the value of [sla_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_description !== $v || $v === '') {
            $this->sla_description = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_DESCRIPTION;
        }

    } // setSlaDescription()

    /**
     * Set the value of [sla_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_type !== $v || $v === '') {
            $this->sla_type = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_TYPE;
        }

    } // setSlaType()

    /**
     * Set the value of [sla_tas_start] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaTasStart($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_tas_start !== $v || $v === '') {
            $this->sla_tas_start = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_TAS_START;
        }

    } // setSlaTasStart()

    /**
     * Set the value of [sla_tas_end] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaTasEnd($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_tas_end !== $v || $v === '') {
            $this->sla_tas_end = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_TAS_END;
        }

    } // setSlaTasEnd()

    /**
     * Set the value of [sla_time_duration] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSlaTimeDuration($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sla_time_duration !== $v || $v === 0) {
            $this->sla_time_duration = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_TIME_DURATION;
        }

    } // setSlaTimeDuration()

    /**
     * Set the value of [sla_time_duration_mode] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaTimeDurationMode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_time_duration_mode !== $v || $v === 'HOURS') {
            $this->sla_time_duration_mode = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_TIME_DURATION_MODE;
        }

    } // setSlaTimeDurationMode()

    /**
     * Set the value of [sla_conditions] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaConditions($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_conditions !== $v || $v === '') {
            $this->sla_conditions = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_CONDITIONS;
        }

    } // setSlaConditions()

    /**
     * Set the value of [sla_pen_enabled] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSlaPenEnabled($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sla_pen_enabled !== $v || $v === 0) {
            $this->sla_pen_enabled = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_PEN_ENABLED;
        }

    } // setSlaPenEnabled()

    /**
     * Set the value of [sla_pen_time] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSlaPenTime($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sla_pen_time !== $v || $v === 0) {
            $this->sla_pen_time = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_PEN_TIME;
        }

    } // setSlaPenTime()

    /**
     * Set the value of [sla_pen_time_mode] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaPenTimeMode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_pen_time_mode !== $v || $v === 'HOURS') {
            $this->sla_pen_time_mode = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_PEN_TIME_MODE;
        }

    } // setSlaPenTimeMode()

    /**
     * Set the value of [sla_pen_value] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSlaPenValue($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sla_pen_value !== $v || $v === 0) {
            $this->sla_pen_value = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_PEN_VALUE;
        }

    } // setSlaPenValue()

    /**
     * Set the value of [sla_pen_value_unit] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaPenValueUnit($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_pen_value_unit !== $v || $v === '') {
            $this->sla_pen_value_unit = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_PEN_VALUE_UNIT;
        }

    } // setSlaPenValueUnit()

    /**
     * Set the value of [sla_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSlaStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sla_status !== $v || $v === '') {
            $this->sla_status = $v;
            $this->modifiedColumns[] = SlaPeer::SLA_STATUS;
        }

    } // setSlaStatus()

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

            $this->sla_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->sla_name = $rs->getString($startcol + 2);

            $this->sla_description = $rs->getString($startcol + 3);

            $this->sla_type = $rs->getString($startcol + 4);

            $this->sla_tas_start = $rs->getString($startcol + 5);

            $this->sla_tas_end = $rs->getString($startcol + 6);

            $this->sla_time_duration = $rs->getInt($startcol + 7);

            $this->sla_time_duration_mode = $rs->getString($startcol + 8);

            $this->sla_conditions = $rs->getString($startcol + 9);

            $this->sla_pen_enabled = $rs->getInt($startcol + 10);

            $this->sla_pen_time = $rs->getInt($startcol + 11);

            $this->sla_pen_time_mode = $rs->getString($startcol + 12);

            $this->sla_pen_value = $rs->getInt($startcol + 13);

            $this->sla_pen_value_unit = $rs->getString($startcol + 14);

            $this->sla_status = $rs->getString($startcol + 15);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 16; // 16 = SlaPeer::NUM_COLUMNS - SlaPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Sla object", $e);
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
            $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            SlaPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(SlaPeer::DATABASE_NAME);
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
                    $pk = SlaPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += SlaPeer::doUpdate($this, $con);
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


            if (($retval = SlaPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SlaPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSlaUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getSlaName();
                break;
            case 3:
                return $this->getSlaDescription();
                break;
            case 4:
                return $this->getSlaType();
                break;
            case 5:
                return $this->getSlaTasStart();
                break;
            case 6:
                return $this->getSlaTasEnd();
                break;
            case 7:
                return $this->getSlaTimeDuration();
                break;
            case 8:
                return $this->getSlaTimeDurationMode();
                break;
            case 9:
                return $this->getSlaConditions();
                break;
            case 10:
                return $this->getSlaPenEnabled();
                break;
            case 11:
                return $this->getSlaPenTime();
                break;
            case 12:
                return $this->getSlaPenTimeMode();
                break;
            case 13:
                return $this->getSlaPenValue();
                break;
            case 14:
                return $this->getSlaPenValueUnit();
                break;
            case 15:
                return $this->getSlaStatus();
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
        $keys = SlaPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSlaUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getSlaName(),
            $keys[3] => $this->getSlaDescription(),
            $keys[4] => $this->getSlaType(),
            $keys[5] => $this->getSlaTasStart(),
            $keys[6] => $this->getSlaTasEnd(),
            $keys[7] => $this->getSlaTimeDuration(),
            $keys[8] => $this->getSlaTimeDurationMode(),
            $keys[9] => $this->getSlaConditions(),
            $keys[10] => $this->getSlaPenEnabled(),
            $keys[11] => $this->getSlaPenTime(),
            $keys[12] => $this->getSlaPenTimeMode(),
            $keys[13] => $this->getSlaPenValue(),
            $keys[14] => $this->getSlaPenValueUnit(),
            $keys[15] => $this->getSlaStatus(),
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
        $pos = SlaPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setSlaUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setSlaName($value);
                break;
            case 3:
                $this->setSlaDescription($value);
                break;
            case 4:
                $this->setSlaType($value);
                break;
            case 5:
                $this->setSlaTasStart($value);
                break;
            case 6:
                $this->setSlaTasEnd($value);
                break;
            case 7:
                $this->setSlaTimeDuration($value);
                break;
            case 8:
                $this->setSlaTimeDurationMode($value);
                break;
            case 9:
                $this->setSlaConditions($value);
                break;
            case 10:
                $this->setSlaPenEnabled($value);
                break;
            case 11:
                $this->setSlaPenTime($value);
                break;
            case 12:
                $this->setSlaPenTimeMode($value);
                break;
            case 13:
                $this->setSlaPenValue($value);
                break;
            case 14:
                $this->setSlaPenValueUnit($value);
                break;
            case 15:
                $this->setSlaStatus($value);
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
        $keys = SlaPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setSlaUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setSlaName($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setSlaDescription($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSlaType($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setSlaTasStart($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setSlaTasEnd($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSlaTimeDuration($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setSlaTimeDurationMode($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setSlaConditions($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setSlaPenEnabled($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setSlaPenTime($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setSlaPenTimeMode($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setSlaPenValue($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setSlaPenValueUnit($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setSlaStatus($arr[$keys[15]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SlaPeer::DATABASE_NAME);

        if ($this->isColumnModified(SlaPeer::SLA_UID)) {
            $criteria->add(SlaPeer::SLA_UID, $this->sla_uid);
        }

        if ($this->isColumnModified(SlaPeer::PRO_UID)) {
            $criteria->add(SlaPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(SlaPeer::SLA_NAME)) {
            $criteria->add(SlaPeer::SLA_NAME, $this->sla_name);
        }

        if ($this->isColumnModified(SlaPeer::SLA_DESCRIPTION)) {
            $criteria->add(SlaPeer::SLA_DESCRIPTION, $this->sla_description);
        }

        if ($this->isColumnModified(SlaPeer::SLA_TYPE)) {
            $criteria->add(SlaPeer::SLA_TYPE, $this->sla_type);
        }

        if ($this->isColumnModified(SlaPeer::SLA_TAS_START)) {
            $criteria->add(SlaPeer::SLA_TAS_START, $this->sla_tas_start);
        }

        if ($this->isColumnModified(SlaPeer::SLA_TAS_END)) {
            $criteria->add(SlaPeer::SLA_TAS_END, $this->sla_tas_end);
        }

        if ($this->isColumnModified(SlaPeer::SLA_TIME_DURATION)) {
            $criteria->add(SlaPeer::SLA_TIME_DURATION, $this->sla_time_duration);
        }

        if ($this->isColumnModified(SlaPeer::SLA_TIME_DURATION_MODE)) {
            $criteria->add(SlaPeer::SLA_TIME_DURATION_MODE, $this->sla_time_duration_mode);
        }

        if ($this->isColumnModified(SlaPeer::SLA_CONDITIONS)) {
            $criteria->add(SlaPeer::SLA_CONDITIONS, $this->sla_conditions);
        }

        if ($this->isColumnModified(SlaPeer::SLA_PEN_ENABLED)) {
            $criteria->add(SlaPeer::SLA_PEN_ENABLED, $this->sla_pen_enabled);
        }

        if ($this->isColumnModified(SlaPeer::SLA_PEN_TIME)) {
            $criteria->add(SlaPeer::SLA_PEN_TIME, $this->sla_pen_time);
        }

        if ($this->isColumnModified(SlaPeer::SLA_PEN_TIME_MODE)) {
            $criteria->add(SlaPeer::SLA_PEN_TIME_MODE, $this->sla_pen_time_mode);
        }

        if ($this->isColumnModified(SlaPeer::SLA_PEN_VALUE)) {
            $criteria->add(SlaPeer::SLA_PEN_VALUE, $this->sla_pen_value);
        }

        if ($this->isColumnModified(SlaPeer::SLA_PEN_VALUE_UNIT)) {
            $criteria->add(SlaPeer::SLA_PEN_VALUE_UNIT, $this->sla_pen_value_unit);
        }

        if ($this->isColumnModified(SlaPeer::SLA_STATUS)) {
            $criteria->add(SlaPeer::SLA_STATUS, $this->sla_status);
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
        $criteria = new Criteria(SlaPeer::DATABASE_NAME);

        $criteria->add(SlaPeer::SLA_UID, $this->sla_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getSlaUid();
    }

    /**
     * Generic method to set the primary key (sla_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setSlaUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Sla (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setSlaName($this->sla_name);

        $copyObj->setSlaDescription($this->sla_description);

        $copyObj->setSlaType($this->sla_type);

        $copyObj->setSlaTasStart($this->sla_tas_start);

        $copyObj->setSlaTasEnd($this->sla_tas_end);

        $copyObj->setSlaTimeDuration($this->sla_time_duration);

        $copyObj->setSlaTimeDurationMode($this->sla_time_duration_mode);

        $copyObj->setSlaConditions($this->sla_conditions);

        $copyObj->setSlaPenEnabled($this->sla_pen_enabled);

        $copyObj->setSlaPenTime($this->sla_pen_time);

        $copyObj->setSlaPenTimeMode($this->sla_pen_time_mode);

        $copyObj->setSlaPenValue($this->sla_pen_value);

        $copyObj->setSlaPenValueUnit($this->sla_pen_value_unit);

        $copyObj->setSlaStatus($this->sla_status);


        $copyObj->setNew(true);

        $copyObj->setSlaUid(''); // this is a pkey column, so set to default value

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
     * @return     Sla Clone of current object.
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
     * @return     SlaPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SlaPeer();
        }
        return self::$peer;
    }
}

