<?php
function getconsolidated()
{  $caseColumns = array();
   $caseColumns[] = array("header" => "#",           "dataIndex" => "APP_NUMBER",            "width" => 45, "align" => "center");
   $caseColumns[] = array("header" => "Case",        "dataIndex" => "APP_TITLE",             "width" => 150);
   $caseColumns[] = array("header" => "UserUid",     "dataIndex" => "USR_UID",               "width" => 50, "hidden" => true, "hideable" => false);
   $caseColumns[] = array("header" => "PreUsrUid",   "dataIndex" => "PREVIOUS_USR_UID",      "width" => 50, "hidden" => true, "hideable" => false);
   $caseColumns[] = array("header" => "Task",        "dataIndex" => "APP_TAS_TITLE",         "width" => 120);
   $caseColumns[] = array("header" => "Process",     "dataIndex" => "APP_PRO_TITLE",         "width" => 120);
   $caseColumns[] = array("header" => "Sent by",     "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
   $caseColumns[] = array("header" => "Due Date",    "dataIndex" => "DEL_TASK_DUE_DATE",     "width" => 110);
   $caseColumns[] = array("header" => "Last Modify", "dataIndex" => "APP_UPDATE_DATE",       "width" => 110);
   $caseColumns[] = array("header" => "Priority",    "dataIndex" => "DEL_PRIORITY",          "width" => 50);

   $caseReaderFields = array();
   $caseReaderFields[] = array("name" => "APP_UID");
   $caseReaderFields[] = array("name" => "USR_UID");
   $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
   $caseReaderFields[] = array("name" => "DEL_INDEX");
   $caseReaderFields[] = array("name" => "APP_NUMBER");
   $caseReaderFields[] = array("name" => "APP_TITLE");
   $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
   $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
   $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
   $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
   $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
   $caseReaderFields[] = array("name" => "DEL_PRIORITY");
   $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
   $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
   $caseReaderFields[] = array("name" => "APP_STATUS");

   return array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => 20, "dateformat" => "M d, Y");
}

function getAdditionalFields($action, $confCasesList)
{  $caseColumns = array();
   $caseReaderFields = array();

   if (!empty($confCasesList) && !empty($confCasesList["second"]["data"])) {
     foreach($confCasesList["second"]["data"] as $fieldData) {
       if ( $fieldData["fieldType"] != "key") {
         $label = $fieldData["label"];
         $caseColumns[] = array("header" => $label, "dataIndex" => $fieldData["name"], "width" => $fieldData["width"], "align" => $fieldData["align"]);
         $caseReaderFields[] = array( "name" => $fieldData["name"]);
       }
     }
     return array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => $confCasesList["rowsperpage"], "dateformat" => $confCasesList["dateformat"]);
   }
   else {
     switch ($action) {
       case "consolidated":
       default:
         $action = "consolidated";
         $config = getconsolidated();
       break;
     }
     return $config;
   }
}





$action = isset($_GET["action"])? $_GET["action"] : (isset($_POST["action"])? $_POST["action"] : "consolidated");

G::LoadClass("BasePeer");
G::LoadClass("configuration");
require_once ("classes/model/Fields.php");
require_once ("classes/model/AppCacheView.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/Users.php");
require_once ("classes/model/Content.php");
require_once ("classes/model/Task.php");
//require_once ("classes/model/CaseConsolidated.php");

$oHeadPublisher = &headPublisher::getSingleton();

G::loadClass("pmFunctions");

$sql = "SELECT *
        FROM   PM_REPORT LEFT JOIN CONTENT ON (PM_REPORT.PMR_UID = CONTENT.CON_ID)
        WHERE  CONTENT.CON_CATEGORY = 'PMR_TITLE' AND CONTENT.CON_LANG = '" . SYS_LANG . "' AND PMR_STATUS='ACTIVE'";
$aTaskConsolidated = executeQuery($sql);

$TRANSLATIONS["LABEL_GRID_LOADING"]    = G::LoadTranslation("ID_CASES_LIST_GRID_LOADING");
$TRANSLATIONS["LABEL_REFRESH"]         = G::LoadTranslation("ID_REFRESH_LABEL");
$TRANSLATIONS["MESSAGE_REFRESH"]       = G::LoadTranslation("ID_REFRESH_MESSAGE");
$TRANSLATIONS["LABEL_OPT_READ"]        = G::LoadTranslation("ID_OPT_READ");
$TRANSLATIONS["LABEL_OPT_UNREAD"]      = G::LoadTranslation("ID_OPT_UNREAD");
$TRANSLATIONS["LABEL_OPT_ALL"]         = G::LoadTranslation("ID_OPT_ALL");
$TRANSLATIONS["LABEL_OPT_STARTED"]     = G::LoadTranslation("ID_OPT_STARTED");
$TRANSLATIONS["LABEL_OPT_COMPLETED"]   = G::LoadTranslation("ID_OPT_COMPLETED");
$TRANSLATIONS["LABEL_EMPTY_PROCESSES"] = G::LoadTranslation("ID_EMPTY_PROCESSES");
$TRANSLATIONS["LABEL_EMPTY_USERS"]     = G::LoadTranslation("ID_EMPTY_USERS");
$TRANSLATIONS["LABEL_EMPTY_SEARCH"]    = G::LoadTranslation("ID_EMPTY_SEARCH");
$TRANSLATIONS["LABEL_EMPTY_CASE"]      = G::LoadTranslation("ID_EMPTY_CASE");
$TRANSLATIONS["LABEL_SEARCH"]          = G::LoadTranslation("ID_SEARCH");
$TRANSLATIONS["LABEL_OPT_JUMP"]        = G::LoadTranslation("ID_OPT_JUMP");
$TRANSLATIONS["LABEL_DISPLAY_ITEMS"]   = G::LoadTranslation("ID_DISPLAY_ITEMS");
$TRANSLATIONS["LABEL_DISPLAY_EMPTY"]   = G::LoadTranslation("ID_DISPLAY_EMPTY");
$TRANSLATIONS["LABEL_OPEN_CASE"]       = G::LoadTranslation("ID_OPEN_CASE");
$TRANSLATIONS["ID_DERIVATE"]           = "Derivate";

$TRANSLATIONS2 = G::getTranslations(array(
  "ID_CASESLIST_APP_UID", "ID_CONFIRM", "ID_MSG_CONFIRM_DELETE_CASES", "ID_DELETE", "ID_REASSIGN",
  "ID_VIEW", "ID_UNPAUSE", "ID_PROCESSING", "ID_CONFIRM_UNPAUSE_CASE",
  "ID_PROCESS", "ID_STATUS", "ID_USER", "ID_DELEGATE_DATE_FROM", "ID_TO", "ID_FILTER_BY_DELEGATED_DATE",
  "ID_TO_DO", "ID_DRAFT", "ID_COMPLETED", "ID_CANCELLED", "ID_PAUSED",
  "ID_PRO_DESCRIPTION", "ID_PRO_TITLE", "ID_CATEGORY", "ID_STATUS", "ID_PRO_USER", "ID_PRO_CREATE_DATE", "ID_PRO_DEBUG", "ID_INBOX", "ID_DRAFT",
  "ID_COMPLETED", "ID_CANCELLED", "ID_TOTAL_CASES", "ID_ENTER_SEARCH_TERM", "ID_ACTIVATE", "ID_DEACTIVATE",
  "ID_SELECT", "ID_SEARCH", "ID_NO_SELECTION_WARNING", "ID_SELECT_ONE_AT_LEAST", "ID_MSG_CONFIRM_DELETE_CASES2",
  "ID_PAUSE_CASE_TO_DATE", "ID_DELETING_ELEMENTS", "ID_MSG_CONFIRM_CANCEL_CASE", "ID_MSG_CONFIRM_CANCEL_CASES",
  "ID_OPEN_CASE", "ID_PAUSE_CASE", "ID_REASSIGN", "ID_DELETE", "ID_CANCEL", "ID_UNPAUSE_CASE","ID_MSG_CONFIRM_DELETE_CASE",
  "ID_ACTIONS", "ID_CLOSE", "ID_REASSIGN_ALL", "UNCHECK_ALL", "ID_SUBMIT"
));

$TRANSLATIONS = array_merge($TRANSLATIONS, $TRANSLATIONS2);

$conf = new Configurations();

try {
  $confCasesList        = $conf->getConfiguration("casesList", $action);
  $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
}
catch (Exception $e) {
  $confCasesList = array();
  $generalConfCasesList = array();
}

$config = getAdditionalFields($action, $confCasesList);

if (isset($generalConfCasesList["casesListRowNumber"]) && !empty($generalConfCasesList["casesListRowNumber"])) {
  $pageSize = intval($generalConfCasesList["casesListRowNumber"]);
}
else {
  $pageSize = intval($config["rowsperpage"]);
}

$aData = array();
$aAllData = array();

$sql = "SELECT ADD_TAB_NAME, ADD_TAB_DESCRIPTION, ADD_TAB_UID, PRO_UID
        FROM   ADDITIONAL_TABLES
        WHERE  ADD_TAB_TAG = 'plugin@simplereport'";
$resultSQL = executeQuery($sql);

$i = 0;
foreach ($resultSQL as $key => $value) {
  $tasTitle = trim($resultSQL[$key]['ADD_TAB_DESCRIPTION']) != '' ? $resultSQL[$key]['ADD_TAB_DESCRIPTION'] : $resultSQL[$key]['ADD_TAB_NAME'];
  $aAllData[$resultSQL[$key]['ADD_TAB_UID']]['data'] = "{title: '" .$tasTitle . "',
                                                         layout: 'fit',
                                                         items: [
                                                           new Ext.Panel({
                                                             id: 'pmx-panel-" . $i . "',
                                                             title: '',
                                                             layout: 'fit'
                                                           })
                                                         ],
                                                         listeners: {
                                                           'activate': function () {
                                                             generateGrid('pmx-panel-" . $i . "', '" . $resultSQL[$key]['ADD_TAB_UID'] . "', '" . $resultSQL[$key]['PRO_UID'] . "');
                                                           }
                                                         }
                                                        }";
  $i++;
}

foreach ($aAllData as $aValue) {
  $aData[] = $aValue["data"];
}

$items = implode(",", $aData);
$items = "[".$items."]";

$userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;

$oAppCache = new AppCacheView();

//$processes = getProcessArray($action, $userUid );

$oHeadPublisher->assign("TRANSLATIONS", $TRANSLATIONS); //translations
$oHeadPublisher->assign("pageSize",     $pageSize );    //sending the page size
$oHeadPublisher->assign("action",       $action );      //sending the fields to get from proxy
$oHeadPublisher->assign("Items",        $items);
//$oHeadPublisher->assign("processValues", $processes);   //sending the columns to display in grid

$oHeadPublisher->assign("PATH_PLUGINS", PATH_PLUGINS); //vvvvv

$oHeadPublisher->addExtJsScript("pmReports/report", false ); //adding a javascript file .js
$oHeadPublisher->addContent("pmReports/report"); //adding a html file  .html.

G::RenderPage("publish", "extJs");
?>