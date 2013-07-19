<?php
function setDefaultFields($tasUid, $pmrUid, $proUid)
{  $operators = array("VARCHAR" => array(array("", "- EMPTY -"), array("LIKE", "LIKE"), array("NOT LIKE", "NOT LIKE"), array("=", "="), array("!=", "!=")),
                      "INT"     => array(array("", "- EMPTY -"), array("=", "="), array(">", ">"), array(">=", ">="), array("<", "<"), array("<=", "<="), array("!=", "!=")));
  
   G::LoadClass("configuration");
   $hasTextArea = false;

   $conf = new Configurations();
   $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
   if (isset($generalConfCasesList["casesListDateFormat"]) && !empty($generalConfCasesList["casesListDateFormat"])) {
     $dateFormat = $generalConfCasesList["casesListDateFormat"];
   }
   else {
     $dateFormat = "Y/m/d";
   }

   $caseColumns        = array();
   $caseReaderFields   = array();
   $comboBoxList       = array();
   $filterFields       = array();

   $caseColumns[]      = array("header" => "APP_UID",   "dataIndex" => "APP_UID","width" => 100, "hidden" => true, "hideable" => false, "editor" => "");
   $caseReaderFields[] = array("name"   => "APP_UID" );
   $caseColumns[]      = array("header" => "#",         "dataIndex" => "APP_NUMBER","width" => 35, "" => "APP_NUMBER");
   $caseReaderFields[] = array("name"   => "APP_NUMBER");
   $caseColumns[]      = array("header" => "DEL_INDEX", "dataIndex" => "DEL_INDEX","width" => 100, "hidden" => true, "hideable" => false, "editor" => "");
   $caseReaderFields[] = array("name"   => "DEL_INDEX");

   ///////
   $oCriteria = new Criteria("workflow");

   //SELECT
   $oCriteria->addSelectColumn(DynaformPeer::DYN_UID);
   //FROM
   //WHERE
   $oCriteria->add(DynaformPeer::PRO_UID, $proUid);
   $oCriteria->add(DynaformPeer::DYN_TYPE, "xmlform");
   
   //query
   $oDataset = DynaformPeer::doSelectRS($oCriteria);
   $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
   ///////

   $sql = "SELECT *
           FROM   FIELDS
           WHERE  ADD_TAB_UID = '" . $pmrUid . "'
           ORDER BY FLD_INDEX ASC";
   $resultSQL = executeQuery($sql);
   
   foreach ($resultSQL as $aRow) {
     //if ($aRow["FLD_NAME"] == "APP_NUMBER") 1;//$aRow["FLD_DYN_NAME"] = G::LoadTranslation("ID_CASE");
     if ($aRow["FLD_NAME"] != "APP_UID" and $aRow["FLD_NAME"] != "APP_NUMBER") {
       $fieldLabel = $aRow["FLD_DESCRIPTION"];

       ///////
       $oDataset->seek(0);
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
           if ($aRow["FLD_NAME"] == $field->name) {
             $fieldLabel = $field->label;
             $sw = 1;
             break; //End foreach
           }
         }
       }
       ///////

       switch ($aRow["FLD_TYPE"]) {
         case "DATE":
           $align = "center";
           $size  = 50;
           //if(isset($aRow["FLD_SIZE"])) $size = $aRow["FLD_SIZE"] * 5;
           $width  = $size;
           $renderer = "* function (value) {
                            return Ext.isDate(value)? value.dateFormat(\"$dateFormat\") : value;
                          } *";

           $editor = "* new Ext.form.DateField({
                          format: \"$dateFormat\"
                        }) *";

           $caseColumns[] = array("header" => $fieldLabel, "dataIndex" => $aRow["FLD_NAME"], "width" => (int)$width, "align" => $align, "editor" => $editor , "frame" => "true", "clicksToEdit" => "1");
           $caseReaderFields[] = array("name" => $aRow["FLD_NAME"]);

           if($aRow["FLD_FILTER"] == "1") {
             $filterFields[] = array(array("width" => 50,
                                           "xtype" => "compositefield",
                                           "fieldLabel" => $fieldLabel,
                                           "name" => $aRow["FLD_NAME"] . "_combo",
                                           "anchor" => "-20",
                                           "defaults" => array("flex" => 1),
                                           "items" => array(
                                             array("xtype" => "datefield", "name" => $aRow["FLD_NAME"]."__FROM__", "format" => "Y-m-d"),
                                             array("xtype" => "datefield", "name" => $aRow["FLD_NAME"]."__TO__",   "format" => "Y-m-d")
                                           )
                                          )
                                    );
           }
           //$filterFields[] = array ("name" => $val->name, "fieldLabel" => $val->label. "", "xtype" => "datefield", "format" => "Y-m-d" );
           //$filterFields[] = array ("name" => $val->name, "fieldLabel" => $val->label. "" . G::LoadTranslation("ID_TO"), "xtype" => "datefield" ,"format" => "Y-m-d");
           break;

         case "FLOAT":
         case "INT":
           $align = "right";
           $size = 50;

           if(isset($aRow["FLD_SIZE"])) $size = $aRow["FLD_SIZE"] * 5;
           $width  = $size;
           $editor = "* new Ext.form.NumberField({
                          maxValue: 1000000,
                          allowDecimals: true,
                          allowNegative: true
                        }) *";

           //if ($val->mode != "edit"){$editor = "";}
           $caseColumns[] = array( "header" => $fieldLabel, "dataIndex" => $aRow["FLD_NAME"], "width" => (int)$width, "align" => $align, "editor" => $editor , "frame" => "true", "clicksToEdit" => "1");
           $caseReaderFields[] = array( "name" => $aRow["FLD_NAME"]);

           if ($aRow["FLD_FILTER"] == "1") {
             $filterFields[] = array(array("xtype" => "compositefield",
                                           "fieldLabel" => $fieldLabel,
                                           "name" => $aRow["FLD_NAME"] . "_combo",
                                           "anchor" => "-20",
                                           "defaults" => array("flex" => 1),
                                           "items" => array(array("width" => 80,
                                                                  "xtype" => "combo",
                                                                  "mode" => "local",
                                                                  "triggerAction" => "all",
                                                                  "editable" => false,
                                                                  "fieldLabel" => "",
                                                                  "name" => $aRow["FLD_NAME"] . "_operator",
                                                                  "displayField" => "name",
                                                                  "valueField" => "value",
                                                                  "store" => $operators["INT"]
                                                                 ),
                                                            array("xtype" => "textfield",
                                                                  "name" => $aRow["FLD_NAME"]
                                                                 )
                                                           )
                                          )
                                    );
           }
           break;

         case "TEXT":
         case "VARCHAR":
         default:
           $align = "left";
           $size  = 50;
           if(isset($aRow["FLD_SIZE"])) $size = $aRow["FLD_SIZE"] * 5;
           $width  = $size;
           $editor = "* new Ext.form.TextField({
                          allowBlank: false
                        }) *";
           $caseColumns[] = array( "header" => $fieldLabel, "dataIndex" => $aRow["FLD_NAME"], "width" => (int)$width, "align" => $align, "editor" => $editor, "frame" => "true", "clicksToEdit" => "1");
           $caseReaderFields[] = array( "name" => $aRow["FLD_NAME"]);

           if ($aRow["FLD_FILTER"] == "1") {
             $filterFields[] = array(array("xtype" => "compositefield",
                                           "fieldLabel" => $fieldLabel,
                                           "name" => $aRow["FLD_NAME"] . "_combo",
                                           "anchor" => "-20",
                                           "defaults" => array("flex" => 1),
                                           "items" => array(array("width" => 80,
                                                                  "xtype" => "combo",
                                                                  "mode" => "local",
                                                                  "triggerAction" => "all",
                                                                  "editable" => false,
                                                                  "fieldLabel" => "",
                                                                  "name" => $aRow["FLD_NAME"] . "_operator",
                                                                  "displayField" => "name",
                                                                  "valueField" => "value",
                                                                  "store" => $operators["VARCHAR"]
                                                                 ),
                                                            array("xtype" => "textfield",
                                                                  "name" => $aRow["FLD_NAME"]
                                                                 )
                                                           )
                                          )
                                    );
           } 
           break;
       }

       $arrayField = explode("-", $aRow["FLD_DYN_UID"]);
       $fieldTypeAux = array_pop($arrayField);
       $fieldNameAux = implode("-", $arrayField);
       
       switch ($fieldTypeAux) {
         case "dropdown":
         case "yesno":
           if (!preg_match("/^.*_label$/", $fieldNameAux)) {
             $comboBoxList[] = $aRow["FLD_NAME"];
           }
           break;
       }
     }
   }

   return array("caseColumns" => $caseColumns,
                "caseReaderFields" => $caseReaderFields,
                "filterFields" => $filterFields,
                "comboBoxList" => $comboBoxList,
                "hasTextArea" => $hasTextArea,
                "rowsperpage" => 20,
                "dateformat" => "M d, Y");
}





$callback = isset($_POST["callback"])? $_POST["callback"] : "stcCallback1001";
$dir      = isset($_POST["dir"])?      $_POST["dir"]      : "DESC";
$sort     = isset($_POST["sort"])?     $_POST["sort"]     : "";
$query    = isset($_POST["query"])?    $_POST["query"]    : "";
$tabUid   = isset($_POST["table"])?    $_POST["table"]    : "";
$tasUid   = isset($_POST["tasUid"])?   $_POST["tasUid"]   : "0";
$xaction  = isset($_POST["xaction"])?  $_POST["xaction"]  : "applyChanges";

$tasUid = isset($_POST["tasUid"])? $_POST["tasUid"] : "";
$pmrUid = isset($_POST["pmrUid"])? $_POST["pmrUid"] : "";
$proUid = isset($_POST["proUid"])? $_POST["proUid"] : "";


try {
  //load classes
  G::LoadClass("case");
  G::LoadClass("pmFunctions");
  $tmpArray = setDefaultFields($tasUid, $pmrUid, $proUid);
  $array["columnModel"]  = $tmpArray["caseColumns"];
  $array["readerFields"] = $tmpArray["caseReaderFields"];
  $array["filterFields"] = $tmpArray["filterFields"];
  $array["comboBoxList"] = $tmpArray["comboBoxList"];
  $array["hasTextArea"]  = $tmpArray["hasTextArea"];
  $temp = G::json_encode($array);

  $temp = str_replace('"*', '',  $temp);
  $temp = str_replace('*"', '',  $temp);
  $temp = str_replace('\t', '',  $temp);
  $temp = str_replace('\n', '',  $temp);
  $temp = str_replace('\r', '',  $temp);
  $temp = str_replace('\/', '/', $temp);
  $temp = str_replace('\"', '"', $temp);
  $temp = str_replace('"checkcolumn"', '\'checkcolumn\'', $temp);
  
  echo $temp;
  exit(0);
}
catch (Exception $e) {
  echo G::json_encode($e->getMessage());
}
?>