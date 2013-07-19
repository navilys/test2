<?php
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
         $sqlQuery = G::replaceDataField($G_FORM->fields[$key]->sql, $aFields ["APP_DATA"]);
       }
       if ((is_array($val->options)) && (!empty($val->options))) {
         foreach ($val->options as $key1 => $val1) {
           $array[] = array("id" => $key1, "value" => $val1);
         }
       }
       if ($val->type == "yesno") {
         $array[] = array("id" => 1, "value" => strtoupper(G::LoadTranslation("ID_YES")));
         $array[] = array("id" => 0, "value" => strtoupper(G::LoadTranslation("ID_NO")));
       }
     }
   }

   if ($sqlQuery != null) {
     $aResult = executeQuery($sqlQuery);
     if ($aResult == false) {
       $aResult = array();
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
           $arrayTmp["value"] = $value;
         }
       }

       if ($i == 1) {
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
  
   return null;
}

function checkValidDate($field) {
  //previous to PHP 5.1.0 you would compare with -1, instead of false
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

$tasUid = isset($_POST["tasUid"])? $_POST["tasUid"] : "";
$dynUid = isset($_POST["dynUid"])? $_POST["dynUid"] : "";
$proUid = isset($_POST["proUid"])? $_POST["proUid"] : "";
$rowUid = isset($_POST["rowUid"])? $_POST["rowUid"] : "";
$dropdownList = isset($_POST ["dropList"])? G::json_decode($_POST ["dropList"]) : array();

try {
  G::loadClass("pmFunctions");
  G::LoadClass("BasePeer");
  G::LoadClass("configuration");
  G::LoadClass("case");
  require_once ( "classes/model/AppCacheView.php" );

  $userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;
  $response = array();
  $searchFields = array();
  //
  $query = "SELECT REP_TAB_UID
            FROM   CASE_CONSOLIDATED
            WHERE  TAS_UID = '" . $tasUid . "'";
  $caseConsolidated = executeQuery($query);
  
  $var = '';
  foreach ($caseConsolidated as $item) {
    require_once 'classes/model/ReportTable.php';
    $criteria = new Criteria();
    $criteria->addSelectColumn(ReportTablePeer::REP_TAB_NAME);
    
    //$criteria->add(ReportTablePeer::PRO_UID,$sProUid);
    $criteria->add(ReportTablePeer::REP_TAB_UID,$item["REP_TAB_UID"]);
    
    $result = ReportTablePeer::doSelectRS($criteria);
    $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $result->next();
    $dataRes = Array();
    if($dataRes = $result->getRow()){
      $var = $dataRes['REP_TAB_NAME'];
    }
    else {
      throw (new Exception("Not found the report table"));
    }
  }
  //$var = "__" . $tasUid;

  //SELECT * FROM `&&6395712624d42bd40106f20011047729` as T
  //LEFT JOIN APP_CACHE_VIEW as A on(T.`APP_UID` = A.`APP_UID`)
  //WHERE DEL_THREAD_STATUS = 'OPEN' and
  //TAS_UID = '6395712624d42bd40106f20011047729' AND
  //USR_UID = '00000000000000000000000000000001'
  
  $className = $var;//"__" . $tasUid;
  $tableName = $className;
  if (!class_exists($className)) {
    require_once (PATH_DB . SYS_SYS . PATH_SEP . "classes" . PATH_SEP . $className . ".php");
  }

  //require (PATH_DB.SYS_SYS.PATH_SEP."classes".PATH_SEP.$className.".php");
  //echo PATH_DB.SYS_SYS.PATH_SEP."classes".PATH_SEP.$className.".php";
  //echo ('$tmpQueryField='.$tableName.'Peer::APP_UID;');
  //$tableName = "PMC".$tasUid;
  //eval ('$tmpQueryField='.$tableName.'Peer::APP_UID;');
  //eval ("\$totalCount=".$tableName."Peer::doCount( \$oCriteria, true );");

  $oCriteria = new Criteria("workflow");
  $oCriteria->addSelectColumn("*");
  //$oCriteria->addSelectColumn ( $tmpQueryField );
  //$oCriteria->addSelectColumn ( $className.'.APP_UID' );
  $oCriteria->addSelectColumn($var . ".APP_UID");
  //$oCriteria->addSelectColumn ( $className.'.textarea' );
  //$oCriteria->addSelectColumn ( __6395712624d42bd40106f20011047729Peer::texto );
  $oCriteria->addJoin($var . ".APP_UID", AppCacheViewPeer::APP_UID, Criteria::LEFT_JOIN);
  //$oCriteria->addJoin ($className.'.APP_UID', AppCacheViewPeer::APP_UID, Criteria::LEFT_JOIN);
  $oCriteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
  $oCriteria->add(AppCacheViewPeer::TAS_UID, $tasUid);
  $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);
  $oCriteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO");
  //$oCriteria->add ( __6395712624d42bd40106f20011047729Peer::APP_UID, '%' . $search . '%', Criteria::LIKE );
  //$oCriteria->add ( __6395712624d42bd40106f20011047729Peer::APP_NUMBER, '%' . $search . '%', Criteria::LIKE );
  //$oNewCriteria = new Criteria( 'workflow' );
  //$oNewCriteria->getNewCriterion ( __6395712624d42bd40106f20011047729Peer::textarea, '%' . $search . '%', Criteria::LIKE );
  //$oCriteria->add ( $oNewCriteria );
  //$oCriteria->add ( $var.'.textarea' , '%17%', Criteria::LIKE );
  //$oCriteria->add ( $tableName.'.textyyyyy' , '%17%', Criteria::LIKE );
  //eval ('$oDataset = '.$className.'Peer::doSelectRS($oCriteria);');
  // var_dump ( $oCriteria );    
  //$params = array();
  // var_dump ( $params );
  //$sql = BasePeer::createSelectSql($oCriteria, $params);
  //echo $sql;
  //echo 'newest';
  //eval ('$totalCount='.$tableName.'Peer::doCount( $oCriteria, true );');
  //echo var_dump($totalCount);
  //$totalCount = __6395712624d42bd40106f20011047729Peer::doCount ($oCriteria,true);
  //$oDataset = AppCacheViewPeer::doSelectRS($oCriteria);
  //die();
  // applying filters

  if ($search != "") {
    $filename = $proUid . PATH_SEP . $dynUid . ".xml";
    
    $G_FORM = new xmlform();
    $G_FORM->home = PATH_DYNAFORM;
    $G_FORM->parseFile($filename, SYS_LANG, true);

    foreach ($G_FORM->fields as $key => $val) {
      switch ($val->type) {
        case "textarea":
          $searchFields[] = strtolower($val->name);
          break;
        case "text":
          $searchFields[] = strtolower($val->name);
          break;
      }
    }
    
    $oNewCriteria = new Criteria("workflow");
    $counter = 0;

    $oTmpCriteria = null;

    //foreach( $searchFields as $fieldData ) {
    //  eval('$oField='.$tableName.'Peer::'.$fieldData.';');
    //  if ( $counter == 0 ) {
    //    $oTmpCriteria = $oNewCriteria->getNewCriterion ( $oField, '%' . $search . '%', Criteria::LIKE );
    //  }
    //  else {
    //    $oTmpCriteria = $oNewCriteria->getNewCriterion ( $oField, '%' . $search . '%', Criteria::LIKE )->addOr($oTmpCriteria);
    //  }
    //  $counter++;
    //}

    //foreach( $searchFields as $fieldData ) {
    //  //eval('$oField='.$tableName.'Peer::'.$fieldData.';');
    //  $oField = $tableName.'.'.$fieldData;
    //  if ( $counter == 0 ) {
    //    $oTmpCriteria = $oNewCriteria->getNewCriterion ( $oField, '%' . $search . '%', Criteria::LIKE );
    //  }
    //  else {
    //    $oTmpCriteria = $oNewCriteria->getNewCriterion ( $oField, '%' . $search . '%', Criteria::LIKE )->addOr($oTmpCriteria);
    //  }
    //  $counter++;
    //}

    if ($oTmpCriteria != null) {
      $oCriteria->add(
              $oCriteria->getNewCriterion(AppCacheViewPeer::APP_NUMBER, $search, Criteria::LIKE)->addOr($oTmpCriteria)
      );
    }
    else {
      $oCriteria->add($oCriteria->getNewCriterion(AppCacheViewPeer::APP_NUMBER, $search, Criteria::LIKE));
    }
  }
  
  //$params = array(
  //  array('column' => "DEL_THREAD_STATUS", 'value' =>'OPEN'),
  //  array('column' => "TAS_UID", 'value' =>$tasUid),
  //  array('column' => "USR_UID", 'value' =>$userUid),
  //  array('column' => "APP_STATUS", 'value' =>'TO_DO'),
  //  array('column' => "APP_NUMBER", 'value' =>$search)
  //);
  
  //$params = array();
  //var_dump ( $params );
  //$sql = BasePeer::createSelectSql($oCriteria, $params);
  //echo $sql;
  //end filters
  
  $oCriteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);
  
  //pagination pagination attributes
  $oCriteria->setLimit($limit);
  $oCriteria->setOffset($start);
  //end of pagination attributes
  
  $oDataset = AppCacheViewPeer::doSelectRS($oCriteria);
  //eval('$oDataset = '.$className.'Peer::doSelectRS($oCriteria);');

  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  //$oDataset->next();
  
  $aTaskConsolidated = array();
  
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

    $aTaskConsolidated[] = $aRow;
  }

  //var_dump( $aTaskConsolidated );
  //$sQuery = 'SELECT *
  //           FROM   '.$var.' as T
  //                  LEFT JOIN APP_CACHE_VIEW as A on(T.APP_UID = A.APP_UID)
  //           WHERE  DEL_THREAD_STATUS = "OPEN" AND
  //                  TAS_UID = "'.$tasUid.'" AND
  //                  USR_UID = "'.$userUid.'"';
  //echo '<br>';
  //echo $sQuery;
  //$aTaskConsolidated = executeQuery ($sQuery);


  foreach ($aTaskConsolidated as $key => $val) {
    //if ($key == 'fecha'){
    foreach ($val as $iKey => $iVal) {
      if (checkValidDate($iVal)) {
        $val[$iKey] = str_replace("-", "/", $val[$iKey]);
      }
    }
    //}
    $response["data"][] = $val;
  }
  
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
?>