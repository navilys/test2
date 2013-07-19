<?php
function setDefaultFields($proUid, $tasUid, $dynUid)
{  G::LoadClass ("configuration");
   $hasTextArea = false;

   $conf = new Configurations();
   $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
   if (isset($generalConfCasesList["casesListDateFormat"]) && !empty($generalConfCasesList["casesListDateFormat"])) {
     $dateFormat = $generalConfCasesList["casesListDateFormat"];
   }
   else {
     $dateFormat = "Y/m/d";
   }
   
   //$filename = PATH_DYNAFORM . $proUid . PATH_SEP . $dynUid . ".xml";
   $filename = $proUid . PATH_SEP . $dynUid . ".xml";
   
   //$G_FORM = new xmlform($dynUid, PATH_DYNAFORM);
   $G_FORM = new xmlform();
   //$G_FORM->home = (defined("PATH_XMLFORM"))? PATH_XMLFORM : ((defined("PATH_DYNAFORM"))? PATH_DYNAFORM : null);
   $G_FORM->home = PATH_DYNAFORM;
   $G_FORM->parseFile($filename, SYS_LANG, true);

   $caseColumns      = array();
   $caseReaderFields = array();
   
   $dropList          = array();
   $comboBoxYesNoList = array();
   
   $caseColumns[]      = array("header" => "APP_UID","dataIndex" => "APP_UID","width" => 100, "hidden" => true, "hideable"=>false);
   $caseReaderFields[] = array("name"   => "APP_UID" );
   $caseColumns[]      = array("header" => "#","dataIndex" => "APP_NUMBER","width" => 40);
   $caseColumns[]      = array("header" => G::LoadTranslation("ID_TITLE"),"dataIndex" => "APP_TITLE","width" => 180,"renderer" => "renderTitle");
   $caseColumns[]      = array("header" => "Summary","width" => 60,"renderer" => "renderSummary", "align" => "center");
   $caseReaderFields[] = array("name"   => "APP_NUMBER" );
   $caseReaderFields[] = array("name"   => "APP_TITLE" );
   $caseColumns[]      = array("header" => "DEL_INDEX","dataIndex" => "DEL_INDEX","width" => 100, "hidden" => true, "hideable"=>false);
   $caseReaderFields[] = array("name"   => "DEL_INDEX" );
   //$caseColumns[] = array("header" => "FLAG", "dataIndex" => "FLAG", "width" => 55, "xtype"=>"checkcolumn");
   //$caseReaderFields[] = array("name" => "FLAG", "type"=>"bool");
   //var_dump($G_FORM->fields);

   foreach ($G_FORM->fields as $key => $val) {
     $editor = null;
     $renderer = null;

     switch ($val->type) {
       case "dropdown":
         $dropList[] = $val->name;
         $align = "left";
         
         if ($val->mode != "edit") {
           $disabled = "true";
         }
         else {
           $disabled = "false";
         }

         $editor = "* new Ext.form.ComboBox({
                        id: 'cbo" . $val->name . "_" . $proUid . "',
                          
                        valueField:   'value',
                        displayField: 'text',

                        /*store: comboStore,*/
                        store: new Ext.data.JsonStore({
                          storeId: 'store" . $val->name . "_" . $proUid . "',
                          proxy: new Ext.data.HttpProxy({
                            url: 'proxyDataCombobox'
                          }),
                          root: 'records',
                          fields: [{name: 'value'},
                                   {name: 'text'}
                                  ]
                        }),
                          
                        triggerAction: 'all',
                        mode:     'local',
                        editable: false,
                        disabled: " . $disabled . ",
                        lazyRender: false
                      }) *";

         $width = $val->colWidth;

         $caseColumns[] = array("xtype" => "combocolumn", "gridId" => "gridId", "header" => $val->label, "dataIndex" => $val->name, "width" => (int)$width, "align" => $align, "editor" => $editor, "frame" => "true", "clicksToEdit" => "1");
         $caseReaderFields[] = array("name" => $val->name);
         break;

       case "date":
         //minValue: '01/01/06',
         //disabledDays: [0, 6],
         //disabledDaysText: 'Plants are not available on the weekends'
         
         $align = "center"; 
         $size = 100;
         if (isset($val->size)) {
           $size = $val->size * 10;
         }
         
         $width = $size;

         $editor = "* new Ext.form.DateField({
                        format: '{$dateFormat}'
                      }) *";

         //$renderer = "* formatDate *";
         $renderer = "* function (value){
                          return Ext.isDate(value)? value.dateFormat('{$dateFormat}') : value;    
                        } *";

         if ($val->mode != "edit") {
           $editor = null;
         }
         
         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'editor' => $editor, 'renderer' => $renderer, 'frame' => 'true', 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name, 'type' => 'date');
         break;

       case "currency":
         //align: 'right',
         //renderer: 'usMoney',
         //allowBlank: false,
         //allowNegative: false,
         $align = 'right';
         $size = 100;
         
         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;
         
         $editor = "* new Ext.form.NumberField({
                        maxValue: 1000000,
                        allowDecimals: true,
                        allowNegative: true
                      }) *";
         
         if ($val->mode != "edit") {
           $editor = null;
         }

         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor ,'frame' => 'true', 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name);
         break;
 
       case "percentage":
         $align = 'right';
         $size = 100;

         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;

         $editor = "* new Ext.form.NumberField({
                        maxValue: 100,
                        allowDecimals: true
                      }) *";

         $renderer = "* function (value){
                          return (value + ' %');
                        } *";

         if ($val->mode != 'edit') {
           $editor = null;
         }

         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor , 'renderer' => $renderer, 'frame' => 'true', 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name);
         break;

       case "textarea":
         $align = 'left';
         $size = 200;

         if (isset($val->size)) {
           $size = $val->size * 15;
         }

         $width = $size;

         if ($val->mode != 'edit') {
           $disabled = 'true';
         }
         else {
           $disabled = 'false';
         }
         
         $editor   = "* new Ext.form.TextArea({
                          growMin: 60,
                          growMax: 1000,
                          grow: true,
                          autoHeight: true,
                          readOnly: {$disabled},

                          enterIsSpecial: false,
                          preventScrollbars: false
                        }) *";
         
         $renderer = "* function (value) {  return (value);  } *";

         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor, 'renderer' => $renderer , 'frame' => 'true', 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name);
         
         $hasTextArea = true;
         break;

       case "link":
         $align = 'center';
         $size = 100;

         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;

         $editor = "* new Ext.form.TextField({
                        allowBlank: false
                      }) *";

         $renderer = "* function (value){
                          return '<a href=\"' + value + '\">' + value + '</a>';
                        } *";

         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor, 'renderer' => $renderer, 'frame' => 'true', 'hidden' => 'true', 'hideable' => false, 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name);
         break;

       case "hidden":
         $align = 'left';
         $size = 100;
         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;

         $editor = "* new Ext.form.TextField({
                        allowBlank: false
                      }) *";
         
         $caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor,'frame' => 'true', 'hidden' => 'true', 'hideable' => false, 'clicksToEdit' => '1');
         $caseReaderFields[] = array('name' => $val->name);
         break;

       case "yesno":
         $align = "right";
         $size = 50;

         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;
         $dropList[] = $val->name;
         $comboBoxYesNoList[] = $val->name;

         if ($val->mode != "edit") {
           $disabled = "true";
         }
         else {
           $disabled = "false";
         }

         $editor="* new Ext.form.ComboBox({
                      id: 'cbo" . $val->name . "_" . $proUid . "',

                      valueField:   'value',
                      displayField: 'text',

                      store: new Ext.data.ArrayStore({
                        storeId: 'store" . $val->name . "_" . $proUid . "',
                        fields: ['value', 'text'],
                        data: [[1, 'YES'],
                               [0, 'NO']
                              ]
                      }),

                      typeAhead: true,

                      triggerAction: 'all',
                      mode: 'local',
                      editable: false,
                      disabled : " . $disabled . ",
                      lazyRender: true
                    }) *";

/*
         $renderer = "* function(value) {
                          idx = this.editor.store.find(this.editor.valueField, value);
                          if (currentFieldEdited == '{$val->name}') {
                            if (rec = this.editor.store.getAt(idx)) {
                              rowLabels['{$val->name}'] = rec.get(this.editor.displayField);
                                            return rec.get(this.editor.displayField);
                            }
                            else {
                              return value;
                            }
                          }
                          else {
                            if (typeof(currentFieldEdited) == 'undefined') {
                              return value;
                            }
                            else {
                              return (rowLabels['{$val->name}']);
                            }
                          }
                        } *";
*/

         //$caseColumns[] = array('header' => $val->label, 'dataIndex' => $val->name, 'width' => (int)$width, 'align' => $align, 'editor' => $editor, 'renderer' => $renderer, 'frame' => 'true', 'clicksToEdit' => '1');
         $caseColumns[] = array("xtype" => "combocolumn", "gridId" => "gridId", "header" => $val->label, "dataIndex" => $val->name, "width" => (int)$width, "align" => $align, "editor" => $editor, "frame" => "true", "clicksToEdit" => "1");
         $caseReaderFields[] = array("name" => $val->name);
         break; 

       case "text":
       default:
         $align = "left";
         $size = 100;
         
         if (isset($val->size)) {
           $size = $val->size * 10;
         }

         $width = $size;
         $editor = "* new Ext.form.TextField({
                        allowBlank: false
                      }) *";

/*
									$renderer = "* function(value) {
									                 return value;////////////////
																										Ext.MessageBox.alert('Alert', 'hola');
																								} *";
 */       
         if ($val->mode != "edit") {
           $editor = null;
           //$renderer = null;
         }

         $caseColumns[] = array("header" => $val->label, "dataIndex" => $val->name, "width" => (int)$width, "align" => $align, "editor" => $editor, "frame" => "true", "clicksToEdit" => "1");
         $caseReaderFields[] = array("name" => $val->name);
     }
   };
   
   //$caseColumns[] = array('header' => 'jecutar', 'dataIndex' => 'FLAGS', 'width' => 55 , 'xtype'=> 'checkcolumn');
   //$caseReaderFields[] = array('name' => 'FLAGS', 'type'=> 'bool');

   return array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "dropList" => $dropList, "comboBoxYesNoList" => $comboBoxYesNoList, "hasTextArea" => $hasTextArea, "rowsperpage" => 20, "dateformat" => "M d, Y");
}


$callback = isset($_POST["callback"])? $_POST["callback"] : "stcCallback1001";
$dir      = isset($_POST["dir"])?      $_POST["dir"]      : "DESC";
$sort     = isset($_POST["sort"])?     $_POST["sort"]     : "";
$query    = isset($_POST["query"])?    $_POST["query"]    : "";
$tabUid   = isset($_POST["table"])?    $_POST["table"]    : "";
$xaction  = isset($_POST["xaction"])?  $_POST["xaction"]  : "applyChanges";

$tasUid   = isset($_POST["tasUid"])? $_POST["tasUid"] : "";
$dynUid   = isset($_POST["dynUid"])? $_POST["dynUid"] : "";
$proUid   = isset($_POST["proUid"])? $_POST["proUid"] : "";

try {
  //load classes
  G::LoadClass ('case');
  G::LoadClass ('pmFunctions');
  
  $tmpArray = setDefaultFields($proUid, $tasUid, $dynUid);
  $array ['columnModel']  = $tmpArray['caseColumns'];
  $array ['readerFields'] = $tmpArray['caseReaderFields'];
  
  $array ["dropList"]          = $tmpArray["dropList"];
  $array ["comboBoxYesNoList"] = $tmpArray["comboBoxYesNoList"];

  $array ['hasTextArea'] = $tmpArray['hasTextArea'];
  $temp = G::json_encode($array);

  //$temp = str_replace("***","'",$temp);
  $temp = str_replace('"*','',  $temp);
  $temp = str_replace('*"','',  $temp);
  $temp = str_replace('\t','',  $temp);
  $temp = str_replace('\n','',  $temp);
  $temp = str_replace('\/','/', $temp);
  $temp = str_replace('\"','"', $temp);
  $temp = str_replace('"checkcolumn"','\'checkcolumn\'',$temp);
  
  print $temp;
  die;
}
catch (Exception $e) {
  print G::json_encode( $e->getMessage());
}
?>