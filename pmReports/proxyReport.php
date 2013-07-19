<?php
function comboBoxGetText($appUid, $proUid, $fieldName, $fieldVal)
{  $oCriteria = new Criteria("workflow");

   //SELECT
   $oCriteria->addSelectColumn(DynaformPeer::DYN_UID);
   //FROM
   //WHERE
   $oCriteria->add(DynaformPeer::PRO_UID, $proUid);
   $oCriteria->add(DynaformPeer::DYN_TYPE, "xmlform");

   //query
   $oDataset = DynaformPeer::doSelectRS($oCriteria);
   $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

   $dynaformUID = null;
   $sw = 0;

   while ($oDataset->next() && $sw == 0) {
     $row = $oDataset->getRow();

     $dynaformUIDAux = $row["DYN_UID"];

     //Load the variables
     $filename = $proUid . PATH_SEP . $dynaformUIDAux . ".xml";

     $frm = new xmlform();
     $frm->home = PATH_DYNAFORM;
     $frm->parseFile($filename, SYS_LANG, true);

     foreach ($frm->fields as $key => $field) {
       if ($fieldName == $field->name) {
         $dynaformUID = $dynaformUIDAux;
         $sw = 1;
         break; //End foreach
       }
     }
   }

   //Load the variables
   $oCase = new Cases();

   if ($dynaformUID != '') {
     $filename = $proUid . PATH_SEP . $dynaformUID . ".xml";

     $frm = new xmlform();
     $frm->home = PATH_DYNAFORM;
     $frm->parseFile($filename, SYS_LANG, true);

     $aFields = $oCase->loadCase($appUid);

     $arrayTmp = array();
     $array = array();
     $sqlQuery = "";
     foreach ($frm->fields as $key => $field) {
       if ($fieldName == $field->name) {
         if (isset($frm->fields[$key]->sql) && $frm->fields[$key]->sql != "") {
           $sqlQuery = G::replaceDataField($frm->fields[$key]->sql, $aFields["APP_DATA"]);
         }

         if ((is_array($field->options)) && (!empty($field->options))) {
           foreach ($field->options as $key2 => $val2) {
             $array[] = array("id" => $key2, "value" => $val2);
           }
         }

         if ($field->type == "yesno") {
           $array[] = array("id" => 1, "value" => strtoupper(G::LoadTranslation("ID_YES")));
           $array[] = array("id" => 0, "value" => strtoupper(G::LoadTranslation("ID_NO")));
         }
       }
     }

     if ($sqlQuery != "") {
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

     foreach ($array as $newKey => $newValue){
       if ($newValue["id"] == $fieldVal){
         return $newValue["value"];
       }
     }
   }
   else {
     return $fieldVal;
   }

   return null;
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





//Getting the extJs parameters
$callback = isset($_POST["callback"])? $_POST["callback"] : "stcCallback1001";
$dir      = isset($_POST["dir"])?      $_POST["dir"]    : "DESC";
$sort     = isset($_POST["sort"])?     $_POST["sort"]   : "";
$start    = isset($_POST["start"])?    $_POST["start"]  : "0";
$limit    = isset($_POST["limit"])?    $_POST["limit"]  : "20";
$filter   = isset($_POST["filter"])?   $_POST["filter"] : "";
$search   = isset($_POST["search"])?   $_POST["search"] : "";
$user     = isset($_POST["user"])?     $_POST["user"]    : "";
$status   = isset($_POST["status"])?   strtoupper($_POST["status"]) : "";
$action   = isset($_GET["action"])?    $_GET["action"] : (isset($_POST["action"])? $_POST["action"] : "todo");
$type     = isset($_GET["type"])?      $_GET["type"] : (isset($_POST["type"])? $_POST["type"] : "extjs");
$user     = isset($_POST["user"])?     $_POST["user"] : "";
$dateFrom = isset($_POST["dateFrom"])? substr($_POST["dateFrom"], 0, 10) : "";
$dateTo   = isset($_POST["dateTo"])?   substr($_POST["dateTo"], 0, 10) : "";

$pmrUid = isset($_POST["pmrUid"])? $_POST["pmrUid"] : "";
$dynUid = isset($_POST["dynUid"])? $_POST["dynUid"] : "";
$proUid = isset($_POST["proUid"])? $_POST["proUid"] : "";
$rowUid = isset($_POST["rowUid"])? $_POST["rowUid"] : "";
$comboBoxList = isset($_POST["comboBoxList"])? G::json_decode($_POST["comboBoxList"]) : array();
$filterList   = isset($_POST["filterList"])? G::json_decode($_POST["filterList"]) : array();

try {
  G::loadClass("pmFunctions");
  G::LoadClass("BasePeer");
  G::LoadClass("configuration");
  G::LoadClass("case");
  require_once ("classes/model/AppCacheView.php");

  $userUid = (isset($_SESSION["USER_LOGGED"]) && $_SESSION["USER_LOGGED"] != "")? $_SESSION["USER_LOGGED"] : null;
  $result = array();
  $searchFields = array();
  //eval("$var = \"PMR_\" . $pmrUid . \"\";");

  $sQueryTable = "SELECT *
                  FROM   ADDITIONAL_TABLES
                  WHERE  ADD_TAB_UID = '$pmrUid'";

  $aTable = executeQuery($sQueryTable);

  $className = $aTable[1]["ADD_TAB_CLASS_NAME"];
  $tableName = $aTable[1]["ADD_TAB_NAME"];

  if (!class_exists($className)) {
    require_once (PATH_DB . SYS_SYS . PATH_SEP . "classes" . PATH_SEP . $className . ".php");
  }

  $oCriteria = new Criteria("workflow");
  $oCriteria->addSelectColumn("*");
  $oCriteria->addJoin($tableName . ".APP_UID", ApplicationPeer::APP_UID, Criteria::LEFT_JOIN);

  eval("\$totalCount = " . $className . "Peer::doCount(\$oCriteria, true);");

  if (is_object($filterList)) {
    //Get field types
    $types = array();
    $fields = executeQuery("SELECT * FROM FIELDS WHERE ADD_TAB_UID = '" . $pmrUid . "'");

    foreach ($fields as $field) {
      $types[$field["FLD_NAME"]] = $field;
    }

    $types["APP_NUMBER"] = array("FLD_DYN_NAME" => "APP_NUMBER", "FLD_TYPE" => "INT");

    //Apply filter
    $filterListData = array();

    foreach ($filterList as $fieldName => $fieldValue) {
      if(substr($fieldName, -9) != "_operator") {
        $filterListData[str_replace("__TO__", "", str_replace("__FROM__", "", $fieldName))] = $fieldValue;
      }
    }

    foreach ($filterListData as $fieldName => $fieldValue) {
      if (($fieldValue != "" && $filterList->{$fieldName . "_operator"} != "") ||
          ((strtoupper($types[$fieldName]["FLD_TYPE"]) == "DATE" ) &&
           ($filterList->{$fieldName . "__FROM__"} != "" || $filterList->{$fieldName . "__TO__"} != "")
          )
         ) {
        $sField = $tableName . "." . $types[$fieldName]["FLD_DYN_NAME"];

        switch (strtoupper($types[$fieldName]["FLD_TYPE"])) {
          case "VARCHAR":
          case "TEXT":
            if (!in_array($types[$fieldName]["FLD_DYN_NAME"], $comboBoxList)) {
              $oCriteria->add($sField, "$sField " . $filterList->{$fieldName . "_operator"} . " '" . str_replace("'", "\'", $fieldValue) . "'", Criteria::CUSTOM);
            }
            break;
          case "INT":
          case "FLOAT":
            $oCriteria->add($sField, "$sField " . $filterList->{$fieldName . "_operator"} . str_replace("'", "\'", $fieldValue) . "", Criteria::CUSTOM);
            break;
          case "DATE":
            if($filterList->{$fieldName . "__FROM__"} != "" and  $filterList->{$fieldName . "__TO__"} != "") {
              $oCriteria->add($sField, "$sField BETWEEN '" . $filterList->{$fieldName . "__FROM__"} . "' AND '" . $filterList->{$fieldName . "__TO__"} . "'", Criteria::CUSTOM);
            }
            else {
              if ($filterList->{$fieldName . "__FROM__"} == "") {
                $oCriteria->add($sField, "$sField = '" . $filterList->{$fieldName . "__TO__"} . "'", Criteria::CUSTOM);
              }
              else{
                $oCriteria->add($sField, "$sField = '" . $filterList->{$fieldName . "__FROM__"} . "'", Criteria::CUSTOM);
              }
            }
            break;
          default:
            $oCriteria->add($sField, "$sField " . $filterList->{$fieldName . "_operator"} . " '" . str_replace("'", "\'", $fieldValue) . "'", Criteria::CUSTOM);
            break;
        }
      }
    }
  }

  eval("\$totalCount = " . $className . "Peer::doCount(\$oCriteria, true);");

  $oCriteria->setLimit($limit);
  $oCriteria->setOffset($start);

  $oDataset = ApplicationPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $aTaskConsolidated = array();

  while ($oDataset->next()) {
    $aRow = $oDataset->getRow();
    $sw = 1;

    foreach ($aRow as $dataKey => $dataField) {
      foreach($comboBoxList as $comboBoxField) {
        if ($comboBoxField == $dataKey) {
          $appUid = $aRow["APP_UID"];
          $fieldVal = $aRow[$comboBoxField];

          $comboBoxText = comboBoxGetText($appUid, $proUid, $comboBoxField, $fieldVal);

          if (isset($filterList->{$comboBoxField}) && !empty($filterList->{$comboBoxField})) {
            $sql = "SELECT COUNT(table1.column1) AS N
                    FROM   (SELECT '$comboBoxText' AS column1) AS table1
                    WHERE  table1.column1 " . $filterList->{$comboBoxField . "_operator"} . " '" . str_replace("'", "\'", $filterList->{$comboBoxField}) . "'";
            $resultSQL = executeQuery($sql);

            if (($resultSQL[1]["N"] == 0)) {
              $sw = 0;
              break;
            }
          }

          $aRow[$comboBoxField] = $comboBoxText;

          $aRow[$comboBoxField . "__pmRDropdownId"] = $fieldVal;
        }
      }

      if ($sw == 0) {
        break;
      }
    }

    if ($sw == 1) {
      $aTaskConsolidated [] = $aRow;
    }
  }
  

  $result["data"] = array();

  foreach ($aTaskConsolidated as $key => $val) {
    foreach ($val as $iKey => $iVal) {
          if (checkValidDate($iVal)) {
            $val[$iKey] = str_replace("-", "/", $val[$iKey]);
          }
    }

    $result["data"][] = $val;
  }
  $result["aTaskConsolidated"] = $aTaskConsolidated;
  $result["totalCount"] = $totalCount;

  echo G::json_encode($result);
}
catch (Exception $e) {
  $msg = array("error" => $e->getMessage());

  echo G::json_encode($msg);
}
?>