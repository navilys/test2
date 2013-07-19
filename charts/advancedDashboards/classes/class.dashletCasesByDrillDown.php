<?php
require_once ("classes/interfaces/dashletInterface.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");





class dashletCasesByDrillDown implements DashletInterface
{
  const version = '1.1';

  private $xLabel;
  private $yLabel;
  private $category;
  private $optionUser;

  public static $arrayCategory = array(
    array("PROCESS",        "Processes"),
    array("USER",           "Users"),
    array("GROUP",          "Groups"),
    array("DEPARTMENT",     "Departments"),
    array("ACCOMPLISHMENT", "Overdue vs. On Schedule")
  );

  public static $arrayOptionUser = array(
    array(1, "Current user"),
    array(0, "All users")
  );

   public static function getAdditionalFields($className)
   {  $additionalFields = array();

      ///////
      $storeCategory = new stdclass();
      $storeCategory->xtype = "arraystore";
      $storeCategory->idIndex = 0;
      $storeCategory->fields = array("value", "text");
      $storeCategory->data = self::$arrayCategory;

      $storeOptionUser = new stdclass();
      $storeOptionUser->xtype = "arraystore";
      $storeOptionUser->idIndex = 0;
      $storeOptionUser->fields = array("value", "text");
      $storeOptionUser->data = self::$arrayOptionUser;

      ///////
      $txtSubTitle = new stdclass();
      $txtSubTitle->xtype = "hidden";
      $txtSubTitle->id   = "DAS_INS_SUBTITLE";
      $txtSubTitle->name = "DAS_INS_SUBTITLE";
      $txtSubTitle->value = " - " . self::$arrayCategory[0][1] . " - @@USR_USERNAME";
      $additionalFields[] = $txtSubTitle;

      ///////
      $txtXLabel = new stdclass();
      $txtXLabel->xtype = "textfield";
      $txtXLabel->name = "DAS_CASBYDDXLABEL";
      $txtXLabel->fieldLabel = "Label for Axis X";
      //$txtXLabel->allowBlank = false;
      $txtXLabel->width = 320;
      $txtXLabel->value = null;
      $additionalFields[] = $txtXLabel;

      ///////
      $txtYLabel = new stdclass();
      $txtYLabel->xtype = "textfield";
      $txtYLabel->name = "DAS_CASBYDDYLABEL";
      $txtYLabel->fieldLabel = "Label for Axis Y";
      //$txtYLabel->allowBlank = false;
      $txtYLabel->width = 320;
      $txtYLabel->value = null;
      $additionalFields[] = $txtYLabel;

      ///////
      $listeners = new stdclass();
      $listeners->select = "
      function (combo, record, index) {
        var r;
        var i;

        ///////
        var str = \" - \";

        var combo1 = Ext.ComponentMgr.get(\"DAS_CASBYDDCATEGORY\");
        r = combo1.findRecord(combo1.valueField, combo1.getValue());
        i = combo1.store.indexOf(r);
        str = str + combo1.store.getAt(i).get(combo1.displayField);

        ///////
        str = str + \" - \";

        var combo2 = Ext.ComponentMgr.get(\"DAS_CASBYDDOPTIONUSER\");

        if (combo2.getValue() == \"1\") {
          str = str + \"@@USR_USERNAME\";
        }
        else {
          r = combo2.findRecord(combo2.valueField, combo2.getValue());
          i = combo2.store.indexOf(r);
          str = str + combo2.store.getAt(i).get(combo2.displayField);
        }

        ///////
        Ext.ComponentMgr.get(\"DAS_INS_SUBTITLE\").setValue(str);
      }
      ";

      ///////
      $cboCategory = new stdclass();
      $cboCategory->xtype = "combo";
      $cboCategory->id   = "DAS_CASBYDDCATEGORY";
      $cboCategory->name = "DAS_CASBYDDCATEGORY";

      $cboCategory->valueField = "value";
      $cboCategory->displayField = "text";
      $cboCategory->value = self::$arrayCategory[0][0];
      $cboCategory->store = $storeCategory;

      $cboCategory->triggerAction = "all";
      $cboCategory->mode = "local";
      $cboCategory->editable = false;

      $cboCategory->width = 320;
      $cboCategory->fieldLabel = "Show by";
      $cboCategory->listeners = $listeners;
      $additionalFields[] = $cboCategory;

      ///////
      //$arrayRadio = array();

      //$rdoUser1 = new stdclass();
      //$rdoUser1->name = "DAS_CASBYDDOPTIONUSER";
      //$rdoUser1->inputValue = 1;
      //$rdoUser1->checked = true;
      //$rdoUser1->boxLabel = "Only by this user";
      //$arrayRadio[] = $rdoUser1;

      //$rdoUser0 = new stdclass();
      //$rdoUser0->name = "DAS_CASBYDDOPTIONUSER";
      //$rdoUser0->inputValue = 0;
      //$rdoUser0->boxLabel = "All users";
      //$arrayRadio[] = $rdoUser0;

      //$rdogrpUser = new stdclass();
      //$rdogrpUser->xtype = "radiogroup";
      //$rdogrpUser->fieldLabel = null;
      //$rdogrpUser->columns = 1;
      //$rdogrpUser->items = $arrayRadio;
      //$additionalFields[] = $rdogrpUser;

      $cboOptionUser = new stdclass();
      $cboOptionUser->xtype = "combo";
      $cboOptionUser->id   = "DAS_CASBYDDOPTIONUSER";
      $cboOptionUser->name = "DAS_CASBYDDOPTIONUSER";

      $cboOptionUser->valueField = "value";
      $cboOptionUser->displayField = "text";
      $cboOptionUser->value = self::$arrayOptionUser[0][0];
      $cboOptionUser->store = $storeOptionUser;

      $cboOptionUser->triggerAction = "all";
      $cboOptionUser->mode = "local";
      $cboOptionUser->editable = false;

      $cboOptionUser->width = 320;
      $cboOptionUser->fieldLabel = "Cases of";
      $cboOptionUser->listeners = $listeners;
      $additionalFields[] = $cboOptionUser;

      ///////
      return ($additionalFields);
   }

  public static function getXTemplate($className)
  {  return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->xLabel = (isset($config["DAS_CASBYDDXLABEL"]))? $config["DAS_CASBYDDXLABEL"] : null;
    $this->yLabel = (isset($config["DAS_CASBYDDYLABEL"]))? $config["DAS_CASBYDDYLABEL"] : null;
    $this->category   = $config["DAS_CASBYDDCATEGORY"];
    $this->optionUser = $config["DAS_CASBYDDOPTIONUSER"];

    return true;
  }

   public function render($width = 300)
   {  $cnn = Propel::getConnection("workflow");
      $stmt = $cnn->createStatement();

      ///////
      $title = null;

      $array = self::$arrayCategory;

      for ($i = 0; $i <= count($array) - 1; $i++) {
        if ($array[$i][0] == $this->category) {
          $title = $array[$i][1];
        }
      }

      $title = (!empty($this->xLabel))? $this->xLabel : $title;

      ///////
      $caseData = null;
      $user_logged = ($this->optionUser == 1)? $_SESSION["USER_LOGGED"] : null;

      if ($this->category != "ACCOMPLISHMENT") {
        $result = CaseLibrary::caseData(0, $this->category, "ALL", "ALL", null, null, $user_logged, null, null);
        $sw = 0;

        for ($i = 0; $i <= count($result) - 1; $i++) {
          $field_uid = $result[$i][0];
          $fieldName = $result[$i][1];
          $fieldNumRec = $result[$i][2];

          ///////
          if (!empty($field_uid)) {
            //When is USER, Cases unassigned, not have user
            $aux_process_uid = null;
            $aux_task_uid = null;
            $aux_user_uid = null;
            $aux_group_uid = null;
            $aux_department_uid = null;

            switch ($this->category) {
              case "PROCESS":
                $aux_process_uid = $field_uid;
                $aux_user_uid = $user_logged;
                break;

              case "USER":
                $aux_user_uid = $field_uid;
                break;

              case "GROUP":
                $aux_group_uid = $field_uid;
                $aux_user_uid = $user_logged;
                break;

              case "DEPARTMENT":
                $aux_department_uid = $field_uid;
                $aux_user_uid = $user_logged;
                break;
            }

            ///////
            $inbox = CaseLibrary::caseData(0, "CASE", "TO_DO", "TO_DO", $aux_process_uid, $aux_task_uid, $aux_user_uid, $aux_group_uid, $aux_department_uid);
            $draft = CaseLibrary::caseData(0, "CASE", "DRAFT", "DRAFT", $aux_process_uid, $aux_task_uid, $aux_user_uid, $aux_group_uid, $aux_department_uid);
            $participated = CaseLibrary::caseData(0, "CASE", "ALL", "ALL",               $aux_process_uid, $aux_task_uid, $aux_user_uid, $aux_group_uid, $aux_department_uid);
            $unassigned   = CaseLibrary::caseData(0, "CASE", "UNASSIGNED", "UNASSIGNED", $aux_process_uid, $aux_task_uid, $aux_user_uid, $aux_group_uid, $aux_department_uid);
            $paused       = CaseLibrary::caseData(0, "CASE", "PAUSED", "PAUSED",         $aux_process_uid, $aux_task_uid, $aux_user_uid, $aux_group_uid, $aux_department_uid);

            ///////
            $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"$fieldName\", $fieldNumRec, \"$fieldName ^ \",
                                                                  {  \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"EVENT_CLICK\"],
                                                                     \"DATA\": [";
            $sw2 = 0;

            if ($inbox[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Inbox\", " . $inbox[0] . ", \"Inbox ^ \", \"caseGrid('', 'TO_DO', '$fieldName', '$aux_process_uid', '$aux_task_uid', '$aux_user_uid', '$aux_group_uid', '$aux_department_uid');\"]";
              $sw2 = 1;
            }
            if ($draft[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Draft\", " . $draft[0] . ", \"Draft ^ \", \"caseGrid('', 'DRAFT', '$fieldName', '$aux_process_uid', '$aux_task_uid', '$aux_user_uid', '$aux_group_uid', '$aux_department_uid');\"]";
              $sw2 = 1;
            }
            if ($participated[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Participated\", " . $participated[0] . ", \"Participated ^ \", \"caseGrid('', 'ALL',        '$fieldName', '$aux_process_uid', '$aux_task_uid', '$aux_user_uid', '$aux_group_uid', '$aux_department_uid');\"]";
              $sw2 = 1;
            }
            if ($unassigned[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Unassigned\", " . $unassigned[0] . ", \"Unassigned ^ \",       \"caseGrid('', 'UNASSIGNED', '$fieldName', '$aux_process_uid', '$aux_task_uid', '$aux_user_uid', '$aux_group_uid', '$aux_department_uid');\"]";
              $sw2 = 1;
            }
            if ($paused[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Paused\", " . $paused[0] . ", \"Paused ^ \",                   \"caseGrid('', 'PAUSED',     '$fieldName', '$aux_process_uid', '$aux_task_uid', '$aux_user_uid', '$aux_group_uid', '$aux_department_uid');\"]";
              $sw2 = 1;
            }

            ///////
            $caseData = $caseData . "     ]
                                       }
                                     ]";

            $sw = 1;
          }
        }
      }
      else {
        $arrayACCOMP = array("OVERDUE", "OVERDUENOT");
        $sw = 0;

        for ($i = 0; $i <= count($arrayACCOMP) - 1; $i++) {
          $fieldName = null;

          switch ($arrayACCOMP[$i]) {
            case "OVERDUE":    $fieldName = "Overdue"; break;
            case "OVERDUENOT": $fieldName = "On Schedule"; break;
          }

          ///////
          $result = CaseLibrary::caseData(0, $arrayACCOMP[$i], "ALL", "ALL", null, null, $user_logged, null, null);

          if ($result[0] > 0) {
            $inbox  = CaseLibrary::caseData(0, $arrayACCOMP[$i], "TO_DO", "TO_DO", null, null, $user_logged, null, null);
            $draft  = CaseLibrary::caseData(0, $arrayACCOMP[$i], "DRAFT", "DRAFT", null, null, $user_logged, null, null);
            $participated = CaseLibrary::caseData(0, $arrayACCOMP[$i], "ALL", "ALL",               null, null, $user_logged, null, null);
            $unassigned   = CaseLibrary::caseData(0, $arrayACCOMP[$i], "UNASSIGNED", "UNASSIGNED", null, null, $user_logged, null, null);
            $paused       = CaseLibrary::caseData(0, $arrayACCOMP[$i], "PAUSED", "PAUSED",         null, null, $user_logged, null, null);

            ///////
            $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"$fieldName\", " . $result[0] . ", \"$fieldName ^ \",
                                                                  {  \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"EVENT_CLICK\"],
                                                                     \"DATA\": [";
            $sw2 = 0;

            if ($inbox[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Inbox\", " . $inbox[0] . ", \"Inbox ^ \", \"caseGrid('" . $arrayACCOMP[$i] . "', 'TO_DO', '$fieldName', '', '', '$user_logged', '', '');\"]";
              $sw2 = 1;
            }
            if ($draft[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Draft\", " . $draft[0] . ", \"Draft ^ \", \"caseGrid('" . $arrayACCOMP[$i] . "', 'DRAFT', '$fieldName', '', '', '$user_logged', '', '');\"]";
              $sw2 = 1;
            }
            if ($participated[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Participated\", " . $participated[0] . ", \"Participated ^ \", \"caseGrid('" . $arrayACCOMP[$i] . "', 'ALL',        '$fieldName', '', '', '$user_logged', '', '');\"]";
              $sw2 = 1;
            }
            if ($unassigned[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Unassigned\", " . $unassigned[0] . ", \"Unassigned ^ \",       \"caseGrid('" . $arrayACCOMP[$i] . "', 'UNASSIGNED', '$fieldName', '', '', '$user_logged', '', '');\"]";
              $sw2 = 1;
            }
            if ($paused[0] > 0) {
              $caseData = $caseData . (($sw2 == 1)? ", " : null) . "[\"$fieldName\", \"Paused\", " . $paused[0] . ", \"Paused ^ \",                   \"caseGrid('" . $arrayACCOMP[$i] . "', 'PAUSED',     '$fieldName', '', '', '$user_logged', '', '');\"]";
              $sw2 = 1;
            }

            ///////
            $caseData = $caseData . "     ]
                                       }
                                     ]";

            $sw = 1;
          }
        }
      }

      ///////
      $javascriptChart = null;
      $htmlChart = "<div style=\"font-size: 0.80em;\">No results</div>";

      if ($caseData != null) {
        $zoom = (!isset($_REQUEST["z"]))? "<a href=\\\"javascript:;\\\" onclick=\\\"window.open(\'" . Library::getUrl() . "&z=1\', \'_blank\'); return (false);\\\" title=\\\"Zoom\\\"><img src=\\\"/plugin/advancedDashboards/icons/zoom25x25.png\\\" alt=\\\"\\\" /></a>" : null;

        $javascriptChart = "
        var chartData = {
          \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"DRILL_DATA\"],
          \"DATA\": [$caseData]
        };

        \$(function () {
          \$(\"#chart_div_static\").ddBarChart({
            chartData: chartData,
            action: \"init\",
            xOddClass: \"ddchart-ui-state-active\",
            xEvenClass: \"ddchart-ui-state-default\",
            yOddClass: \"ddchart-ui-state-active\",
            yEvenClass: \"ddchart-ui-state-default\",
            xWrapperClass: \"ddchart-x-axis-title\",
            chartWrapperClass: \"ddchart-ui-widget-content\",
            chartBarClass: \"ui-state-focus ui-corner-top\",
            chartBarHoverClass: \"ddchart-ui-state-highlight\",
            callBeforeLoad: function (){\$(\"#loading-Notification_static\").fadeIn(500);},
            callAfterLoad: function (){\$(\"#loading-Notification_static\").stop().fadeOut(0);},
            tooltipSettings: {extraClass: \"ui-widget ddchart-ui-widget-content ui-corner-all\"},
            yLabel: \"" . $this->yLabel . "\",
            zoom: \"$zoom\"
          });
        });
        ";

        $htmlChart = "
        <div style=\"margin-top: 0.25em; position: relative; width: 100%; height: 95%;\">
          <div id=\"chart_div_static\" style=\"position: relative; height: 95%; width: 100%;\"></div>
          <div id=\"loading-Notification_static\" class=\"chart_loading ui-widget ddchart-ui-widget-content ui-state-error\">Loading...</div>
        </div>
        ";
      }

      ///////
      $html = "
      <html xmlns=\"http://www.w3.org/1999/xhtml\">
      <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

        <title></title>

        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/jquery-ui-1.8.17.custom.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/demo_table_jui.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchart.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchartCustom.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/dataTablesCustom.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/style.css\" rel=\"stylesheet\" />

        <!--[if IE]>
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchartCustom.ie.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/dataTablesCustom.ie.css\" rel=\"stylesheet\" />
        <![endif]-->

        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery-latest.js\"></script>
        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.tooltip.js\"></script>
        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.ddchart.js\"></script>
        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.dataTables.js\"></script>

        <style type=\"text/css\">
        .dataTables_info {
          width: 26em;
        }

        .dataTables_paginate {
          width: 5em;
        }
        </style>

        <script type=\"text/javascript\">
        function divShow(option)
        {  document.getElementById(\"containerChart\").style.display = \"none\";
           document.getElementById(\"containerGrid\").style.display = \"none\";

           switch (option) {
             case \"chart\": document.getElementById(\"containerChart\").style.display = \"inline\"; break;
             case \"grid\":  document.getElementById(\"containerGrid\").style.display = \"inline\"; break;
           }
        }

        var dataTableCase;

        function caseGrid(category, appStatus, grdTitle, process_uid, task_uid, user_uid, group_uid, department_uid)
        {  var appAction = \"\";

           switch (appStatus) {
             case \"TO_DO\":      appAction = \"todo\"; break;
             case \"DRAFT\":      appAction = \"draft\"; break;
             case \"UNASSIGNED\": appAction = \"unassigned\"; break;
             case \"PAUSED\":     appAction = \"paused\"; break;
             default:
               //if appStatus = ALL, then \"Participated\"
               //if appStatus = CLOSED, then \"casesInbox + casesCompleted\"
               appAction = \"sent\";
               break;
           }

           if (typeof(dataTableCase) != \"undefined\") {
             dataTableCase.fnDestroy();
           }

           dataTableCase = \$(\"#dataTableCase\").dataTable({
             \"bProcessing\": true,
             \"bServerSide\": true,
             \"sAjaxSource\": \"../advancedDashboards/dashletCasesByDrillDownAjax.php\",
             \"bJQueryUI\": true,

             \"sScrollX\": \"500px\", //width
             \"sScrollY\": \"130px\", //height

             \"sDom\": \"lfrt<'F'ip>T\", //OK
             iDisplayLength: 20,
             \"bLengthChange\": false, //remove comboBox, of size page
             \"bFilter\": false, //quita el textBox search
             \"bSort\": false,

             \"aoColumns\": [
               {\"bVisible\": false},
               {\"bVisible\": false},
               null,
               null,
               null,
               null,
               null,
               null,
               null,
               null
             ],

             \"fnServerData\": function (sSource, aoData, fnCallback) {
               //Add some extra data to the sender
               aoData.push({\"name\": \"option\", \"value\": \"DATA\"});
               aoData.push({\"name\": \"category\", \"value\": category});
               aoData.push({\"name\": \"appStatus\", \"value\": appStatus});
               aoData.push({\"name\": \"process_uid\", \"value\": process_uid});
               aoData.push({\"name\": \"task_uid\", \"value\": task_uid});
               aoData.push({\"name\": \"user_uid\", \"value\": user_uid});
               aoData.push({\"name\": \"group_uid\", \"value\": group_uid});
               aoData.push({\"name\": \"department_uid\", \"value\": department_uid});

               \$.ajax({
                 \"dataType\": \"json\",
                 \"type\": \"POST\",
                 \"url\": sSource,
                 \"data\": aoData,
                 \"success\": function (json) {
                   //Do whatever additional processing you want on the callback, then tell DataTables
                   document.getElementById(\"grdTitle\").innerHTML = grdTitle;
                   divShow(\"grid\");

                   fnCallback(json);
                 }
               });
             }
           });
        }

        $javascriptChart
        </script>
      </head>
      <body style=\"margin: 0; padding: 0; overflow: hidden;\" class=\"ui-widget\">
      <div id=\"containerChart\">
        $htmlChart
      </div>

      <div id=\"containerGrid\" class=\"dnone\">
        <div class=\"fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix\" style=\"padding: 0.25em;\">
          <div id=\"grdTitle\" class=\"floatl\" style=\"width: 17.5em;\"></div>
          <div class=\"floatr\" style=\"width: 14em; text-align: right;\"><a href=\"javascript:;\" onclick=\"divShow('chart'); return (false);\">&lt;&lt;&nbsp;Return to the chart</a></div>
          <div class=\"clearf\"></div>
        </div>
        <table id=\"dataTableCase\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
          <thead>
            <tr>
              <th>appUid</th>
              <th>appDelIndex</th>
              <th>#</th>
              <th>Status</th>
              <th>Case</th>
              <th>Process</th>
              <th>Task</th>
              <th>Sent&nbsp;by</th>
              <th>Current&nbsp;user</th>
              <th>Due&nbsp;date</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan=\"10\" class=\"dataTables_empty\">Loading data...</td>
            </tr>
          </tbody>
          <!--
          <tfoot>
            <tr>
              <th>appUid</th>
              <th>appDelIndex</th>
              <th>#</th>
              <th>Status</th>
              <th>Case</th>
              <th>Process</th>
              <th>Task</th>
              <th>Sent&nbsp;by</th>
              <th>Current&nbsp;user</th>
              <th>Due&nbsp;date</th>
            </tr>
          </tfoot>
          -->
        </table>
      </div>
      </body>
      </html>
      ";

      echo $html;
   }
}
?>