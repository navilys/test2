<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ErConfigurationPeer.php';

/**
 * Base class that represents a row from the 'ER_CONFIGURATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseErConfiguration extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ErConfigurationPeer
	 */
	protected static $peer;


	/**
	 * The value for the er_uid field.
	 * @var        string
	 */
	protected $er_uid = '';


	/**
	 * The value for the er_title field.
	 * @var        string
	 */
	protected $er_title = '';


	/**
	 * The value for the pro_uid field.
	 * @var        string
	 */
	protected $pro_uid = '';


	/**
	 * The value for the er_template field.
	 * @var        string
	 */
	protected $er_template = '';


	/**
	 * The value for the dyn_uid field.
	 * @var        string
	 */
	protected $dyn_uid = '';


	/**
	 * The value for the er_valid_days field.
	 * @var        int
	 */
	protected $er_valid_days = 5;


	/**
	 * The value for the er_action_assign field.
	 * @var        string
	 */
	protected $er_action_assign = '';


	/**
	 * The value for the er_object_uid field.
	 * @var        string
	 */
	protected $er_object_uid = '';


	/**
	 * The value for the er_action_start_case field.
	 * @var        int
	 */
	protected $er_action_start_case = 0;


	/**
	 * The value for the tas_uid field.
	 * @var        string
	 */
	protected $tas_uid = '';


	/**
	 * The value for the er_action_execute_trigger field.
	 * @var        int
	 */
	protected $er_action_execute_trigger = 0;


	/**
	 * The value for the tri_uid field.
	 * @var        string
	 */
	protected $tri_uid = '';


	/**
	 * The value for the er_create_date field.
	 * @var        int
	 */
	protected $er_create_date;


	/**
	 * The value for the er_update_date field.
	 * @var        int
	 */
	protected $er_update_date;

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
	 * Get the [er_uid] column value.
	 * 
	 * @return     string
	 */
	public function getErUid()
	{

		return $this->er_uid;
	}

	/**
	 * Get the [er_title] column value.
	 * 
	 * @return     string
	 */
	public function getErTitle()
	{

		return $this->er_title;
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
	 * Get the [er_template] column value.
	 * 
	 * @return     string
	 */
	public function getErTemplate()
	{

		return $this->er_template;
	}

	/**
	 * Get the [dyn_uid] column value.
	 * 
	 * @return     string
	 */
	public function getDynUid()
	{

		return $this->dyn_uid;
	}

	/**
	 * Get the [er_valid_days] column value.
	 * 
	 * @return     int
	 */
	public function getErValidDays()
	{

		return $this->er_valid_days;
	}

	/**
	 * Get the [er_action_assign] column value.
	 * 
	 * @return     string
	 */
	public function getErActionAssign()
	{

		return $this->er_action_assign;
	}

	/**
	 * Get the [er_object_uid] column value.
	 * 
	 * @return     string
	 */
	public function getErObjectUid()
	{

		return $this->er_object_uid;
	}

	/**
	 * Get the [er_action_start_case] column value.
	 * 
	 * @return     int
	 */
	public function getErActionStartCase()
	{

		return $this->er_action_start_case;
	}

	/**
	 * Get the [tas_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTasUid()
	{

		return $this->tas_uid;
	}

	/**
	 * Get the [er_action_execute_trigger] column value.
	 * 
	 * @return     int
	 */
	public function getErActionExecuteTrigger()
	{

		return $this->er_action_execute_trigger;
	}

	/**
	 * Get the [tri_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTriUid()
	{

		return $this->tri_uid;
	}

	/**
	 * Get the [optionally formatted] [er_create_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getErCreateDate($format = 'Y-m-d H:i:s')
	{

		if ($this->er_create_date === null || $this->er_create_date === '') {
			return null;
		} elseif (!is_int($this->er_create_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->er_create_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [er_create_date] as date/time value: " . var_export($this->er_create_date, true));
			}
		} else {
			$ts = $this->er_create_date;
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
	 * Get the [optionally formatted] [er_update_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getErUpdateDate($format = 'Y-m-d H:i:s')
	{

		if ($this->er_update_date === null || $this->er_update_date === '') {
			return null;
		} elseif (!is_int($this->er_update_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->er_update_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [er_update_date] as date/time value: " . var_export($this->er_update_date, true));
			}
		} else {
			$ts = $this->er_update_date;
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
	 * Set the value of [er_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_uid !== $v || $v === '') {
			$this->er_uid = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_UID;
		}

	} // setErUid()

	/**
	 * Set the value of [er_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_title !== $v || $v === '') {
			$this->er_title = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_TITLE;
		}

	} // setErTitle()

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
			$this->modifiedColumns[] = ErConfigurationPeer::PRO_UID;
		}

	} // setProUid()

	/**
	 * Set the value of [er_template] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErTemplate($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_template !== $v || $v === '') {
			$this->er_template = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_TEMPLATE;
		}

	} // setErTemplate()

	/**
	 * Set the value of [dyn_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDynUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->dyn_uid !== $v || $v === '') {
			$this->dyn_uid = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::DYN_UID;
		}

	} // setDynUid()

	/**
	 * Set the value of [er_valid_days] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErValidDays($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->er_valid_days !== $v || $v === 5) {
			$this->er_valid_days = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_VALID_DAYS;
		}

	} // setErValidDays()

	/**
	 * Set the value of [er_action_assign] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErActionAssign($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_action_assign !== $v || $v === '') {
			$this->er_action_assign = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_ACTION_ASSIGN;
		}

	} // setErActionAssign()

	/**
	 * Set the value of [er_object_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErObjectUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_object_uid !== $v || $v === '') {
			$this->er_object_uid = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_OBJECT_UID;
		}

	} // setErObjectUid()

	/**
	 * Set the value of [er_action_start_case] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErActionStartCase($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->er_action_start_case !== $v || $v === 0) {
			$this->er_action_start_case = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_ACTION_START_CASE;
		}

	} // setErActionStartCase()

	/**
	 * Set the value of [tas_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTasUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tas_uid !== $v || $v === '') {
			$this->tas_uid = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::TAS_UID;
		}

	} // setTasUid()

	/**
	 * Set the value of [er_action_execute_trigger] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErActionExecuteTrigger($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->er_action_execute_trigger !== $v || $v === 0) {
			$this->er_action_execute_trigger = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER;
		}

	} // setErActionExecuteTrigger()

	/**
	 * Set the value of [tri_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTriUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tri_uid !== $v || $v === '') {
			$this->tri_uid = $v;
			$this->modifiedColumns[] = ErConfigurationPeer::TRI_UID;
		}

	} // setTriUid()

	/**
	 * Set the value of [er_create_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErCreateDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [er_create_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->er_create_date !== $ts) {
			$this->er_create_date = $ts;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_CREATE_DATE;
		}

	} // setErCreateDate()

	/**
	 * Set the value of [er_update_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErUpdateDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [er_update_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->er_update_date !== $ts) {
			$this->er_update_date = $ts;
			$this->modifiedColumns[] = ErConfigurationPeer::ER_UPDATE_DATE;
		}

	} // setErUpdateDate()

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

			$this->er_uid = $rs->getString($startcol + 0);

			$this->er_title = $rs->getString($startcol + 1);

			$this->pro_uid = $rs->getString($startcol + 2);

			$this->er_template = $rs->getString($startcol + 3);

			$this->dyn_uid = $rs->getString($startcol + 4);

			$this->er_valid_days = $rs->getInt($startcol + 5);

			$this->er_action_assign = $rs->getString($startcol + 6);

			$this->er_object_uid = $rs->getString($startcol + 7);

			$this->er_action_start_case = $rs->getInt($startcol + 8);

			$this->tas_uid = $rs->getString($startcol + 9);

			$this->er_action_execute_trigger = $rs->getInt($startcol + 10);

			$this->tri_uid = $rs->getString($startcol + 11);

			$this->er_create_date = $rs->getTimestamp($startcol + 12, null);

			$this->er_update_date = $rs->getTimestamp($startcol + 13, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 14; // 14 = ErConfigurationPeer::NUM_COLUMNS - ErConfigurationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ErConfiguration object", $e);
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
			$con = Propel::getConnection(ErConfigurationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ErConfigurationPeer::doDelete($this, $con);
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
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(ErConfigurationPeer::DATABASE_NAME);
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
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
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
					$pk = ErConfigurationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += ErConfigurationPeer::doUpdate($this, $con);
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
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = ErConfigurationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ErConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getErUid();
				break;
			case 1:
				return $this->getErTitle();
				break;
			case 2:
				return $this->getProUid();
				break;
			case 3:
				return $this->getErTemplate();
				break;
			case 4:
				return $this->getDynUid();
				break;
			case 5:
				return $this->getErValidDays();
				break;
			case 6:
				return $this->getErActionAssign();
				break;
			case 7:
				return $this->getErObjectUid();
				break;
			case 8:
				return $this->getErActionStartCase();
				break;
			case 9:
				return $this->getTasUid();
				break;
			case 10:
				return $this->getErActionExecuteTrigger();
				break;
			case 11:
				return $this->getTriUid();
				break;
			case 12:
				return $this->getErCreateDate();
				break;
			case 13:
				return $this->getErUpdateDate();
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
		$keys = ErConfigurationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getErUid(),
			$keys[1] => $this->getErTitle(),
			$keys[2] => $this->getProUid(),
			$keys[3] => $this->getErTemplate(),
			$keys[4] => $this->getDynUid(),
			$keys[5] => $this->getErValidDays(),
			$keys[6] => $this->getErActionAssign(),
			$keys[7] => $this->getErObjectUid(),
			$keys[8] => $this->getErActionStartCase(),
			$keys[9] => $this->getTasUid(),
			$keys[10] => $this->getErActionExecuteTrigger(),
			$keys[11] => $this->getTriUid(),
			$keys[12] => $this->getErCreateDate(),
			$keys[13] => $this->getErUpdateDate(),
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
		$pos = ErConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setErUid($value);
				break;
			case 1:
				$this->setErTitle($value);
				break;
			case 2:
				$this->setProUid($value);
				break;
			case 3:
				$this->setErTemplate($value);
				break;
			case 4:
				$this->setDynUid($value);
				break;
			case 5:
				$this->setErValidDays($value);
				break;
			case 6:
				$this->setErActionAssign($value);
				break;
			case 7:
				$this->setErObjectUid($value);
				break;
			case 8:
				$this->setErActionStartCase($value);
				break;
			case 9:
				$this->setTasUid($value);
				break;
			case 10:
				$this->setErActionExecuteTrigger($value);
				break;
			case 11:
				$this->setTriUid($value);
				break;
			case 12:
				$this->setErCreateDate($value);
				break;
			case 13:
				$this->setErUpdateDate($value);
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
		$keys = ErConfigurationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setErUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setErTitle($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setProUid($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setErTemplate($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDynUid($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setErValidDays($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setErActionAssign($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setErObjectUid($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setErActionStartCase($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTasUid($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setErActionExecuteTrigger($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setTriUid($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setErCreateDate($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setErUpdateDate($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ErConfigurationPeer::DATABASE_NAME);

		if ($this->isColumnModified(ErConfigurationPeer::ER_UID)) $criteria->add(ErConfigurationPeer::ER_UID, $this->er_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_TITLE)) $criteria->add(ErConfigurationPeer::ER_TITLE, $this->er_title);
		if ($this->isColumnModified(ErConfigurationPeer::PRO_UID)) $criteria->add(ErConfigurationPeer::PRO_UID, $this->pro_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_TEMPLATE)) $criteria->add(ErConfigurationPeer::ER_TEMPLATE, $this->er_template);
		if ($this->isColumnModified(ErConfigurationPeer::DYN_UID)) $criteria->add(ErConfigurationPeer::DYN_UID, $this->dyn_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_VALID_DAYS)) $criteria->add(ErConfigurationPeer::ER_VALID_DAYS, $this->er_valid_days);
		if ($this->isColumnModified(ErConfigurationPeer::ER_ACTION_ASSIGN)) $criteria->add(ErConfigurationPeer::ER_ACTION_ASSIGN, $this->er_action_assign);
		if ($this->isColumnModified(ErConfigurationPeer::ER_OBJECT_UID)) $criteria->add(ErConfigurationPeer::ER_OBJECT_UID, $this->er_object_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_ACTION_START_CASE)) $criteria->add(ErConfigurationPeer::ER_ACTION_START_CASE, $this->er_action_start_case);
		if ($this->isColumnModified(ErConfigurationPeer::TAS_UID)) $criteria->add(ErConfigurationPeer::TAS_UID, $this->tas_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER)) $criteria->add(ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER, $this->er_action_execute_trigger);
		if ($this->isColumnModified(ErConfigurationPeer::TRI_UID)) $criteria->add(ErConfigurationPeer::TRI_UID, $this->tri_uid);
		if ($this->isColumnModified(ErConfigurationPeer::ER_CREATE_DATE)) $criteria->add(ErConfigurationPeer::ER_CREATE_DATE, $this->er_create_date);
		if ($this->isColumnModified(ErConfigurationPeer::ER_UPDATE_DATE)) $criteria->add(ErConfigurationPeer::ER_UPDATE_DATE, $this->er_update_date);

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
		$criteria = new Criteria(ErConfigurationPeer::DATABASE_NAME);

		$criteria->add(ErConfigurationPeer::ER_UID, $this->er_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getErUid();
	}

	/**
	 * Generic method to set the primary key (er_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setErUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ErConfiguration (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setErTitle($this->er_title);

		$copyObj->setProUid($this->pro_uid);

		$copyObj->setErTemplate($this->er_template);

		$copyObj->setDynUid($this->dyn_uid);

		$copyObj->setErValidDays($this->er_valid_days);

		$copyObj->setErActionAssign($this->er_action_assign);

		$copyObj->setErObjectUid($this->er_object_uid);

		$copyObj->setErActionStartCase($this->er_action_start_case);

		$copyObj->setTasUid($this->tas_uid);

		$copyObj->setErActionExecuteTrigger($this->er_action_execute_trigger);

		$copyObj->setTriUid($this->tri_uid);

		$copyObj->setErCreateDate($this->er_create_date);

		$copyObj->setErUpdateDate($this->er_update_date);


		$copyObj->setNew(true);

		$copyObj->setErUid(''); // this is a pkey column, so set to default value

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
	 * @return     ErConfiguration Clone of current object.
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
	 * @return     ErConfigurationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ErConfigurationPeer();
		}
		return self::$peer;
	}

} // BaseErConfiguration
