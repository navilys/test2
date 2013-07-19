<?php
if (!isset($_REQUEST['action'])) {
  $_REQUEST['action'] = 'todo';
}

 $today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
 G::LoadClass ( 'configuration' );
 $generalConfCasesList = array();
 $conf = new Configurations();
 $generalConfCasesList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '' );

 if (isset($generalConfCasesList['casesListDateFormat'])&&!empty($generalConfCasesList['casesListDateFormat'])){
   $dateFormat = $generalConfCasesList['casesListDateFormat'];
 } else {
   $dateFormat = 'M d, Y';
 }

$priorities = array('1'=>'VL', '2'=>'L', '3'=>'N', '4'=>'H', '5'=>'VH');

$labels = array('APP_NUMBER' => '#',
                'APP_TITLE' => 'Case',
                'APP_PRO_TITLE' => 'Process',
                'APP_TAS_TITLE' => 'Task',
                'DEL_TASK_DUE_DATE' => 'Due date',
                'APP_UPDATE_DATE' => 'Last Modify',
                'DEL_PRIORITY' => 'Priority',
                'APP_DEL_PREVIOUS_USER' => 'Sent By',
                'APP_CURRENT_USER' => 'Current User',
                'APP_STATUS' => 'Status');

require_once 'classes/model/AppCacheView.php';
$appCacheView = new AppCacheView();

switch ($_REQUEST['action']) {
  case 'draft':
    $listTitle = 'Draft';
    $criteria = $appCacheView->getDraftListCriteria($_SESSION['USER_LOGGED']);
    $fields = array('APP_NUMBER',
                    'APP_TITLE',
                    'APP_PRO_TITLE',
                    'APP_TAS_TITLE',
                    'DEL_TASK_DUE_DATE',
                    'APP_UPDATE_DATE',
                    'DEL_PRIORITY');
  break;
  case 'sent':
    $listTitle = 'Participated';
    $criteria = $appCacheView->getSentListCriteria($_SESSION['USER_LOGGED']);
    $fields = array('APP_NUMBER',
                    'APP_TITLE',
                    'APP_PRO_TITLE',
                    'APP_TAS_TITLE',
                    'APP_DEL_PREVIOUS_USER',
                    'APP_CURRENT_USER',
                    'APP_UPDATE_DATE',
                    'APP_STATUS');
  break;
  case 'selfservice':
  case 'unassigned':
    $listTitle = 'Unassigned';
    $criteria = $appCacheView->getUnassignedListCriteria($_SESSION['USER_LOGGED']);
    $fields = array('APP_NUMBER',
                    'APP_TITLE',
                    'APP_PRO_TITLE',
                    'APP_TAS_TITLE',
                    'APP_DEL_PREVIOUS_USER',
                    'APP_UPDATE_DATE');
  break;
  case 'paused':
    $listTitle = 'Paused';
    $criteria = $appCacheView->getPausedListCriteria($_SESSION['USER_LOGGED']);
    $fields = array('APP_NUMBER',
                    'APP_TITLE',
                    'APP_PRO_TITLE',
                    'APP_TAS_TITLE',
                    'APP_DEL_PREVIOUS_USER',
                    'APP_UPDATE_DATE');
  break;
  case 'todo':
  default:
    $listTitle = 'Inbox';
    $criteria = $appCacheView->getToDoListCriteria($_SESSION['USER_LOGGED']);
    $fields = array('APP_NUMBER',
                    'APP_TITLE',
                    'APP_PRO_TITLE',
                    'APP_TAS_TITLE',
                    'APP_DEL_PREVIOUS_USER',
                    'DEL_TASK_DUE_DATE',
                    'APP_UPDATE_DATE',
                    'DEL_PRIORITY');
  break;
}

$columns = array();
foreach ($fields as $field) {
  $columns[] = isset($labels[$field]) ? $labels[$field] : '-';
}

$criteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);
$dataset = AppCacheViewPeer::doSelectRS($criteria);
$dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$dataset->next();
$cases = array();
while($row = $dataset->getRow()) {
  $case = array();
  foreach ($fields as $field) {
    if ($field == 'APP_TITLE') {
      $row[$field] .= '<span style="visibility:hidden; display: none;">../cases/cases_Open</span>';
      $row[$field] .= '<span style="visibility:hidden; display: none;">' . $row['APP_UID'] . '</span>';
      $row[$field] .= '<span style="visibility:hidden; display: none;">' . $row['DEL_INDEX'] . '</span>';
      $row[$field] .= '<span style="visibility:hidden; display: none;">' . $_REQUEST['action'] . '</span>';
    }
    if ($field == 'APP_STATUS') {
      $row[$field] = G::LoadTranslation("ID_{$row['APP_STATUS']}");
    }
    if ($field == 'DEL_PRIORITY') {
      $row[$field] = G::LoadTranslation("ID_PRIORITY_{$priorities[$row['DEL_PRIORITY']]}");
    }
    if ($field == 'DEL_TASK_DUE_DATE') {
    	$dueDate = mktime(0, 0, 0, substr($row['DEL_TASK_DUE_DATE'],5,2)  , substr($row['DEL_TASK_DUE_DATE'],8,2), substr($row['DEL_TASK_DUE_DATE'],0,4));
    	$color =  ($dueDate < $today) ? " color:red;" : 'color:green;';
	    $row[$field] = "<span style='".$color."'>".date($dateFormat, strtotime($row['DEL_TASK_DUE_DATE']))."</span>";
	  }
	  if ($field == 'APP_UPDATE_DATE') {
	    $row[$field] = date($dateFormat, strtotime($row['APP_UPDATE_DATE']));
	  }
    $case[$field] = isset($row[$field]) ? $row[$field] : '-';
  }
  $cases[] = $case;
  $dataset->next();
}

require_once PATH_PLUGINS . 'N_InOutlook/classes/core/class.nightlies.php';
require_once PATH_PLUGINS . 'N_InOutlook/classes/class.inOutlookList.php';
$inOutlookList = new InOutlookList($listTitle);
$inOutlookList->columns = $columns;
$inOutlookList->rows = $cases;
$inOutlookList->hide = array(count($columns) - 1);
$inOutlookList->customJS = "
var getParameter = function(paramName) {
  var searchString = window.location.search.substring(1), i, val, params = searchString.split('&');
  for (i = 0; i < params.length; i++) {
    val = params[i].split('=');
    if (val[0] == paramName) {
      return unescape(val[1]);
    }
  }
  return null;
}

setInterval(function(){
  $.ajax({
    url: '../N_InOutlook/services/rest',
    data: {method: 'checkChanges', action: getParameter('action'), userUID: '" . Nightlies::encrypt((isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : ''), Nightlies::key) . "', counter: oTable.fnGetData().length},
    dataType: 'json',
    success: function(response) {
      if (response.changes) {
        window.location = window.location.href;
      }
    }
  });
}, 10000);";
$inOutlookList->printTemplate();
?>