<?php
require_once ("classes/interfaces/dashletInterface.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");





class dashletCustomDrillDown implements DashletInterface
{
  const version = '1.1';

  private $title; //$xLabel
  private $yLabel;
  private $sql;
  private $optionList;
  private $strApos = "@@apos";

  public static $arrayYesNo = array(
    array(1, "Yes"),
    array(0, "No")
  );

  public function require_once2($dir)
  {
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if (!is_dir($dir . $file) && $file != "." && $file != ".." && !preg_match("/^.*Peer\.php$/", $file)) {
            require_once ($dir . $file);
          }
        }
        closedir($dh);
      }
    }
  }

   public function customData($node, $variableValue, $arraySQL, $n, $i, $stmt)
   {  $varReplace = array();

      $varReplace["USER_LOGGED"]  = $_SESSION["USER_LOGGED"];
      $varReplace["USR_USERNAME"] = $_SESSION["USR_USERNAME"];

      foreach ($variableValue as $key => $value) {
        $varReplace[$key] = $value;
      }

      ///////
      if ($i == ($n - 1) + 1) {
        return (array(0, null, null));
      }
      else {
        if($this->optionList == 1 && $i == $n - 1) { //Last SQL
          $sql = G::replaceDataField($arraySQL[$i], $varReplace);
          $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

          ///////
          $fieldHead = null;

          if ($rsSQL->getRecordCount() > 0) {
            $rsSQL->next();

            $row = $rsSQL->getRow();

            ///////
            $arrayKey = array_keys($row);

            for ($i1 = 0; $i1 <= count($arrayKey) - 1; $i1++) {
              $fieldHead = $fieldHead . (($i1 > 0)? "," : null) . $arrayKey[$i1];
            }
          }

          ///////
          $sql = str_replace(array("\n", "\r"), array(" ", " "), $sql);
          $sql = str_replace("\"", $this->strApos, $sql);
          $sql = str_replace("'",  $this->strApos, $sql);

          //preg_match("/^SELECT(.*)FROM.*$/i", $sql, $matches);
          //$sqlSELECT = null;

          //if (count($matches) > 0) {
          //  $sqlSELECT = str_replace($this->strApos, "'", $matches[1]);
          //}

          ///////
          $data = "\"listGrid('Results', '$fieldHead', '$sql');\"";

          return (array(0, null, $data));
        }
        else {
          $error = 0;
          $errorMessage = null;
          $data = null;

          try {
            $sql = G::replaceDataField($arraySQL[$i], $varReplace);
            $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            if ($rsSQL->getRecordCount() > 0) {
              $swSQL2 = (isset($arraySQL[$i + 1]) && $arraySQL[$i + 1] != null)? 1 : 0;

              ///////
              $data = "{\"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\"";

              if($this->optionList == 1 && $i + 1 == $n - 1) { //Last SQL
                $data = $data . ", \"EVENT_CLICK\"";
              }
              else {
                $data = $data . (($swSQL2 == 1)? ", \"DRILL_DATA\"" : null);
              }

              $data = $data . "],
                        \"DATA\": [";

              ///////
              $sw = 0;

              while ($rsSQL->next()) {
                $row = $rsSQL->getRow();;

                ///////
                $variableValueAux = $row;  //array //array_keys($array)   //Get keys
                $row = array_values($row); //array //array_values($array) //Keys numerics (0, 1, ...)

                ///////
                $fieldLabel = null;
                $fieldValue = 0;

                ///////
                if (isset($row[0]) && !empty($row[0])) {
                  $fieldLabel = $row[0];
                }
                else {
                  throw (new Exception("Query " . ($i + 1) . ", first field doesn't exist."));
                }

                if (isset($row[1]) && !empty($row[1]) && preg_match("/^\d+$/", $row[1])) {
                  $fieldValue = intval($row[1]);
                }
                else {
                  throw (new Exception("Query " . ($i + 1) . ", second field doesn't exist or it isn't a number"));
                }

                ///////
                if ($fieldValue > 0) {
                  $dataAux = null;

                  if ($swSQL2 == 1) {
                    $answer = $this->customData($fieldLabel, $variableValueAux, $arraySQL, $n, $i + 1, $stmt);

                    if ($answer[0] == 1) {
                      throw (new Exception($answer[1]));
                    }

                    $dataAux = $answer[2];
                  }

                  $data = $data . (($sw == 1)? ", " : null) . "[\"$node\", \"$fieldLabel\", $fieldValue, \"$fieldLabel ^ \"" . (($dataAux != null)? ", $dataAux" : null) . "]";
                  $sw = 1;
                }
              }

              $data = $data. "]
              }
              ";
            }

            if ($data == null) {
              throw (new Exception("Query " . ($i + 1) . ", no results."));
            }
          }
          catch (Exception $e) {
            $error = 1;
            $errorMessage = $e->getMessage();
          }

          return (array($error, $errorMessage, $data));
        }
      }
   }

   public static function getAdditionalFields($className)
   {
      $listeners = new stdclass();
      $listeners->blur = "function() { this.setValue(this.getValue().trim()); }";

      $additionalFields = array();

      ///////
      $storeYesNo = new stdclass();
      $storeYesNo->xtype = "arraystore";
      $storeYesNo->idIndex = 0;
      $storeYesNo->fields = array("value", "text");
      $storeYesNo->data = self::$arrayYesNo;

      ///////
      $txtTitle = new stdclass();
      $txtTitle->xtype = "textfield";
      $txtTitle->name = "DAS_CUSDDTITLE";
      $txtTitle->fieldLabel = "Label for Axis X";
      $txtTitle->allowBlank = false;
      $txtTitle->width = 320;
      $txtTitle->value = null;
      $txtTitle->listeners = $listeners;
      $additionalFields[] = $txtTitle;

      ///////
      $txtYLabel = new stdclass();
      $txtYLabel->xtype = "textfield";
      $txtYLabel->name = "DAS_CUSDDYLABEL";
      $txtYLabel->fieldLabel = "Label for Axis Y";
      //$txtYLabel->allowBlank = false;
      $txtYLabel->width = 320;
      $txtYLabel->value = null;
      $txtYLabel->listeners = $listeners;
      $additionalFields[] = $txtYLabel;

      ///////
      $txtSQL = new stdclass();
      $txtSQL->xtype = "textarea";
      $txtSQL->name = "DAS_CUSDDSQL";
      $txtSQL->fieldLabel = "Query";
      $txtSQL->allowBlank = false;
      $txtSQL->width = 320;
      $txtSQL->height = 250;
      $txtSQL->value = null;
      $txtSQL->listeners = $listeners;
      $additionalFields[] = $txtSQL;

      ///////
      //$chkList = new stdclass();
      //$chkList->xtype = "checkbox";
      //$chkList->name = "DAS_CUSDDLIST";
      //$chkList->fieldLabel = "Using the last query to generate the list";
      //$additionalFields[] = $chkList;

      ///////
      //$arrayCheckbox = array();

      //$chkList = new stdclass();
      //$chkList->name = "DAS_CUSDDOPTIONLIST";
      //$chkList->boxLabel = "Using the last query to generate the list";
      //$arrayCheckbox[] = $chkList;

      //$chkgrpList = new stdclass();
      //$chkgrpList->xtype = "checkboxgroup";
      //$chkgrpList->fieldLabel = null;
      //$chkgrpList->columns = 1;
      //$chkgrpList->items = $arrayCheckbox;
      //$additionalFields[] = $chkgrpList;

      ///////
      $cboOptionList = new stdclass();
      $cboOptionList->xtype = "combo";
      $cboOptionList->name = "DAS_CUSDDOPTIONLIST";

      $cboOptionList->valueField = "value";
      $cboOptionList->displayField = "text";
      $cboOptionList->value = self::$arrayYesNo[0][0];
      $cboOptionList->store = $storeYesNo;

      $cboOptionList->triggerAction = "all";
      $cboOptionList->mode = "local";
      $cboOptionList->editable = false;

      $cboOptionList->width = 320;
      $cboOptionList->fieldLabel = "Using the last query to generate the list";
      $additionalFields[] = $cboOptionList;

      ///////
      return ($additionalFields);
   }

  public static function getXTemplate($className)
  {
    return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->title  = $config["DAS_CUSDDTITLE"];
    $this->yLabel = (isset($config["DAS_CUSDDYLABEL"]))? $config["DAS_CUSDDYLABEL"] : null;
    $this->sql        = $config["DAS_CUSDDSQL"];
    $this->optionList = $config["DAS_CUSDDOPTIONLIST"];

    return true;
  }

   public function render($width = 300)
   {  //$this->require_once2(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP); //Tables files
      //$this->require_once2(PATH_DATA_SITE . "classes" . PATH_SEP); //PMTables files





      ///////
      //De los casos,                                            agrupar por procesos y obtener el numero de casos
      //De los casos, del proceso, obtener la ultima delagacion, agrupar por tareas   y obtener el numero de casos
      //Del proceso, obtener todas sus tareas

      /*
      SELECT (SELECT CON.CON_VALUE
              FROM   CONTENT AS CON
              WHERE  CON.CON_ID = APP.PRO_UID AND CON.CON_CATEGORY = 'PRO_TITLE' AND CON.CON_LANG = 'en'
             ) AS PRO_NAME,
             COUNT(APP.PRO_UID),
             APP.PRO_UID,
             NOW() AS FIELD_DATE
      FROM   APPLICATION AS APP
      GROUP BY APP.PRO_UID
      ORDER BY PRO_NAME ASC

      ;

      SELECT (SELECT CON.CON_VALUE
              FROM   CONTENT AS CON
              WHERE  CON.CON_ID = APPD.TAS_UID AND CON.CON_CATEGORY = 'TAS_TITLE' AND CON.CON_LANG = 'en'
             ) AS TASK_NAME,
             COUNT(APPD.TAS_UID),
             @@PRO_UID AS PRO_UID,
             @@PRO_NAME AS PRO_NAME,
             @@FIELD_DATE AS FIELD_DATE
      FROM   APPLICATION AS APP,
             (SELECT APPD1.APP_UID, APPD1.DEL_INDEX, APPD1.TAS_UID
              FROM   APP_DELEGATION AS APPD1
              WHERE  APPD1.DEL_INDEX = (SELECT MAX(APPD2.DEL_INDEX)
                                        FROM   APP_DELEGATION AS APPD2
                                        WHERE  APPD2.APP_UID = APPD1.APP_UID
                                       )
             ) AS APPD
      WHERE  APP.PRO_UID = @@PRO_UID AND APP.APP_UID = APPD.APP_UID
      GROUP BY APPD.TAS_UID
      ORDER BY TASK_NAME ASC

      ;

      SELECT @@PRO_NAME AS PRO_NAME,
             @@FIELD_DATE AS FIELD_DATE,
             CON.CON_VALUE,
             CON.CON_CATEGORY
      FROM   TASK AS TASK,
             CONTENT AS CON
      WHERE  TASK.PRO_UID = @@PRO_UID AND TASK.TAS_UID = CON.CON_ID AND CON.CON_CATEGORY = 'TAS_TITLE' AND CON.CON_LANG = 'en';
      */

      $cnn = Propel::getConnection("workflow");
      $stmt = $cnn->createStatement();

      ///////
      $arraySQL = array();
      $arraySQLAux = explode(";", $this->sql);

      for ($i = 0; $i <= count($arraySQLAux) - 1; $i++) {
        $sqlAux = trim($arraySQLAux[$i]);

        if (!empty($sqlAux)) {
          $arraySQL[] = $sqlAux;
        }
      }

      ///////
      $error = 0;
      $errorMessage = null;
      $data = null;

      ///////
      try {
        if($this->optionList == 1 && count($arraySQL) == 1) {
          throw (new Exception("You have a single query, it can not be used for list"));
        }

        $answer = $this->customData($this->title, array(), $arraySQL, count($arraySQL), 0, $stmt);

        if ($answer[0] == 1) {
          throw (new Exception($answer[1]));
        }

        $data = $answer[2];
      }
      catch (Exception $e) {
        $error = 1;
        $errorMessage = $e->getMessage();
        $data = null;
      }

      ///////
      $html = null;

      if ($error == 0) {
        $javascriptChart = null;
        $htmlChart = "<div style=\"font-size: 0.80em;\">No results</div>";

        if ($data != null) {
          $zoom = (!isset($_REQUEST["z"]))? "<a href=\\\"javascript:;\\\" onclick=\\\"window.open(\'" . Library::getUrl() . "&z=1\', \'_blank\'); return (false);\\\" title=\\\"Zoom\\\"><img src=\\\"/plugin/advancedDashboards/icons/zoom25x25.png\\\" alt=\\\"\\\" /></a>" : null;

          $javascriptChart = "
          var chartData = $data;

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
          function strReplace(strs, strr, str)
          {  var expresion = eval(\"/\" + strs + \"/gi\");

             return (str.replace(expresion, strr));
          }

          function divShow(option)
          {  document.getElementById(\"containerChart\").style.display = \"none\";
             document.getElementById(\"containerGrid\").style.display = \"none\";

             switch (option) {
               case \"chart\": document.getElementById(\"containerChart\").style.display = \"inline\"; break;
               case \"grid\":  document.getElementById(\"containerGrid\").style.display = \"inline\"; break;
             }
          }

          var dataTableCase;

          function listGrid(grdTitle, fieldHead, sql)
          {  sql = strReplace(\"" . $this->strApos . "\", \"'\", sql)

             var arrayFieldHead = (fieldHead != \"\")? fieldHead.split(\",\") : [];

             document.getElementById(\"containerResult\").style.display = \"none\";
             document.getElementById(\"containerMessage\").style.display = \"none\";

             document.getElementById(\"grdTitle\").innerHTML = grdTitle;

             if (arrayFieldHead.length > 0) {
               //var html = \"\";

               //for (var i = 0; i <= arrayFieldHead.length - 1; i++) {
               //  html = html + \"<th>\" + arrayFieldHead[i]  + \"</th>\";
               //}

               ///////
               document.getElementById(\"containerResult\").style.display = \"inline\";

               ///////
               //document.getElementById(\"theadField\").innerHTML = html;

               var thead = document.getElementById(\"dataTableCase\").getElementsByTagName(\"thead\")[0]; //<thead>
               var tr = thead.getElementsByTagName(\"tr\")[0]; //<tr>

               var th = tr.getElementsByTagName(\"th\"); //<th>

               for (var i = 0; i <= th.length - 1; i++) {
                 th[i].parentNode.removeChild(th[i]);
               }

               for (var i = 0; i <= arrayFieldHead.length - 1; i++) {
                 var th = document.createElement(\"th\");
                 var texto = document.createTextNode(arrayFieldHead[i]);
                 th.appendChild(texto);

                 tr.appendChild(th);
               }

               //document.getElementById(\"tbody\").innerHTML = \"<tr><td colspan=\\\"\" + arrayFieldHead.length  + \"\\\" class=\\\"dataTables_empty\\\">Loading data...</td></tr>\";

               var tbody = document.getElementById(\"dataTableCase\").getElementsByTagName(\"tbody\")[0]; //<tbody>

               var tr = tbody.getElementsByTagName(\"tr\");

               for (var i = 0; i <= tr.length - 1; i++) {
                 tr[i].parentNode.removeChild(tr[i]);
               }

               var tr = document.createElement(\"tr\");
               var texto = document.createTextNode(\"<td colspan=\\\"\" + arrayFieldHead.length  + \"\\\" class=\\\"dataTables_empty\\\">Loading data...</td>\");
               tr.appendChild(texto);

               tbody.appendChild(tr);

               ///////
               if (typeof(dataTableCase) != \"undefined\") {
                 dataTableCase.fnDestroy();
               }

               dataTableCase = \$(\"#dataTableCase\").dataTable({
                 \"bProcessing\": true,
                 \"bServerSide\": true,
                 \"sAjaxSource\": \"../advancedDashboards/dashletCustomDrillDownAjax.php\",
                 \"bJQueryUI\": true,

                 \"sScrollX\": \"500px\", //width
                 \"sScrollY\": \"130px\", //height

                 \"sDom\": \"lfrt<'F'ip>T\", //OK
                 iDisplayLength: 20,
                 \"bLengthChange\": false, //remove comboBox, of size page
                 \"bFilter\": false, //quita el textBox search
                 \"bSort\": false,

                 \"fnServerData\": function (sSource, aoData, fnCallback) {
                   //Add some extra data to the sender
                   aoData.push({\"name\": \"option\", \"value\": \"DATA\"});
                   aoData.push({\"name\": \"sql\", \"value\": sql});

                   \$.ajax({
                     \"dataType\": \"json\",
                     \"type\": \"POST\",
                     \"url\": sSource,
                     \"data\": aoData,
                     \"success\": function (json) {
                       //Do whatever additional processing you want on the callback, then tell DataTables
                       divShow(\"grid\");

                       fnCallback(json);
                     }
                   });
                 }
               });
             }
             else {
               document.getElementById(\"containerMessage\").style.display = \"inline\";

               document.getElementById(\"containerMessage\").innerHTML = \"No results\";
               divShow(\"grid\");
             }
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

          <div id=\"containerResult\">
            <table id=\"dataTableCase\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
              <thead>
                <tr>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <!--
              <tfoot>
                <tr>
                </tr>
              </tfoot>
              -->
            </table>
          </div>

          <div id=\"containerMessage\" class=\"dnone\">
          </div>
        </div>
        </body>
        </html>
        ";
      }
      else {
        $html = "
        <html xmlns=\"http://www.w3.org/1999/xhtml\">
        <head>
          <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

          <title></title>

          <link type=\"text/css\" rel=\"stylesheet\" href=\"/plugin/advancedDashboards/css-files/style.css\" />
        </head>
        <body>
        <h5><ins>Your query is not valid.</ins></h5>
        $errorMessage
        </body>
        </html>
        ";
      }

      echo $html;
   }
}
?>