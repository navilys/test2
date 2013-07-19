<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/FtpMonitorLogsDetailsPeer.php';

/**
 * Base class that represents a row from the 'FTP_MONITOR_LOGS_DETAILS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFtpMonitorLogsDetails extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        FtpMonitorLogsDetailsPeer
	 */
	protected static $peer;


	/**
	 * The value for the ftp_log_det_uid field.
	 * @var        string
	 */
	protected $ftp_log_det_uid = '';


	/**
	 * The value for the ftp_log_uid field.
	 * @var        string
	 */
	protected $ftp_log_uid = '';


	/**
	 * The value for the app_uid field.
	 * @var        string
	 */
	protected $app_uid = '';


	/**
	 * The value for the execution_datetime field.
	 * @var        string
	 */
	protected $execution_datetime;


	/**
	 * The value for the full_path field.
	 * @var        string
	 */
	protected $full_path = '';


	/**
	 * The value for the have_xml field.
	 * @var        string
	 */
	protected $have_xml = 'FALSE';


	/**
	 * The value for the variables field.
	 * @var        string
	 */
	protected $variables = '';


	/**
	 * The value for the status field.
	 * @var        string
	 */
	protected $status = '';


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description = '';

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
	 * Get the [ftp_log_det_uid] column value.
	 * 
	 * @return     string
	 */
	public function getFtpLogDetUid()
	{

		return $this->ftp_log_det_uid;
	}

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
	 * Get the [app_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAppUid()
	{

		return $this->app_uid;
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
	 * Get the [full_path] column value.
	 * 
	 * @return     string
	 */
	public function getFullPath()
	{

		return $this->full_path;
	}

	/**
	 * Get the [have_xml] column value.
	 * 
	 * @return     string
	 */
	public function getHaveXml()
	{

		return $this->have_xml;
	}

	/**
	 * Get the [variables] column value.
	 * 
	 * @return     string
	 */
	public function getVariables()
	{

		return $this->variables;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     string
	 */
	public function getStatus()
	{

		return $this->status;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Set the value of [ftp_log_det_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFtpLogDetUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ftp_log_det_uid !== $v || $v === '') {
			$this->ftp_log_det_uid = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::FTP_LOG_DET_UID;
		}

	} // setFtpLogDetUid()

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
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::FTP_LOG_UID;
		}

	} // setFtpLogUid()

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
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::APP_UID;
		}

	} // setAppUid()

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
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::EXECUTION_DATETIME;
		}

	} // setExecutionDatetime()

	/**
	 * Set the value of [full_path] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFullPath($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->full_path !== $v || $v === '') {
			$this->full_path = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::FULL_PATH;
		}

	} // setFullPath()

	/**
	 * Set the value of [have_xml] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setHaveXml($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->have_xml !== $v || $v === 'FALSE') {
			$this->have_xml = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::HAVE_XML;
		}

	} // setHaveXml()

	/**
	 * Set the value of [variables] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setVariables($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->variables !== $v || $v === '') {
			$this->variables = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::VARIABLES;
		}

	} // setVariables()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->status !== $v || $v === '') {
			$this->status = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::STATUS;
		}

	} // setStatus()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->description !== $v || $v === '') {
			$this->description = $v;
			$this->modifiedColumns[] = FtpMonitorLogsDetailsPeer::DESCRIPTION;
		}

	} // setDescription()

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

			$this->ftp_log_det_uid = $rs->getString($startcol + 0);

			$this->ftp_log_uid = $rs->getString($startcol + 1);

			$this->app_uid = $rs->getString($startcol + 2);

			$this->execution_datetime = $rs->getString($startcol + 3);

			$this->full_path = $rs->getString($startcol + 4);

			$this->have_xml = $rs->getString($startcol + 5);

			$this->variables = $rs->getString($startcol + 6);

			$this->status = $rs->getString($startcol + 7);

			$this->description = $rs->getString($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = FtpMonitorLogsDetailsPeer::NUM_COLUMNS - FtpMonitorLogsDetailsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating FtpMonitorLogsDetails object", $e);
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
			$con = Propel::getConnection(FtpMonitorLogsDetailsPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			FtpMonitorLogsDetailsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(FtpMonitorLogsDetailsPeer::DATABASE_NAME);
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
					$pk = FtpMonitorLogsDetailsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += FtpMonitorLogsDetailsPeer::doUpdate($this, $con);
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


			if (($retval = FtpMonitorLogsDetailsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = FtpMonitorLogsDetailsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFtpLogDetUid();
				break;
			case 1:
				return $this->getFtpLogUid();
				break;
			case 2:
				return $this->getAppUid();
				break;
			case 3:
				return $this->getExecutionDatetime();
				break;
			case 4:
				return $this->getFullPath();
				break;
			case 5:
				return $this->getHaveXml();
				break;
			case 6:
				return $this->getVariables();
				break;
			case 7:
				return $this->getStatus();
				break;
			case 8:
				return $this->getDescription();
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
		$keys = FtpMonitorLogsDetailsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getFtpLogDetUid(),
			$keys[1] => $this->getFtpLogUid(),
			$keys[2] => $this->getAppUid(),
			$keys[3] => $this->getExecutionDatetime(),
			$keys[4] => $this->getFullPath(),
			$keys[5] => $this->getHaveXml(),
			$keys[6] => $this->getVariables(),
			$keys[7] => $this->getStatus(),
			$keys[8] => $this->getDescription(),
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
		$pos = FtpMonitorLogsDetailsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFtpLogDetUid($value);
				break;
			case 1:
				$this->setFtpLogUid($value);
				break;
			case 2:
				$this->setAppUid($value);
				break;
			case 3:
				$this->setExecutionDatetime($value);
				break;
			case 4:
				$this->setFullPath($value);
				break;
			case 5:
				$this->setHaveXml($value);
				break;
			case 6:
				$this->setVariables($value);
				break;
			case 7:
				$this->setStatus($value);
				break;
			case 8:
				$this->setDescription($value);
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
		$keys = FtpMonitorLogsDetailsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setFtpLogDetUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFtpLogUid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAppUid($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setExecutionDatetime($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFullPath($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setHaveXml($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setVariables($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatus($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDescription($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(FtpMonitorLogsDetailsPeer::DATABASE_NAME);

		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::FTP_LOG_DET_UID)) $criteria->add(FtpMonitorLogsDetailsPeer::FTP_LOG_DET_UID, $this->ftp_log_det_uid);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::FTP_LOG_UID)) $criteria->add(FtpMonitorLogsDetailsPeer::FTP_LOG_UID, $this->ftp_log_uid);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::APP_UID)) $criteria->add(FtpMonitorLogsDetailsPeer::APP_UID, $this->app_uid);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::EXECUTION_DATETIME)) $criteria->add(FtpMonitorLogsDetailsPeer::EXECUTION_DATETIME, $this->execution_datetime);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::FULL_PATH)) $criteria->add(FtpMonitorLogsDetailsPeer::FULL_PATH, $this->full_path);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::HAVE_XML)) $criteria->add(FtpMonitorLogsDetailsPeer::HAVE_XML, $this->have_xml);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::VARIABLES)) $criteria->add(FtpMonitorLogsDetailsPeer::VARIABLES, $this->variables);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::STATUS)) $criteria->add(FtpMonitorLogsDetailsPeer::STATUS, $this->status);
		if ($this->isColumnModified(FtpMonitorLogsDetailsPeer::DESCRIPTION)) $criteria->add(FtpMonitorLogsDetailsPeer::DESCRIPTION, $this->description);

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
		$criteria = new Criteria(FtpMonitorLogsDetailsPeer::DATABASE_NAME);

		$criteria->add(FtpMonitorLogsDetailsPeer::FTP_LOG_DET_UID, $this->ftp_log_det_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getFtpLogDetUid();
	}

	/**
	 * Generic method to set the primary key (ftp_log_det_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setFtpLogDetUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of FtpMonitorLogsDetails (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setFtpLogUid($this->ftp_log_uid);

		$copyObj->setAppUid($this->app_uid);

		$copyObj->setExecutionDatetime($this->execution_datetime);

		$copyObj->setFullPath($this->full_path);

		$copyObj->setHaveXml($this->have_xml);

		$copyObj->setVariables($this->variables);

		$copyObj->setStatus($this->status);

		$copyObj->setDescription($this->description);


		$copyObj->setNew(true);

		$copyObj->setFtpLogDetUid(''); // this is a pkey column, so set to default value

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
	 * @return     FtpMonitorLogsDetails Clone of current object.
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
	 * @return     FtpMonitorLogsDetailsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new FtpMonitorLogsDetailsPeer();
		}
		return self::$peer;
	}

} // BaseFtpMonitorLogsDetails
