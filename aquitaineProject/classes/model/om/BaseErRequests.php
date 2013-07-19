<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ErRequestsPeer.php';

/**
 * Base class that represents a row from the 'ER_REQUESTS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseErRequests extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ErRequestsPeer
	 */
	protected static $peer;


	/**
	 * The value for the er_req_uid field.
	 * @var        string
	 */
	protected $er_req_uid = '';


	/**
	 * The value for the er_uid field.
	 * @var        string
	 */
	protected $er_uid = '';


	/**
	 * The value for the er_req_data field.
	 * @var        string
	 */
	protected $er_req_data = '0';


	/**
	 * The value for the er_req_date field.
	 * @var        int
	 */
	protected $er_req_date;


	/**
	 * The value for the er_req_completed field.
	 * @var        int
	 */
	protected $er_req_completed = 0;


	/**
	 * The value for the er_req_completed_date field.
	 * @var        int
	 */
	protected $er_req_completed_date;

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
	 * Get the [er_req_uid] column value.
	 * 
	 * @return     string
	 */
	public function getErReqUid()
	{

		return $this->er_req_uid;
	}

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
	 * Get the [er_req_data] column value.
	 * 
	 * @return     string
	 */
	public function getErReqData()
	{

		return $this->er_req_data;
	}

	/**
	 * Get the [optionally formatted] [er_req_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getErReqDate($format = 'Y-m-d H:i:s')
	{

		if ($this->er_req_date === null || $this->er_req_date === '') {
			return null;
		} elseif (!is_int($this->er_req_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->er_req_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [er_req_date] as date/time value: " . var_export($this->er_req_date, true));
			}
		} else {
			$ts = $this->er_req_date;
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
	 * Get the [er_req_completed] column value.
	 * 
	 * @return     int
	 */
	public function getErReqCompleted()
	{

		return $this->er_req_completed;
	}

	/**
	 * Get the [optionally formatted] [er_req_completed_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getErReqCompletedDate($format = 'Y-m-d H:i:s')
	{

		if ($this->er_req_completed_date === null || $this->er_req_completed_date === '') {
			return null;
		} elseif (!is_int($this->er_req_completed_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->er_req_completed_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [er_req_completed_date] as date/time value: " . var_export($this->er_req_completed_date, true));
			}
		} else {
			$ts = $this->er_req_completed_date;
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
	 * Set the value of [er_req_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErReqUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_req_uid !== $v || $v === '') {
			$this->er_req_uid = $v;
			$this->modifiedColumns[] = ErRequestsPeer::ER_REQ_UID;
		}

	} // setErReqUid()

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
			$this->modifiedColumns[] = ErRequestsPeer::ER_UID;
		}

	} // setErUid()

	/**
	 * Set the value of [er_req_data] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setErReqData($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->er_req_data !== $v || $v === '0') {
			$this->er_req_data = $v;
			$this->modifiedColumns[] = ErRequestsPeer::ER_REQ_DATA;
		}

	} // setErReqData()

	/**
	 * Set the value of [er_req_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErReqDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [er_req_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->er_req_date !== $ts) {
			$this->er_req_date = $ts;
			$this->modifiedColumns[] = ErRequestsPeer::ER_REQ_DATE;
		}

	} // setErReqDate()

	/**
	 * Set the value of [er_req_completed] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErReqCompleted($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->er_req_completed !== $v || $v === 0) {
			$this->er_req_completed = $v;
			$this->modifiedColumns[] = ErRequestsPeer::ER_REQ_COMPLETED;
		}

	} // setErReqCompleted()

	/**
	 * Set the value of [er_req_completed_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setErReqCompletedDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [er_req_completed_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->er_req_completed_date !== $ts) {
			$this->er_req_completed_date = $ts;
			$this->modifiedColumns[] = ErRequestsPeer::ER_REQ_COMPLETED_DATE;
		}

	} // setErReqCompletedDate()

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

			$this->er_req_uid = $rs->getString($startcol + 0);

			$this->er_uid = $rs->getString($startcol + 1);

			$this->er_req_data = $rs->getString($startcol + 2);

			$this->er_req_date = $rs->getTimestamp($startcol + 3, null);

			$this->er_req_completed = $rs->getInt($startcol + 4);

			$this->er_req_completed_date = $rs->getTimestamp($startcol + 5, null);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = ErRequestsPeer::NUM_COLUMNS - ErRequestsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ErRequests object", $e);
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
			$con = Propel::getConnection(ErRequestsPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ErRequestsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ErRequestsPeer::DATABASE_NAME);
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
					$pk = ErRequestsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += ErRequestsPeer::doUpdate($this, $con);
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


			if (($retval = ErRequestsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ErRequestsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getErReqUid();
				break;
			case 1:
				return $this->getErUid();
				break;
			case 2:
				return $this->getErReqData();
				break;
			case 3:
				return $this->getErReqDate();
				break;
			case 4:
				return $this->getErReqCompleted();
				break;
			case 5:
				return $this->getErReqCompletedDate();
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
		$keys = ErRequestsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getErReqUid(),
			$keys[1] => $this->getErUid(),
			$keys[2] => $this->getErReqData(),
			$keys[3] => $this->getErReqDate(),
			$keys[4] => $this->getErReqCompleted(),
			$keys[5] => $this->getErReqCompletedDate(),
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
		$pos = ErRequestsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setErReqUid($value);
				break;
			case 1:
				$this->setErUid($value);
				break;
			case 2:
				$this->setErReqData($value);
				break;
			case 3:
				$this->setErReqDate($value);
				break;
			case 4:
				$this->setErReqCompleted($value);
				break;
			case 5:
				$this->setErReqCompletedDate($value);
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
		$keys = ErRequestsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setErReqUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setErUid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setErReqData($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setErReqDate($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setErReqCompleted($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setErReqCompletedDate($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ErRequestsPeer::DATABASE_NAME);

		if ($this->isColumnModified(ErRequestsPeer::ER_REQ_UID)) $criteria->add(ErRequestsPeer::ER_REQ_UID, $this->er_req_uid);
		if ($this->isColumnModified(ErRequestsPeer::ER_UID)) $criteria->add(ErRequestsPeer::ER_UID, $this->er_uid);
		if ($this->isColumnModified(ErRequestsPeer::ER_REQ_DATA)) $criteria->add(ErRequestsPeer::ER_REQ_DATA, $this->er_req_data);
		if ($this->isColumnModified(ErRequestsPeer::ER_REQ_DATE)) $criteria->add(ErRequestsPeer::ER_REQ_DATE, $this->er_req_date);
		if ($this->isColumnModified(ErRequestsPeer::ER_REQ_COMPLETED)) $criteria->add(ErRequestsPeer::ER_REQ_COMPLETED, $this->er_req_completed);
		if ($this->isColumnModified(ErRequestsPeer::ER_REQ_COMPLETED_DATE)) $criteria->add(ErRequestsPeer::ER_REQ_COMPLETED_DATE, $this->er_req_completed_date);

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
		$criteria = new Criteria(ErRequestsPeer::DATABASE_NAME);

		$criteria->add(ErRequestsPeer::ER_REQ_UID, $this->er_req_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getErReqUid();
	}

	/**
	 * Generic method to set the primary key (er_req_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setErReqUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ErRequests (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setErUid($this->er_uid);

		$copyObj->setErReqData($this->er_req_data);

		$copyObj->setErReqDate($this->er_req_date);

		$copyObj->setErReqCompleted($this->er_req_completed);

		$copyObj->setErReqCompletedDate($this->er_req_completed_date);


		$copyObj->setNew(true);

		$copyObj->setErReqUid(''); // this is a pkey column, so set to default value

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
	 * @return     ErRequests Clone of current object.
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
	 * @return     ErRequestsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ErRequestsPeer();
		}
		return self::$peer;
	}

} // BaseErRequests
