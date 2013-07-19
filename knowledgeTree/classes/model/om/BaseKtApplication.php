<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/KtApplicationPeer.php';

/**
 * Base class that represents a row from the 'KT_APPLICATION' table.
 *
 * 
 *
 * @package    model.om
 */
abstract class BaseKtApplication extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        KtApplicationPeer
	 */
	protected static $peer;


	/**
	 * The value for the app_uid field.
	 * @var        string
	 */
	protected $app_uid = '';


	/**
	 * The value for the kt_folder_id field.
	 * @var        int
	 */
	protected $kt_folder_id = 0;


	/**
	 * The value for the kt_parent_id field.
	 * @var        int
	 */
	protected $kt_parent_id = 0;


	/**
	 * The value for the kt_folder_name field.
	 * @var        string
	 */
	protected $kt_folder_name = '';


	/**
	 * The value for the kt_full_path field.
	 * @var        string
	 */
	protected $kt_full_path = '';


	/**
	 * The value for the kt_folder_output field.
	 * @var        int
	 */
	protected $kt_folder_output = 0;


	/**
	 * The value for the kt_folder_attachment field.
	 * @var        int
	 */
	protected $kt_folder_attachment = 0;


	/**
	 * The value for the kt_folder_email field.
	 * @var        int
	 */
	protected $kt_folder_email = 0;


	/**
	 * The value for the kt_create_user field.
	 * @var        string
	 */
	protected $kt_create_user = '';


	/**
	 * The value for the kt_create_date field.
	 * @var        int
	 */
	protected $kt_create_date;


	/**
	 * The value for the kt_update_date field.
	 * @var        int
	 */
	protected $kt_update_date;

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
	 * Get the [app_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAppUid()
	{

		return $this->app_uid;
	}

	/**
	 * Get the [kt_folder_id] column value.
	 * 
	 * @return     int
	 */
	public function getKtFolderId()
	{

		return $this->kt_folder_id;
	}

	/**
	 * Get the [kt_parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getKtParentId()
	{

		return $this->kt_parent_id;
	}

	/**
	 * Get the [kt_folder_name] column value.
	 * 
	 * @return     string
	 */
	public function getKtFolderName()
	{

		return $this->kt_folder_name;
	}

	/**
	 * Get the [kt_full_path] column value.
	 * 
	 * @return     string
	 */
	public function getKtFullPath()
	{

		return $this->kt_full_path;
	}

	/**
	 * Get the [kt_folder_output] column value.
	 * 
	 * @return     int
	 */
	public function getKtFolderOutput()
	{

		return $this->kt_folder_output;
	}

	/**
	 * Get the [kt_folder_attachment] column value.
	 * 
	 * @return     int
	 */
	public function getKtFolderAttachment()
	{

		return $this->kt_folder_attachment;
	}

	/**
	 * Get the [kt_folder_email] column value.
	 * 
	 * @return     int
	 */
	public function getKtFolderEmail()
	{

		return $this->kt_folder_email;
	}

	/**
	 * Get the [kt_create_user] column value.
	 * 
	 * @return     string
	 */
	public function getKtCreateUser()
	{

		return $this->kt_create_user;
	}

	/**
	 * Get the [optionally formatted] [kt_create_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getKtCreateDate($format = 'Y-m-d H:i:s')
	{

		if ($this->kt_create_date === null || $this->kt_create_date === '') {
			return null;
		} elseif (!is_int($this->kt_create_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->kt_create_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [kt_create_date] as date/time value: " . var_export($this->kt_create_date, true));
			}
		} else {
			$ts = $this->kt_create_date;
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
	 * Get the [optionally formatted] [kt_update_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getKtUpdateDate($format = 'Y-m-d H:i:s')
	{

		if ($this->kt_update_date === null || $this->kt_update_date === '') {
			return null;
		} elseif (!is_int($this->kt_update_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->kt_update_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [kt_update_date] as date/time value: " . var_export($this->kt_update_date, true));
			}
		} else {
			$ts = $this->kt_update_date;
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
	 * Set the value of [app_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAppUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->app_uid !== $v || $v === '') {
			$this->app_uid = $v;
			$this->modifiedColumns[] = KtApplicationPeer::APP_UID;
		}

	} // setAppUid()

	/**
	 * Set the value of [kt_folder_id] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtFolderId($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_folder_id !== $v || $v === 0) {
			$this->kt_folder_id = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FOLDER_ID;
		}

	} // setKtFolderId()

	/**
	 * Set the value of [kt_parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtParentId($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_parent_id !== $v || $v === 0) {
			$this->kt_parent_id = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_PARENT_ID;
		}

	} // setKtParentId()

	/**
	 * Set the value of [kt_folder_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKtFolderName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->kt_folder_name !== $v || $v === '') {
			$this->kt_folder_name = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FOLDER_NAME;
		}

	} // setKtFolderName()

	/**
	 * Set the value of [kt_full_path] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKtFullPath($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->kt_full_path !== $v || $v === '') {
			$this->kt_full_path = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FULL_PATH;
		}

	} // setKtFullPath()

	/**
	 * Set the value of [kt_folder_output] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtFolderOutput($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_folder_output !== $v || $v === 0) {
			$this->kt_folder_output = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FOLDER_OUTPUT;
		}

	} // setKtFolderOutput()

	/**
	 * Set the value of [kt_folder_attachment] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtFolderAttachment($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_folder_attachment !== $v || $v === 0) {
			$this->kt_folder_attachment = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FOLDER_ATTACHMENT;
		}

	} // setKtFolderAttachment()

	/**
	 * Set the value of [kt_folder_email] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtFolderEmail($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_folder_email !== $v || $v === 0) {
			$this->kt_folder_email = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_FOLDER_EMAIL;
		}

	} // setKtFolderEmail()

	/**
	 * Set the value of [kt_create_user] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKtCreateUser($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->kt_create_user !== $v || $v === '') {
			$this->kt_create_user = $v;
			$this->modifiedColumns[] = KtApplicationPeer::KT_CREATE_USER;
		}

	} // setKtCreateUser()

	/**
	 * Set the value of [kt_create_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtCreateDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [kt_create_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->kt_create_date !== $ts) {
			$this->kt_create_date = $ts;
			$this->modifiedColumns[] = KtApplicationPeer::KT_CREATE_DATE;
		}

	} // setKtCreateDate()

	/**
	 * Set the value of [kt_update_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtUpdateDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [kt_update_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->kt_update_date !== $ts) {
			$this->kt_update_date = $ts;
			$this->modifiedColumns[] = KtApplicationPeer::KT_UPDATE_DATE;
		}

	} // setKtUpdateDate()

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

			$this->app_uid = $rs->getString($startcol + 0);

			$this->kt_folder_id = $rs->getInt($startcol + 1);

			$this->kt_parent_id = $rs->getInt($startcol + 2);

			$this->kt_folder_name = $rs->getString($startcol + 3);

			$this->kt_full_path = $rs->getString($startcol + 4);

			$this->kt_folder_output = $rs->getInt($startcol + 5);

			$this->kt_folder_attachment = $rs->getInt($startcol + 6);

			$this->kt_folder_email = $rs->getInt($startcol + 7);

			$this->kt_create_user = $rs->getString($startcol + 8);

			$this->kt_create_date = $rs->getTimestamp($startcol + 9, null);

			$this->kt_update_date = $rs->getTimestamp($startcol + 10, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 11; // 11 = KtApplicationPeer::NUM_COLUMNS - KtApplicationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating KtApplication object", $e);
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
			$con = Propel::getConnection(KtApplicationPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			KtApplicationPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(KtApplicationPeer::DATABASE_NAME);
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
					$pk = KtApplicationPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += KtApplicationPeer::doUpdate($this, $con);
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


			if (($retval = KtApplicationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = KtApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAppUid();
				break;
			case 1:
				return $this->getKtFolderId();
				break;
			case 2:
				return $this->getKtParentId();
				break;
			case 3:
				return $this->getKtFolderName();
				break;
			case 4:
				return $this->getKtFullPath();
				break;
			case 5:
				return $this->getKtFolderOutput();
				break;
			case 6:
				return $this->getKtFolderAttachment();
				break;
			case 7:
				return $this->getKtFolderEmail();
				break;
			case 8:
				return $this->getKtCreateUser();
				break;
			case 9:
				return $this->getKtCreateDate();
				break;
			case 10:
				return $this->getKtUpdateDate();
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
		$keys = KtApplicationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAppUid(),
			$keys[1] => $this->getKtFolderId(),
			$keys[2] => $this->getKtParentId(),
			$keys[3] => $this->getKtFolderName(),
			$keys[4] => $this->getKtFullPath(),
			$keys[5] => $this->getKtFolderOutput(),
			$keys[6] => $this->getKtFolderAttachment(),
			$keys[7] => $this->getKtFolderEmail(),
			$keys[8] => $this->getKtCreateUser(),
			$keys[9] => $this->getKtCreateDate(),
			$keys[10] => $this->getKtUpdateDate(),
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
		$pos = KtApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAppUid($value);
				break;
			case 1:
				$this->setKtFolderId($value);
				break;
			case 2:
				$this->setKtParentId($value);
				break;
			case 3:
				$this->setKtFolderName($value);
				break;
			case 4:
				$this->setKtFullPath($value);
				break;
			case 5:
				$this->setKtFolderOutput($value);
				break;
			case 6:
				$this->setKtFolderAttachment($value);
				break;
			case 7:
				$this->setKtFolderEmail($value);
				break;
			case 8:
				$this->setKtCreateUser($value);
				break;
			case 9:
				$this->setKtCreateDate($value);
				break;
			case 10:
				$this->setKtUpdateDate($value);
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
		$keys = KtApplicationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAppUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setKtFolderId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setKtParentId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setKtFolderName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setKtFullPath($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setKtFolderOutput($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setKtFolderAttachment($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setKtFolderEmail($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setKtCreateUser($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setKtCreateDate($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setKtUpdateDate($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(KtApplicationPeer::DATABASE_NAME);

		if ($this->isColumnModified(KtApplicationPeer::APP_UID)) $criteria->add(KtApplicationPeer::APP_UID, $this->app_uid);
		if ($this->isColumnModified(KtApplicationPeer::KT_FOLDER_ID)) $criteria->add(KtApplicationPeer::KT_FOLDER_ID, $this->kt_folder_id);
		if ($this->isColumnModified(KtApplicationPeer::KT_PARENT_ID)) $criteria->add(KtApplicationPeer::KT_PARENT_ID, $this->kt_parent_id);
		if ($this->isColumnModified(KtApplicationPeer::KT_FOLDER_NAME)) $criteria->add(KtApplicationPeer::KT_FOLDER_NAME, $this->kt_folder_name);
		if ($this->isColumnModified(KtApplicationPeer::KT_FULL_PATH)) $criteria->add(KtApplicationPeer::KT_FULL_PATH, $this->kt_full_path);
		if ($this->isColumnModified(KtApplicationPeer::KT_FOLDER_OUTPUT)) $criteria->add(KtApplicationPeer::KT_FOLDER_OUTPUT, $this->kt_folder_output);
		if ($this->isColumnModified(KtApplicationPeer::KT_FOLDER_ATTACHMENT)) $criteria->add(KtApplicationPeer::KT_FOLDER_ATTACHMENT, $this->kt_folder_attachment);
		if ($this->isColumnModified(KtApplicationPeer::KT_FOLDER_EMAIL)) $criteria->add(KtApplicationPeer::KT_FOLDER_EMAIL, $this->kt_folder_email);
		if ($this->isColumnModified(KtApplicationPeer::KT_CREATE_USER)) $criteria->add(KtApplicationPeer::KT_CREATE_USER, $this->kt_create_user);
		if ($this->isColumnModified(KtApplicationPeer::KT_CREATE_DATE)) $criteria->add(KtApplicationPeer::KT_CREATE_DATE, $this->kt_create_date);
		if ($this->isColumnModified(KtApplicationPeer::KT_UPDATE_DATE)) $criteria->add(KtApplicationPeer::KT_UPDATE_DATE, $this->kt_update_date);

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
		$criteria = new Criteria(KtApplicationPeer::DATABASE_NAME);

		$criteria->add(KtApplicationPeer::APP_UID, $this->app_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getAppUid();
	}

	/**
	 * Generic method to set the primary key (app_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setAppUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of KtApplication (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setKtFolderId($this->kt_folder_id);

		$copyObj->setKtParentId($this->kt_parent_id);

		$copyObj->setKtFolderName($this->kt_folder_name);

		$copyObj->setKtFullPath($this->kt_full_path);

		$copyObj->setKtFolderOutput($this->kt_folder_output);

		$copyObj->setKtFolderAttachment($this->kt_folder_attachment);

		$copyObj->setKtFolderEmail($this->kt_folder_email);

		$copyObj->setKtCreateUser($this->kt_create_user);

		$copyObj->setKtCreateDate($this->kt_create_date);

		$copyObj->setKtUpdateDate($this->kt_update_date);


		$copyObj->setNew(true);

		$copyObj->setAppUid(''); // this is a pkey column, so set to default value

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
	 * @return     KtApplication Clone of current object.
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
	 * @return     KtApplicationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new KtApplicationPeer();
		}
		return self::$peer;
	}

} // BaseKtApplication
