<?php
require_once ("classes/interfaces/dashletInterface.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");





class dashletCasesDrillDown implements DashletInterface
{
  const version = '1.1';

  private $xLabel;
  private $yLabel;
  private $optionUser;

  public static $arrayOptionUser = array(
    array(1, "Current user"),
    array(0, "All users")
  );

   public function chartData($user_uid, $appStatus, $node)
   {  //$appStatus = TO_DO, DRAFT, PAUSED, ...

      $caseData = "
      \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"DRILL_DATA\"],
      \"DATA\": [";

      $result = CaseLibrary::caseData(0, "PROCESS", $appStatus, $appStatus, null, null, $user_uid, null, null);

      for ($i = 0; $i <= count($result) - 1; $i++) {
        $process_uid = $result[$i][0];
        $proName = $result[$i][1];
        $proNumRec = $result[$i][2];

        ///////
        $caseData = $caseData . (($i > 0)? ", " : null) . "[\"$node\", \"$proName\", $proNumRec, \"$proName ^ \",
                                                              {  \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"EVENT_CLICK\"],
                                                                 \"DATA\": [";

        $result1 = CaseLibrary::caseData(0, "TASK", $appStatus, $appStatus, $process_uid, null, $user_uid, null, null);

        for ($i1 = 0; $i1 <= count($result1) - 1; $i1++) {
          $task_uid = $result1[$i1][0];
          $taskName = $result1[$i1][1];
          $taskNumRec = $result1[$i1][2];

          $caseData = $caseData . (($i1 > 0)? ", " : null) . "[\"$proName\", \"$taskName\", $taskNumRec,\"$taskName ^ \", \"caseGrid('$appStatus', '$taskName', '$process_uid', '$task_uid', '$user_uid');\"]";
        }

        ///////
        $caseData = $caseData . "     ]
                                   }
                                 ]";
      }

      $caseData = $caseData. "]";

      return ($caseData);
   }

   public static function getAdditionalFields($className)
   {
     $additionalFields = array();

     ///////
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
     $txtSubTitle->value = " - @@USR_USERNAME";
     $additionalFields[] = $txtSubTitle;

     ///////
     $txtXLabel = new stdclass();
     $txtXLabel->xtype = "textfield";
     $txtXLabel->name = "DAS_XLABEL";
     $txtXLabel->fieldLabel = "Label for Axis X";
     //$txtXLabel->allowBlank = false;
     $txtXLabel->width = 320;
     $txtXLabel->value = null;
     $additionalFields[] = $txtXLabel;

     ///////
     $txtYLabel = new stdclass();
     $txtYLabel->xtype = "textfield";
     $txtYLabel->name = "DAS_YLABEL";
     $txtYLabel->fieldLabel = "Label for Axis Y";
     //$txtYLabel->allowBlank = false;
     $txtYLabel->width = 320;
     $txtYLabel->value = null;
     $additionalFields[] = $txtYLabel;

     ///////
     $listeners = new stdclass();
     $listeners->select = "
     function (combo, record, index) {
       var str = \" - \" + ((combo.getValue() == \"1\")? \"@@USR_USERNAME\" : combo.store.getAt(index).get(combo.displayField));

       Ext.ComponentMgr.get(\"DAS_INS_SUBTITLE\").setValue(str);
     }
     ";

     $cboOptionUser = new stdclass();
     $cboOptionUser->xtype = "combo";
     $cboOptionUser->name = "DAS_OPTIONUSER";

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
  {
    return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->xLabel = (isset($config["DAS_XLABEL"]))? $config["DAS_XLABEL"] : null;
    $this->yLabel = (isset($config["DAS_YLABEL"]))? $config["DAS_YLABEL"] : null;
    $this->optionUser = (isset($config["DAS_OPTIONUSER"]))? $config["DAS_OPTIONUSER"] : self::$arrayOptionUser[0][0];

    return true;
  }

   public function render($width = 300)
   {
      $title = (!empty($this->xLabel))? $this->xLabel : "Cases";

      ///////
      $caseData = null;
      $userLogged = ($this->optionUser == 1)? $_SESSION["USER_LOGGED"] : null;

      $inbox = CaseLibrary::caseData(0, "CASE", "TO_DO", "TO_DO", null, null, $userLogged, null, null);
      $draft = CaseLibrary::caseData(0, "CASE", "DRAFT", "DRAFT", null, null, $userLogged, null, null);
      $participated = CaseLibrary::caseData(0, "CASE", "ALL", "ALL", null, null, $userLogged, null, null);
      $unassigned   = CaseLibrary::caseData(0, "CASE", "UNASSIGNED", "UNASSIGNED", null, null, $userLogged, null, null);
      $paused       = CaseLibrary::caseData(0, "CASE", "PAUSED", "PAUSED", null, null, $userLogged, null, null);

      $sw = 0;

      if ($inbox[0] > 0) {
        $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"Inbox\", " . $inbox[0] . ", \"Inbox ^ \", {" . $this->chartData($userLogged, "TO_DO", "Inbox") . "}]";
        $sw = 1;
      }
      if ($draft[0] > 0) {
        $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"Draft\", " . $draft[0] . ", \"Draft ^ \", {" . $this->chartData($userLogged, "DRAFT", "Draft") . "}]";
        $sw = 1;
      }
      if ($participated[0] > 0) {
        $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"Participated\", " . $participated[0] . ", \"Participated ^ \", {" . $this->chartData($userLogged, "ALL", "Participated") . "}]";
        $sw = 1;
      }
      if ($unassigned[0] > 0) {
        $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"Unassigned\", " . $unassigned[0] . ", \"Unassigned ^ \", {" . $this->chartData($userLogged, "UNASSIGNED", "Unassigned") . "}]";
        $sw = 1;
      }
      if ($paused[0] > 0) {
        $caseData = $caseData . (($sw == 1)? ", " : null) . "[\"$title\", \"Paused\", " . $paused[0] . ", \"Paused ^ \", {" . $this->chartData($userLogged, "PAUSED", "Paused") . "}]";
        $sw = 1;
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

        .ex_highlight #dataTableCase tbody tr.even:hover, #dataTableCase tbody tr.even td.highlighted {
          background: #E0EAEF;
        }

        .ex_highlight #dataTableCase tbody tr.odd:hover, #dataTableCase tbody tr.odd td.highlighted {
          background: #E0EAEF;
        }

        .ex_highlight_row #dataTableCase tr.even:hover {
          background: #E0EAEF;
        }

        .ex_highlight_row #dataTableCase tr.even:hover td.sorting_1 {
          background: #DDFF75;
        }

        .ex_highlight_row #dataTableCase tr.even:hover td.sorting_2 {
          background: #E7FF9E;
        }

        .ex_highlight_row #dataTableCase tr.even:hover td.sorting_3 {
          background: #E2FF89;
        }

        .ex_highlight_row #dataTableCase tr.odd:hover {
          background: #E0EAEF;
        }

        .ex_highlight_row #dataTableCase tr.odd:hover td.sorting_1 {
          background: #D6FF5C;
        }

        .ex_highlight_row #dataTableCase tr.odd:hover td.sorting_2 {
          background: #E0FF84;
        }

        .ex_highlight_row #dataTableCase tr.odd:hover td.sorting_3 {
          background: #DBFF70;
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

        function caseGrid(appStatus, grdTitle, process_uid, task_uid, user_uid)
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
             //$(\"#dataTableCase\").dataTable().fnDestroy(); //Destroy the old dataTable
             dataTableCase.fnDestroy(); //Destroy the old dataTable
           }

           dataTableCase = \$(\"#dataTableCase\").dataTable({
             \"bProcessing\": true,
             \"bServerSide\": true,
             \"sAjaxSource\": \"../advancedDashboards/dashletCasesDrillDownAjax.php\",
             \"bJQueryUI\": true,
             //\"bScrollCollapse\": true,
             //\"sScrollXInner\": \"100%\",

             //\"sScrollX\": \"100%\", //width
             \"sScrollX\": \"500px\", //width
             \"sScrollY\": \"130px\", //height

             //\"sPaginationType\": \"full_numbers\",
             //\"bPaginate\": true,

             //\"sDom\": \"<'pagerTitle'>lfrt<'F'ip>T\", //OK
             \"sDom\": \"lfrt<'F'ip>T\", //OK

             iDisplayLength: 20,
             //iDisplayStart: 3,

             \"bLengthChange\": false, //remove comboBox, of size page
             //\"aLengthMenu\": [[10, 25, 50, -1], [10, 25, 50, \"All\"]],

             \"bFilter\": false, //quita el textBox search
             //oSearch: {\"sSearch\": \"Type here...\", \"bRegex\": false, \"bSmart\": false},

             \"bSort\": false,
             //\"bInfo\": false, //pager, hide information

             //\"bAutoWidth\": false,
             //\"aoColumns\": [
             //  {\"sWidth\": \"50px\"},
             //  {\"sWidth\": \"100px\"},
             //  {\"sWidth\": \"100px\"},
             //  {\"sWidth\": \"100px\"},
             //  {\"sWidth\": \"100px\"},
             //  {\"sWidth\": \"50px\"}
             //],

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

             //\"fnServerData\": function (sSource, aoData, fnCallback) {
             //  //Add some extra data to the sender
             //  aoData.push({\"name\": \"var1\", \"value\": \"value1\"});
             //  aoData.push({\"name\": \"var2\", \"value\": \"value2\"});
             //
             //  \$.getJSON(sSource, aoData, function (json) {
             //    //Do whatever additional processing you want on the callback, then tell DataTables
             //    fnCallback(json);
             //  });
             //}

             /*
             \"fnInitComplete\": function (oSettings, json) {
               \$(dataTableCase.fnGetNodes()).click(function (e) {
               });
             },
             */

             \"fnServerData\": function (sSource, aoData, fnCallback) {
               //Add some extra data to the sender
               aoData.push({\"name\": \"option\", \"value\": \"DATA\"});
               aoData.push({\"name\": \"appStatus\", \"value\": appStatus});
               aoData.push({\"name\": \"process_uid\", \"value\": process_uid});
               aoData.push({\"name\": \"task_uid\", \"value\": task_uid});
               aoData.push({\"name\": \"user_uid\", \"value\": user_uid});

               //\$.getJSON(sSource, aoData, function (json) {
               //  //Do whatever additional processing you want on the callback, then tell DataTables
               //  fnCallback(json);
               //});

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

           //\$(\"#dataTableCase tbody\").click(function () {
           //  alert(\"hi\");
           //});

           \$(\"#dataTableCase tbody\").undelegate(\"tr\", \"click\");

           \$(\"#dataTableCase tbody\").delegate(\"tr\", \"click\", function (e) {
             //e.currentTarget._DT_RowIndex
             //e.currentTarget.cells[3].childNodes[0].nodeValue
             //e.currentTarget.cells[3].firstChild.nodeValue
             //e.currentTarget.cells[3].innerHTML

             var data = dataTableCase.fnGetData(this);
             window.open(\"" . Library::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/plugins/advancedDashboards/dashletCasesDrillDownCaseOpen?APP_UID=\" + data[0]  + \"&DEL_INDEX=\" + data[1]  + \"&action=\" + appAction + \"&sysSkin=" . SYS_SKIN . "\", \"_blank\");
           });

           \$(\"#dataTableCase tbody\").delegate(\"tr\", \"mouseover\", function (e) {
             this.style.background = \"#E0EAEF\";
           });
           \$(\"#dataTableCase tbody\").delegate(\"tr\", \"mouseout\", function (e) {
             //odd  = impar
             //even = par
             this.style.background = (this.className == \"odd\")? \"#FFFFFF\" : \"#F2F2F2\"; //dataTablesCustom.css
           });
        }

        /*
        \$(document).ready(function () {
        });
        */

        $javascriptChart
        </script>
      </head>
      <body style=\"margin: 0; padding: 0; overflow: hidden;\" class=\"ui-widget\">
      <div id=\"containerChart\">
        $htmlChart
      </div>

      <div id=\"containerGrid\" class=\"dnone ex_highlight_row\">
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