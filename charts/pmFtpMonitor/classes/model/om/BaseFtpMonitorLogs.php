<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/FtpMonitorLogsPeer.php';

/**
 * Base class that represents a row from the 'FTP_MONITOR_LOGS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFtpMonitorLogs extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        FtpMonitorLogsPeer
	 */
	protected static $peer;


	/**
	 * The value for the ftp_log_uid field.
	 * @var        string
	 */
	protected $ftp_log_uid = '';


	/**
	 * The value for the ftp_uid field.
	 * @var        string
	 */
	protected $ftp_uid = '';


	/**
	 * The value for the execution_date field.
	 * @var        string
	 */
	protected $execution_date = '';


	/**
	 * The value for the execution_time field.
	 * @var        string
	 */
	protected $execution_time = '';


	/**
	 * The value for the result field.
	 * @var        string
	 */
	protected $result = '';


	/**
	 * The value for the execution_datetime field.
	 * @var        string
	 */
	protected $execution_datetime;


	/**
	 * The value for the failed field.
	 * @var        int
	 */
	protected $failed = 0;


	/**
	 * The value for the succeeded field.
	 * @var        int
	 */
	protected $succeeded = 0;


	/**
	 * The value for the processed field.
	 * @var        int
	 */
	protected $processed = 0;

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
	 * Get the [ftp_log_uid] column value.
	 * 
	 * @return     string
	 */
	public function getFtpLogUid()
	{

		return $this->ftp_log_uid;
	}

	/**
	 * Get the [ftp_uid] column value.
	 * 
	 * @return     string
	 */
	public function getFtpUid()
	{

		return $this->ftp_uid;
	}

	/**
	 * Get the [execution_date] column value.
	 * 
	 * @return     string
	 */
	public function getExecutionDate()
	{

		return $this->execution_date;
	}

	/**
	 * Get the [execution_time] column value.
	 * 
	 * @return     string
	 */
	public function getExecutionTime()
	{

		return $this->execution_time;
	}

	/**
	 * Get the [result] column value.
	 * 
	 * @return     string
	 */
	public function getResult()
	{

		return $this->result;
	}

	/**
	 * Get the [execution_datetime] column value.
	 * 
	 * @return     string
	 */
	public function getExecutionDatetime()
	{

		return $this->execution_datetime;
	}

	/**
	 * Get the [failed] column value.
	 * 
	 * @return     int
	 */
	public function getFailed()
	{

		return $this->failed;
	}

	/**
	 * Get the [succeeded] column value.
	 * 
	 * @return     int
	 */
	public function getSucceeded()
	{

		return $this->succeeded;
	}

	/**
	 * Get the [processed] column value.
	 * 
	 * @return     int
	 */
	public function getProcessed()
	{

		return $this->processed;
	}

	/**
	 * Set the value of [ftp_log_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFtpLogUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ftp_log_uid !== $v || $v === '') {
			$this->ftp_log_uid = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::FTP_LOG_UID;
		}

	} // setFtpLogUid()

	/**
	 * Set the value of [ftp_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFtpUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ftp_uid !== $v || $v === '') {
			$this->ftp_uid = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::FTP_UID;
		}

	} // setFtpUid()

	/**
	 * Set the value of [execution_date] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExecutionDate($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->execution_date !== $v || $v === '') {
			$this->execution_date = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::EXECUTION_DATE;
		}

	} // setExecutionDate()

	/**
	 * Set the value of [execution_time] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExecutionTime($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->execution_time !== $v || $v === '') {
			$this->execution_time = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::EXECUTION_TIME;
		}

	} // setExecutionTime()

	/**
	 * Set the value of [result] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setResult($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->result !== $v || $v === '') {
			$this->result = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::RESULT;
		}

	} // setResult()

	/**
	 * Set the value of [execution_datetime] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setExecutionDatetime($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->execution_datetime !== $v) {
			$this->execution_datetime = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::EXECUTION_DATETIME;
		}

	} // setExecutionDatetime()

	/**
	 * Set the value of [failed] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setFailed($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->failed !== $v || $v === 0) {
			$this->failed = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::FAILED;
		}

	} // setFailed()

	/**
	 * Set the value of [succeeded] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setSucceeded($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->succeeded !== $v || $v === 0) {
			$this->succeeded = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::SUCCEEDED;
		}

	} // setSucceeded()

	/**
	 * Set the value of [processed] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setProcessed($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->processed !== $v || $v === 0) {
			$this->processed = $v;
			$this->modifiedColumns[] = FtpMonitorLogsPeer::PROCESSED;
		}

	} // setProcessed()

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

			$this->ftp_log_uid = $rs->getString($startcol + 0);

			$this->ftp_uid = $rs->getString($startcol + 1);

			$this->execution_date = $rs->getString($startcol + 2);

			$this->execution_time = $rs->getString($startcol + 3);

			$this->result = $rs->getString($startcol + 4);

			$this->execution_datetime = $rs->getString($startcol + 5);

			$this->failed = $rs->getInt($startcol + 6);

			$this->succeeded = $rs->getInt($startcol + 7);

			$this->processed = $rs->getInt($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = FtpMonitorLogsPeer::NUM_COLUMNS - FtpMonitorLogsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating FtpMonitorLogs object", $e);
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
			$con = Propel::getConnection(FtpMonitorLogsPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			FtpMonitorLogsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(FtpMonitorLogsPeer::DATABASE_NAME);
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
					$pk = FtpMonitorLogsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += FtpMonitorLogsPeer::doUpdate($this, $con);
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


			if (($retval = FtpMonitorLogsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = FtpMonitorLogsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFtpLogUid();
				break;
			case 1:
				return $this->getFtpUid();
				break;
			case 2:
				return $this->getExecutionDate();
				break;
			case 3:
				return $this->getExecutionTime();
				break;
			case 4:
				return $this->getResult();
				break;
			case 5:
				return $this->getExecutionDatetime();
				break;
			case 6:
				return $this->getFailed();
				break;
			case 7:
				return $this->getSucceeded();
				break;
			case 8:
				return $this->getProcessed();
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
		$keys = FtpMonitorLogsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getFtpLogUid(),
			$keys[1] => $this->getFtpUid(),
			$keys[2] => $this->getExecutionDate(),
			$keys[3] => $this->getExecutionTime(),
			$keys[4] => $this->getResult(),
			$keys[5] => $this->getExecutionDatetime(),
			$keys[6] => $this->getFailed(),
			$keys[7] => $this->getSucceeded(),
			$keys[8] => $this->getProcessed(),
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
		$pos = FtpMonitorLogsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFtpLogUid($value);
				break;
			case 1:
				$this->setFtpUid($value);
				break;
			case 2:
				$this->setExecutionDate($value);
				break;
			case 3:
				$this->setExecutionTime($value);
				break;
			case 4:
				$this->setResult($value);
				break;
			case 5:
				$this->setExecutionDatetime($value);
				break;
			case 6:
				$this->setFailed($value);
				break;
			case 7:
				$this->setSucceeded($value);
				break;
			case 8:
				$this->setProcessed($value);
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
		$keys = FtpMonitorLogsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setFtpLogUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFtpUid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setExecutionDate($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setExecutionTime($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setResult($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setExecutionDatetime($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFailed($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setSucceeded($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setProcessed($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(FtpMonitorLogsPeer::DATABASE_NAME);

		if ($this->isColumnModified(FtpMonitorLogsPeer::FTP_LOG_UID)) $criteria->add(FtpMonitorLogsPeer::FTP_LOG_UID, $this->ftp_log_uid);
		if ($this->isColumnModified(FtpMonitorLogsPeer::FTP_UID)) $criteria->add(FtpMonitorLogsPeer::FTP_UID, $this->ftp_uid);
		if ($this->isColumnModified(FtpMonitorLogsPeer::EXECUTION_DATE)) $criteria->add(FtpMonitorLogsPeer::EXECUTION_DATE, $this->execution_date);
		if ($this->isColumnModified(FtpMonitorLogsPeer::EXECUTION_TIME)) $criteria->add(FtpMonitorLogsPeer::EXECUTION_TIME, $this->execution_time);
		if ($this->isColumnModified(FtpMonitorLogsPeer::RESULT)) $criteria->add(FtpMonitorLogsPeer::RESULT, $this->result);
		if ($this->isColumnModified(FtpMonitorLogsPeer::EXECUTION_DATETIME)) $criteria->add(FtpMonitorLogsPeer::EXECUTION_DATETIME, $this->execution_datetime);
		if ($this->isColumnModified(FtpMonitorLogsPeer::FAILED)) $criteria->add(FtpMonitorLogsPeer::FAILED, $this->failed);
		if ($this->isColumnModified(FtpMonitorLogsPeer::SUCCEEDED)) $criteria->add(FtpMonitorLogsPeer::SUCCEEDED, $this->succeeded);
		if ($this->isColumnModified(FtpMonitorLogsPeer::PROCESSED)) $criteria->add(FtpMonitorLogsPeer::PROCESSED, $this->processed);

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
		$criteria = new Criteria(FtpMonitorLogsPeer::DATABASE_NAME);

		$criteria->add(FtpMonitorLogsPeer::FTP_LOG_UID, $this->ftp_log_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getFtpLogUid();
	}

	/**
	 * Generic method to set the primary key (ftp_log_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setFtpLogUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of FtpMonitorLogs (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFtpUid($this->ftp_uid);

		$copyObj->setExecutionDate($this->execution_date);

		$copyObj->setExecutionTime($this->execution_time);

		$copyObj->setResult($this->result);

		$copyObj->setExecutionDatetime($this->execution_datetime);

		$copyObj->setFailed($this->failed);

		$copyObj->setSucceeded($this->succeeded);

		$copyObj->setProcessed($this->processed);


		$copyObj->setNew(true);

		$copyObj->setFtpLogUid(''); // this is a pkey column, so set to default value

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
	 * @return     FtpMonitorLogs Clone of current object.
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
	 * @return     FtpMonitorLogsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new FtpMonitorLogsPeer();
		}
		return self::$peer;
	}

} // BaseFtpMonitorLogs
