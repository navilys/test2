<?php

require_once 'classes/model/om/BaseErRequests.php';


/**
 * Skeleton subclass for representing a row from the 'ER_REQUESTS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class ErRequests extends BaseErRequests {

  private $filterThisFields = array('ER_REQ_UID', 'ER_UID', 'ER_REQ_DATA', 'ER_REQ_DATE', 'ER_REQ_COMPLETED', 'ER_REQ_COMPLETED_DATE');

  public function load($erRequestUid) {
    try {
      $erRequestInstance = ErRequestsPeer::retrieveByPK($erRequestUid);
      if (!is_null($erRequestInstance)) {
        $fields = $erRequestInstance->toArray(BasePeer::TYPE_FIELDNAME);
        $fields['ER_REQ_DATA'] = @unserialize($fields['ER_REQ_DATA']);
        return $fields;
      }
      else {
        return array();
      }
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

    $connection = Propel::getConnection(ErRequestsPeer::DATABASE_NAME);
    try {
      if (!isset($data['ER_REQ_UID'])) {
        $data['ER_REQ_UID'] = '';
      }
      if ($data['ER_REQ_UID'] == '') {
        $data['ER_REQ_UID'] = G::generateUniqueID();
        $data['ER_REQ_DATE'] = date('Y-m-d H:i:s');
        $erRequestsInstance = new ErRequests();
      }
      else {
        $erRequestsInstance = ErRequestsPeer::retrieveByPK($data['ER_REQ_UID']);
      }
      if (is_array($data['ER_REQ_DATA'])) {
        $data['ER_REQ_DATA'] = @serialize($data['ER_REQ_DATA']);
      }
      $erRequestsInstance->fromArray($data, BasePeer::TYPE_FIELDNAME);
      if ($erRequestsInstance->validate()) {
        $connection->begin();
        $result = $erRequestsInstance->save();
        $connection->commit();
        return $data['ER_REQ_UID'];
      }
      else {
        $message = '';
        $validationFailures = $erRequestsInstance->getValidationFailures();
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
      $erRequestsInstance = ErRequestsPeer::retrieveByPK($erUid);
      if (is_null($erRequestsInstance)) {
        throw new Exception('The record "' . $erUid . '" not exists.');
      }
      $erRequestsInstance->delete();
    }
    catch (Exception $error) {
      throw $error;
    }
  }

} // ErRequests
