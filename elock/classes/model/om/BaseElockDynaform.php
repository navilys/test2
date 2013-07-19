<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ElockDynaformPeer.php';

/**
 * Base class that represents a row from the 'ELOCK_DYNAFORM' table.
 *
 * 
 *
 * @package    classes.model.om
 */
abstract class BaseElockDynaform extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ElockDynaformPeer
	 */
	protected static $peer;


	/**
	 * The value for the uid_dynaform field.
	 * @var        string
	 */
	protected $uid_dynaform = '';


	/**
	 * The value for the uid_application field.
	 * @var        string
	 */
	protected $uid_application = '';


	/**
	 * The value for the base64 field.
	 * @var        string
	 */
	protected $base64 = '';


	/**
	 * The value for the user field.
	 * @var        string
	 */
	protected $user = '';


	/**
	 * The value for the timestamp field.
	 * @var        string
	 */
	protected $timestamp = '';

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
	 * Get the [uid_dynaform] column value.
	 * 
	 * @return     string
	 */
	public function getUidDynaform()
	{

		return $this->uid_dynaform;
	}

	/**
	 * Get the [uid_application] column value.
	 * 
	 * @return     string
	 */
	public function getUidApplication()
	{

		return $this->uid_application;
	}

	/**
	 * Get the [base64] column value.
	 * 
	 * @return     string
	 */
	public function getBase64()
	{

		return $this->base64;
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
	 * Get the [timestamp] column value.
	 * 
	 * @return     string
	 */
	public function getTimestamp()
	{

		return $this->timestamp;
	}

	/**
	 * Set the value of [uid_dynaform] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUidDynaform($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->uid_dynaform !== $v || $v === '') {
			$this->uid_dynaform = $v;
			$this->modifiedColumns[] = ElockDynaformPeer::UID_DYNAFORM;
		}

	} // setUidDynaform()

	/**
	 * Set the value of [uid_application] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setUidApplication($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->uid_application !== $v || $v === '') {
			$this->uid_application = $v;
			$this->modifiedColumns[] = ElockDynaformPeer::UID_APPLICATION;
		}

	} // setUidApplication()

	/**
	 * Set the value of [base64] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setBase64($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->base64 !== $v || $v === '') {
			$this->base64 = $v;
			$this->modifiedColumns[] = ElockDynaformPeer::BASE64;
		}

	} // setBase64()

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
			$this->modifiedColumns[] = ElockDynaformPeer::USER;
		}

	} // setUser()

	/**
	 * Set the value of [timestamp] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTimestamp($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->timestamp !== $v || $v === '') {
			$this->timestamp = $v;
			$this->modifiedColumns[] = ElockDynaformPeer::TIMESTAMP;
		}

	} // setTimestamp()

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

			$this->uid_dynaform = $rs->getString($startcol + 0);

			$this->uid_application = $rs->getString($startcol + 1);

			$this->base64 = $rs->getString($startcol + 2);

			$this->user = $rs->getString($startcol + 3);

			$this->timestamp = $rs->getString($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = ElockDynaformPeer::NUM_COLUMNS - ElockDynaformPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ElockDynaform object", $e);
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
			$con = Propel::getConnection(ElockDynaformPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ElockDynaformPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ElockDynaformPeer::DATABASE_NAME);
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
					$pk = ElockDynaformPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += ElockDynaformPeer::doUpdate($this, $con);
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


			if (($retval = ElockDynaformPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ElockDynaformPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getUidDynaform();
				break;
			case 1:
				return $this->getUidApplication();
				break;
			case 2:
				return $this->getBase64();
				break;
			case 3:
				return $this->getUser();
				break;
			case 4:
				return $this->getTimestamp();
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
		$keys = ElockDynaformPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getUidDynaform(),
			$keys[1] => $this->getUidApplication(),
			$keys[2] => $this->getBase64(),
			$keys[3] => $this->getUser(),
			$keys[4] => $this->getTimestamp(),
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
		$pos = ElockDynaformPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setUidDynaform($value);
				break;
			case 1:
				$this->setUidApplication($value);
				break;
			case 2:
				$this->setBase64($value);
				break;
			case 3:
				$this->setUser($value);
				break;
			case 4:
				$this->setTimestamp($value);
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
		$keys = ElockDynaformPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setUidDynaform($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUidApplication($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setBase64($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUser($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setTimestamp($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ElockDynaformPeer::DATABASE_NAME);

		if ($this->isColumnModified(ElockDynaformPeer::UID_DYNAFORM)) $criteria->add(ElockDynaformPeer::UID_DYNAFORM, $this->uid_dynaform);
		if ($this->isColumnModified(ElockDynaformPeer::UID_APPLICATION)) $criteria->add(ElockDynaformPeer::UID_APPLICATION, $this->uid_application);
		if ($this->isColumnModified(ElockDynaformPeer::BASE64)) $criteria->add(ElockDynaformPeer::BASE64, $this->base64);
		if ($this->isColumnModified(ElockDynaformPeer::USER)) $criteria->add(ElockDynaformPeer::USER, $this->user);
		if ($this->isColumnModified(ElockDynaformPeer::TIMESTAMP)) $criteria->add(ElockDynaformPeer::TIMESTAMP, $this->timestamp);

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
		$criteria = new Criteria(ElockDynaformPeer::DATABASE_NAME);

		$criteria->add(ElockDynaformPeer::UID_DYNAFORM, $this->uid_dynaform);
		$criteria->add(ElockDynaformPeer::UID_APPLICATION, $this->uid_application);

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

		$pks[0] = $this->getUidDynaform();

		$pks[1] = $this->getUidApplication();

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

		$this->setUidDynaform($keys[0]);

		$this->setUidApplication($keys[1]);

	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ElockDynaform (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setBase64($this->base64);

		$copyObj->setUser($this->user);

		$copyObj->setTimestamp($this->timestamp);


		$copyObj->setNew(true);

		$copyObj->setUidDynaform(''); // this is a pkey column, so set to default value

		$copyObj->setUidApplication(''); // this is a pkey column, so set to default value

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
	 * @return     ElockDynaform Clone of current object.
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
	 * @return     ElockDynaformPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ElockDynaformPeer();
		}
		return self::$peer;
	}

} // BaseElockDynaform
