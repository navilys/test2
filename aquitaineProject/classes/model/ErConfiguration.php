<?php

require_once 'classes/model/om/BaseErConfiguration.php';


/**
 * Skeleton subclass for representing a row from the 'ER_CONFIGURATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class ErConfiguration extends BaseErConfiguration {

  private $filterThisFields = array('ER_UID', 'ER_TITLE', 'PRO_UID', 'ER_TEMPLATE', 'DYN_UID', 'ER_VALID_DAYS', 'ER_ACTION_ASSIGN', 'ER_OBJECT_UID',
                                    'ER_ACTION_START_CASE', 'TAS_UID', 'ER_ACTION_EXECUTE_TRIGGER', 'TRI_UID', 'ER_CREATE_DATE', 'ER_UPDATE_DATE');

  public function load($erUid) {
    try {
      $erConfigurationInstance = ErConfigurationPeer::retrieveByPK($erUid);
      if (!$erConfigurationInstance) {
        throw new Exception('Record not found (' . $erUid . ')');
      }
      $fields = $erConfigurationInstance->toArray(BasePeer::TYPE_FIELDNAME);
      return $fields;
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  public function createOrUpdate($data) {
    foreach ($data as $field => $value) {
      if (!in_array($field, $this->filterThisFields)) {
        unset($data[$field]);
      }
    }
    $connection = Propel::getConnection(ErConfigurationPeer::DATABASE_NAME);
    try {
      if (!isset($data['ER_UID'])) {
        $data['ER_UID'] = '';
      }
      if ($data['ER_UID'] == '') {
        $data['ER_UID'] = G::generateUniqueID();
        $data['ER_CREATE_DATE'] = date('Y-m-d H:i:s');
        $erConfigurationInstance = new ErConfiguration();
      }
      else {
        $erConfigurationInstance = ErConfigurationPeer::retrieveByPK($data['ER_UID']);
      }
      if (!isset($data['ER_VALID_DAYS'])) {
        $data['ER_VALID_DAYS'] = 5;
      }
      if ($data['ER_ACTION_ASSIGN'] == '') {
        $data['ER_OBJECT_UID'] = '';
      }
      if ($data['TAS_UID'] == '') {
        $data['ER_ACTION_START_CASE'] = 0;
      }
      else {
        $data['ER_ACTION_START_CASE'] = 1;
      }
      if ($data['TRI_UID'] == '') {
        $data['ER_ACTION_EXECUTE_TRIGGER'] = 0;
      }
      else {
        $data['ER_ACTION_EXECUTE_TRIGGER'] = 1;
      }
      $data['ER_UPDATE_DATE'] = date('Y-m-d H:i:s');
      $erConfigurationInstance->fromArray($data, BasePeer::TYPE_FIELDNAME);
      if ($erConfigurationInstance->validate()) {
        $connection->begin();
        $result = $erConfigurationInstance->save();
        $connection->commit();
        return $data['ER_UID'];
      }
      else {
        $message = '';
        $validationFailures = $erConfigurationInstance->getValidationFailures();
        foreach($validationFailures as $validationFailure) {
          $message .= $validationFailure->getMessage() . '. ';
        }
        throw(new Exception('Error trying to update: ' . $message));
      }
    }
    catch (Exception $error) {
      $connection->rollback();
      throw $error;
    }
  }

  public function remove($erUid) {
    try {
      $erConfigurationInstance = ErConfigurationPeer::retrieveByPK($erUid);
      if (is_null($erConfigurationInstance)) {
        throw new Exception('The record "' . $erUid . '" not exists.');
      }
      $erConfigurationInstance->delete();
    }
    catch (Exception $error) {
      throw $error;
    }
  }

} // ErConfiguration
