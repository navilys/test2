<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'propel/util/Criteria.php';

include_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhUserRolePeer.php';

/**
 * Base class that represents a row from the 'PH_USER_ROLE' table.
 *
 * 
 *
 * @package plugins.pentahoreports.classes.model.om
 */
abstract class BasePhUserRole extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PhUserRolePeer
	 */
	protected static $peer;


	/**
	 * The value for the rol_obj_uid field.
	 * @var        string
	 */
	protected $rol_obj_uid = '0';


	/**
	 * The value for the rol_uid field.
	 * @var        string
	 */
	protected $rol_uid = '0';


	/**
	 * The value for the obj_uid field.
	 * @var        string
	 */
	protected $obj_uid = '0';


	/**
	 * The value for the obj_type field.
	 * @var        string
	 */
	protected $obj_type = '0';


	/**
	 * The value for the obj_dashboard field.
	 * @var        string
	 */
	protected $obj_dashboard = '0';

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
	 * Get the [rol_obj_uid] column value.
	 * 
	 * @return     string
	 */
	public function getRolObjUid()
	{

		return $this->rol_obj_uid;
	}

	/**
	 * Get the [rol_uid] column value.
	 * 
	 * @return     string
	 */
	public function getRolUid()
	{

		return $this->rol_uid;
	}

	/**
	 * Get the [obj_uid] column value.
	 * 
	 * @return     string
	 */
	public function getObjUid()
	{

		return $this->obj_uid;
	}

	/**
	 * Get the [obj_type] column value.
	 * 
	 * @return     string
	 */
	public function getObjType()
	{

		return $this->obj_type;
	}

	/**
	 * Get the [obj_dashboard] column value.
	 * 
	 * @return     string
	 */
	public function getObjDashboard()
	{

		return $this->obj_dashboard;
	}

	/**
	 * Set the value of [rol_obj_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRolObjUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rol_obj_uid !== $v || $v === '0') {
			$this->rol_obj_uid = $v;
			$this->modifiedColumns[] = PhUserRolePeer::ROL_OBJ_UID;
		}

	} // setRolObjUid()

	/**
	 * Set the value of [rol_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setRolUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->rol_uid !== $v || $v === '0') {
			$this->rol_uid = $v;
			$this->modifiedColumns[] = PhUserRolePeer::ROL_UID;
		}

	} // setRolUid()

	/**
	 * Set the value of [obj_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->obj_uid !== $v || $v === '0') {
			$this->obj_uid = $v;
			$this->modifiedColumns[] = PhUserRolePeer::OBJ_UID;
		}

	} // setObjUid()

	/**
	 * Set the value of [obj_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->obj_type !== $v || $v === '0') {
			$this->obj_type = $v;
			$this->modifiedColumns[] = PhUserRolePeer::OBJ_TYPE;
		}

	} // setObjType()

	/**
	 * Set the value of [obj_dashboard] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setObjDashboard($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->obj_dashboard !== $v || $v === '0') {
			$this->obj_dashboard = $v;
			$this->modifiedColumns[] = PhUserRolePeer::OBJ_DASHBOARD;
		}

	} // setObjDashboard()

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

			$this->rol_obj_uid = $rs->getString($startcol + 0);

			$this->rol_uid = $rs->getString($startcol + 1);

			$this->obj_uid = $rs->getString($startcol + 2);

			$this->obj_type = $rs->getString($startcol + 3);

			$this->obj_dashboard = $rs->getString($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 5; // 5 = PhUserRolePeer::NUM_COLUMNS - PhUserRolePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PhUserRole object", $e);
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
			$con = Propel::getConnection(PhUserRolePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PhUserRolePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PhUserRolePeer::DATABASE_NAME);
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
					$pk = PhUserRolePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += PhUserRolePeer::doUpdate($this, $con);
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


			if (($retval = PhUserRolePeer::doValidate($this, $columns)) !== true) {
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
		$pos = PhUserRolePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getRolObjUid();
				break;
			case 1:
				return $this->getRolUid();
				break;
			case 2:
				return $this->getObjUid();
				break;
			case 3:
				return $this->getObjType();
				break;
			case 4:
				return $this->getObjDashboard();
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
		$keys = PhUserRolePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getRolObjUid(),
			$keys[1] => $this->getRolUid(),
			$keys[2] => $this->getObjUid(),
			$keys[3] => $this->getObjType(),
			$keys[4] => $this->getObjDashboard(),
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
		$pos = PhUserRolePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setRolObjUid($value);
				break;
			case 1:
				$this->setRolUid($value);
				break;
			case 2:
				$this->setObjUid($value);
				break;
			case 3:
				$this->setObjType($value);
				break;
			case 4:
				$this->setObjDashboard($value);
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
		$keys = PhUserRolePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setRolObjUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setRolUid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setObjUid($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setObjType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setObjDashboard($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PhUserRolePeer::DATABASE_NAME);

		if ($this->isColumnModified(PhUserRolePeer::ROL_OBJ_UID)) $criteria->add(PhUserRolePeer::ROL_OBJ_UID, $this->rol_obj_uid);
		if ($this->isColumnModified(PhUserRolePeer::ROL_UID)) $criteria->add(PhUserRolePeer::ROL_UID, $this->rol_uid);
		if ($this->isColumnModified(PhUserRolePeer::OBJ_UID)) $criteria->add(PhUserRolePeer::OBJ_UID, $this->obj_uid);
		if ($this->isColumnModified(PhUserRolePeer::OBJ_TYPE)) $criteria->add(PhUserRolePeer::OBJ_TYPE, $this->obj_type);
		if ($this->isColumnModified(PhUserRolePeer::OBJ_DASHBOARD)) $criteria->add(PhUserRolePeer::OBJ_DASHBOARD, $this->obj_dashboard);

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
		$criteria = new Criteria(PhUserRolePeer::DATABASE_NAME);

		$criteria->add(PhUserRolePeer::ROL_OBJ_UID, $this->rol_obj_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getRolObjUid();
	}

	/**
	 * Generic method to set the primary key (rol_obj_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setRolObjUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of PhUserRole (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setRolUid($this->rol_uid);

		$copyObj->setObjUid($this->obj_uid);

		$copyObj->setObjType($this->obj_type);

		$copyObj->setObjDashboard($this->obj_dashboard);


		$copyObj->setNew(true);

		$copyObj->setRolObjUid('0'); // this is a pkey column, so set to default value

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
	 * @return     PhUserRole Clone of current object.
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
	 * @return     PhUserRolePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PhUserRolePeer();
		}
		return self::$peer;
	}

} // BasePhUserRole
