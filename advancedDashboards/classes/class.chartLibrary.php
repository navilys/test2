<?php
class ChartLibrary
{
    public static function getHeadTags($chartType)
    {
        $htmlHeadTags = "
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/jquery-ui-1.8.17.custom.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/demo_table_jui.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/dataTablesCustom.css\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/style.css\" rel=\"stylesheet\" />

        <!--[if IE]>
        <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/dataTablesCustom.ie.css\" rel=\"stylesheet\" />
        <![endif]-->

        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery-latest.js\"></script>
        <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.dataTables.js\"></script>

        <style type=\"text/css\">
        #loading {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 5000;

            padding-left: 1.5em;

            background: url(/plugin/advancedDashboards/icons/Loading2.gif) no-repeat left top;
            font: normal 0.80em arial, verdana, helvetica, sans-serif;
        }
        </style>
        ";

        switch ($chartType) {
            case "BAR":
                $htmlHeadTags = $htmlHeadTags . "
                <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchart.css\" rel=\"stylesheet\" />
                <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchartCustom.css\" rel=\"stylesheet\" />

                <!--[if IE]>
                <link type=\"text/css\" href=\"/plugin/advancedDashboards/css-files/ddchartCustom.ie.css\" rel=\"stylesheet\" />
                <![endif]-->

                <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.tooltip.js\"></script>
                <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/js/jquery.ddchart.js\"></script>

                <style type=\"text/css\">
                .chart_loading {
                    position: absolute;
                    bottom: 0%;
                    left: 0%;
                    height: 10%;
                    width: 10%;
                    padding: 0;
                    margin: 0;
                    z-index: 1000;
                    text-align: center;
                }
                </style>
                ";
                break;
            case "PIE":
            case "FUN":
                $htmlHeadTags = $htmlHeadTags . "
                <link type=\"text/css\" rel=\"stylesheet\" href=\"/plugin/advancedDashboards/thirdparty/jquery.jqplot/jquery.jqplot.min.css\" />

                <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/thirdparty/jquery.jqplot/jquery.jqplot.min.js\"></script>
                ";

                switch ($chartType) {
                    case "PIE":
                        $htmlHeadTags = $htmlHeadTags . "<script type=\"text/javascript\" src=\"/plugin/advancedDashboards/thirdparty/jquery.jqplot/plugins/jqplot.pieRenderer.min.js\"></script>";
                        break;
                    case "FUN":
                        $htmlHeadTags = $htmlHeadTags . "<script type=\"text/javascript\" src=\"/plugin/advancedDashboards/thirdparty/jquery.jqplot/plugins/jqplot.funnelRenderer.min.js\"></script>";
                        break;
                }

                $htmlHeadTags = $htmlHeadTags . "
                <!--[if IE]>
                <script type=\"text/javascript\" src=\"/plugin/advancedDashboards/thirdparty/jquery.jqplot/excanvas.min.js\"></script>
                <![endif]-->

                <style type=\"text/css\">
                .jqplot-target {
                    color: #000000;

                    font: normal 12px arial, verdana, helvetica, sans-serif;
                }

                table.jqplot-table-legend {
                    z-index: 1000;
                    top: 0 !important;

                    /*
                    margin-top: 30px;
                    margin-bottom: 0;
                    */
                }

                tr.jqplot-table-legend {
                    /*height: 5px;*/
                    color: #000000;
                }

                tr.jqplot-table-legend a {
                    color: #000000;
                    text-decoration: none;
                }

                tr.jqplot-table-legend a:hover {
                    background: #F8DA4E;
                }

                #chartNode {
                    color: #000000;

                    font: bold 11px arial, verdana, helvetica, sans-serif;
                    text-align: center;
                }

                #chartNodeLink {
                    padding-top: 0.5em;

                    width: 85%;
                }

                #chartNodeLink a {
                    color: #000000;
                    text-decoration: none;
                }

                #chartNodeLink a:hover {
                    background: #F8DA4E;
                }

                #chartNodeZoom a {
                    display: block;
                    padding: 0.25em 0;

                    width: 55px;
                }

                #chartNodeZoom a:hover {
                    background: #F8DA4E;
                }

                #chartNodeZoom img {
                    border: 0;
                }

                #myToolTip {
                    display: none;
                    position: absolute;
                    z-index: 5000;

                    border: 1px solid #2E7190;
                    padding: 0.35em;

                    background: #FCFDFD;
                    color: #222222;

                    font: bold 18px arial, verdana, helvetica, sans-serif;
                    text-align: center;
                }
                </style>
                ";
                break;
        }

        return $htmlHeadTags;
    }

    public static function getScript($chartType, $arrayData)
    {
        $arrayScript = array();

        switch ($chartType) {
            case "BAR":
                $data = $arrayData["data"];
                $yLabel = $arrayData["yLabel"];
                $zoom = $arrayData["zoom"];

                $javascript = "
                var chartData = $data;

                \$(document).ready(
                    function ()
                    {
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
                            yLabel: \"$yLabel\",
                            zoom: \"$zoom\"
                        });

                        \$(document).on(
                            \"click\",
                            \".ddchart-x-axis-title\",
                            function ()
                            {
                                \$(this).find(\"span\").each(
                                    function ()
                                    {
                                        \$(this).css(\"background\", \"#FFFFFF\");
                                    }
                                );
                            }
                        );

                        \$(\".ddchart-x-axis-title\").delegate(
                            \"span.dd-chart-context-drillup\",
                            \"mouseover\",
                            function (e)
                            {
                                this.style.background = \"#F8DA4E\";
                            }
                        );
                        \$(\".ddchart-x-axis-title\").delegate(
                            \"span.dd-chart-context-drillup\",
                            \"mouseout\",
                            function (e)
                            {
                                this.style.background = \"#FFFFFF\";
                            }
                        );
                    }
                );
                ";

                $html = "
                <div style=\"margin-top: 0.25em; position: relative; width: 100%; height: 95%;\">
                    <div id=\"chart_div_static\" style=\"position: relative; height: 95%; width: 100%;\"></div>
                    <div id=\"loading-Notification_static\" class=\"chart_loading ui-widget ddchart-ui-widget-content ui-state-error\">" . G::LoadTranslation("ID_LOADING_GRID") . "</div>
                </div>
                ";

                $arrayScript["javascript"] = $javascript;
                $arrayScript["html"] = $html;
                break;
            case "PIE":
            case "FUN":
                $zoom = $arrayData["zoom"];
                $htmlNoRecords = $arrayData["htmlNoRecords"];

                $paramsName = $arrayData["paramsName"];
                $paramsUrlAjax = $arrayData["paramsUrlAjax"];
                $paramsAjax    = $arrayData["paramsAjax"];
                $paramsNodeEvent = $arrayData["paramsNodeEvent"];
                $paramsOnloadEvent = $arrayData["paramsOnloadEvent"];

                //$seriesColors = null;
                //
                //for ($i = 0; $i <= 500 - 1; $i++) {
                //    $seriesColors = $seriesColors . (($seriesColors != null)? ", " : null) . "\"#" . base_convert(rand(50, 255), 10, 16) . base_convert(rand(125, 255), 10, 16) . base_convert(rand(50, 255), 10, 16) . "\"";
                //}

                $javascript = "
                var nodeData = [];
                var mouseX = 0;
                var mouseY = 0;

                function plotDrawProcessAjax($paramsName)
                {
                    document.getElementById(\"loading\").style.display = \"inline\";

                    \$.ajax({
                        url: \"$paramsUrlAjax\",
                        type: \"POST\",
                        dataType: \"json\",
                        data: {
                            $paramsAjax
                        },
                        success: function (response)
                        {
                            if (response.status == \"OK\") {
                                if (response.data.length > 0) {
                                    //Chart data
                                    var chartData = [];
                                    var chartDataSerieColor = [];
                                    var chartDataSerieColorDefault = [
                                        \"#4BB2C5\", \"#EAA228\", \"#C5B47F\", \"#579575\", \"#839557\", \"#958C12\", \"#953579\", \"#4B5DE4\", \"#D8B83F\", \"#FF5800\", \"#0085CC\", \"#C747A3\", \"#CDDF54\", \"#FBD178\", \"#26B4E3\", \"#BD70C7\",
                                        \"#c2bf68\", \"#73c6dc\", \"#64ed99\", \"#e4ce6d\", \"#3fa756\", \"#b59950\", \"#5486c3\", \"#d4d057\", \"#cb9c59\", \"#fd8b8c\", \"#81e7f4\", \"#b78e99\", \"#94ae7c\", \"#fc9dfd\", \"#69a671\", \"#8ef99e\", \"#ac8cad\", \"#70f363\", \"#96d295\", \"#bdd1ab\", \"#498085\", \"#3dd5a0\", \"#a591ee\", \"#ef8e53\", \"#edb293\", \"#5eec89\", \"#cbb8a1\", \"#79e08e\", \"#aa9c47\", \"#40f5cb\", \"#b980d1\", \"#3f888e\", \"#aed1ae\", \"#9cc7cb\", \"#bdbb50\", \"#51d732\", \"#a9b58f\", \"#4ae35d\", \"#a7ac8f\", \"#bcb57e\", \"#878984\", \"#589196\", \"#b5e04d\", \"#64a1c2\", \"#fdfa56\", \"#4e8be4\", \"#4fd770\", \"#ace742\", \"#d8ae8e\", \"#6883e7\", \"#b5bafb\", \"#3ad24e\", \"#9ea3eb\", \"#bac356\", \"#7cc14e\", \"#a1d365\", \"#85e6f4\", \"#c3b1cd\", \"#d4984d\", \"#62bb58\", \"#4a8bb8\", \"#459071\", \"#62d5ad\", \"#4da94e\", \"#71d9bb\", \"#8e9c75\", \"#c1d14d\", \"#b5aba0\", \"#8290cb\", \"#9eaf5f\", \"#c4bf76\", \"#7ccb95\", \"#bbea53\", \"#69fb99\", \"#85a15c\", \"#40db8d\", \"#84b445\", \"#9f848e\", \"#40b8ad\", \"#dafdfc\", \"#39d796\", \"#7e8343\", \"#e2dbef\", \"#35fee8\", \"#9cb053\", \"#c6bae8\", \"#54ee71\", \"#68b17e\", \"#c4badb\", \"#72a2d7\", \"#6ea797\", \"#d2d8a2\", \"#e4c568\", \"#d3c766\", \"#bc88b7\", \"#dee649\", \"#c7fcfb\", \"#389c80\", \"#85f9e0\", \"#609f4d\", \"#37c590\", \"#9da851\", \"#3f97c2\", \"#76fe69\", \"#aad37b\", \"#62bd53\", \"#79994e\", \"#759e7f\", \"#c3d275\", \"#a4f0ab\", \"#c0f450\", \"#50b595\", \"#6fbebe\", \"#32eabb\", \"#6ab474\", \"#b3d2da\", \"#d47d39\", \"#f1a86d\", \"#7082f4\", \"#b4cbdc\", \"#60a2c9\", \"#7eb655\", \"#e1ddbc\", \"#9fdd99\", \"#5a7ef0\", \"#9dd0a9\", \"#77b5aa\", \"#7facef\", \"#bad4f7\", \"#aea4a4\", \"#8bc1e0\", \"#55f26c\", \"#78df35\", \"#34a1ce\", \"#9cbbd0\", \"#8c7d86\", \"#35a9df\", \"#aeda5b\", \"#9daee4\", \"#95fd54\", \"#3ab3c0\", \"#e8c9aa\", \"#54f677\", \"#58f7b1\", \"#f4b845\", \"#c5f145\", \"#4cf48b\", \"#fac050\", \"#55819d\", \"#3ac099\", \"#5dc5ee\", \"#ebb799\", \"#96cd8a\", \"#dbe57d\", \"#8cdeda\", \"#a0b9c4\", \"#b4cab1\", \"#3fc64d\", \"#5edd55\", \"#c9e2c0\", \"#63fe64\", \"#52f1c0\", \"#b9ae6f\", \"#439746\", \"#8ed1df\", \"#699470\", \"#fbe7ea\", \"#adf090\", \"#c98959\", \"#ece9fa\", \"#ac85f7\", \"#df9ae0\", \"#9ff060\", \"#ddfb8a\", \"#f1b340\", \"#d1d665\", \"#42d33e\", \"#fb9ff3\", \"#8bff38\", \"#b3f2e3\", \"#adbdf1\", \"#a5a951\", \"#86ee38\", \"#b4d8ff\", \"#3fcf87\", \"#4eb246\", \"#81bdcf\", \"#8db936\", \"#81f235\", \"#87c2ef\", \"#6b8d87\", \"#5cd6cc\", \"#7b89b1\", \"#82dc74\", \"#82e5f6\", \"#d7f67c\", \"#eca6e1\", \"#bbe073\", \"#c0905e\", \"#c3c6cb\", \"#b2ebe5\", \"#3a83a4\", \"#d4b1b7\", \"#86e57f\", \"#c99555\", \"#bffe47\", \"#3cf288\", \"#ebc656\", \"#5f9e74\", \"#8bfae8\", \"#56c9c7\", \"#3cced1\", \"#aeb356\", \"#66e9fa\", \"#b4c652\", \"#d89d50\", \"#eda43b\", \"#7597af\", \"#99b3e3\", \"#dcecda\", \"#c58185\", \"#8d8738\", \"#5fd78e\", \"#83f86a\", \"#7ec8de\", \"#9faf43\", \"#bda380\", \"#c6cdaa\", \"#758c32\", \"#58f9e2\", \"#33d4e9\", \"#878bfa\", \"#8ea8ba\", \"#eadcaf\", \"#558a57\", \"#33cfa6\", \"#44a5e2\", \"#938093\", \"#3dacac\", \"#3ec4a2\", \"#efc65e\", \"#d9fc75\", \"#d4b4b9\", \"#8ea681\", \"#3ebd96\", \"#64bd4a\", \"#d8c989\", \"#bb848f\", \"#4f8cd9\", \"#c9947c\", \"#6c89ef\", \"#98f4ea\", \"#dcd872\", \"#9590b4\", \"#e5984a\", \"#7cb7b0\", \"#949f5b\", \"#ecf668\", \"#7b8680\", \"#55e6a5\", \"#9f88b9\", \"#8fcaa6\", \"#79b368\", \"#baf386\", \"#6ee2b1\", \"#878e3f\", \"#38cd75\", \"#62c066\", \"#98ef75\", \"#e6824d\", \"#8bc860\", \"#4481d9\", \"#b9ae61\", \"#ef824d\", \"#76a8ec\", \"#f5de3a\", \"#35e2b9\", \"#787e55\", \"#adbf3b\", \"#f0af45\", \"#3ee8bc\", \"#6cf4c3\", \"#46c743\", \"#75bc4b\", \"#91e890\", \"#7de15c\", \"#86e3fc\", \"#3f8dff\", \"#63dc99\", \"#6cd2e8\", \"#80dac3\", \"#3cffb0\", \"#cd8957\", \"#deb4bb\", \"#f8f196\", \"#889e66\", \"#b3d43a\", \"#afdc54\", \"#aefceb\", \"#489ea3\", \"#ffd068\", \"#c2d768\", \"#72b77c\", \"#98a2d4\", \"#549dbd\", \"#b8d4f2\", \"#eda3ad\", \"#f6f376\", \"#4ac070\", \"#36cea5\", \"#a7cd5a\", \"#dea6e8\", \"#47cf75\", \"#918db0\", \"#65a3e3\", \"#f1f99e\", \"#e3eddb\", \"#90e7c8\", \"#d5f663\", \"#45f9e3\", \"#b8c194\", \"#e0acd6\", \"#c8b98a\", \"#3ef6a5\", \"#bc94e1\", \"#9f8bd7\", \"#3efbba\", \"#e8b592\", \"#b09984\", \"#e1a67a\", \"#c5fbe6\", \"#5ae862\", \"#fec5c2\", \"#89cdb3\", \"#fda2d8\", \"#dee8ee\", \"#b6f0e8\", \"#70e172\", \"#d1aea0\", \"#559be1\", \"#9ef8da\", \"#848fb4\", \"#b48e58\", \"#77c6d5\", \"#f8c442\", \"#d0afeb\", \"#bf80d2\", \"#a7a8a1\", \"#e88aef\", \"#88a050\", \"#69e544\", \"#439761\", \"#c6ea7d\", \"#ec93f1\", \"#c28e93\", \"#d2f3e3\", \"#bdcae8\", \"#90925e\", \"#ff8373\", \"#eebaac\", \"#3fdd82\", \"#52e8ab\", \"#81c489\", \"#cdb7ac\", \"#be90c8\", \"#52f6b5\", \"#35cc61\", \"#ec8583\", \"#4b858d\", \"#8cfdee\", \"#3882b7\", \"#899792\", \"#34ca34\", \"#8caa90\", \"#3980af\", \"#d095a0\", \"#86974f\", \"#b68a5d\", \"#399a6a\", \"#95d467\", \"#83d870\", \"#3b8d99\", \"#9c8e44\", \"#9ec88b\", \"#fdcd91\", \"#acafb7\", \"#4ce4e0\", \"#6ab5f6\", \"#96ba57\", \"#cffae0\", \"#36aba2\", \"#74b1bc\", \"#dcf4d8\", \"#efb681\", \"#7ab4ff\", \"#da7f80\", \"#919056\", \"#71b4af\", \"#68f442\", \"#8dd539\", \"#6ed882\", \"#df7fd4\", \"#9bebc2\", \"#73e14f\", \"#c38ca7\", \"#c2f7ab\", \"#43b1ca\", \"#68d954\", \"#e5fc42\", \"#f6b3cd\", \"#fdd98f\", \"#80c593\", \"#548573\", \"#e5af43\", \"#34885b\", \"#aae44e\", \"#55f0a0\", \"#ee8f63\", \"#437e5d\", \"#53fbb3\", \"#effa77\", \"#7ea9e9\", \"#e0bff6\", \"#53ae77\", \"#64af89\", \"#8efc5e\", \"#aa8f44\", \"#4b8461\", \"#7c8f64\", \"#a8a45d\", \"#5c9a54\", \"#a1cb99\", \"#8bb633\", \"#81cc80\", \"#c6ecd0\", \"#4fa4ca\", \"#7bf1e7\", \"#8e7ef3\", \"#bead42\", \"#f1f980\", \"#4e91ae\", \"#70d85b\", \"#d88eb6\", \"#d9c065\", \"#5a9b46\", \"#f8ae84\", \"#c2dd6d\", \"#aa956f\", \"#9deebc\", \"#aee5b2\", \"#fcf7d2\", \"#ab9c93\", \"#d583ae\", \"#8bed4a\", \"#bf847b\", \"#d380c9\", \"#58dc92\", \"#94a5b8\", \"#d2ea9d\", \"#8eb673\", \"#40b465\", \"#e07e96\", \"#74e6a0\", \"#f19c83\", \"#3bf68f\", \"#84da94\", \"#4df25b\", \"#aeae9c\", \"#67917a\", \"#d3ccd4\", \"#46d55d\", \"#7ac160\", \"#deec38\", \"#7fe269\", \"#d0e95a\", \"#5f9aed\", \"#c2acd8\", \"#ebfb58\", \"#889a78\", \"#d07ef4\", \"#a48bb1\", \"#d0b94f\", \"#fea4fe\", \"#36d5d0\", \"#6eb7ac\", \"#97d4da\", \"#85ad57\", \"#5da050\", \"#84d77f\", \"#cab882\", \"#be7e99\", \"#70e3f8\", \"#8de268\", \"#8be5f3\", \"#5c8981\", \"#d6c93d\", \"#b1fe88\", \"#d697c0\", \"#f5cc80\", \"#75a9de\"
                                    ];
                                    var arrayAux = [];
                                    var i = 0;

                                    for (i = 0; i <= response.data.length - 1; i++) {
                                        chartData.push([response.data[i].label, parseInt(response.data[i].value), response.data[i].event, response.data[i].label2]);

                                        if (response.data[i].serieColor) {
                                            arrayAux.push({n: parseInt(response.data[i].value), color: response.data[i].serieColor});
                                        }
                                    }
                ";

                switch ($chartType) {
                    case "PIE":
                        $javascript = $javascript . "
                        ";
                        break;
                    case "FUN":
                        $javascript = $javascript . "
                                    arrayAux.sort(function (obj1, obj2) { return obj2.n - obj1.n; });
                        ";
                        break;
                }

                $javascript = $javascript . "
                                    for (i = 0; i <= arrayAux.length - 1; i++) {
                                        chartDataSerieColor.push(arrayAux[i].color);
                                    }

                                    var arraySeriesColors = (chartDataSerieColor.length > 0)? chartDataSerieColor : chartDataSerieColorDefault;

                                    //Node data
                                    nodeData[index] = [node, \"plotDrawProcessAjax($paramsNodeEvent);\"];
                                    var nodeLink = \"\";

                                    if (index > 0) {
                                        for (i = 0; i <= index - 1; i++) {
                                            nodeLink = nodeLink + ((nodeLink != \"\")? \" > \" : \"\") + \"<a href=\\\"javascript:;\\\" onclick=\\\"\" + nodeData[i][1] + \" return false;\\\">\" + nodeData[i][0] + \"</a>\";
                                        }
                                    }

                                    nodeLink = nodeLink + ((nodeLink != \"\")? \" > \" : \"\") + nodeData[index][0];

                                    var htmlChart = \"<div style='background: #FFFFFF;'>\";
                                    htmlChart = htmlChart + \"<div id='chart' style='overflow-x: hidden; overflow-y: auto; width: 99%; height: 85%;'></div>\";
                                    htmlChart = htmlChart + \"<div id='chartNode'>\";
                                    htmlChart = htmlChart + \"    <div id='chartNodeZoom' class='floatl'>$zoom</div>\";
                                    htmlChart = htmlChart + \"    <div id='chartNodeLink' class='floatl'>\" + nodeLink + \"</div>\";
                                    htmlChart = htmlChart + \"    <div class='clearf'></div>\";
                                    htmlChart = htmlChart + \"</div>\";
                                    htmlChart = htmlChart + \"<div id='myToolTip'></div>\";
                                    htmlChart = htmlChart + \"</div>\";

                                    document.getElementById(\"containerChart\").innerHTML = htmlChart;

                                    //Draw chart
                                    var plot = \$.jqplot(
                                        \"chart\",
                                        [chartData],
                                        {
                                            title: \"\",
                                            seriesColors: arraySeriesColors,

                                            grid: {
                                                background: \"#FFFFFF\",
                                                borderWidth: 0,
                                                shadow: false
                                            },
                                            gridPadding: {
                                                top: 0,
                                                bottom: 0,
                                                left: 0,
                                                right: 0
                                            },
                                            legend: {
                                                show: true,
                                                location: \"e\", //\"w\"
                                                border: \"none\",
                                                textColor: \"#FF0000\",
                                                fontSize: \"10px\"
                                            },
                ";

                switch ($chartType) {
                    case "PIE":
                        $javascript = $javascript . "
                                            seriesDefaults: {
                                                shadow: true,
                                                renderer: \$.jqplot.PieRenderer,
                                                rendererOptions: {
                                                    showDataLabels: true,
                                                    //dataLabels: \"value\",
                                                    //dataLabelFormatString: \"%s cases\",
                                                    dataLabels: \"value\",
                                                    dataLabelFormatString: \"%s\",
                                                    sliceMargin: 5
                                                }
                                            }
                        ";
                        break;
                    case "FUN":
                        $javascript = $javascript . "
                                            seriesDefaults: {
                                                shadow: true,
                                                renderer: \$.jqplot.FunnelRenderer,
                                                rendererOptions: {
                                                    showDataLabels: true,
                                                    dataLabels: \"value\",
                                                    dataLabelFormatString: \"%s\"
                                                }
                                            }
                        ";
                        break;
                }

                $javascript = $javascript . "
                                        }
                                    );

                                    \$(\"#chart\").bind(
                                        \"jqplotDataClick\",
                                        function (evt, seriesIndex, pointIndex, data)
                                        {
                                            eval(data[2]);
                                        }
                                    );

                                    \$(\"#chart\").bind(
                                        \"jqplotDataHighlight\",
                                        function (evt, seriesIndex, pointIndex, data)
                                        {
                                            if (\$(\"#myToolTip\").is(\":hidden\")) {
                                                \$(\"#myToolTip\").html(data[3] + \"<br />\" + data[1]).css({left: mouseX + 5, top: mouseY}).show();
                                            }
                                        }
                                    );

                                    \$(\"#chart\").bind(
                                        \"jqplotDataUnhighlight\",
                                        function (evt, seriesIndex, pointIndex, data)
                                        {
                                            \$(\"#myToolTip\").hide();
                                        }
                                    );
                                } else {
                                    document.getElementById(\"containerChart\").innerHTML = \"$htmlNoRecords\";
                                }
                            } else {
                                document.body.innerHTML = \"<div id='loading' style='display: none;'></div><h5><ins>Your query is not valid.</ins></h5>\" + response.message;
                            }

                            document.getElementById(\"loading\").style.display = \"none\";
                        }
                    });
                }

                \$(document).mousemove(
                    function (e)
                    {
                        mouseX = e.pageX;
                        mouseY = e.pageY;
                    }
                );

                \$(document).ready(
                    function ()
                    {
                        plotDrawProcessAjax($paramsOnloadEvent);
                    }
                );
                ";

                $html = null;

                $arrayScript["javascript"] = $javascript;
                $arrayScript["html"] = $html;
                break;
        }

        return $arrayScript;
    }

    public static function tableLegendLabel($label, $value, $event)
    {
        $l = substr($label, 0, 40);

        return "<a href=\"javascript:;\" onclick=\"$event return false;\" onmouseover=\"if (\$('#myToolTip').is(':hidden')) { \$('#myToolTip').html('" . htmlentities($label, ENT_QUOTES, "UTF-8") . "' + '<br />' + $value).css({left: mouseX + 5, top: mouseY + 5}).show(); }\" onmouseout=\"\$('#myToolTip').hide();\">" . htmlentities($l, ENT_QUOTES, "UTF-8") . "</a>" . str_repeat("&nbsp;", 40 - strlen($l));
    }

    public static function casesDrillDownData($node, $chartType, $sequence, $index, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
    {
        switch ($chartType) {
            case "BAR":
                $caseData = null;
                break;
            case "PIE":
            case "FUN":
                $arrayCaseData = array();
                break;
        }

        $arraySequence = explode("/", strtoupper($sequence));
        $swRecursion = ($index == count($arraySequence) - 1)? 0 : 1;

        $configData = serialize($arrayConfigData);
        $configData = str_replace("\"", "@@doubleQuote", $configData);
        $configData = str_replace("'",  "@@singleQuote", $configData);

        if ($arraySequence[$index] != "STATUS") {
            $result = CaseLibrary::caseData(0, $arraySequence[$index], $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

            for ($i = 0; $i <= count($result) - 1; $i++) {
                $fieldUid = $result[$i][0];
                $fieldName = $result[$i][1];
                $fieldNumRec = $result[$i][2];

                $auxProcessUid = $processUid;
                $auxTaskUid  = $taskUid;
                $auxUserUid  = $userUid;
                $auxGroupUid = $groupUid;
                $auxDepartmentUid = $departmentUid;

                switch ($arraySequence[$index]) {
                    case "PROCESS":
                        $auxProcessUid = $fieldUid;
                        break;
                    case "TASK":
                        $auxTaskUid = $fieldUid;
                        break;
                    case "USER":
                        $auxUserUid = $fieldUid;
                        break;
                }

                //When is USER, Cases unassigned, not have user
                if (empty($fieldUid)) {
                    $swRecursion = 0;
                }

                switch ($chartType) {
                    case "BAR":
                        $caseDataAux = ($swRecursion == 1)? self::casesDrillDownData($fieldName, $chartType, $sequence, $index + 1, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $auxProcessUid, $auxTaskUid, $auxUserUid, $auxGroupUid, $auxDepartmentUid) : "\"caseGrid('$fieldName', '$status', '$dateIni', '$dateEnd', '$configData', '$auxProcessUid', '$auxTaskUid', '$auxUserUid', '$auxGroupUid', '$auxDepartmentUid');\"";

                        $caseData = $caseData . (($caseData != null)? ", " : null) . "[\"$node\", \"$fieldName\", $fieldNumRec, \"$fieldName ^ \"" . (($caseDataAux != null)? ", " . $caseDataAux : null) . "]";
                        break;
                    case "PIE":
                    case "FUN":
                        $event = ($swRecursion == 1)? "plotDrawProcessAjax('$fieldName', '$chartType', '$sequence', " . ($index + 1) . ", '$status', '$delStatus', '$dateIni', '$dateEnd', '$configData', '$auxProcessUid', '$auxTaskUid', '$auxUserUid', '$auxGroupUid', '$auxDepartmentUid');" : "caseGrid('$fieldName', '$status', '$dateIni', '$dateEnd', '$configData', '$auxProcessUid', '$auxTaskUid', '$auxUserUid', '$auxGroupUid', '$auxDepartmentUid');";

                        $arrayCaseData[] = array(
                            "label"  => self::tableLegendLabel($fieldName, $fieldNumRec, $event),
                            "value"  => $fieldNumRec,
                            "event"  => $event,
                            "label2" => $fieldName
                        );
                        break;
                }
            }
        } else {
            //$arrayConfigData["caseType"]
            $arrayStatus = $arrayConfigData["status"];

            for ($i = 0; $i <= count($arrayStatus) - 1; $i++) {
                $result = CaseLibrary::caseData(0, "CASE", $arrayStatus[$i][0], $arrayStatus[$i][0], $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

                if ($result[0] > 0) {
                    switch ($chartType) {
                        case "BAR":
                            $caseDataAux = ($swRecursion == 1)? self::casesDrillDownData($arrayStatus[$i][1], $chartType, $sequence, $index + 1, $arrayStatus[$i][0], $arrayStatus[$i][0], $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid) : "\"caseGrid('" . $arrayStatus[$i][1] . "', '" . $arrayStatus[$i][0] . "', '$dateIni', '$dateEnd', '$configData', '$processUid', '$taskUid', '$userUid', '$groupUid', '$departmentUid');\"";

                            $caseData = $caseData . (($caseData != null)? ", " : null) . "[\"$node\", \"" . $arrayStatus[$i][1] . "\", " . $result[0] . ", \"" . $arrayStatus[$i][1] . " ^ \"" . (($caseDataAux != null)? ", " . $caseDataAux : null) . "]";
                            break;
                        case "PIE":
                        case "FUN":
                            $event = ($swRecursion == 1)? "plotDrawProcessAjax('" . $arrayStatus[$i][1] . "', '$chartType', '$sequence', " . ($index + 1) . ", '" . $arrayStatus[$i][0] . "', '" . $arrayStatus[$i][0] . "', '$dateIni', '$dateEnd', '$configData', '$processUid', '$taskUid', '$userUid', '$groupUid', '$departmentUid');" : "caseGrid('" . $arrayStatus[$i][1] . "', '" . $arrayStatus[$i][0] . "', '$dateIni', '$dateEnd', '$configData', '$processUid', '$taskUid', '$userUid', '$groupUid', '$departmentUid');";

                            $arrayAux = array(
                                "label"  => self::tableLegendLabel($arrayStatus[$i][1], $result[0], $event),
                                "value"  => $result[0],
                                "event"  => $event,
                                "label2" => $arrayStatus[$i][1]
                            );

                            if (count($arrayConfigData["statusColor"]) > 0) {
                                $arrayAux["serieColor"] = $arrayConfigData["statusColor"][$arrayStatus[$i][0]];
                            }

                            $arrayCaseData[] = $arrayAux;
                            break;
                    }
                }
            }
        }

        switch ($chartType) {
            case "BAR":
                if ($caseData != null) {
                    $caseData = "
                    {
                        \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"" . (($swRecursion == 1)? "DRILL_DATA" : "EVENT_CLICK") . "\"],
                        \"DATA\": [$caseData]
                    }";
                }

                return $caseData;
                break;
            case "PIE":
            case "FUN":
                return $arrayCaseData;
                break;
        }
    }

    public static function casesByDrillDownData2($node, $index, $chartType, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
    {
        switch ($chartType) {
            case "BAR":
                $caseData = null;
                break;
            case "PIE":
            case "FUN":
                $arrayCaseData = array();
                break;
        }

        $configData = serialize($arrayConfigData);
        $configData = str_replace("\"", "@@doubleQuote", $configData);
        $configData = str_replace("'",  "@@singleQuote", $configData);

        //$arrayConfigData["caseType"]
        $arrayStatus = $arrayConfigData["status"];

        for ($i = 0; $i <= count($arrayStatus) - 1; $i++) {
            $result = CaseLibrary::caseData(0, $category, $arrayStatus[$i][0], $arrayStatus[$i][0], $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

            if ($result[0] > 0) {
                $event = "caseGrid('" . $arrayStatus[$i][1] . "', '" . (($category == "CASE")? null : $category) . "', '" . $arrayStatus[$i][0] . "', '$dateIni', '$dateEnd', '$configData', '$processUid', '$taskUid', '$userUid', '$groupUid', '$departmentUid');";

                switch ($chartType) {
                    case "BAR":
                        $caseData = $caseData . (($caseData != null)? ", " : null) . "[\"$node\", \"" . $arrayStatus[$i][1] . "\", " . $result[0] . ", \"" . $arrayStatus[$i][1] . " ^ \", \"$event\"]";
                        break;
                    case "PIE":
                    case "FUN":
                        $arrayAux = array(
                            "label"  => self::tableLegendLabel($arrayStatus[$i][1], $result[0], $event),
                            "value"  => $result[0],
                            "event"  => $event,
                            "label2" => $arrayStatus[$i][1]
                        );

                        if (count($arrayConfigData["statusColor"]) > 0) {
                            $arrayAux["serieColor"] = $arrayConfigData["statusColor"][$arrayStatus[$i][0]];
                        }

                        $arrayCaseData[] = $arrayAux;
                        break;
                }
            }
        }

        switch ($chartType) {
            case "BAR":
                return $caseData;
                break;
            case "PIE":
            case "FUN":
                return $arrayCaseData;
                break;
        }
    }

    public static function casesByDrillDownData($node, $index, $chartType, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid)
    {
        switch ($chartType) {
            case "BAR":
                $caseData = null;
                break;
            case "PIE":
            case "FUN":
                $arrayCaseData = array();
                break;
        }

        $configData = serialize($arrayConfigData);
        $configData = str_replace("\"", "@@doubleQuote", $configData);
        $configData = str_replace("'",  "@@singleQuote", $configData);

        if ($category != "ACCOMPLISHMENT") {
            $result = CaseLibrary::caseData(0, $category, $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

            for ($i = 0; $i <= count($result) - 1; $i++) {
                $fieldUid = $result[$i][0];
                $fieldName = $result[$i][1];
                $fieldNumRec = $result[$i][2];

                $auxProcessUid = null;
                $auxTaskUid = null;
                $auxUserUid = null;
                $auxGroupUid = null;
                $auxDepartmentUid = null;

                switch ($category) {
                    case "PROCESS":
                        $auxProcessUid = $fieldUid;
                        $auxUserUid = $userUid;
                        break;
                    case "USER":
                        $auxUserUid = $fieldUid;
                        break;
                    case "GROUP":
                        $auxGroupUid = $fieldUid;
                        $auxUserUid = $userUid;
                        break;
                    case "DEPARTMENT":
                        $auxDepartmentUid = $fieldUid;
                        $auxUserUid = $userUid;
                        break;
                }

                //When is USER, Cases unassigned, not have user
                $eventUnassigned = "caseGrid('$fieldName', '', 'UNASSIGNED', '$dateIni', '$dateEnd', '$configData', '$auxProcessUid', '$auxTaskUid', '$auxUserUid', '$auxGroupUid', '$auxDepartmentUid');";

                switch ($chartType) {
                    case "BAR":
                        $caseDataAux = null;

                        if (!empty($fieldUid)) {
                            $caseDataAux = self::casesByDrillDownData2($fieldName, $index + 1, $chartType, "CASE", null, null, $dateIni, $dateEnd, $arrayConfigData, $auxProcessUid, $auxTaskUid, $auxUserUid, $auxGroupUid, $auxDepartmentUid);
                        } else {
                            $caseDataAux = "[\"$fieldName\", \"" . G::LoadTranslation("ID_UNASSIGNED") . "\", $fieldNumRec, \"" . G::LoadTranslation("ID_UNASSIGNED") . " ^ \", \"$eventUnassigned\"]";
                        }

                        $caseData = $caseData . (($caseData != null)? ", " : null) . "[
                            \"$node\", \"$fieldName\", $fieldNumRec, \"$fieldName ^ \",
                            {
                                \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"EVENT_CLICK\"],
                                \"DATA\": [$caseDataAux]
                            }
                        ]";
                        break;
                    case "PIE":
                    case "FUN":
                        if (!empty($fieldUid)) {
                            $event = "plotDrawProcessAjax('$fieldName', " . ($index + 1) . ", '$chartType', 'CASE', '', '', '$dateIni', '$dateEnd', '$configData', '$auxProcessUid', '$auxTaskUid', '$auxUserUid', '$auxGroupUid', '$auxDepartmentUid');";

                            $arrayCaseData[] = array(
                                "label"  => self::tableLegendLabel($fieldName, $fieldNumRec, $event),
                                "value"  => $fieldNumRec,
                                "event"  => $event,
                                "label2" => $fieldName
                            );
                        } else {
                            $arrayCaseData[] = array(
                                "label"  => self::tableLegendLabel($fieldName, $fieldNumRec, $eventUnassigned),
                                "value"  => $fieldNumRec,
                                "event"  => $eventUnassigned,
                                "label2" => $fieldName
                            );
                        }
                        break;
                }
            }
        } else {
            $arrayAccomp = array("OVERDUE", "OVERDUENOT");

            for ($i = 0; $i <= count($arrayAccomp) - 1; $i++) {
                $fieldName = null;

                switch ($arrayAccomp[$i]) {
                    case "OVERDUE":
                        $fieldName = "Overdue";
                        break;
                    case "OVERDUENOT":
                        $fieldName = "On Schedule";
                        break;
                }

                $result = CaseLibrary::caseData(0, $arrayAccomp[$i], $status, $delStatus, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

                if ($result[0] > 0) {
                    switch ($chartType) {
                        case "BAR":
                            $caseDataAux = self::casesByDrillDownData2($fieldName, $index + 1, $chartType, $arrayAccomp[$i], null, null, $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

                            $caseData = $caseData . (($caseData != null)? ", " : null) . "[
                                \"$node\", \"$fieldName\", " . $result[0] . ", \"$fieldName ^ \",
                                {
                                    \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"EVENT_CLICK\"],
                                    \"DATA\": [$caseDataAux]
                                }
                            ]";
                            break;
                        case "PIE":
                        case "FUN":
                            $event = "plotDrawProcessAjax('$fieldName', " . ($index + 1) . ", '$chartType', '" . $arrayAccomp[$i] . "', '', '', '$dateIni', '$dateEnd', '$configData', '$processUid', '$taskUid', '$userUid', '$groupUid', '$departmentUid');";

                            $arrayCaseData[] = array(
                                "label"  => self::tableLegendLabel($fieldName, $result[0], $event),
                                "value"  => $result[0],
                                "event"  => $event,
                                "label2" => $fieldName
                            );
                            break;
                    }
                }
            }
        }

        switch ($chartType) {
            case "BAR":
                if ($caseData != null) {
                    $caseData = "
                    {
                        \"COLUMNS\": [\"CONTEXT\", \"X_BAR_LABEL\", \"X_BAR_VALUE\", \"TOOL_TIP_TITLE\", \"DRILL_DATA\"],
                        \"DATA\": [$caseData]
                    }";
                }

                return $caseData;
                break;
            case "PIE":
            case "FUN":
                return $arrayCaseData;
                break;
        }
    }

    public static function customDrillDownData($node, $chartType, $swList, $variableValue, $sql, $n, $index, $stmt)
    {
        $variableValue = str_replace("@@doubleQuote", "\"", $variableValue);
        $variableValue = str_replace("@@singleQuote", "'",  $variableValue);
        $variableValue = unserialize($variableValue);

        $arraySql = explode(";", $sql);

        $varReplace = array();
        $varReplace["USER_LOGGED"]  = $_SESSION["USER_LOGGED"];
        $varReplace["USR_USERNAME"] = $_SESSION["USR_USERNAME"];

        foreach ($variableValue as $key => $value) {
            $varReplace[$key] = $value;
        }

        if ($index == ($n - 1) + 1) {
            switch ($chartType) {
                case "BAR":
                    $data = null;

                    return array(0, null, $data);
                    break;
                case "PIE":
                case "FUN":
                    $arrayData = array();

                    return array(0, null, $arrayData);
                    break;
            }
        } else {
            if ($index == $n - 1 && $swList == 1) {
                //Last SQL
                $sqlx = str_replace("@@doubleQuote", "\"", $arraySql[$index]);
                $sqlx = str_replace("@@singleQuote", "'", $arraySql[$index]);
                $sqlx = G::replaceDataField($sqlx, $varReplace);

                $rsSqlx = $stmt->executeQuery($sqlx, ResultSet::FETCHMODE_ASSOC);

                ///////
                $fieldHead = null;

                if ($rsSqlx->getRecordCount() > 0) {
                    $rsSqlx->next();

                    $row = $rsSqlx->getRow();

                    ///////
                    $arrayKey = array_keys($row);

                    for ($i = 0; $i <= count($arrayKey) - 1; $i++) {
                        $fieldHead = $fieldHead . (($i > 0)? "," : null) . $arrayKey[$i];
                    }
                }

                ///////
                //$sql = str_replace(array("\n", "\r"), array(" ", " "), $sql);
                //$sql = str_replace("\"", "@@singleQuote", $sql);
                //$sql = str_replace("'",  "@@singleQuote", $sql);

                //preg_match("/^SELECT(.*)FROM.*$/i", $sql, $matches);
                //$sqlSelect = null;

                //if (count($matches) > 0) {
                //    $sqlSelect = str_replace(""@@singleQuote"", "'", $matches[1]);
                //}

                $sqlx = str_replace("\"", "@@doubleQuote", $sqlx);
                $sqlx = str_replace("'", "@@singleQuote", $sqlx);

                return array(0, null, "\"listGrid('$node', '$fieldHead', '$sqlx');\"");
            } else {
                $error = 0;
                $errorMessage = null;

                switch ($chartType) {
                    case "BAR":
                        $data = null;
                        break;
                    case "PIE":
                    case "FUN":
                        $arrayData = array();
                        break;
                }

                try {
                    $sqlx = str_replace("@@doubleQuote", "\"", $arraySql[$index]);
                    $sqlx = str_replace("@@singleQuote", "'", $arraySql[$index]);
                    $sqlx = G::replaceDataField($sqlx, $varReplace);

                    $rsSqlx = $stmt->executeQuery($sqlx, ResultSet::FETCHMODE_ASSOC);

                    if ($rsSqlx->getRecordCount() > 0) {
                        $swSqlx2 = (isset($arraySql[$index + 1]) && $arraySql[$index + 1] != null)? 1 : 0;
                        $sw = 0;

                        while ($rsSqlx->next()) {
                            $row = $rsSqlx->getRow();

                            //Set values
                            $variableValueAux = $row;  //array //array_keys($array)   //Get keys
                            $row = array_values($row); //array //array_values($array) //Keys numerics (0, 1, ...)

                            $variableValueAux = serialize($variableValueAux);
                            $variableValueAux = str_replace("\"", "@@doubleQuote", $variableValueAux);
                            $variableValueAux = str_replace("'",  "@@singleQuote", $variableValueAux);

                            //Get values
                            $fieldLabel = null;
                            $fieldValue = 0;

                            if (isset($row[0]) && !empty($row[0])) {
                                $fieldLabel = $row[0];
                            } else {
                                throw (new Exception("Query " . ($index + 1) . ", first field doesn't exist."));
                            }

                            if (isset($row[1]) && !empty($row[1]) && preg_match("/^\d+$/", $row[1])) {
                                $fieldValue = intval($row[1]);
                            } else {
                                throw (new Exception("Query " . ($index + 1) . ", second field doesn't exist or it isn't a number"));
                            }

                            ///////
                            if ($fieldValue > 0) {
                                switch ($chartType) {
                                    case "BAR":
                                        $answer = array();

                                        if ($swSqlx2 == 1) {
                                            $answer = self::customDrillDownData($fieldLabel, $chartType, $swList, $variableValueAux, $sql, $n, $index + 1, $stmt);

                                            if ($answer[0] == 1) {
                                                throw (new Exception($answer[1]));
                                            }
                                        }

                                        $dataAux = (isset($answer[2]))? $answer[2] : null;

                                        $data = $data . (($sw == 1)? ", " : null) . "[\"$node\", \"$fieldLabel\", $fieldValue, \"$fieldLabel ^ \"" . (($dataAux != null)? ", $dataAux" : null) . "]";
                                        break;
                                    case "PIE":
                                    case "FUN":
                                        $event = "";

                                        if ($swSqlx2 == 1) {
                                            if ($index + 1 == $n - 1 && $swList == 1) {
                                                $answer = self::customDrillDownData($fieldLabel, $chartType, $swList, $variableValueAux, $sql, $n, $index + 1, $stmt);

                                                if ($answer[0] == 1) {
                                                    throw (new Exception($answer[1]));
                                                }

                                                $event = (isset($answer[2]))? str_replace("\"", null, $answer[2]) : "";
                                            } else {
                                                $event = "plotDrawProcessAjax('$fieldLabel', '$chartType', $swList, '$variableValueAux', '$sql', $n, " . ($index + 1) . ");";
                                            }
                                        }

                                        $arrayData[] = array(
                                            "label"  => self::tableLegendLabel($fieldLabel, $fieldValue, $event),
                                            "value"  => $fieldValue,
                                            "event"  => $event,
                                            "label2" => $fieldLabel
                                        );
                                        break;
                                }

                                $sw = 1;
                            }
                        }

                        switch ($chartType) {
                            case "BAR":
                                if ($data != null) {
                                    $data = "
                                    {
                                        \"COLUMNS\": [
                                            \"CONTEXT\",
                                            \"X_BAR_LABEL\",
                                            \"X_BAR_VALUE\",
                                            \"TOOL_TIP_TITLE\"
                                            " . (($index + 1 == $n - 1 && $swList == 1)? ", \"EVENT_CLICK\"" : (($swSqlx2 == 1)? ", \"DRILL_DATA\"" : null)) . "
                                        ],
                                        \"DATA\": [$data]
                                    }
                                    ";
                                }
                                break;
                            case "PIE":
                            case "FUN":
                                break;
                        }
                    }

                    switch ($chartType) {
                        case "BAR":
                            if ($data == null) {
                                throw (new Exception("Query " . ($index + 1) . ", no results."));
                            }
                            break;
                        case "PIE":
                        case "FUN":
                            if (count($arrayData) == 0) {
                                throw (new Exception("Query " . ($index + 1) . ", no results."));
                            }
                            break;
                    }
                } catch (Exception $e) {
                    $error = 1;
                    $errorMessage = $e->getMessage();
                }

                switch ($chartType) {
                    case "BAR":
                        return array($error, $errorMessage, $data);
                        break;
                    case "PIE":
                    case "FUN":
                        return array($error, $errorMessage, $arrayData);
                        break;
                }
            }
        }
    }
}

