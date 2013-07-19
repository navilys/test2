<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/KtDocumentPeer.php';

/**
 * Base class that represents a row from the 'KT_DOCUMENT' table.
 *
 * 
 *
 * @package    model.om
 */
abstract class BaseKtDocument extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        KtDocumentPeer
	 */
	protected static $peer;


	/**
	 * The value for the doc_uid field.
	 * @var        string
	 */
	protected $doc_uid = '';


	/**
	 * The value for the doc_type field.
	 * @var        string
	 */
	protected $doc_type = '';


	/**
	 * The value for the doc_pmtype field.
	 * @var        string
	 */
	protected $doc_pmtype = 'OUTPUT';


	/**
	 * The value for the pro_uid field.
	 * @var        string
	 */
	protected $pro_uid = '';


	/**
	 * The value for the app_uid field.
	 * @var        string
	 */
	protected $app_uid = '';


	/**
	 * The value for the kt_document_id field.
	 * @var        int
	 */
	protected $kt_document_id = 0;


	/**
	 * The value for the kt_status field.
	 * @var        string
	 */
	protected $kt_status = '';


	/**
	 * The value for the kt_document_title field.
	 * @var        string
	 */
	protected $kt_document_title = '';


	/**
	 * The value for the kt_full_path field.
	 * @var        string
	 */
	protected $kt_full_path = '';


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
	 * Get the [doc_uid] column value.
	 * 
	 * @return     string
	 */
	public function getDocUid()
	{

		return $this->doc_uid;
	}

	/**
	 * Get the [doc_type] column value.
	 * 
	 * @return     string
	 */
	public function getDocType()
	{

		return $this->doc_type;
	}

	/**
	 * Get the [doc_pmtype] column value.
	 * 
	 * @return     string
	 */
	public function getDocPmtype()
	{

		return $this->doc_pmtype;
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
	 * Get the [app_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAppUid()
	{

		return $this->app_uid;
	}

	/**
	 * Get the [kt_document_id] column value.
	 * 
	 * @return     int
	 */
	public function getKtDocumentId()
	{

		return $this->kt_document_id;
	}

	/**
	 * Get the [kt_status] column value.
	 * 
	 * @return     string
	 */
	public function getKtStatus()
	{

		return $this->kt_status;
	}

	/**
	 * Get the [kt_document_title] column value.
	 * 
	 * @return     string
	 */
	public function getKtDocumentTitle()
	{

		return $this->kt_document_title;
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
	 * Set the value of [doc_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDocUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->doc_uid !== $v || $v === '') {
			$this->doc_uid = $v;
			$this->modifiedColumns[] = KtDocumentPeer::DOC_UID;
		}

	} // setDocUid()

	/**
	 * Set the value of [doc_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDocType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->doc_type !== $v || $v === '') {
			$this->doc_type = $v;
			$this->modifiedColumns[] = KtDocumentPeer::DOC_TYPE;
		}

	} // setDocType()

	/**
	 * Set the value of [doc_pmtype] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDocPmtype($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->doc_pmtype !== $v || $v === 'OUTPUT') {
			$this->doc_pmtype = $v;
			$this->modifiedColumns[] = KtDocumentPeer::DOC_PMTYPE;
		}

	} // setDocPmtype()

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
			$this->modifiedColumns[] = KtDocumentPeer::PRO_UID;
		}

	} // setProUid()

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
			$this->modifiedColumns[] = KtDocumentPeer::APP_UID;
		}

	} // setAppUid()

	/**
	 * Set the value of [kt_document_id] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setKtDocumentId($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kt_document_id !== $v || $v === 0) {
			$this->kt_document_id = $v;
			$this->modifiedColumns[] = KtDocumentPeer::KT_DOCUMENT_ID;
		}

	} // setKtDocumentId()

	/**
	 * Set the value of [kt_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKtStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->kt_status !== $v || $v === '') {
			$this->kt_status = $v;
			$this->modifiedColumns[] = KtDocumentPeer::KT_STATUS;
		}

	} // setKtStatus()

	/**
	 * Set the value of [kt_document_title] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setKtDocumentTitle($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->kt_document_title !== $v || $v === '') {
			$this->kt_document_title = $v;
			$this->modifiedColumns[] = KtDocumentPeer::KT_DOCUMENT_TITLE;
		}

	} // setKtDocumentTitle()

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
			$this->modifiedColumns[] = KtDocumentPeer::KT_FULL_PATH;
		}

	} // setKtFullPath()

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
			$this->modifiedColumns[] = KtDocumentPeer::KT_CREATE_USER;
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
			$this->modifiedColumns[] = KtDocumentPeer::KT_CREATE_DATE;
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
			$this->modifiedColumns[] = KtDocumentPeer::KT_UPDATE_DATE;
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

			$this->doc_uid = $rs->getString($startcol + 0);

			$this->doc_type = $rs->getString($startcol + 1);

			$this->doc_pmtype = $rs->getString($startcol + 2);

			$this->pro_uid = $rs->getString($startcol + 3);

			$this->app_uid = $rs->getString($startcol + 4);

			$this->kt_document_id = $rs->getInt($startcol + 5);

			$this->kt_status = $rs->getString($startcol + 6);

			$this->kt_document_title = $rs->getString($startcol + 7);

			$this->kt_full_path = $rs->getString($startcol + 8);

			$this->kt_create_user = $rs->getString($startcol + 9);

			$this->kt_create_date = $rs->getTimestamp($startcol + 10, null);

			$this->kt_update_date = $rs->getTimestamp($startcol + 11, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 12; // 12 = KtDocumentPeer::NUM_COLUMNS - KtDocumentPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating KtDocument object", $e);
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
			$con = Propel::getConnection(KtDocumentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			KtDocumentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(KtDocumentPeer::DATABASE_NAME);
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
					$pk = KtDocumentPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += KtDocumentPeer::doUpdate($this, $con);
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


			if (($retval = KtDocumentPeer::doValidate($this, $columns)) !== true) {
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
		$pos = KtDocumentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDocUid();
				break;
			case 1:
				return $this->getDocType();
				break;
			case 2:
				return $this->getDocPmtype();
				break;
			case 3:
				return $this->getProUid();
				break;
			case 4:
				return $this->getAppUid();
				break;
			case 5:
				return $this->getKtDocumentId();
				break;
			case 6:
				return $this->getKtStatus();
				break;
			case 7:
				return $this->getKtDocumentTitle();
				break;
			case 8:
				return $this->getKtFullPath();
				break;
			case 9:
				return $this->getKtCreateUser();
				break;
			case 10:
				return $this->getKtCreateDate();
				break;
			case 11:
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
		$keys = KtDocumentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDocUid(),
			$keys[1] => $this->getDocType(),
			$keys[2] => $this->getDocPmtype(),
			$keys[3] => $this->getProUid(),
			$keys[4] => $this->getAppUid(),
			$keys[5] => $this->getKtDocumentId(),
			$keys[6] => $this->getKtStatus(),
			$keys[7] => $this->getKtDocumentTitle(),
			$keys[8] => $this->getKtFullPath(),
			$keys[9] => $this->getKtCreateUser(),
			$keys[10] => $this->getKtCreateDate(),
			$keys[11] => $this->getKtUpdateDate(),
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
		$pos = KtDocumentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDocUid($value);
				break;
			case 1:
				$this->setDocType($value);
				break;
			case 2:
				$this->setDocPmtype($value);
				break;
			case 3:
				$this->setProUid($value);
				break;
			case 4:
				$this->setAppUid($value);
				break;
			case 5:
				$this->setKtDocumentId($value);
				break;
			case 6:
				$this->setKtStatus($value);
				break;
			case 7:
				$this->setKtDocumentTitle($value);
				break;
			case 8:
				$this->setKtFullPath($value);
				break;
			case 9:
				$this->setKtCreateUser($value);
				break;
			case 10:
				$this->setKtCreateDate($value);
				break;
			case 11:
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
		$keys = KtDocumentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDocUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDocType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDocPmtype($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setProUid($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAppUid($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setKtDocumentId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setKtStatus($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setKtDocumentTitle($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setKtFullPath($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setKtCreateUser($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setKtCreateDate($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setKtUpdateDate($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(KtDocumentPeer::DATABASE_NAME);

		if ($this->isColumnModified(KtDocumentPeer::DOC_UID)) $criteria->add(KtDocumentPeer::DOC_UID, $this->doc_uid);
		if ($this->isColumnModified(KtDocumentPeer::DOC_TYPE)) $criteria->add(KtDocumentPeer::DOC_TYPE, $this->doc_type);
		if ($this->isColumnModified(KtDocumentPeer::DOC_PMTYPE)) $criteria->add(KtDocumentPeer::DOC_PMTYPE, $this->doc_pmtype);
		if ($this->isColumnModified(KtDocumentPeer::PRO_UID)) $criteria->add(KtDocumentPeer::PRO_UID, $this->pro_uid);
		if ($this->isColumnModified(KtDocumentPeer::APP_UID)) $criteria->add(KtDocumentPeer::APP_UID, $this->app_uid);
		if ($this->isColumnModified(KtDocumentPeer::KT_DOCUMENT_ID)) $criteria->add(KtDocumentPeer::KT_DOCUMENT_ID, $this->kt_document_id);
		if ($this->isColumnModified(KtDocumentPeer::KT_STATUS)) $criteria->add(KtDocumentPeer::KT_STATUS, $this->kt_status);
		if ($this->isColumnModified(KtDocumentPeer::KT_DOCUMENT_TITLE)) $criteria->add(KtDocumentPeer::KT_DOCUMENT_TITLE, $this->kt_document_title);
		if ($this->isColumnModified(KtDocumentPeer::KT_FULL_PATH)) $criteria->add(KtDocumentPeer::KT_FULL_PATH, $this->kt_full_path);
		if ($this->isColumnModified(KtDocumentPeer::KT_CREATE_USER)) $criteria->add(KtDocumentPeer::KT_CREATE_USER, $this->kt_create_user);
		if ($this->isColumnModified(KtDocumentPeer::KT_CREATE_DATE)) $criteria->add(KtDocumentPeer::KT_CREATE_DATE, $this->kt_create_date);
		if ($this->isColumnModified(KtDocumentPeer::KT_UPDATE_DATE)) $criteria->add(KtDocumentPeer::KT_UPDATE_DATE, $this->kt_update_date);

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
		$criteria = new Criteria(KtDocumentPeer::DATABASE_NAME);

		$criteria->add(KtDocumentPeer::DOC_UID, $this->doc_uid);
		$criteria->add(KtDocumentPeer::DOC_TYPE, $this->doc_type);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getDocUid();

		$pks[1] = $this->getDocType();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{

		$this->setDocUid($keys[0]);

		$this->setDocType($keys[1]);

	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of KtDocument (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDocPmtype($this->doc_pmtype);

		$copyObj->setProUid($this->pro_uid);

		$copyObj->setAppUid($this->app_uid);

		$copyObj->setKtDocumentId($this->kt_document_id);

		$copyObj->setKtStatus($this->kt_status);

		$copyObj->setKtDocumentTitle($this->kt_document_title);

		$copyObj->setKtFullPath($this->kt_full_path);

		$copyObj->setKtCreateUser($this->kt_create_user);

		$copyObj->setKtCreateDate($this->kt_create_date);

		$copyObj->setKtUpdateDate($this->kt_update_date);


		$copyObj->setNew(true);

		$copyObj->setDocUid(''); // this is a pkey column, so set to default value

		$copyObj->setDocType(''); // this is a pkey column, so set to default value

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
	 * @return     KtDocument Clone of current object.
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
	 * @return     KtDocumentPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new KtDocumentPeer();
		}
		return self::$peer;
	}

} // BaseKtDocument
