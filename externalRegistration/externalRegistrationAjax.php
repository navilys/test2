<?php
set_include_path(PATH_PLUGINS . 'externalRegistration' . PATH_SEPARATOR . get_include_path());
require_once 'classes/model/ErConfiguration.php';
require_once 'classes/model/ErRequests.php';
require_once 'classes/model/Content.php';

// General Validations
if (!isset($_REQUEST['action'])) {
  $_REQUEST['action'] = '';
}
if (!isset($_REQUEST['limit'])) {
  $_REQUEST['limit'] = '';
}
if (!isset($_REQUEST['start'])) {
  $_REQUEST['start'] = '';
}
function addTitlle($Category, $Id, $Lang) {
  $content = new Content();
  $value = $content->load($Category,'', $Id, $Lang);
  return $value;
}
// Initialize response object
$response = new stdclass();
$response->status = 'OK';

// Main switch
try {
  switch ($_REQUEST['action']) {
    case 'listExternalRegistration':
      // Para el listado
      $criteria = new Criteria();
      $criteria->addSelectColumn('COUNT(*)');
      $criteria->addSelectColumn(ErConfigurationPeer::ER_UID);
      $result = ErConfigurationPeer::doSelectRS($criteria);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $result->next();
      $totalCount = $result->getRow();
      $totalCount = $totalCount['COUNT(*)'];

      $criteria = new Criteria();
      $criteria->addSelectColumn(ErConfigurationPeer::ER_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_TITLE);
      $criteria->addSelectColumn(ErConfigurationPeer::PRO_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_TEMPLATE);
      $criteria->addSelectColumn(ErConfigurationPeer::DYN_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_ASSIGN);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_OBJECT_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_START_CASE);
      $criteria->addSelectColumn(ErConfigurationPeer::TAS_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_ACTION_EXECUTE_TRIGGER);
      $criteria->addSelectColumn(ErConfigurationPeer::TRI_UID);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_CREATE_DATE);
      $criteria->addSelectColumn(ErConfigurationPeer::ER_UPDATE_DATE);

      $criteria->addDescendingOrderByColumn(ErConfigurationPeer::ER_CREATE_DATE);

      $criteria->setLimit( $_REQUEST['limit'] );
      $criteria->setOffset( $_REQUEST['start'] );
      $result = ErConfigurationPeer::doSelectRS($criteria);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $data = Array();
      $arrayDyn = Array();
      require_once 'classes/model/Content.php';
      $content = new Content();
      $index = 0;
      $link = (G::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/externalRegistration/services/';
      while( $result->next() ) {
        $data[] = $result->getRow();
        $criteriaReq = new Criteria();
        $criteriaReq->addSelectColumn( 'COUNT(*)');

        $criteriaReq->add(ErRequestsPeer::ER_UID,$data[$index]['ER_UID']);

        $resultReq = ErRequestsPeer::doSelectRS($criteriaReq);
        $resultReq->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $resultReq->next();
        $totalCountReq = $resultReq->getRow();
        $data[$index]['REQ_RECEIVED'] = $totalCountReq['COUNT(*)'];

        $criteriaReq = new Criteria();
        $criteriaReq->addSelectColumn( 'COUNT(*)');

        $criteriaReq->add(ErRequestsPeer::ER_UID,$data[$index]['ER_UID']);
        $criteriaReq->add(ErRequestsPeer::ER_REQ_COMPLETED,'1');

        $resultReq = ErRequestsPeer::doSelectRS($criteriaReq);
        $resultReq->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $resultReq->next();
        $totalCountReq = $resultReq->getRow();
        $data[$index]['REQ_COMPLETED'] = $totalCountReq['COUNT(*)'];

        $swDYN = false;
        foreach($arrayDyn as $k => $field){
          if($arrayDyn[$k]['DYN_UID'] == $data[$index]['DYN_UID'] && $arrayDyn[$k]['LANG'] == SYS_LANG){
            $data[$index]['DYN_TITLE'] = $arrayDyn[$k]['DYN_TITLE'];
            $swDYN = true;
          }
        }
        if(!$swDYN){
          $data[$index]['DYN_TITLE'] = addTitlle ('DYN_TITLE',$data[$index]['DYN_UID'], SYS_LANG);
          $data[$index]['DYN_TITLE'] = ($data[$index]['DYN_TITLE'] == '')? '- None -': $data[$index]['DYN_TITLE'];
          $arrayDyn[] = Array('DYN_UID'=>$data[$index]['DYN_UID'] , 'DYN_TITLE'=>$data[$index]['DYN_TITLE'],'LANG' => SYS_LANG);
        }
        $data[$index]['VIEW_FORM'] = '<a href="' . $link . 'registrationForm?ER_UID=' . G::encrypt($data[$index]['ER_UID'], URL_KEY) . '" target="_blank">View Form</a>';
        $index++;
      }
      $response = array();
      $response['totalCount'] = $totalCount;
      $response['data']       = $data;
    break;
    case 'editExternalRegistration':
      // Para crear y editar
    break;
    case 'deleteExternalRegistration':
      // Para borrar
      if (!isset($_REQUEST['ER_UID'])) {
        $_REQUEST['ER_UID'] = '';
      }
      $criteria = new Criteria();
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_UID);
      $criteria->add(ErRequestsPeer::ER_UID,$_REQUEST['ER_UID']);

      $criteria->addDescendingOrderByColumn(ErRequestsPeer::ER_REQ_DATE);
      $result = ErRequestsPeer::doSelectRS($criteria);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $data = Array();
      $index = 0;
      $erRequestsInstance = new ErRequests();
      while( $result->next() ) {
        $data[] = $result->getRow();
        try {
          $erRequestsInstance->remove($data[$index]['ER_REQ_UID']);
        }
        catch (Exception $error) {
          throw $error;
        }
        $index++;
      }
      $erConfigurationInstance = new ErConfiguration();
      try {
        $erConfigurationInstance->remove($_REQUEST['ER_UID']);
        $response->success = true;
      }
      catch (Exception $error) {
        throw $error;
      }
    break;
    case 'loadResources':
      // Action Validations
      if (!isset($_REQUEST['PRO_UID'])) {
        $_REQUEST['PRO_UID'] = '';
      }
      if($_REQUEST['PRO_UID'] == ''){
        require_once 'classes/model/Process.php';
         $criteria = new Criteria();
        $criteria->addSelectColumn(ProcessPeer::PRO_UID);
        $criteria->add(ProcessPeer::PRO_STATUS,'ACTIVE');
        $criteria->addDescendingOrderByColumn(ProcessPeer::PRO_CREATE_DATE);
        $result = ProcessPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $index = 0;
        while( $result->next() ) {
          $data[] = $result->getRow();
          $data[$index]['PRO_TITLE'] = addTitlle ('PRO_TITLE',$data[$index]['PRO_UID'], SYS_LANG);
          $index++;
        }
        $response->data = array();
        $response->data = $data;
      }
      else{
        require_once 'classes/model/Dynaform.php';
        $criteria = new Criteria();
        $criteria->addSelectColumn(DynaformPeer::PRO_UID);
        $criteria->addSelectColumn(DynaformPeer::DYN_UID);
        $criteria->add(DynaformPeer::PRO_UID, $_REQUEST['PRO_UID']);
        $criteria->add(DynaformPeer::DYN_TYPE, 'xmlform');
        $criteria->addDescendingOrderByColumn(DynaformPeer::PRO_UID);
        $result = DynaformPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $dynaforms = array();
        $index = 0;
        while( $result->next() ) {
          $data[] = $result->getRow();
          $data[$index]['DYN_TITLE'] = addTitlle ('DYN_TITLE',$data[$index]['DYN_UID'], SYS_LANG);
          $dynaforms[] = array('DYN_UID' => $data[$index]['DYN_UID'], 'DYN_TITLE' => $data[$index]['DYN_TITLE']);
          $index++;
        }
        $response->dynaforms = array();
        (count($dynaforms)>0)?array_unshift($dynaforms,array('DYN_UID'=>' ','DYN_TITLE'=> '-- None --')) : '';
        $response->dynaforms = $dynaforms;

        require_once 'classes/model/Triggers.php';
        $criteria = new Criteria();
        $criteria->addSelectColumn(TriggersPeer::PRO_UID);
        $criteria->addSelectColumn(TriggersPeer::TRI_UID);
        $criteria->add(TriggersPeer::PRO_UID, $_REQUEST['PRO_UID']);
        $criteria->addDescendingOrderByColumn(TriggersPeer::PRO_UID);
        $result = TriggersPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $triggers = array();
        $index = 0;
        while ($result->next()) {
          $data[] = $result->getRow();
          $data[$index]['TRI_TITLE'] = addTitlle('TRI_TITLE', $data[$index]['TRI_UID'], SYS_LANG);
          $triggers[] = array('TRI_UID' => $data[$index]['TRI_UID'], 'TRI_TITLE' => $data[$index]['TRI_TITLE']);
          $index++;
        }
        $response->triggers = array();
        if (count($triggers) > 0) {
          array_unshift($triggers,array('TRI_UID' => ' ', 'TRI_TITLE' => '-- None --'));
        }
        $response->triggers = $triggers;

        $templates   = array();
        $templates[] = 'dummy';
        $path        = PATH_DATA_MAILTEMPLATES . $_REQUEST['PRO_UID'] . PATH_SEP;
        G::verifyPath($path, true);
        if (!file_exists($path . 'externalRegistration.html')) {
          @copy(PATH_PLUGINS . 'externalRegistration' . PATH_SEP . 'data' . PATH_SEP . 'externalRegistration.html', $path . 'externalRegistration.html');
        }
        $directory = dir($path);
        while ($object = $directory->read()) {
          if (($object !== '.') && ($object !== '..') && ($object !== 'alert_message.html')) {
            $templates[] = array('FILE' => $object, 'NAME' => $object);
          }
        }
        $response->templates = array();
        array_shift($templates);
        $response->templates = $templates;

        $AssignUser = Array();
        $AssignUser[]= array('LABEL' => 'Task','VALUE' => 'TASK');
        $AssignUser[]= array('LABEL' => 'Group','VALUE' => 'GROUP');
        $AssignUser[]= array('LABEL' => 'Department','VALUE' => 'DEPARTMENT');

        $response->AssignUser = array();
        (count($AssignUser)>0)?array_unshift($AssignUser,array('VALUE'=>' ','LABEL'=> '-- None --')) : '';
        $response->AssignUser = $AssignUser;

        require_once 'classes/model/Task.php';
        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_UID);
        $criteria->add(TaskPeer::PRO_UID,$_REQUEST['PRO_UID']);
        $criteria->add(TaskPeer::TAS_START ,'TRUE');
        $result = TaskPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $index = 0;
        while( $result->next() ) {
          $data[] = $result->getRow();
          $data[$index]['TAS_TITLE'] = addTitlle ('TAS_TITLE',$data[$index]['TAS_UID'], SYS_LANG);
          $index++;
        }
        $response->TasStart = array();
        (count($data)>0)? array_unshift($data,array('TAS_UID'=>' ','TAS_TITLE'=> '-- None --')) : '';
        $response->TasStart = $data;

        $response->success = true;
      }
    break;
    case 'loadElements':
      // Para cargar las tareas, grupos y/o departamentos
      $data = Array();
      $index = 0;
      if (!isset($_REQUEST['OBJECT_VALUE'])) {
        $_REQUEST['OBJECT_VALUE'] = '';
      }
      $dataResult = array();
      switch ($_REQUEST['OBJECT_VALUE']) {
        case 'TASK':
        require_once 'classes/model/Task.php';
        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_UID);
        $criteria->add(TaskPeer::PRO_UID,$_REQUEST['PRO_UID']);
        $result = TaskPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while( $result->next() ) {
          $data[] = $result->getRow();
          $dataResult[] = array('VALUE' => $data[$index]['TAS_UID'],'LABEL' =>  addTitlle ('TAS_TITLE',$data[$index]['TAS_UID'], SYS_LANG));
          $index++;
        }
        break;
        case 'GROUP':
          require_once 'classes/model/Groupwf.php';
          $criteria = new Criteria();
          $criteria->addSelectColumn(GroupwfPeer::GRP_UID);
          $criteria->add(GroupwfPeer::GRP_STATUS,'ACTIVE');
          $result = GroupwfPeer::doSelectRS($criteria);
          $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          while( $result->next() ) {
            $data[] = $result->getRow();
            $dataResult[] = array('LABEL' =>  addTitlle ('GRP_TITLE',$data[$index]['GRP_UID'], SYS_LANG), 'VALUE' => $data[$index]['GRP_UID']);
            $index++;
          }
          asort($dataResult);
        break;
        case 'DEPARTMENT':
          require_once 'classes/model/Department.php';
          $criteria = new Criteria();
          $criteria->addSelectColumn(DepartmentPeer::DEP_UID );
          $criteria->add(DepartmentPeer::DEP_STATUS,'ACTIVE');
          $result = DepartmentPeer::doSelectRS($criteria);
          $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          while( $result->next() ) {
            $data[] = $result->getRow();
            $dataResult[] = array('VALUE' => $data[$index]['DEP_UID'],'LABEL' =>  addTitlle ('DEPO_TITLE',$data[$index]['DEP_UID'], SYS_LANG));
            $index++;
          }
        break;
      }
      $response->objectName = array();
      (count($dataResult)>0)?array_unshift($dataResult,array('VALUE'=>' ','LABEL'=> '-- None --')) : '';
      $response->objectName = $dataResult;
    break;
    case 'saveExternalRegistration':
      // Copy template to the process if not exists
      $path = PATH_DATA_MAILTEMPLATES . $_REQUEST['PRO_UID'] . PATH_SEP;
      G::verifyPath($path, true);
      if (!file_exists($path . 'externalRegistration.html')) {
        @copy(PATH_PLUGINS . 'externalRegistration' . PATH_SEP . 'data' . PATH_SEP . 'externalRegistration.html', $path . 'externalRegistration.html');
      }
      // Save configuration
      $erConfigurationInstance = new ErConfiguration();
      try {
        $response->ER_UID = $erConfigurationInstance->createOrUpdate($_REQUEST);
        $response->success = true;
      }
      catch (Exception $error) {
        throw $error;
      }
    break;
    case 'listExternalRegistrationLogs':
      // Para el listado de los request
      if (!isset($_REQUEST['ER_UID'])) {
        $_REQUEST['ER_UID'] = '';
      }
      $criteria = new Criteria();
      $criteria->addSelectColumn('COUNT(*)');
      $criteria->add(ErRequestsPeer::ER_UID, $_REQUEST['ER_UID']);
      $result = ErRequestsPeer::doSelectRS($criteria);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $result->next();
      $totalCount = $result->getRow();
      $totalCount = $totalCount['COUNT(*)'];

      $criteria = new Criteria();
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_UID);
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_DATA);
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_DATE);
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_COMPLETED);
      $criteria->addSelectColumn(ErRequestsPeer::ER_REQ_COMPLETED_DATE);
      $criteria->add(ErRequestsPeer::ER_UID, $_REQUEST['ER_UID']);

      $criteria->addDescendingOrderByColumn(ErRequestsPeer::ER_REQ_DATE);

      $criteria->setLimit( $_REQUEST['limit'] );
      $criteria->setOffset( $_REQUEST['start'] );
      $result = ErRequestsPeer::doSelectRS($criteria);
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $data = Array();
      $index = 0;
      while( $result->next() ) {
        $data[] = $result->getRow();
        $data[$index]['ER_REQ_DATA'] = unserialize($data[$index]['ER_REQ_DATA']);
        $data[$index]['FULL_NAME'] = $data[$index]['ER_REQ_DATA']['__USR_FIRSTNAME__'].' '.$data[$index]['ER_REQ_DATA']['__USR_LASTNAME__'];
        $data[$index]['EMAIL'] = $data[$index]['ER_REQ_DATA']['__USR_EMAIL__'];
        $data[$index]['ER_REQ_COMPLETED'] =  ($data[$index]['ER_REQ_COMPLETED'] == 1)? 'YES' : 'NO';
        $data[$index]['ER_REQ_COMPLETED_DATE'] =  ($data[$index]['ER_REQ_COMPLETED_DATE'] != '')? $data[$index]['ER_REQ_COMPLETED_DATE'] : '-';
        $index++;
      }

      $response = array();
      $response['totalCount'] = $totalCount;
      $response['data']       = $data;
    break;
    case 'viewRequestForm':
      // Para mostrar el formulario enviado por el usuario externo
      if (!isset($_REQUEST['ER_REQ_UID'])) {
        $_REQUEST['ER_REQ_UID'] = '';
      }
      if ($_REQUEST['ER_REQ_UID'] == '') {
        die('Value for parameter "ER_REQ_UID" is invalid.');
      }
      die('<iframe src="viewRegistrationForm?ER_REQ_UID=' . $_REQUEST['ER_REQ_UID'] . '" style="width: 99%; height: 365px; border: 0px;"></iframe>');
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