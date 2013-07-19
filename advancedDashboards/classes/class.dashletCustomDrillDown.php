<?php
require_once ("classes" . PATH_SEP . "interfaces" . PATH_SEP . "dashletInterface.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.chartLibrary.php");





class dashletCustomDrillDown implements DashletInterface
{
    const version = "1.1";

    private $chartType;
    private $title; //$xLabel
    private $yLabel;
    private $sql;
    private $optionList;

    public static $arrayChartType = array(
        array("BAR", "Bar"),
        array("PIE", "Pie"),
        array("FUN", "Funnel")
    );

    public static $arrayYesNo = array(
        array(1, "Yes"),
        array(0, "No")
    );





    /*
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
    */

    public static function getAdditionalFields($className)
    {
        $additionalFields = array();

        //Stores
        $storeChartType = new stdclass();
        $storeChartType->xtype = "arraystore";
        $storeChartType->idIndex = 0;
        $storeChartType->fields = array("value", "text");
        $storeChartType->data = self::$arrayChartType;

        $storeYesNo = new stdclass();
        $storeYesNo->xtype = "arraystore";
        $storeYesNo->idIndex = 0;
        $storeYesNo->fields = array("value", "text");
        $storeYesNo->data = self::$arrayYesNo;

        //Fields
        //Chart type
        $listeners = new stdclass();
        $listeners->select = "
        function (combo, record, index)
        {
            Ext.getCmp(\"DAS_CUSDDTITLE\").container.parent().child(\"label\").dom.innerHTML = \"Label for Axis X\";

            Ext.getCmp(\"DAS_CUSDDYLABEL\").show();

            var chartType = Ext.getCmp(\"DAS_CHART_TYPE\").getValue();

            if (chartType == \"PIE\" || chartType == \"FUN\") {
                Ext.getCmp(\"DAS_CUSDDTITLE\").container.parent().child(\"label\").dom.innerHTML = \"Label\";

                Ext.getCmp(\"DAS_CUSDDYLABEL\").hide();
            }
        }
        ";

        $cboChartType = new stdclass();
        $cboChartType->xtype = "combo";
        $cboChartType->id    = "DAS_CHART_TYPE";
        $cboChartType->name  = "DAS_CHART_TYPE";

        $cboChartType->valueField = "value";
        $cboChartType->displayField = "text";
        $cboChartType->value = self::$arrayChartType[0][0];
        $cboChartType->store = $storeChartType;

        $cboChartType->triggerAction = "all";
        $cboChartType->mode = "local";
        $cboChartType->editable = false;

        $cboChartType->width = 320;
        $cboChartType->fieldLabel = "Chart type";
        $cboChartType->listeners = $listeners;
        $additionalFields[] = $cboChartType;

        //Label X
        $listeners = new stdclass();
        $listeners->blur = "
        function (text)
        {
            Ext.getCmp(\"DAS_CUSDDTITLE\").setValue(Ext.util.Format.trim(Ext.getCmp(\"DAS_CUSDDTITLE\").getValue()));
        }
        ";

        $txtXLabel = new stdclass();
        $txtXLabel->xtype = "textfield";
        $txtXLabel->id    = "DAS_CUSDDTITLE";
        $txtXLabel->name  = "DAS_CUSDDTITLE";

        $txtXLabel->fieldLabel = "Label for Axis X";
        $txtXLabel->width = 320;
        $txtXLabel->value = null;
        $txtXLabel->listeners = $listeners;
        $additionalFields[] = $txtXLabel;

        //Label Y
        $listeners = new stdclass();
        $listeners->blur = "
        function (text)
        {
            Ext.getCmp(\"DAS_CUSDDYLABEL\").setValue(Ext.util.Format.trim(Ext.getCmp(\"DAS_CUSDDYLABEL\").getValue()));
        }
        ";

        $txtYLabel = new stdclass();
        $txtYLabel->xtype = "textfield";
        $txtYLabel->id    = "DAS_CUSDDYLABEL";
        $txtYLabel->name  = "DAS_CUSDDYLABEL";

        $txtYLabel->fieldLabel = "Label for Axis Y";
        $txtYLabel->width = 320;
        $txtYLabel->value = null;
        $txtYLabel->maxLength = 38;
        $txtYLabel->listeners = $listeners;
        $additionalFields[] = $txtYLabel;

        //SQL
        $listeners = new stdclass();
        $listeners->blur = "
        function (text)
        {
            Ext.getCmp(\"DAS_CUSDDSQL\").setValue(Ext.util.Format.trim(Ext.getCmp(\"DAS_CUSDDSQL\").getValue()));
        }
        ";

        $txtSql = new stdclass();
        $txtSql->xtype = "textarea";
        $txtSql->id    = "DAS_CUSDDSQL";
        $txtSql->name  = "DAS_CUSDDSQL";

        $txtSql->fieldLabel = "Query";
        $txtSql->width = 320;
        $txtSql->height = 250;
        $txtSql->value = null;
        $txtSql->listeners = $listeners;
        $additionalFields[] = $txtSql;

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

        //List
        $cboOptionList = new stdclass();
        $cboOptionList->xtype = "combo";
        $cboOptionList->id    = "DAS_CUSDDOPTIONLIST";
        $cboOptionList->name  = "DAS_CUSDDOPTIONLIST";

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

        //Field afterRender
        $compAuxRender1 = new stdclass();
        $compAuxRender1->_afterRender = "
        Ext.getCmp(\"DAS_CUSDDTITLE\").allowBlank = false;
        Ext.getCmp(\"DAS_CUSDDSQL\").allowBlank = false;
        ";
        $additionalFields[] = $compAuxRender1;

        $compAuxRender2 = new stdclass();
        $compAuxRender2->_afterRender = "
        //Set initial values

        //Show hide DAS_CUSDDYLABEL
        if (dashletInstance.DAS_CHART_TYPE && dashletInstance.DAS_CHART_TYPE != \"\") {
            Ext.getCmp(\"DAS_CUSDDTITLE\").container.parent().child(\"label\").dom.innerHTML = \"Label for Axis X\";

            Ext.getCmp(\"DAS_CUSDDYLABEL\").show();

            if (dashletInstance.DAS_CHART_TYPE == \"PIE\" || dashletInstance.DAS_CHART_TYPE == \"FUN\") {
                Ext.getCmp(\"DAS_CUSDDTITLE\").container.parent().child(\"label\").dom.innerHTML = \"Label\";

                Ext.getCmp(\"DAS_CUSDDYLABEL\").hide();
            }

            dashletInstance.DAS_CHART_TYPE = \"\";
        }
        ";
        $additionalFields[] = $compAuxRender2;

        return $additionalFields;
    }

    public static function getXTemplate($className)
    {
        return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
    }

    public function setup($config)
    {
        $this->chartType = (isset($config["DAS_CHART_TYPE"]))? $config["DAS_CHART_TYPE"] : self::$arrayChartType[0][0];
        $this->title  = $config["DAS_CUSDDTITLE"];
        $this->yLabel = (isset($config["DAS_CUSDDYLABEL"]))? $config["DAS_CUSDDYLABEL"] : null;
        $this->sql        = $config["DAS_CUSDDSQL"];
        $this->optionList = $config["DAS_CUSDDOPTIONLIST"];

        return true;
    }

    public function render($width = 300)
    {
        //$this->require_once2(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP); //Tables files
        //$this->require_once2(PATH_DATA_SITE . "classes" . PATH_SEP); //PMTables files





        ///////
        //De los casos,                                            agrupar por procesos y obtener el numero de casos
        //De los casos, del proceso, obtener la ultima delegacion, agrupar por tareas   y obtener el numero de casos
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

        //Get data
        $sql = str_replace(array("\n", "\r"), array(" ", " "), $this->sql);
        $sql = str_replace("\"", "@@doubleQuote", $sql);
        $sql = str_replace("'",  "@@singleQuote", $sql);

        $arraySql = array();
        $arraySqlAux = explode(";", $sql);

        for ($i = 0; $i <= count($arraySqlAux) - 1; $i++) {
            $sqlAux = trim($arraySqlAux[$i]);

            if (!empty($sqlAux)) {
                $arraySql[] = $sqlAux;
            }
        }

        $sql = implode(";", $arraySql);

        $swList = $this->optionList;

        //Set html
        $html = null;

        try {
            if (count($arraySql) == 1 && $swList == 1) {
                throw (new Exception("You have a single query, it can not be used for list"));
            }

            $variableValue = serialize(array());
            $variableValue = str_replace("\"", "@@doubleQuote", $variableValue);
            $variableValue = str_replace("'",  "@@singleQuote", $variableValue);

            //Set html
            $htmlNoRecords = "<div style='font-size: 0.80em;'>" . G::LoadTranslation("ID_NO_RECORDS_FOUND") . "</div>";

            $javascriptChart = null;
            $htmlChart = $htmlNoRecords;

            $zoom = (!isset($_REQUEST["z"]))? "<a href=\\\"javascript:;\\\" onclick=\\\"window.open(\'" . Library::getUrl() . "&z=1\', \'_blank\'); return false;\\\" title=\\\"Zoom\\\"><img src=\\\"/plugin/advancedDashboards/icons/zoom25x25.png\\\" alt=\\\"\\\" /></a>" : null;

            switch ($this->chartType) {
                case "BAR":
                    $data = null;

                    $answer = ChartLibrary::customDrillDownData($this->title, $this->chartType, $swList, $variableValue, $sql, count($arraySql), 0, $stmt);

                    if ($answer[0] == 1) {
                        throw (new Exception($answer[1]));
                    }

                    $data = $answer[2];

                    if ($data != null) {
                        $arrayAux = ChartLibrary::getScript(
                            $this->chartType,
                            array(
                                "data" => $data,
                                "yLabel" => (!isset($_REQUEST["z"]))? substr($this->yLabel, 0, 10) : $this->yLabel,
                                "zoom" => $zoom
                            )
                        );

                        $javascriptChart = $arrayAux["javascript"];
                        $htmlChart = $arrayAux["html"];
                    }
                    break;
                case "PIE":
                case "FUN":
                    $arrayAux = ChartLibrary::getScript(
                        $this->chartType,
                        array(
                            "zoom" => $zoom,
                            "htmlNoRecords" => $htmlNoRecords,
                            "paramsName" => "node, chartType, swList, variableValue, sql, n, index",
                            "paramsUrlAjax" => "../advancedDashboards/dashletCustomDrillDownAjax.php",
                            "paramsAjax" => "
                                \"option\": \"CHARTDATA\",
                                \"node\": node,
                                \"chartType\": chartType,
                                \"swList\": swList,
                                \"variableValue\": variableValue,
                                \"sql\": sql,
                                \"n\": n,
                                \"index\": index
                            ",

                            "paramsNodeEvent" => "'\" + node + \"', '\" + chartType + \"', \" + swList + \", '\" + variableValue + \"', '\" + sql + \"', \" + n + \", \" + index + \"",

                            "paramsOnloadEvent" => "\"" . $this->title . "\", \"" . $this->chartType . "\", $swList, \"$variableValue\", \"$sql\", " . count($arraySql) . ", 0"
                        )
                    );

                    $javascriptChart = $arrayAux["javascript"];
                    $htmlChart = $arrayAux["html"];
                    break;
            }

            $bodyAtributes = null;

            switch ($this->chartType) {
                case "BAR":
                    $bodyAtributes = "style=\"overflow: hidden;\" class=\"ui-widget\"";
                    break;
                case "PIE":
                case "FUN":
                    $bodyAtributes = "style=\"overflow: hidden;\" class=\"ui-widget\"";
                    break;
            }

            $html = "
            <html xmlns=\"http://www.w3.org/1999/xhtml\">
            <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

                <title></title>

                " . ChartLibrary::getHeadTags($this->chartType) . "

                <script type=\"text/javascript\">
                function stringReplace(strSearch, strReplace, str)
                {
                    var expression = eval(\"/\" + strSearch + \"/g\");

                    return str.replace(expression, strReplace);
                }

                function divShow(option)
                {
                    document.getElementById(\"containerChart\").style.display = \"none\";
                    document.getElementById(\"containerGrid\").style.display = \"none\";

                    switch (option) {
                        case \"chart\": document.getElementById(\"containerChart\").style.display = \"inline\"; break;
                        case \"grid\":  document.getElementById(\"containerGrid\").style.display = \"inline\"; break;
                    }
                }

                var dataTableCase;

                function listGrid(grdTitle, fieldHead, sql)
                {
                    sql = stringReplace(\"@@doubleQuote\", \"\\\"\", sql)
                    sql = stringReplace(\"@@singleQuote\", \"'\", sql)

                    var arrayFieldHead = (fieldHead != \"\")? fieldHead.split(\",\") : [];

                    document.getElementById(\"containerResult\").style.display = \"none\";
                    document.getElementById(\"containerMessage\").style.display = \"none\";

                    document.getElementById(\"grdTitle\").innerHTML = grdTitle;

                    if (arrayFieldHead.length > 0) {
                        //var html = \"\";

                        //for (var i = 0; i <= arrayFieldHead.length - 1; i++) {
                        //    html = html + \"<th>\" + arrayFieldHead[i]  + \"</th>\";
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

                        //document.getElementById(\"tbody\").innerHTML = \"<tr><td colspan=\\\"\" + arrayFieldHead.length  + \"\\\" class=\\\"dataTables_empty\\\">" . G::LoadTranslation("ID_LOADING_GRID") . "</td></tr>\";

                        var tbody = document.getElementById(\"dataTableCase\").getElementsByTagName(\"tbody\")[0]; //<tbody>

                        var tr = tbody.getElementsByTagName(\"tr\");

                        for (var i = 0; i <= tr.length - 1; i++) {
                            tr[i].parentNode.removeChild(tr[i]);
                        }

                        var tr = document.createElement(\"tr\");
                        var texto = document.createTextNode(\"<td colspan=\\\"\" + arrayFieldHead.length  + \"\\\" class=\\\"dataTables_empty\\\">" . G::LoadTranslation("ID_LOADING_GRID") . "</td>\");
                        tr.appendChild(texto);

                        tbody.appendChild(tr);

                        ///////
                        if (typeof(dataTableCase) != \"undefined\") {
                            dataTableCase.fnDestroy();
                        }

                        \$.fn.dataTableExt.oPagination.iFullNumbersShowPages = 2;

                        dataTableCase = \$(\"#dataTableCase\").dataTable({
                            \"bProcessing\": true,
                            \"bServerSide\": true,
                            \"sAjaxSource\": \"../advancedDashboards/dashletCustomDrillDownAjax.php\",
                            \"bJQueryUI\": true,

                            \"sScrollX\": \"500px\", //width
                            \"sScrollY\": \"130px\", //height

                            \"bPaginate\": true,
                            \"sPaginationType\": \"full_numbers\",

                            \"sDom\": \"lfrt<'F'ip>T\", //OK
                            iDisplayLength: 20,
                            \"bLengthChange\": false, //remove comboBox, of size page
                            \"bFilter\": false, //quita el textBox search
                            \"bSort\": false,

                            \"fnServerData\": function (sSource, aoData, fnCallback) {
                                //Add some extra data to the sender
                                aoData.push({\"name\": \"option\", \"value\": \"GRIDDATA\"});
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
                    } else {
                        document.getElementById(\"containerMessage\").innerHTML = \"" . G::LoadTranslation("ID_NO_RECORDS_FOUND") . "\";

                        document.getElementById(\"containerMessage\").style.display = \"inline\";
                        divShow(\"grid\");
                    }
                }

                $javascriptChart
                </script>
            </head>
            <body $bodyAtributes>
            <div id=\"loading\">
                " . G::LoadTranslation("ID_LOADING_GRID") . "
            </div>

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
        } catch (Exception $e) {
            //Set html
            $html = "
            <html xmlns=\"http://www.w3.org/1999/xhtml\">
            <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

                <title></title>

                <link type=\"text/css\" rel=\"stylesheet\" href=\"/plugin/advancedDashboards/css-files/style.css\" />
            </head>
            <body>
            <h5><ins>Your query is not valid.</ins></h5>
            " . $e->getMessage() . "
            </body>
            </html>
            ";
        }

        echo $html;
    }
}

