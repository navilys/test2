<?php
/**
 * @section Filename
 * PhRoleReport.php
 * @subsection Description
 * Skeleton subclass for representing a row from the 'PH_ROLE_REPORT' table.
 * 
 * @author gustavo cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.model
 */

require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/om/BasePhRoleReport.php';
require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhRole.php';
require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhReport.php';
require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhUserRole.php';
require_once PATH_CORE.'classes/class.groups.php';
require_once PATH_CORE.'classes/model/Department.php';


class PhRoleReport extends BasePhRoleReport {
   /**
    * create a new assignment of a report in a role
    * @author gustavo cruz <gustavo@colosa.com>
    * @param Array $aData the assignment data
    * @return Boolean true or false if has been created or not
    */
   function create($aData) {
    try {
      // assembling the search of the assignment
      $oCriteria = new Criteria();
      $oCriteria->add(PhRoleReportPeer::ROL_UID, $aData['ROL_UID']);
      $oCriteria->add(PhRoleReportPeer::REP_UID, $aData['REP_UID']);
      $oDataset = PhRoleReportPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      // if found return false
      if (is_array($aRow)) {
        return false;
      } else {
      // else create a new record for the assignment
        $aData['ROL_REP_UID'] = G::generateUniqueID();
        $oRoleReport = new PhRoleReport();
        $oRoleReport->fromArray($aData, BasePeer::TYPE_FIELDNAME);
        $iResult = $oRoleReport->save();
        return true;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /**
   * This method gets the role/report information from a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @return Object $oDataset from the executed criteria object
   */
  function getAllRoleReports(){
    try {
      // assemblign the criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->addSelectColumn(PhReportPeer::REP_UID);
      $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $criteria->addSelectColumn(PhReportPeer::REP_PATH);
      $criteria->addSelectColumn(PhReportPeer::REP_NAME);
      $criteria->add(PhRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      $criteria->addJoin(PhRolePeer::ROL_UID, PhRoleReportPeer::ROL_UID);
      $criteria->addJoin(PhRoleReportPeer::REP_UID, PhReportPeer::REP_UID);
      // executing the query with the criteria object
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning a dataset object.
      return $oDataset;

    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * get all the role/report information for all roles
   * @deprecated
   */
  function getAllRoleReportsArray(){
    $oDatasetReports = $this->getAllRoleReports();
    $elements = array();
    $reportTree = array();
    foreach ($oDatasetReports as $oReport){
      $directory = explode("/", $oReport['REP_PATH']);
      $variableVar = "elements";
      for ($j=1;$j<count($directory);$j++){
        $variableVar = $variableVar."['{$directory[$j]}']";
      }
      $variableVar = $variableVar;
      echo $variableVar."---";
      $$variableVar = array ("REP_NAME" => $oReport['REP_NAME'], "REP_UID" => $oReport['REP_UID']);
      $reportTree = array_merge($elements, $reportTree);
      print_r ($reportTree);
      echo "<br>";
    }
    return $reportTree;
  }

  /** 
   * get the role/report information from a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $roleUid
   * @return Object $oDataset from the executed criteria object
   */
  function getRoleReports($roleUid){
    try {
      // assemblign the criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->addSelectColumn(PhReportPeer::REP_UID);
      $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $criteria->addSelectColumn(PhReportPeer::REP_PATH);
      $criteria->addSelectColumn(PhReportPeer::REP_NAME);
      $criteria->add(PhRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      $criteria->add(PhRolePeer::ROL_UID, $roleUid);
      $criteria->addJoin(PhRolePeer::ROL_UID, PhRoleReportPeer::ROL_UID);
      $criteria->addJoin(PhRoleReportPeer::REP_UID, PhReportPeer::REP_UID);
      // executing the query based on the criteria object
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning a dataset object
      return $oDataset;

    } catch( exception $e ) {
      throw $e;
    }
  }

  /** 
   * get the role/report information from a role using a report title
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $repTitle the title of the report
   * @return Object $oDataset from the executed criteria object
   */
  function loadByTitle ($repTitle){
    try {
      // Assembling the criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->addSelectColumn(PhReportPeer::REP_UID);
      $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $criteria->addSelectColumn(PhReportPeer::REP_PATH);
      $criteria->addSelectColumn(PhReportPeer::REP_NAME);
      $criteria->add(PhRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      $criteria->add(PhReportPeer::REP_TITLE, $repTitle);
      $criteria->addJoin(PhRolePeer::ROL_UID, PhRoleReportPeer::ROL_UID);
      $criteria->addJoin(PhRoleReportPeer::REP_UID, PhReportPeer::REP_UID);
      // Executing the query based on the criteria object
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // Returning the dataset object
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }

  }
  /**
   * Checks if a Report has been assigned to a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param roleUid role uid
   * @param repUid report uid
   * @return Boolean true or false if the report is or not assigned respectively
   */
  function isReportInRole($roleUid,$repUid){
    try {
      // Assembling the search criteria
      $criteria = new Criteria();
      $criteria->add(PhRoleReportPeer::REP_UID, $repUid);
      $criteria->add(PhRoleReportPeer::ROL_UID, $roleUid);
      // counting the number of results
      $oDataset = PhRolePeer::doCount($criteria);
      // if exists the count will be greater/equal than 1
      if ($oDataset>=1){
        return true;
      } else {
        return false;
      }
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * get all the dashboards assigned to a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param roleUid
   * @return $oDataset dataset object
   */
  function getRoleDashboards($roleUid){
    try {
      // Assembling the Criteria object
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_UID);
      $criteria->addSelectColumn(PhRolePeer::ROL_CODE);
      $criteria->addSelectColumn(PhReportPeer::REP_UID);
      $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $criteria->addSelectColumn(PhReportPeer::REP_NAME);
      $criteria->add(PhRolePeer::ROL_UID, "", Criteria::NOT_EQUAL);
      // getting only the xcdf dashboard files,
      $criteria->add(PhReportPeer::REP_TITLE, '%.xcdf', Criteria::LIKE);
      $criteria->add(PhRolePeer::ROL_UID, $roleUid);
      $criteria->addJoin(PhRolePeer::ROL_UID, PhRoleReportPeer::ROL_UID);
      $criteria->addJoin(PhRoleReportPeer::REP_UID, PhReportPeer::REP_UID);
      // executing the criteria object
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      // returning the dataset object
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }
  }
  /** 
   * Delete an assignment of a report into a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param Array $aData the assignment data
   * @return Mixed $iResult the query response
   */
  function deleteReportRole($aData){
    $oRoleReport = new PhRoleReport();
    // loading from array
    $oRoleReport->fromArray($aData, BasePeer::TYPE_FIELDNAME);
    // deleting the assignment
    $iResult = $oRoleReport->delete();
    return $iResult;
  }

  /**
   * This method checks if the user has a report assigned
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $objUid the object Uid that can be a group, department or user
   * @param String $repUid the report Uid
   * 
   */
  function userIsAssignedReport($objUid, $repUid){
    // initializing the variables
    $userRole = new PhUserRole();
    $allRoles = new PhRole();
    $roleReport = new PhRoleReport();

    $assigned = false;
    // iterating over the user roles
    foreach ($userRole->getAllUserRoles() as $role){
      $roleUid = $role['ROL_UID'];
      // iterating over the assigned roles
      foreach ($roleReport->getRoleReports($roleUid) as $report){
        // if the report is registered
        if ($report['REP_TITLE']==$repUid){
          // if found according the object type verify if the report has been assigned
          switch ($role['OBJ_TYPE']){
            // case the objType is a user
            case 'USER':
              if ($this->objectIsAssignedReport($objUid, $repUid)){
                $assigned = true;
                return $assigned;
              }
            break;
            // case the objType is a group
            case 'GROUP':
              $groups = new Groups();
              $oGroups = $groups->getUserGroups($objUid);

              foreach( $oGroups as $group ){
                if ($this->objectIsAssignedReport($group->getGrpUid(), $repUid)){
                  $assigned = true;
                  return $assigned;
                }
              }
            break;
            // case the objType is a department
            case 'DEPARTMENT':
              return ($this->findUserInDepartments('',$repUid,$objUid));
            break;
          }
        }
      }
    }
    return $assigned;
  }

  /**
   * Verify if an object is assigned to a report
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $objUid the object id that have access to the report (user)
   * @param String $repUid reportUid means also the report filename
   * @return Boolean true/false if the object has been assigned or not.
   */
  function objectIsAssignedReport($objUid, $repUid) {
    $userRole   = new PhUserRole();
    $roleReport = new PhRoleReport();
    $assigned   = false;
    // iterating over the roles 
    foreach ($userRole->getRolesByObject($objUid) as $role){
      $roleUid = $role['ROL_UID'];
      // iterating over the roles assigned 
      foreach ($roleReport->getRoleReports($roleUid) as $report){
        if ($report['REP_TITLE']==$repUid){
          // if assigned return true
          $assigned = true;
          return $assigned;
        }
      }

    }
    return $assigned;
  }

  /**
   * Find if a department is assigned to a report and then verifies
   * if a user is assigned to that department.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $parentDepartment the parent department
   * @param String $repUid reportUid means also the report filename
   * @param String $objUid the object id that have access to the report (user)
   * @return Boolean true/false if a user has been found or not.
   */
  function findUserInDepartments($parentDepartment, $repUid, $objUid){
    $assigned = false;
    $oDepartment = new Department();
    $departments = $oDepartment->getDepartments($parentDepartment);
    // search each department
    foreach ($departments as $department){
      if ($this->objectIsAssignedReport($department['DEP_UID'], $repUid)){
        if($oDepartment->existsUserInDepartment($department['DEP_UID'],$objUid)){
          $assigned = true;
          return $assigned;
        }
      }
      // recursively search on inner departments
      if ($department['HAS_CHILDREN']!=0){
        $assigned = $this->findUserInDepartments($department['DEP_UID'],$repUid,$objUid);
        if($assigned == true){
          return $assigned;
        }
      }
    }
    return $assigned;
  }

  /**
   * Get the primary key from a record passing the role and report uid's
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $rolUid role uid
   * @param String $repUid role uid
   */
  function getRolRepUidFromData ($rolUid, $repUid){
    try {
      // Assembling the new object criteria
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->add(PhRoleReportPeer::REP_UID, $repUid);
      $criteria->add(PhRoleReportPeer::ROL_UID, $rolUid);
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      // return the report uid if found, if not, return false
      if ($aRow = $oDataset->getRow()){
        return $aRow['ROL_REP_UID'];
      } else {
        return false;
      }

    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * Delete recursively an assignment from a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param $rolRepUid assignment uid
   * @param $rolUid    role uid
   * @return void
   */
  function recursiveDelete ($rolRepUid, $rolUid){
    try {
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhRoleReportPeer::REP_UID);
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_UID);
      $criteria->addSelectColumn(PhRoleReportPeer::ROL_REP_UID);
      $criteria->add(PhRoleReportPeer::ROL_REP_UID, $rolRepUid);
      $oDataset = PhRolePeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      $oReport  = PhReportPeer::retrieveByPK($aRow['REP_UID']);
      $repName  = $oReport->getRepName();
      $repChildren = $oReport->getReportChildren($repName);
      foreach ($repChildren as $repChild){
        $repUid = $repChild['REP_UID'];
        $currentRolRepUid = $this->getRolRepUidFromData($rolUid, $repUid);

        if ($currentRolRepUid!=''){
          $oTempRoleReport = PhRoleReportPeer::retrieveByPK($currentRolRepUid);
          $oTempRoleReport->delete();
        }
      }
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * Recursively assign a report or folder to a role
   * @author gustavo cruz <gustavo@colosa.com>
   * @param $repUid report uid
   * @param $rolUid role uid
   * @return void
   */
  function recursiveAssign ($repUid, $rolUid){
    try {
      $oReport  = PhReportPeer::retrieveByPK($repUid);
      $repName  = $oReport->getRepName();
      $repChildren = $oReport->getReportChildren($repName);
      foreach ($repChildren as $repChild){
        $currentRepUid = $repChild['REP_UID'];
        $currentRolRepUid = $this->getRolRepUidFromData($rolUid, $currentRepUid);
        if ($currentRolRepUid==false){
          $aData['REP_UID'] = $currentRepUid;
          $aData['ROL_UID'] = $rolUid;
          $oTempRoleReport = new PhRoleReport();
        }
        $oCurrentReport  = PhReportPeer::retrieveByPK($currentRepUid);
        $currentRepName  = $oCurrentReport->getRepName();
        $repCurrentChildren  = $oCurrentReport->getReportChildren($repName);
          if (count($repCurrentChildren>0)){
            $this->recursiveAssign($currentRepUid,$rolUid);
          }
      }
    } catch( exception $e ) {
      throw $e;
    }
  }
} // PhRoleReport
