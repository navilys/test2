<?php

set_include_path(PATH_PLUGINS . 'pmReports' . PATH_SEPARATOR . get_include_path());

require_once 'classes/model/AdditionalTables.php';
require_once 'classes/model/PmReportPermissions.php';



// Initialize response object
$response = new stdclass();
$response->status = 'OK';

// General Validations
$action         = isset($_REQUEST['action']) ? $_REQUEST['action']: '';
$limit_size     = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : 20;
$start          = isset($_REQUEST['start']) ? $_REQUEST['start']: 0;
$limit          = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit_size;
$filter         = isset($_REQUEST['textFilter']) ? $_REQUEST['textFilter'] : '';
$tabName        = isset($_REQUEST['ADD_TAB_NAME']) ? $_REQUEST['ADD_TAB_NAME'] : '';
$pro_uid        = isset($_REQUEST['pro_uid']) ? $_REQUEST['pro_uid'] : null;
$addTabUid      = isset($_REQUEST['ADD_TAB_UID']) ? $_REQUEST['ADD_TAB_UID']: 0;

// Main switch
try {
  switch ($_REQUEST['action']) {
    case 'permissionList';
        $headPublisher = &headPublisher::getSingleton();

        $headPublisher->assign("ADD_TAB_NAME",  $tabName);
        $headPublisher->assign("ADD_TAB_UID",   $addTabUid );
        $headPublisher->assign("PRO_UID",       $pro_uid );

        $headPublisher->addExtJsScript("pmReports/rptPermissionList", false ); //adding a javascript file .js
        $headPublisher->addContent("pmReports/rptPermissionList"); //adding a html file  .html.
        $headPublisher->addExtJsScript("pmReports/reportPermissionForm", false ); //adding a javascript file .js
        $headPublisher->addContent("pmReports/reportPermissionForm"); //adding a html file  .html.

        G::RenderPage("publish", "extJs");
        die();
        break;
    case 'listSimpleReports':
        G::LoadClass('pmTable');

        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TYPE);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TAG);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::PRO_UID);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);

        $oCriteria->add(AdditionalTablesPeer::ADD_TAB_TAG, 'plugin@simplereport', Criteria::EQUAL);
        if (isset($process)) {
            foreach ($process as $key => $pro_uid) {
                if ($key == 'equal') {
                    $oCriteria->add(AdditionalTablesPeer::PRO_UID, $pro_uid, Criteria::EQUAL);
                } else {
                    $oCriteria->add(AdditionalTablesPeer::PRO_UID, $pro_uid, Criteria::NOT_EQUAL);
                }
            }
        }

        if ($filter != '' && is_string($filter)) {
            $oCriteria->add(
            $oCriteria->getNewCriterion(AdditionalTablesPeer::ADD_TAB_NAME, '%' . $filter . '%', Criteria::LIKE)->addOr(
            $oCriteria->getNewCriterion(AdditionalTablesPeer::ADD_TAB_DESCRIPTION, '%' . $filter . '%', Criteria::LIKE))
            );
        }

        $criteriaCount = clone $oCriteria;
        $count = AdditionalTablesPeer::doCount($criteriaCount);

        $oCriteria->setLimit($limit);
        $oCriteria->setOffset($start);

        $oDataset = AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $addTables = Array();
        $proUids = Array();

        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $row['PRO_TITLE'] = $row['PRO_DESCRIPTION'] = '';
            $addTables[] = $row;
            if ($row['PRO_UID'] != '') {
                $proUids[] = $row['PRO_UID'];
            }
        }

        //process details will have the info about the processes
        $procDetails = Array();
  
        if (count($proUids) > 0) {
            //now get the labels for all process, using an array of Uids,
            $c = new Criteria('workflow');
            //$c->add ( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
            $c->add(ContentPeer::CON_LANG, defined('SYS_LANG') ? SYS_LANG : 'en', Criteria::EQUAL);
            $c->add(ContentPeer::CON_ID, $proUids, Criteria::IN);
  
            $dt = ContentPeer::doSelectRS($c);
            $dt->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  
            while ($dt->next()) {
                $row = $dt->getRow();
                $procDetails[$row['CON_ID']][$row['CON_CATEGORY']] = $row['CON_VALUE'];
            }
  
            foreach ($addTables as $i => $addTable) {
                if (isset($procDetails[$addTable['PRO_UID']]['PRO_TITLE'])) {
                    $addTables[$i]['PRO_TITLE'] = $procDetails[$addTable['PRO_UID']]['PRO_TITLE'];
                }
  
                if (isset($procDetails[$addTable['PRO_UID']]['PRO_DESCRIPTION'])) {
                    $addTables[$i]['PRO_DESCRIPTION'] = $procDetails[$addTable['PRO_UID']]['PRO_DESCRIPTION'];
                }
            }
        }
        
        foreach ($addTables as $i => $table) {
            try {
                $con = Propel::getConnection(pmTable::resolveDbSource($table['DBS_UID']));
                $stmt = $con->createStatement();
                $rs = $stmt->executeQuery('SELECT COUNT(*) AS NUM_ROWS from ' . $table['ADD_TAB_NAME']);
                if ($rs->next()) {
                    $r = $rs->getRow();
                    $addTables[$i]['NUM_ROWS'] = $r['NUM_ROWS'];
                } else {
                    $addTables[$i]['NUM_ROWS'] = 0;
                }
                //removing the prefix "PMT" to allow alphabetical order (just in view)
                if (substr($addTables[$i]['ADD_TAB_NAME'], 0, 4) == 'PMT_') {
                    $addTables[$i]['ADD_TAB_NAME'] = substr($addTables[$i]['ADD_TAB_NAME'], 4);
                }
            } catch (Exception $e) {
                $addTables[$i]['NUM_ROWS'] = G::LoadTranslation('ID_TABLE_NOT_FOUND');
            }
        }
          
        $response = array();
        $response['totalCount'] = $count;
        $response['data']       = $addTables;
        break;
    case 'getListPermissions':
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_UID);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::ADD_TAB_UID);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_TYPE);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_OWNER_UID);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_CREATE_DATE);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_UPDATE_DATE);
        $oCriteria->addSelectColumn(PmReportPermissionsPeer::PMR_STATUS);

        $oCriteria->add(PmReportPermissionsPeer::ADD_TAB_UID, $addTabUid);

        $criteriaCount = clone $oCriteria;
        $count = PmReportPermissionsPeer::doCount($criteriaCount);

        $oCriteria->setLimit($limit);
        $oCriteria->setOffset($start);

        $oDataset = PmReportPermissionsPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $data = Array();
        
        require_once 'classes/model/Users.php';
        require_once 'classes/model/Department.php';
        require_once 'classes/model/Groupwf.php';
        $userInstance = new Users();
        $departmentInstance = new Department();
        $groupInstance = new Groupwf();

        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            switch ($row['PMR_TYPE']) {
                //List type
                case 'EVERYBODY':
                    $row['PMR_TYPE_TITLE'] = G::LoadTranslation('ID_ALL_USERS');
                    break;
                case 'USER':
                    try {
                      $user = $userInstance->load($row['PMR_OWNER_UID']);
                      $row['PMR_TYPE_TITLE'] = $user['USR_FIRSTNAME'] . ' ' . $user['USR_LASTNAME'];
                    }
                    catch (Exception $error) {
                      $this->remove($row['PMR_UID']);
                      $row['PMR_UID'] = '';
                    }
                    break;
                case 'DEPARTMENT':
                    try {
                      $department = $departmentInstance->load($row['PMR_OWNER_UID']);
                      $row['PMR_TYPE_TITLE'] = $department['DEPO_TITLE'];
                    }
                    catch (Exception $error) {
                      $this->remove($row['PMR_UID']);
                      $row['PMR_UID'] = '';
                    }
                    break;
                case 'GROUP':
                    try {
                      $group = $groupInstance->load($row['PMR_OWNER_UID']);
                      $row['PMR_TYPE_TITLE'] = $group['GRP_TITLE'];
                    }
                    catch (Exception $error) {
                      $this->remove($row['PMR_UID']);
                      $row['PMR_UID'] = '';
                    }
                    break;
                default:
                    $row['PMR_TYPE_TITLE'] = $row['PMR_TYPE'];
                    break;
            }
            $row['ASSIGNED'] = $row['PMR_TYPE_TITLE']." (".$row['PMR_TYPE'].") ";
            $data[] = $row;
        }
        $response = array();
        $response['totalCount'] = $count;
        $response['data']       = $data;
        break;
    case 'saveReportPermissions':
        $pmReportPermissionsInstance = new PmReportPermissions();
        try {
            $response->PMR_UID = $pmReportPermissionsInstance->createOrUpdate($_REQUEST);
            $response->success = true;
        }
        catch (Exception $error) {
            throw $error;
        }
        break;
    case 'changeStatus':
        if (isset($_REQUEST)) {
            $_REQUEST['PMR_STATUS'] = ($_REQUEST['PMR_STATUS'])? 0:1;
            $pmReportPermissionsInstance = new PmReportPermissions();
            try {
                $response->PMR_UID = $pmReportPermissionsInstance->createOrUpdate($_REQUEST);
                $response->success = true;
            }
            catch (Exception $error) {
                throw $error;
            }
        }
        break;
    case 'deletePermission':
        if (isset($_REQUEST)) {
            $pmReportPermissionsInstance = new PmReportPermissions();
            try {
                $response->PMR_UID = $pmReportPermissionsInstance->remove($_REQUEST);
                $response->success = true;
            }
            catch (Exception $error) {
                throw $error;
            }
        }
        break;
    case 'verifyDate':
        if (isset($_REQUEST)) {
            $response->status = 'OK';
            if (!isset($_REQUEST['PMR_OWNER_UID'])) {
                $_REQUEST['PMR_OWNER_UID'] = null;
            }
            $oCriteria = new Criteria('workflow');

            $oCriteria->add(PmReportPermissionsPeer::ADD_TAB_UID, $_REQUEST['ADD_TAB_UID']);
            $oCriteria->add(PmReportPermissionsPeer::PMR_TYPE, $_REQUEST['PMR_TYPE']);
            $oCriteria->add(PmReportPermissionsPeer::PMR_OWNER_UID, $_REQUEST['PMR_OWNER_UID']);

            $criteriaCount = clone $oCriteria;
            
            $count = PmReportPermissionsPeer::doCount($criteriaCount);
            if ($count > 0) {
                $response->status = 'NO';
            }
        }
        break;
  }
}
catch (Exception $error) {
  $response = new stdclass();
  $response->status = 'ERROR';
  $response->message = $error->getMessage();
}

header('Content-Type: application/json;');
die(G::json_encode($response));