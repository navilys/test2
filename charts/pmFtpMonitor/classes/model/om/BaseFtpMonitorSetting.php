<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/FtpMonitorSettingPeer.php';

/**
 * Base class that represents a row from the 'FTP_MONITOR_SETTING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFtpMonitorSetting extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        FtpMonitorSettingPeer
	 */
	protected static $peer;


	/**
	 * The value for the ftp_uid field.
	 * @var        string
	 */
	protected $ftp_uid = '';


	/**
	 * The value for the connection_type field.
	 * @var        string
	 */
	protected $connection_type = '';


	/**
	 * The value for the host field.
	 * @var        string
	 */
	protected $host = '';


	/**
	 * The value for the port field.
	 * @var        string
	 */
	protected $port = '';


	/**
	 * The value for the user field.
	 * @var        string
	 */
	protected $user = '';


	/**
	 * The value for the pass field.
	 * @var        string
	 */
	protected $pass = '';


	/**
	 * The value for the search_pattern field.
	 * @var        string
	 */
	protected $search_pattern = '';


	/**
	 * The value for the ftp_path field.
	 * @var        string
	 */
	protected $ftp_path = '';


	/**
	 * The value for the input_document_uid field.
	 * @var        string
	 */
	protected $input_document_uid;


	/**
	 * The value for the xml_search field.
	 * @var        string
	 */
	protected $xml_search = '';


	/**
	 * The value for the pro_uid field.
	 * @var        string
	 */
	protected $pro_uid = '';


	/**
	 * The value for the tas_uid field.
	 * @var        string
	 */
	protected $tas_uid = '';


	/**
	 * The value for the del_user_uid field.
	 * @var        string
	 */
	protected $del_user_uid = '';


	/**
	 * The value for the ftp_status field.
	 * @var        string
	 */
	protected $ftp_status = '';

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
	 * Get the [ftp_uid] column value.
	 * 
	 * @return     string
	 */
	public function getFtpUid()
	{

		return $this->ftp_uid;
	}

	/**
	 * Get the [connection_type] column value.
	 * 
	 * @return     string
	 */
	public function getConnectionType()
	{

		return $this->connection_type;
	}

	/**
	 * Get the [host] column value.
	 * 
	 * @return     string
	 */
	public function getHost()
	{

		return $this->host;
	}

	/**
	 * Get the [port] column value.
	 * 
	 * @return     string
	 */
	public function getPort()
	{

		return $this->port;
	}

	/**
	 * Get the [user] column value.
	 * 
	 * @return     string
	 */
	public function getUser()
	{

		return $this->user;
	}

	/**
	 * Get the [pass] column value.
	 * 
	 * @return     string
	 */
	public function getPass()
	{

		return $this->pass;
	}

	/**
	 * Get the [search_pattern] column value.
	 * 
	 * @return     string
	 */
	public function getSearchPattern()
	{

		return $this->search_pattern;
	}

	/**
	 * Get the [ftp_path] column value.
	 * 
	 * @return     string
	 */
	public function getFtpPath()
	{

		return $this->ftp_path;
	}

	/**
	 * Get the [input_document_uid] column value.
	 * 
	 * @return     string
	 */
	public function getInputDocumentUid()
	{

		return $this->input_document_uid;
	}

	/**
	 * Get the [xml_search] column value.
	 * 
	 * @return     string
	 */
	public function getXmlSearch()
	{

		return $this->xml_search;
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
	 * Get the [tas_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTasUid()
	{

		return $this->tas_uid;
	}

	/**
	 * Get the [del_user_uid] column value.
	 * 
	 * @return     string
	 */
	public function getDelUserUid()
	{

		return $this->del_user_uid;
	}

	/**
	 * Get the [ftp_status] column value.
	 * 
	 * @return     string
	 */
	public function getFtpStatus()
	{

		return $this->ftp_status;
	}

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
			$this->modifiedColumns[] = FtpMonitorSettingPeer::FTP_UID;
		}

	} // setFtpUid()

	/**
	 * Set the value of [connection_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setConnectionType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->connection_type !== $v || $v === '') {
			$this->connection_type = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::CONNECTION_TYPE;
		}

	} // setConnectionType()

	/**
	 * Set the value of [host] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setHost($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->host !== $v || $v === '') {
			$this->host = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::HOST;
		}

	} // setHost()

	/**
	 * Set the value of [port] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPort($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->port !== $v || $v === '') {
			$this->port = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::PORT;
		}

	} // setPort()

	/**
	 * Set the value of [user] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUser($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->user !== $v || $v === '') {
			$this->user = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::USER;
		}

	} // setUser()

	/**
	 * Set the value of [pass] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setPass($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pass !== $v || $v === '') {
			$this->pass = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::PASS;
		}

	} // setPass()

	/**
	 * Set the value of [search_pattern] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setSearchPattern($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->search_pattern !== $v || $v === '') {
			$this->search_pattern = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::SEARCH_PATTERN;
		}

	} // setSearchPattern()

	/**
	 * Set the value of [ftp_path] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFtpPath($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ftp_path !== $v || $v === '') {
			$this->ftp_path = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::FTP_PATH;
		}

	} // setFtpPath()

	/**
	 * Set the value of [input_document_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setInputDocumentUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->input_document_uid !== $v) {
			$this->input_document_uid = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::INPUT_DOCUMENT_UID;
		}

	} // setInputDocumentUid()

	/**
	 * Set the value of [xml_search] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setXmlSearch($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->xml_search !== $v || $v === '') {
			$this->xml_search = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::XML_SEARCH;
		}

	} // setXmlSearch()

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
			$this->modifiedColumns[] = FtpMonitorSettingPeer::PRO_UID;
		}

	} // setProUid()

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
			$this->modifiedColumns[] = FtpMonitorSettingPeer::TAS_UID;
		}

	} // setTasUid()

	/**
	 * Set the value of [del_user_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDelUserUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->del_user_uid !== $v || $v === '') {
			$this->del_user_uid = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::DEL_USER_UID;
		}

	} // setDelUserUid()

	/**
	 * Set the value of [ftp_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setFtpStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->ftp_status !== $v || $v === '') {
			$this->ftp_status = $v;
			$this->modifiedColumns[] = FtpMonitorSettingPeer::FTP_STATUS;
		}

	} // setFtpStatus()

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

			$this->ftp_uid = $rs->getString($startcol + 0);

			$this->connection_type = $rs->getString($startcol + 1);

			$this->host = $rs->getString($startcol + 2);

			$this->port = $rs->getString($startcol + 3);

			$this->user = $rs->getString($startcol + 4);

			$this->pass = $rs->getString($startcol + 5);

			$this->search_pattern = $rs->getString($startcol + 6);

			$this->ftp_path = $rs->getString($startcol + 7);

			$this->input_document_uid = $rs->getString($startcol + 8);

			$this->xml_search = $rs->getString($startcol + 9);

			$this->pro_uid = $rs->getString($startcol + 10);

			$this->tas_uid = $rs->getString($startcol + 11);

			$this->del_user_uid = $rs->getString($startcol + 12);

			$this->ftp_status = $rs->getString($startcol + 13);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 14; // 14 = FtpMonitorSettingPeer::NUM_COLUMNS - FtpMonitorSettingPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating FtpMonitorSetting object", $e);
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
			$con = Propel::getConnection(FtpMonitorSettingPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			FtpMonitorSettingPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(FtpMonitorSettingPeer::DATABASE_NAME);
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
					$pk = FtpMonitorSettingPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += FtpMonitorSettingPeer::doUpdate($this, $con);
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


			if (($retval = FtpMonitorSettingPeer::doValidate($this, $columns)) !== true) {
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
		$pos = FtpMonitorSettingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFtpUid();
				break;
			case 1:
				return $this->getConnectionType();
				break;
			case 2:
				return $this->getHost();
				break;
			case 3:
				return $this->getPort();
				break;
			case 4:
				return $this->getUser();
				break;
			case 5:
				return $this->getPass();
				break;
			case 6:
				return $this->getSearchPattern();
				break;
			case 7:
				return $this->getFtpPath();
				break;
			case 8:
				return $this->getInputDocumentUid();
				break;
			case 9:
				return $this->getXmlSearch();
				break;
			case 10:
				return $this->getProUid();
				break;
			case 11:
				return $this->getTasUid();
				break;
			case 12:
				return $this->getDelUserUid();
				break;
			case 13:
				return $this->getFtpStatus();
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
		$keys = FtpMonitorSettingPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getFtpUid(),
			$keys[1] => $this->getConnectionType(),
			$keys[2] => $this->getHost(),
			$keys[3] => $this->getPort(),
			$keys[4] => $this->getUser(),
			$keys[5] => $this->getPass(),
			$keys[6] => $this->getSearchPattern(),
			$keys[7] => $this->getFtpPath(),
			$keys[8] => $this->getInputDocumentUid(),
			$keys[9] => $this->getXmlSearch(),
			$keys[10] => $this->getProUid(),
			$keys[11] => $this->getTasUid(),
			$keys[12] => $this->getDelUserUid(),
			$keys[13] => $this->getFtpStatus(),
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
		$pos = FtpMonitorSettingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFtpUid($value);
				break;
			case 1:
				$this->setConnectionType($value);
				break;
			case 2:
				$this->setHost($value);
				break;
			case 3:
				$this->setPort($value);
				break;
			case 4:
				$this->setUser($value);
				break;
			case 5:
				$this->setPass($value);
				break;
			case 6:
				$this->setSearchPattern($value);
				break;
			case 7:
				$this->setFtpPath($value);
				break;
			case 8:
				$this->setInputDocumentUid($value);
				break;
			case 9:
				$this->setXmlSearch($value);
				break;
			case 10:
				$this->setProUid($value);
				break;
			case 11:
				$this->setTasUid($value);
				break;
			case 12:
				$this->setDelUserUid($value);
				break;
			case 13:
				$this->setFtpStatus($value);
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
		$keys = FtpMonitorSettingPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setFtpUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setConnectionType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setHost($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPort($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUser($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPass($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setSearchPattern($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setFtpPath($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setInputDocumentUid($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setXmlSearch($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setProUid($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setTasUid($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDelUserUid($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setFtpStatus($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(FtpMonitorSettingPeer::DATABASE_NAME);

		if ($this->isColumnModified(FtpMonitorSettingPeer::FTP_UID)) $criteria->add(FtpMonitorSettingPeer::FTP_UID, $this->ftp_uid);
		if ($this->isColumnModified(FtpMonitorSettingPeer::CONNECTION_TYPE)) $criteria->add(FtpMonitorSettingPeer::CONNECTION_TYPE, $this->connection_type);
		if ($this->isColumnModified(FtpMonitorSettingPeer::HOST)) $criteria->add(FtpMonitorSettingPeer::HOST, $this->host);
		if ($this->isColumnModified(FtpMonitorSettingPeer::PORT)) $criteria->add(FtpMonitorSettingPeer::PORT, $this->port);
		if ($this->isColumnModified(FtpMonitorSettingPeer::USER)) $criteria->add(FtpMonitorSettingPeer::USER, $this->user);
		if ($this->isColumnModified(FtpMonitorSettingPeer::PASS)) $criteria->add(FtpMonitorSettingPeer::PASS, $this->pass);
		if ($this->isColumnModified(FtpMonitorSettingPeer::SEARCH_PATTERN)) $criteria->add(FtpMonitorSettingPeer::SEARCH_PATTERN, $this->search_pattern);
		if ($this->isColumnModified(FtpMonitorSettingPeer::FTP_PATH)) $criteria->add(FtpMonitorSettingPeer::FTP_PATH, $this->ftp_path);
		if ($this->isColumnModified(FtpMonitorSettingPeer::INPUT_DOCUMENT_UID)) $criteria->add(FtpMonitorSettingPeer::INPUT_DOCUMENT_UID, $this->input_document_uid);
		if ($this->isColumnModified(FtpMonitorSettingPeer::XML_SEARCH)) $criteria->add(FtpMonitorSettingPeer::XML_SEARCH, $this->xml_search);
		if ($this->isColumnModified(FtpMonitorSettingPeer::PRO_UID)) $criteria->add(FtpMonitorSettingPeer::PRO_UID, $this->pro_uid);
		if ($this->isColumnModified(FtpMonitorSettingPeer::TAS_UID)) $criteria->add(FtpMonitorSettingPeer::TAS_UID, $this->tas_uid);
		if ($this->isColumnModified(FtpMonitorSettingPeer::DEL_USER_UID)) $criteria->add(FtpMonitorSettingPeer::DEL_USER_UID, $this->del_user_uid);
		if ($this->isColumnModified(FtpMonitorSettingPeer::FTP_STATUS)) $criteria->add(FtpMonitorSettingPeer::FTP_STATUS, $this->ftp_status);

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
		$criteria = new Criteria(FtpMonitorSettingPeer::DATABASE_NAME);

		$criteria->add(FtpMonitorSettingPeer::FTP_UID, $this->ftp_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getFtpUid();
	}

	/**
	 * Generic method to set the primary key (ftp_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setFtpUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of FtpMonitorSetting (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setConnectionType($this->connection_type);

		$copyObj->setHost($this->host);

		$copyObj->setPort($this->port);

		$copyObj->setUser($this->user);

		$copyObj->setPass($this->pass);

		$copyObj->setSearchPattern($this->search_pattern);

		$copyObj->setFtpPath($this->ftp_path);

		$copyObj->setInputDocumentUid($this->input_document_uid);

		$copyObj->setXmlSearch($this->xml_search);

		$copyObj->setProUid($this->pro_uid);

		$copyObj->setTasUid($this->tas_uid);

		$copyObj->setDelUserUid($this->del_user_uid);

		$copyObj->setFtpStatus($this->ftp_status);


		$copyObj->setNew(true);

		$copyObj->setFtpUid(''); // this is a pkey column, so set to default value

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
	 * @return     FtpMonitorSetting Clone of current object.
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
	 * @return     FtpMonitorSettingPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new FtpMonitorSettingPeer();
		}
		return self::$peer;
	}

} // BaseFtpMonitorSetting
