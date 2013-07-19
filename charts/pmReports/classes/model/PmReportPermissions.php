<?php

require_once 'classes/model/om/BasePmReportPermissions.php';


/**
 * Skeleton subclass for representing a row from the 'PM_REPORT_PERMISSIONS' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class PmReportPermissions extends BasePmReportPermissions {
    
      private $filterThisFields = array('PMR_UID', 'ADD_TAB_UID', 'PMR_TYPE', 'PMR_OWNER_UID', 'PMR_CREATE_DATE',
                                        'PMR_UPDATE_DATE', 'PMR_STATUS');

    public function load($PmReportPermissionsUid) {
      try {
        $PmReportPermissionsInstance = PmReportPermissionsPeer::retrieveByPK($PmReportPermissionsUid);
        $fields = $PmReportPermissionsInstance->toArray(BasePeer::TYPE_FIELDNAME);
  
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
        $connection = Propel::getConnection(PmReportPermissionsPeer::DATABASE_NAME);
        try {
            if (!isset($data['PMR_UID'])) {
              $data['PMR_UID'] = '';
            }
            if ($data['PMR_UID'] == '') {
              $data['PMR_UID'] = G::generateUniqueID();
              $data['PMR_CREATE_DATE'] = date('Y-m-d H:i:s');
              $PmReportPermissionsInstance = new PmReportPermissions();
            }
            else {
              $PmReportPermissionsInstance = PmReportPermissionsPeer::retrieveByPK($data['PMR_UID'], $data['ADD_TAB_UID']);
            }
            if (!isset($data['PMR_STATUS'])) {
                $data['PMR_STATUS'] = 1;
            }

            $data['PMR_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $PmReportPermissionsInstance->fromArray($data, BasePeer::TYPE_FIELDNAME);

            if ($PmReportPermissionsInstance->validate()) {
              $connection->begin();
              $result = $PmReportPermissionsInstance->save();
              $connection->commit();
              return $data['PMR_UID'];
            } else {
              $message = '';
              $validationFailures = $PmReportPermissionsInstance->getValidationFailures();
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
    
    public function remove($data) {
        try {
          $PmReportPermissionsInstance = PmReportPermissionsPeer::retrieveByPK($data['PMR_UID'], $data['ADD_TAB_UID']);
          if (is_null($PmReportPermissionsInstance)) {
            throw new Exception('The record "' . $data['PMR_UID'] . '" not exists.');
          }
          $PmReportPermissionsInstance->delete();
        }
        catch (Exception $error) {
          throw $error;
        }
    }

} // PmReportPermissions
