<?php
/*
 * Class InOutlook.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

require_once PATH_PLUGINS . 'N_InOutlook/classes/core/class.nightlies.php';

class InOutlook extends Service {

  public function healthCheck() {
    parent::healthCheck();
  }

  public function authenticate($username, $password) {
    $response = Nightlies::authenticate($username, $password);
    $this->response = new stdclass();
    $this->response->status = $response['status'];
    if ($this->response->status == 'OK') {
      $this->response->USR_UID = Nightlies::encrypt($response['result'], Nightlies::key);
    }
    else {
      $this->response->description = $response['result'];
    }
    $this->write();
  }

  public function getNewCases($userUID) {
    $this->response = new stdclass();
    if ($userUID != '') {
      $this->response->status = 'OK';
      $this->response->data = new stdclass();
      G::LoadClass('case');
      $cases = new Cases();
      list($criteria, $xmlfile) = $cases->getConditionCasesList('draft', $userUID);
      $this->response->data->draft = AppDelegationPeer::doCount($criteria);
      list($criteria, $xmlfile) = $cases->getConditionCasesList('to_do', $userUID);
      $this->response->data->to_do = AppDelegationPeer::doCount($criteria);
    }
    else {
      $this->response->status = 'ERROR';
      $this->response->description = 'Parameter "userUID" is required.';
    }
    return $this->response;
  }

  public function authorize($userUID) {
    return Nightlies::getInformationForSession($userUID);
  }

  public function getInitialTasks($userUID, $language) {
    $this->response = new Response();
    if ($language == '') {
      $language = 'en';
    }
    G::LoadClass('case');
    $case = new Cases();
    if ($case->canStartCase($userUID)) {
      $initialTasks = $case->getStartCasesPerType($userUID, '');
      $aux = array_shift($initialTasks);
      if (count($initialTasks) > 0) {
        $this->response->status = 'OK';
        $this->response->initialTasks = array();
        foreach ($initialTasks as $initialTask) {
          $initialTaskVO = new InitialTaskVO();
          $initialTaskVO->TAS_UID = $initialTask['uid'];
          $initialTaskVO->TAS_TITLE = $initialTask['value'];
          $this->response->initialTasks[] = $initialTaskVO;
        }
      }
      else {
        $this->response->status = 'ERROR';
        $this->response->description = 'User can\'t start a case.';
      }
    }
    else {
      $this->response->status = 'ERROR';
      $this->response->description = 'User can\'t start a case.';
    }
    $this->write();
  }

  public function autopilot($userUID, $task, $from, $to, $cc, $bcc, $subject, $body) {
    $this->response = new stdclass();
    try {
      require_once 'classes/model/Task.php';
      $criteria = new Criteria();
      $criteria->addSelectColumn(TaskPeer::PRO_UID);
      $criteria->add(TaskPeer::TAS_UID, $task);
      $dataset = TaskPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      $taskData = $dataset->getRow();
      $process = $taskData['PRO_UID'];
      G::LoadClass('pmFunctions');
  	  $result = PMFNewCase($process, $userUID, $task, array());
  	  if ($result !== 0) {
        G::LoadClass('case');
        $case = new Cases();
        $caseData = $case->loadCase($result);
        $caseData['APP_STATUS'] = 'TO_DO';
        $caseData['APP_DATA'] = array_merge($caseData['APP_DATA'], array('_from'    => $from,
                                                                         '_to'      => $to,
                                                                         '_cc'      => $cc,
                                                                         '_bcc'     => $bcc,
                                                                         '_subject' => $subject,
                                                                         '_body'    => $body));
        $case->updateCase($result, $caseData);
        require_once 'classes/model/AppCacheView.php';
        $appCacheView = AppDelegationPeer::retrieveByPk($result, 1);
        $appCacheView->setAppUid($result);
        $appCacheView->setDelIndex(1);
        $appCacheView->setDelInitDate(null);
        $appCacheView->save();
        $this->response->status = 'OK';
  	  }
  	  else {
        $this->response->status = 'ERROR';
        $this->response->description = 'Cannot create a new case in this process.';
  	  }
  	}
  	catch (Exception $e) {
      $this->response->status = 'ERROR';
      $this->response->description = 'An error has ocurred creating the case.';
  	}
  	$this->write();
  }

}

/*
 * VO Classes
 */

class InitialTaskVO extends stdclass {

  public $TAS_UID = '';
  public $TAS_TITLE = '';

}

?>