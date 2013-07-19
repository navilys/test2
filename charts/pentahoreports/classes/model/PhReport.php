<?php

/**
 * @section Filename
 * PhReport.php
 * @subsection Description
 * Skeleton subclass for representing a row from the 'PH_REPORT' table.
 * This class handles operations related to the PH_REPORT table, that are related with the roles table
 * @author gustavo cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.model
 */

require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/om/BasePhReport.php';
require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhRole.php';
require_once PATH_PLUGINS.'pentahoreports'.PATH_SEP.'classes/model/PhRoleReport.php';

class PhReport extends BasePhReport {
  
  /**
   * This method creates a new record in the PH_REPORT table based in the data sent as a parameter.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param Array $data The new report data
   * @return Boolean True if the record has been added
   */
  function create($aData) {
    try {
      // assembling the search criteria object
      $oCriteria = new Criteria();
      $oCriteria->add(PhReportPeer::REP_UID, $aData['REP_UID']);
      $oCriteria->add(PhReportPeer::REP_TITLE, $aData['REP_TITLE']);
      $oCriteria->add(PhReportPeer::REP_PATH, $aData['REP_PATH']);
      $oDataset = PhReportPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();

      // if there is a report with the same data return
      if (is_array($aRow)) {
        return true;
      } else {
      // else create a new record based in the array
        $oReport = new PhReport();
        $oReport->fromArray($aData, BasePeer::TYPE_FIELDNAME);
        $iResult = $oReport->save();
        return true;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /**
   * This method gets a list of the unassigned reports.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $ROL_UID The role uid
   * @return Object Returns a dataset object of the executed query
   */
  function getUnassignedReports($ROL_UID) {
    try {
      // assembling the reports criteria of the registered reports
      $c = new Criteria();
      $c->addSelectColumn(PhReportPeer::REP_UID);
      $c->add(PhRolePeer::ROL_UID, $ROL_UID);
      $c->addJoin(PhRolePeer::ROL_UID, PhRoleReportPeer::ROL_UID);
      $c->addJoin(PhRoleReportPeer::REP_UID, PhReportPeer::REP_UID);

      $result = PhReportPeer::doSelectRS($c);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $result->next();
      // assembling the array of registered uids
      $a = Array();
      while( $row = $result->getRow() ) {
        $a[] = $row['REP_UID'];
        $result->next();
      }
      // assembling the array of reports that dont exist in the Reports table
      $criteria = new Criteria();
      $criteria->addSelectColumn(PhReportPeer::REP_UID);
      $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $criteria->addSelectColumn(PhReportPeer::REP_NAME);
      $criteria->addSelectColumn(PhReportPeer::REP_PATH);
      $criteria->add(PhReportPeer::REP_UID, $a, Criteria::NOT_IN);
      $oDataset = PhReportPeer::doSelectRS($criteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

      // returning the dataset object
      return $oDataset;
    } catch( exception $e ) {
      throw $e;
    }
  }

  /**
   * This method creates a criteria object of all registered reports.
   * @author gustavo cruz <gustavo@colosa.com>
   * @return Object criteria object for all the registered reports.
   */
  public function listAllReports() {
    try {
      // assembling the criteria object
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(PhReportPeer::REP_UID);
      $oCriteria->addSelectColumn(PhReportPeer::REP_NAME);
      $oCriteria->addSelectColumn(PhReportPeer::REP_TITLE);
      $oCriteria->addSelectColumn(PhReportPeer::REP_PATH);
      $oCriteria->add(PhReportPeer::REP_UID, '', Criteria::NOT_EQUAL);
      // returning the criteria object
      return $oCriteria;
    } catch( exception $oError ) {
      throw (new Exception("Class ROLES::FATAL ERROR. Criteria with rbac Can't initialized"));
    }
  }

  /**
   * This method gets an array of all the registered reports.
   * @author gustavo cruz <gustavo@colosa.com>
   * @return Array array list of all the registered reports.
   */
  public function getAllReports() {
    // getting the list criteria
    $c  = $this->listAllReports();
    $rs = PhReportPeer::DoSelectRs($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    // assembling the reports array
    $aRows = Array();
    while($rs->next()){
      $row = $rs->getRow();
      $aRows[] = $row;
    }
    return $aRows;
  }

  /**
   * This method loads the data of a registered report with an especific uid.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $Uid
   * @return Array Returns the report data in an array .
   */
  public function load($Uid) {
    try {
      // gets the report dataset
      $oRow = PhReportPeer::retrieveByPK($Uid);
      if (! is_null($oRow)) {
        // assembling the results array
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        $aFields['REP_TITLE'] = $this->getRepTitle();
        return $aFields;
      } else {
        throw (new Exception("The '$Uid' row doesn't exists!"));
      }
    } catch( exception $oError ) {
      throw ($oError);
    }
  }

  /**
   * This method gets the report uid based on a report name.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $repName
   * @return String Returns the report uid.
   */
  public function getReportByName($repName){
    // assembling the object criteria
    $criteria = new Criteria();
    $criteria->addSelectColumn(PhReportPeer::REP_UID);
    $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
    $criteria->addSelectColumn(PhReportPeer::REP_PATH);
    $criteria->add(PhReportPeer::REP_TITLE, $repName, Criteria::EQUAL);
    // executing the query
    $oDataset = PhReportPeer::doSelectRS($criteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    // getting the uid
    $oDataset->next();
    $row = $oDataset->getRow();
    $repUid = $row['REP_UID'];
    return($repUid);
  }

  /**
   * This method gets the reports children based on the report name.
   * @author gustavo cruz <gustavo@colosa.com>
   * @param String $repName
   * @return Array Returns the reports inside the report folder.
   */
  public function getReportChildren($repName){
    $aResult = array();
    // assembling the criteria object
    $repLike = "%".$repName;
    $criteria = new Criteria();
    $criteria->addSelectColumn(PhReportPeer::REP_UID);
    $criteria->addSelectColumn(PhReportPeer::REP_TITLE);
    $criteria->addSelectColumn(PhReportPeer::REP_PATH);
    $criteria->add(PhReportPeer::REP_PATH, $repLike, Criteria::LIKE);
    // executing the query
    $oDataset = PhReportPeer::doSelectRS($criteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    // assembling the result array
    while ($aRow = $oDataset->getRow()){
      $aResult[] = $aRow;
      $oDataset->next();
    }
    return($aResult);
  }

} // PhReport
