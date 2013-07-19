<?php

/**
 * @section Filename
 * PhRole.php
 * @subsection Description
 * Skeleton subclass for representing a row from the 'PH_ROLE' table.
 * This class handles operations related to the PH_ROLE table.
 * @author gustavo cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.model
 */

require_once 'om/BasePhRole.php';

class PhRole extends BasePhRole {
   /**
    * The current role code obtained from the database
    */
    private $roleCode;

    /**
     * This method assembles a criteria object in order to obtain all the roles
     * @author gustavo cruz <gustavo@colosa.com>
     * @return Object Criteria object
     */
    public function listAllRoles() {
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(PhRolePeer::ROL_UID);
            $oCriteria->addSelectColumn(PhRolePeer::ROL_CODE);
            $oCriteria->add(PhRolePeer::ROL_UID, '', Criteria::NOT_EQUAL);
            return $oCriteria;
        } catch( exception $oError ) {
            throw (new Exception("Class ROLES::FATAL ERROR. Criteria with rbac Can't initialized"));
        }
    }

    /**
     * This method executes a query object in order to obtain all the roles.
     * @author gustavo cruz <gustavo@colosa.com>
     * @return Array Roles list.
     */

    public function getAllRoles() {
        // getting and executing the query
        $c  = $this->listAllRoles();
        $rs = PhRolePeer::DoSelectRs($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $aRows = Array();
        // assembling the Array list
        while($rs->next()){
        	$row = $rs->getRow();
        	$o = new PhRole();
        	$o->load($row['ROL_UID']);
        	$row['ROL_CODE'] = $o->getRolCode();
        	$aRows[] = $row;
        }
        return $aRows;
    }

    /**
     * This method loads the data of a determined role
     * @author gustavo cruz <gustavo@colosa.com>
     * @param String $Uid role uid
     * @return Array Role data.
     */
    public function load($Uid) {
        try {
            // getting the role by uid
            $oRow = PhRolePeer::retrieveByPK($Uid);
            // checking if the role exists
            if (! is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                $aFields['ROL_NAME'] = $this->getRolCode();
                return $aFields;
            } else {
            // if don't exists thrown an exception
                throw (new Exception("The '$Uid' row doesn't exists!"));
            }
        } catch( exception $oError ) {
            throw ($oError);
        }
    }

    /**
     * This method gets the role code based on a roleUid
     * @author gustavo cruz <gustavo@colosa.com>
     * @param String $roleUid role uid
     * @return String of the role code
     */
    public function getRolCode($roleUid="") {
        try {
            // setting the roleUid
            if ($roleUid!=""){
              $role = PhRolePeer::retrieveByPK($roleUid);
              return $role->rol_code;
            } else {
              return $this->rol_code;
            }
            
        } catch( exception $oError ) {
            throw (new Exception("Class ROLES::FATAL ERROR. Criteria Can't initialized"));
        }
    }

    /**
     * This method creates a new role based in an array of data
     * @author gustavo cruz <gustavo@colosa.com>
     * @param String $Uid role uid
     * @return Boolean true both if the rol exists or if has been created.
     */
    public function create($aData) {
      try {
        // search query
        $oCriteria = new Criteria();
        $oCriteria->add(PhRolePeer::ROL_UID, $aData['ROL_UID']);
        $oDataset = PhRolePeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        // check if record exists
        if (is_array($aRow)) {
          return true;
        } else {
        // if not create a new record
          $oRoleReport = new PhRole();
          $oRoleReport->fromArray($aData, BasePeer::TYPE_FIELDNAME);
          $iResult = $oRoleReport->save();
          return true;
        }
      } catch (Exception $oError) {
        throw($oError);
      }
    }

    /**
     * This method updates the data from a role.
     * @author gustavo cruz <gustavo@colosa.com>
     * @param Array $fields role data.
     * @return Mixed $result Response of the server.
     */
    public function updateRole($fields) {
       try {
           // loading the role
           $this->load($fields['ROL_UID']);
           $rol_name = $fields['ROL_CODE'];
           // setting the data
           $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
           // validating and saving the data in the database
           if ($this->validate()) {
               $result = $this->save();
               $this->setRolCode($rol_name);
               return $result;
           } else {
               throw (new Exception("Failed Validation in class " . get_class($this) . "."));
           }
       } catch( exception $e ) {
           throw ($e);
       }
    }
} // PhRole
