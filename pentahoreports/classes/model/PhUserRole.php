<?php

/**
 * @section Filename
 * PhUserRole.php
 * @subsection Description
 * Skeleton subclass for representing a row from the 'PH_USER_ROLE' table.
 * This class handles operations related to the PH_USER_ROLE table, that are related with the roles table.
 * @author gustavo cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.model
 */

require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/om/BasePhUserRole.php';
require_once PATH_CORE.'classes/model/Content.php';

class PhUserRole extends BasePhUserRole {

  /**
   * This method creates a new record in the table PH_USER_ROLE.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param Array $aData array from the data passed.
   * @return true if the record has been created
   */
  function create($aData) {
    try {
      // Assembling the search criteria object
      $oCriteria = new Criteria();
      $oCriteria->add(PhUserRolePeer::ROL_UID, $aData['ROL_UID']);
      $oCriteria->add(PhUserRolePeer::OBJ_UID, $aData['OBJ_UID']);
      $oDataset = PhUserRolePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      // if found return true
      if (is_array($aRow)) {
        return true;
      } else {
        // if not create the record
        $aData['ROL_OBJ_UID'] = G::generateUniqueID();
        $oUserRole = new PhUserRole();
        $oUserRole->fromArray($aData, BasePeer::TYPE_FIELDNAME);
        $iResult = $oUserRole->save();
        return true;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /**
   * This method loads a record from the table PH_USER_ROLE.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param Array $aData array from the data required.
   * @return true if the record has been created
   */
  public function load($aData) {
    try {
      // Assemblign the criteria object
      $oCriteria = new Criteria();
      $oCriteria->add(PhUserRolePeer::ROL_UID, $aData['ROL_UID']);
      $oCriteria->add(PhUserRolePeer::OBJ_UID, $aData['OBJ_UID']);
      $oDataset = PhUserRolePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $oRow = $oDataset->getRow();
      // if found return the record
      if (!is_null($oRow)) {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        return $aFields;
      } else {
        $rolUid = $aData['ROL_UID'];
        $objUid = $aData['OBJ_UID'];
        throw (new Exception("The '$rolUid','$objUid' row doesn't exists!"));
      }
    } catch( exception $oError ) {
      throw ($oError);
    }
  }

  /**
   * This method gets a criteria object with all the assignments of users into roles.
   * @author gustavo cruz <gustavo@colosa.com>
   * @return Object $oCriteria 
   */
  public function listAllUserRoles() {
    try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $oCriteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $oCriteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $oCriteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $oCriteria->add(PhUserRolePeer::ROL_UID, '', Criteria::NOT_EQUAL);
      return $oCriteria;
    } catch( exception $oError ) {
      throw (new Exception("Class ROLES::FATAL ERROR. Criteria Can't be initialized"));
    }
  }

  /**
   * This method gets all the assignments of users into roles.
   * @author gustavo cruz <gustavo@colosa.com>
   * @return Array $aRows assignments 
   */
  public function getAllUserRoles() {
    $c  = $this->listAllUserRoles();
    $rs = PhUserRolePeer::DoSelectRs($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    $aRows = Array();
    while($rs->next()){
      $row = $rs->getRow();
      $aRows[] = $row;
    }
    return $aRows;
  }

  /**
   * This method gets the users assigned to a role.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param  String $ROL_UID role uid
   * @return Object $oDataset
   */
  function getRoleUsers($ROL_UID) {
    try {
      // Assembling the criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_OBJ_UID);
      $criteria->setDistinct();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
      $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
      $criteria->addSelectColumn(RbacUsersPeer::USR_FIRSTNAME);
      $criteria->addSelectColumn(RbacUsersPeer::USR_LASTNAME);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      // filtering by users
      $criteria->add(PhUserRolePeer::OBJ_TYPE, "USER", Criteria::EQUAL);
      $criteria->add(PhUserRolePeer::ROL_UID, $ROL_UID);

      $criteria->addJoin(PhUserRolePeer::OBJ_UID, RbacUsersPeer::USR_UID);
      // executing the query
      $oDataset = PhUserRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the dataset object
      return $oDataset;

    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method gets the groups assigned to a role.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $ROL_UID role uid
   * @return Object $oDataset
   */
  function getRoleGroups($ROL_UID) {
    try {
      // assembling the Criteria object
      $criteria = new Criteria();
      $criteria->setDistinct();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->addSelectColumn(ContentPeer::CON_VALUE);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      // filtering by groups
      $criteria->add(PhUserRolePeer::OBJ_TYPE, "GROUP", Criteria::EQUAL);
      $criteria->add(PhUserRolePeer::ROL_UID, $ROL_UID);
      //$criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
      $criteria->addJoin(PhUserRolePeer::OBJ_UID, ContentPeer::CON_ID);
      // executing the query
      $oDataset = PhUserRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the dataset
      return $oDataset;

    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method gets the users assigned to a role.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $ROL_UID role uid
   * @return Object $oDataset
   */
  function getRoleDepartments($ROL_UID) {
    try {
      // assembling the Criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_OBJ_UID);
      $criteria->setDistinct();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->addSelectColumn(ContentPeer::CON_VALUE);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      // filtering by department
      $criteria->add(PhUserRolePeer::OBJ_TYPE, "DEPARTMENT", Criteria::EQUAL);
      $criteria->add(PhUserRolePeer::ROL_UID, $ROL_UID);
      //$criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
      $criteria->addJoin(PhUserRolePeer::OBJ_UID, ContentPeer::CON_ID);
      // executing the query
      $oDataset = PhUserRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the dataset
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method gets the roles assigned to a user.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param  String $usrUid user uid
   * @return Object $oDataset
   */
  function getRolesByUser($usrUid) {
    try {
      // assembling the Criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      // filtering by user
      $criteria->add(PhUserRolePeer::OBJ_TYPE, "USER", Criteria::EQUAL);
      $criteria->add(PhUserRolePeer::OBJ_UID, $usrUid);
      //$criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
      $criteria->addJoin(PhUserRolePeer::ROL_UID, PhRolePeer::ROL_UID);
      // executing the query
      $oDataset = PhUserRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the object with the result set
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method gets the roles assigned to an object
   * @param  String $usrUid object uid
   * @return Object $oDataset
   */
  function getRolesByObject($usrUid) {
    try {
      // assembling the Criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_TYPE);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      $criteria->add(PhUserRolePeer::OBJ_UID, $usrUid);
      //$criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
      $criteria->addJoin(PhUserRolePeer::ROL_UID, PhRolePeer::ROL_UID);
      // executing the query
      $oDataset = PhUserRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the object with the result set
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method deletes the assignment of a user in a role.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param  Array  $aData record data
   * @return void
   */
  function deleteUserRole($aData){
    $oUserRole = new PhUserRole();
    $oUserRole->fromArray($aData, BasePeer::TYPE_FIELDNAME);
    $iResult = $oUserRole->delete();
  }

  /**
   * This method verifies the assignment of a user in a role.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param  Array  $aData record data
   * @return void
   */
  function existsInRole($objUid,$rolUid){
    try{
      // Assembling the criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhUserRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_UID);
      $criteria->addSelectColumn(PhUserRolePeer::OBJ_DASHBOARD);
      $criteria->add(PhUserRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      $criteria->add(PhUserRolePeer::OBJ_UID, $objUid);
      $criteria->add(PhUserRolePeer::ROL_UID, $rolUid);
      //$criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
      // executing a count request with the criteria object
      $oDataset = PhUserRolePeer::doCount($criteria);
      // if there are more than 0 records, the user is assigned 
      if ($oDataset>0){
        return true;
      } else {
        return false;
      }

    } catch( exception $e ) {
      throw $e;
    }
  }
} // PhUserRole
