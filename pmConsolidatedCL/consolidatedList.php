<?php
function getProcessArray($action, $userUid)
{  global $oAppCache;

   $processes = array();
   $processes[] = array("", G::LoadTranslation("ID_ALL_PROCESS"));

   switch ($action) {
     case "simple_search":
     case "search":
       //In search action, the query to obtain all process is too slow, so we need to query directly to
       //process and content tables, and for that reason we need the current language in AppCacheView.
       G::loadClass("configuration");
       $oConf = new Configurations;
       $oConf->loadConfig($x, "APP_CACHE_VIEW_ENGINE", "", "", "", "");
       $appCacheViewEngine = $oConf->aConfig;
       $lang = isset($appCacheViewEngine["LANG"])? $appCacheViewEngine["LANG"] : "en";

       $cProcess = new Criteria("workflow");
       $cProcess->clearSelectColumns();
       $cProcess->addSelectColumn(ProcessPeer::PRO_UID);
       $cProcess->addSelectColumn(ContentPeer::CON_VALUE);

       $del = DBAdapter::getStringDelimiter();

       $conds = array();
       $conds[] = array(ProcessPeer::PRO_UID,      ContentPeer::CON_ID);
       $conds[] = array(ContentPeer::CON_CATEGORY, $del . "PRO_TITLE" . $del);
       $conds[] = array(ContentPeer::CON_LANG,     $del . $lang . $del);
       $cProcess->addJoinMC($conds, Criteria::LEFT_JOIN);
       $cProcess->add(ProcessPeer::PRO_STATUS, "ACTIVE");
       $oDataset = ProcessPeer::doSelectRS($cProcess);
       $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

       $oDataset->next();
       while ($aRow = $oDataset->getRow()) {
         $processes[] = array($aRow["PRO_UID"], $aRow["CON_VALUE"]);
         $oDataset->next();
       }

       return ($processes);
       break;

     case "consolidated":
     default:
       $cProcess = $oAppCache->getToDoListCriteria($userUid); //fast enough
     break;
   }

   $cProcess->clearSelectColumns();
   $cProcess->setDistinct();
   $cProcess->addSelectColumn(AppCacheViewPeer::PRO_UID);
   $cProcess->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);
   $oDataset = AppCacheViewPeer::doSelectRS($cProcess);
   $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
   $oDataset->next();

   while ($aRow = $oDataset->getRow()) {
     $processes[] = array($aRow["PRO_UID"], $aRow["APP_PRO_TITLE"]);
     $oDataset->next();
   }

   return ($processes);
}

function getConsolidated()
{  $caseColumns = array ();
   $caseColumns[] = array("header" =>"#",           "dataIndex" => "APP_NUMBER",            "width" => 45, "align" => "center");
   $caseColumns[] = array("header" =>"Case",        "dataIndex" => "APP_TITLE",             "width" => 150);
   $caseColumns[] = array("header" =>"UserUid",     "dataIndex" => "USR_UID",               "width" => 50, "hidden" => true, "hideable" => false);
   $caseColumns[] = array("header" =>"PreUsrUid",   "dataIndex" => "PREVIOUS_USR_UID",      "width" => 50, "hidden" => true, "hideable" => false);
   $caseColumns[] = array("header" =>"Task",        "dataIndex" => "APP_TAS_TITLE",         "width" => 120);
   $caseColumns[] = array("header" =>"Process",     "dataIndex" => "APP_PRO_TITLE",         "width" => 120);
   $caseColumns[] = array("header" =>"Sent by",     "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
   $caseColumns[] = array("header" =>"Due Date",    "dataIndex" => "DEL_TASK_DUE_DATE",     "width" => 110);
   $caseColumns[] = array("header" =>"Last Modify", "dataIndex" => "APP_UPDATE_DATE",       "width" => 110);
   $caseColumns[] = array("header" =>"Priority",    "dataIndex" => "DEL_PRIORITY",          "width" => 50);

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

   return (array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => 20, "dateformat" => "M d, Y"));
}

function getAdditionalFields($action, $confCasesList)
{  $caseColumns = array();
   $caseReaderFields = array();

   if (!empty($confCasesList) && !empty($confCasesList["second"]["data"])) {
     foreach ($confCasesList["second"]["data"] as $fieldData) {
       if ($fieldData["fieldType"] != "key") {
         $label = $fieldData["label"];
         $caseColumns[]      = array("header" => $label, "dataIndex" => $fieldData["name"], "width" => $fieldData["width"], "align" => $fieldData["align"]);
         $caseReaderFields[] = array("name"   => $fieldData["name"]);
       }
     }
     return (array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => $confCasesList["rowsperpage"], "dateformat" => $confCasesList["dateformat"]));
   }
   else {
     switch ($action) {
       case "consolidated":
       default:
         $action = "consolidated";
         $config = getConsolidated();
       break;
     }
     return ($config);
   }
}

$action = isset($_GET["action"])? $_GET["action"] : (isset($_POST["action"])? $_POST["action"] : "consolidated");

$oCriteria = new Criteria("workflow");
$oCriteria->addSelectColumn(CaseConsolidatedPeer::CON_STATUS);
$oCriteria->add(CaseConsolidatedPeer::CON_STATUS, "ACTIVE");
$activeNumRows = CaseConsolidatedPeer::doCount($oCriteria);

G::LoadClass ("BasePeer");
G::LoadClass ("configuration");
require_once ("classes/model/Fields.php");
require_once ("classes/model/AppCacheView.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/Users.php");
require_once ("classes/model/Content.php");
require_once ("classes/model/Task.php");
require_once ("classes/model/CaseConsolidated.php");

//require_once (PATH_PLUGINS . "pmConsolidatedCL" . PATH_SEP . "pmConsolidatedCL" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseConsolidated.php");
//require_once ("/home/edwin/plugins/pmConsolidatedCL/pmConsolidatedCL/classes" . PATH_SEP . "model" . PATH_SEP . "CaseConsolidated.php");

$oHeadPublisher = &headPublisher::getSingleton();

//require_once (PATH_CORE.PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Content.php");

G::loadClass("pmFunctions");
//cambiar esto por PROPEL //CASE_CONSOLIDATED   TASK
$usrUid = $_SESSION["USER_LOGGED"];

$oCriteria = new Criteria("workflow");

$oCriteria->addSelectColumn("*");
$oCriteria->addSelectColumn(CaseConsolidatedPeer::TAS_UID);
$oCriteria->addJoin(CaseConsolidatedPeer::TAS_UID,ContentPeer::CON_ID, Criteria::LEFT_JOIN);
$oCriteria->addJoin(CaseConsolidatedPeer::TAS_UID,TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
$oCriteria->addAnd(ContentPeer::CON_CATEGORY, "TAS_TITLE");
$oCriteria->addAnd(ContentPeer::CON_LANG, "en");

$params = array(); //This will be filled with the parameters
$sql = BasePeer::createSelectSql($oCriteria, $params);

$oDataset = CaseConsolidatedPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
//$oDataset->next();
while ($oDataset->next()) {
  $aRow = $oDataset->getRow();
  //$aTaskConsolidated [] = $aRow;
}

$query = "SELECT *
          FROM   CASE_CONSOLIDATED LEFT JOIN CONTENT ON
                 (CASE_CONSOLIDATED.TAS_UID = CONTENT.CON_ID) LEFT JOIN TASK ON (CASE_CONSOLIDATED.TAS_UID = TASK.TAS_UID)
          WHERE  CONTENT.CON_CATEGORY='TAS_TITLE' AND CONTENT.CON_LANG='en'";
$aTaskConsolidated = executeQuery($query);

$conf = new Configurations();

try {
  $confCasesList        = $conf->getConfiguration("casesList", $action);
  $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
}
catch (Exception $e){
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

$aData    = array();
$aAllData = array();
$aQTY     = array();
$i = 0;

//foreach ($aTaskConsolidated as $value)
//{
$i++;

$query = "SELECT COUNT(APP_CACHE_VIEW.TAS_UID) AS QTY, CON_VALUE, APP_CACHE_VIEW.TAS_UID, DYN_UID, APP_CACHE_VIEW.PRO_UID
          FROM   CASE_CONSOLIDATED
                 LEFT JOIN CONTENT ON (CASE_CONSOLIDATED.TAS_UID = CONTENT.CON_ID)
                 LEFT JOIN APP_CACHE_VIEW ON (CASE_CONSOLIDATED.TAS_UID = APP_CACHE_VIEW.TAS_UID)
                 LEFT JOIN TASK ON (CASE_CONSOLIDATED.TAS_UID = TASK.TAS_UID)
          WHERE  CONTENT.CON_CATEGORY = 'TAS_TITLE' AND
                 CONTENT.CON_LANG = 'en' AND
                 APP_CACHE_VIEW.DEL_THREAD_STATUS = 'OPEN' AND
                 APP_CACHE_VIEW.APP_STATUS = 'TO_DO' AND
                 USR_UID = '" . $usrUid . "'
          GROUP BY APP_CACHE_VIEW.TAS_UID";
$res = executeQuery($query);

foreach ($res as $key => $value) {
  $processUID  = $res[$key]["PRO_UID"];
  $taskUID     = $res[$key]["TAS_UID"];
  $dynaformUID = $res[$key]["DYN_UID"];

  //$tasTitle = $value["CON_VALUE"]." (".$value["QTY"].")";
  $tasTitle = $res[$key]["CON_VALUE"] . " (" . (($activeNumRows > 0)? $res[$key]["QTY"] : 0) . ")";
  $aAllData[$res[$key]["TAS_UID"]]["data"] = "{title: \"".$tasTitle."\",
                                               listeners: {\"activate\": function() {
                                                             generateGrid(\"$processUID\", \"$taskUID\", \"$dynaformUID\");
                                                           }
                                                          }

                                              }";
}
//}

//foreach ($aAllData as $key => $row) {
//  $aQTY[$key] = $row["QTY"];
//}

//array_multisort($aQTY, SORT_DESC, $aAllData);

foreach ($aAllData as $aValue) {
  $aData[] = $aValue["data"];
}

$items = "[" . implode(",", $aData) ."]";

$userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;

$oAppCache = new AppCacheView();

$processes = getProcessArray($action, $userUid);

$oHeadPublisher->assign("pageSize",      $pageSize );    //sending the page size
$oHeadPublisher->assign("action",        $action );      //sending the fields to get from proxy
$oHeadPublisher->assign("Items",         $items);
$oHeadPublisher->assign("processValues", $processes);    //sending the columns to display in grid

$oHeadPublisher->addExtJsScript('app/main', true);

$oHeadPublisher->assign('FORMATS',$conf->getFormats());

$oHeadPublisher->addExtJsScript("pmConsolidatedCL/casesList", false); //Adding a javascript file .js
$oHeadPublisher->addContent("pmConsolidatedCL/casesList"); //Adding a html file .html

G::RenderPage("publish", "extJs");
?>