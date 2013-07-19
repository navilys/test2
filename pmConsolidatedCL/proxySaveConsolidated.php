<?php
/*
function getDropdownLabel($appUid, $proUid, $dynUid, $fieldName, $fieldVal)
{  //load the variables
   $oCase = new Cases();

   $filename = $proUid . PATH_SEP . $dynUid . ".xml";

   $G_FORM = new xmlform();
   $G_FORM->home = PATH_DYNAFORM;
   $G_FORM->parseFile($filename, SYS_LANG, true);

   $aFields = $oCase->loadCase($appUid);

   $arrayTmp = array();
   $array = array();
   $sqlQuery = null;

   foreach ($G_FORM->fields as $key => $val) {
     if ($fieldName == $val->name) {
       if ($G_FORM->fields[$key]->sql != "") {
         $sqlQuery = G::replaceDataField($G_FORM->fields[$key]->sql, $aFields["APP_DATA"]);

         if (is_array($val->options)) {
           foreach ($val->options as $key1 => $val1) {
             $array[] = array("id" => $key1, "value" => $val1);
           }
         }
       }
     }
  }

  if ($sqlQuery != null) {
    $aResult = executeQuery($sqlQuery);
    if ($aResult == false) {
      $aResult=array();
    }
  }
  else {
    $aResult = array();
  }

  foreach ($aResult as $field) {
    $i = 0;

    foreach ($field as $key => $value) {
      if ($i == 0) {
        $arrayTmp["id"] = $value;
        if (count($field) == 1) {
          $arrayTmp["value"]=$value;
        }
      }
      if ($i==1) {
        $arrayTmp["value"] = $value;
      }
      $i++;
    }

    $array[] = $arrayTmp;
  }

  foreach ($array as $newKey => $newValue) {
    if ($newValue["id"] == $fieldVal) {
      return $newValue["value"];
    }
  }

  return (null);
}

function checkValidDate($field)
{  //previous to PHP 5.1.0 you would compare with -1, instead of false
   //$timestamp = strtotime($field)
   if (($timestamp = strtotime($field)) === false || is_double($field) || is_float($field) || is_bool($field) || is_int($field)) {
     //echo "The string ($str) is bogus";
     return false;
   }
   else {
     return true;
     //echo "$str == " . date('l dS \o\f F Y h:i:s A', $timestamp);
   }
}
*/

/*
//Getting the extJs parameters
$callback = isset($_POST["callback"])? $_POST["callback"] : "stcCallback1001";
$dir      = isset($_POST["dir"])?      $_POST["dir"]    : "DESC";
$sort     = isset($_POST["sort"])?     $_POST["sort"]   : "";
$start    = isset($_POST["start"])?    $_POST["start"]  : "0";
$limit    = isset($_POST["limit"])?    $_POST["limit"]  : "20";
$filter   = isset($_POST["filter"])?   $_POST["filter"] : "";
$search   = isset($_POST["search"])?   $_POST["search"] : "";
$user     = isset($_POST["user"])?     $_POST["user"]   : "";
$status   = isset($_POST["status"])?   strtoupper($_POST["status"]) : "";
$action   = isset($_GET["action"])?    $_GET["action"] : (isset($_POST["action"])? $_POST["action"] : "todo");
$type     = isset($_GET["type"])?      $_GET["type"] : (isset($_POST["type"])? $_POST["type"] : "extjs");
$user     = isset($_POST["user"])?     $_POST["user"] : "";
$dateFrom = isset($_POST["dateFrom"])? substr($_POST["dateFrom"], 0, 10) : "";
$dateTo   = isset($_POST["dateTo"])?   substr($_POST["dateTo"], 0, 10) : "";

$tasUid       = isset($_POST["tasUid"])? $_POST["tasUid"] : "6395712624d42bd40106f20011047729";
$appUid       = isset($_POST["appUid"])? $_POST["appUid"] : "";
$appData      = isset($_POST["data"])?   $_POST["data"]   : "";
$dynUid       = isset($_POST["dynUid"])? $_POST["dynUid"] : "9426372784d41ec14ecb024090600345";
$proUid       = isset($_POST["proUid"])? $_POST["proUid"] : "";
$rowUid       = isset($_POST["rowUid"])? $_POST["rowUid"] : "";
$dropdownList = isset($_POST["dropList"])? G::json_decode($_POST["dropList"]) : array();
*/
//Getting the extJs parameters
$proUid  = $_POST["proUid"];
$dynUid  = $_POST["dynUid"];
//$tasUid  = $_POST["tasUid"];
$appData = $_POST["data"];
$dropdownList = isset($_POST["dropList"])? G::json_decode($_POST["dropList"]) : array();
//$limit = $_POST["limit"];
//$start = $_POST["start"];

///////
G::LoadClass("case");
G::LoadClass("pmFunctions");

$delIndex = 1;
$oCase = new Cases();

$arrayAux = (array)(G::json_decode($appData));
$arrayAux = (isset($arrayAux["APP_UID"]))? $arrayAux : (array)($arrayAux[count($arrayAux) - 1]);

$array = array();
$array["form"] = $arrayAux;

$appUid = $array["form"]["APP_UID"];

$fields = $oCase->loadCase($appUid);

if (!isset($fields["DEL_INIT_DATE"])) {
  $oCase->setDelInitDate($appUid, $delIndex);
  //$aFields = $oCase->loadCase($appUid, $delIndex);
  $fields = $oCase->loadCase($appUid, $delIndex);
}

$auxAppDataApplication = $fields["APP_DATA"]["APPLICATION"];
$auxAppDataProcess     = $fields["APP_DATA"]["PROCESS"];
$auxAppDataTask        = $fields["APP_DATA"]["TASK"];
$auxAppDataIndex       = $fields["APP_DATA"]["INDEX"];

$fields["APP_DATA"] = array_merge($fields["APP_DATA"], G::getSystemConstants());
$fields["APP_DATA"] = array_merge($fields["APP_DATA"], $array["form"]);

$fields["APP_DATA"]["APPLICATION"] = $auxAppDataApplication;
$fields["APP_DATA"]["PROCESS"]     = $auxAppDataProcess;
$fields["APP_DATA"]["TASK"]        = $auxAppDataTask;
$fields["APP_DATA"]["INDEX"]       = $auxAppDataIndex;

$aData = array();
$aData["APP_NUMBER"]       = $fields["APP_NUMBER"];
$aData["APP_PROC_STATUS"]  = $fields["APP_PROC_STATUS"];
$aData["APP_DATA"]         = $fields["APP_DATA"];
$aData["DEL_INDEX"]        = $delIndex;
$aData["TAS_UID"]          = $fields["APP_DATA"]["TASK"];
$aData["CURRENT_DYNAFORM"] = $dynUid;
$aData["USER_UID"]         = $_SESSION["USER_LOGGED"];
$aData["APP_STATUS"]       = $fields["APP_STATUS"];
$aData["PRO_UID"]          = $fields["APP_DATA"]["PROCESS"];

$oCase->updateCase($appUid, $aData);

/*
try {
  G::loadClass("pmFunctions");
  G::LoadClass("BasePeer");
  G::LoadClass("configuration");

  require_once ("classes/model/AppCacheView.php");

  $userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;
  $response = array();

  $var = "__" . $tasUid;

  //SELECT * FROM `&&6395712624d42bd40106f20011047729` as T
  //LEFT JOIN APP_CACHE_VIEW as A on(T.`APP_UID` = A.`APP_UID`)
  //WHERE DEL_THREAD_STATUS = 'OPEN' and
  //TAS_UID = '6395712624d42bd40106f20011047729' AND
  //USR_UID = '00000000000000000000000000000001'

  $oCriteria = new Criteria("workflow");
  $oCriteria->addSelectColumn("*");
  $oCriteria->addSelectColumn($var . ".APP_UID");
  $oCriteria->addJoin($var . ".APP_UID", AppCacheViewPeer::APP_UID, Criteria::LEFT_JOIN);
  $oCriteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
  $oCriteria->add(AppCacheViewPeer::TAS_UID, $tasUid);
  $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);
  $oCriteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO");
  $oCriteria->setLimit($limit);
  $oCriteria->setOffset($start);

  //$params = array();
  //$sql = BasePeer::createSelectSql($oCriteria, $params);
  //applying filters

  //if ($search!='') {
  //  $oNewCriteria = new Criteria('workflow');
  //  $counter = 0;
  //  foreach ($oAppCache->confCasesList['second']['data'] as $fieldData) {
  //    if (!in_array($fieldData['name'],$defaultFields)){
  //      $fieldName = $tableName . '.' . $fieldData['name'];
  //      if ($counter == 0) {
  //        $oTmpCriteria = $oNewCriteria->getNewCriterion($fieldName, '%' . $search . '%', Criteria::LIKE);
  //      } else {
  //        $oTmpCriteria = $oNewCriteria->getNewCriterion($fieldName, '%' . $search . '%', Criteria::LIKE)->addOr($oTmpCriteria);
  //      }
  //      $counter++;
  //    }
  //  }
  //}

  G::LoadClass("case");
  G::LoadClass("pmFunctions");

  //load the variables
  $oCase = new Cases();

  //end filters
  $oDataset = AppCacheViewPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  //$oDataset->next();

  while ($oDataset->next()) {
    $aRow = $oDataset->getRow();

    foreach ($aRow as $datakey => $dataField) {
      foreach ($dropdownList as $tmpField) {
        if ($tmpField == $datakey) {
          $appUid = $aRow["APP_UID"];
          $fieldVal = $aRow[$tmpField];

          $aRow[$tmpField] = getDropdownLabel($appUid, $proUid, $dynUid, $tmpField, $fieldVal);
        }
      }
    }
    $aTaskConsolidated [] = $aRow;
  }

  foreach ($aTaskConsolidated as $key => $val) {
    foreach ($val as $iKey => $iVal) {
      if (checkValidDate($iVal)) {
        $val[$iKey] = str_replace("-", "/", $val[$iKey]);
      }
    }

    $response["data"][] = $val;
  }

  $response["success"] = true;
  $usrUid = $_SESSION["USER_LOGGED"];

  $query = "SELECT COUNT(APP_CACHE_VIEW.TAS_UID) AS QTY
            FROM   CASE_CONSOLIDATED
                   LEFT JOIN CONTENT ON (CASE_CONSOLIDATED.TAS_UID = CONTENT.CON_ID)
                   LEFT JOIN APP_CACHE_VIEW ON (CASE_CONSOLIDATED.TAS_UID = APP_CACHE_VIEW.TAS_UID)
                   LEFT JOIN TASK ON (CASE_CONSOLIDATED.TAS_UID = TASK.TAS_UID)
            WHERE  CONTENT.CON_CATEGORY = 'TAS_TITLE' AND
                   CONTENT.CON_LANG = 'en' AND
                   APP_CACHE_VIEW.DEL_THREAD_STATUS = 'OPEN' AND
                   USR_UID = '" . $usrUid . "' AND
                   APP_CACHE_VIEW.TAS_UID = '" . $tasUid . "'";
  $count = executeQuery($query);

  $totalCount = 0;
  foreach ($count as $item) {
    $totalCount = $totalCount + $item["QTY"];
  }

  $response["totalCount"] = $totalCount;

  echo G::json_encode($response);
}
catch (Exception $e) {
  $msg = array("error" => $e->getMessage());
  echo G::json_encode($msg);
}
*/

$response = array();
$response["success"] = true;

echo G::json_encode($response);
?>