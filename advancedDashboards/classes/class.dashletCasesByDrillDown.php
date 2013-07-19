<?php
require_once ("classes" . PATH_SEP . "interfaces" . PATH_SEP . "dashletInterface.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.library.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.caseLibrary.php");
require_once (PATH_PLUGINS . "advancedDashboards" . PATH_SEP . "classes" . PATH_SEP . "class.chartLibrary.php");





class dashletCasesByDrillDown implements DashletInterface
{
    const version = "1.1";

    private $chartType;
    private $xLabel;
    private $yLabel;
    private $caseType;
    private $onRiskRemainingDay;
    private $category;
    private $optionUser;

    public static $arrayChartType = array();

    public static $arrayCaseType = array();

    public static $arrayCategory = array();

    public static $arrayOptionUser = array();

    public static $arrayDateFilter = array();


    public static function getAdditionalFields($className)
    {
        self::$arrayChartType[0][0] = "BAR";
        self::$arrayChartType[0][1] = "Bar";
        self::$arrayChartType[1][0] = "PIE";
        self::$arrayChartType[1][1] = "Pie";
        self::$arrayChartType[2][0] = "FUN";
        self::$arrayChartType[2][1] = "Funnel";

        self::$arrayCaseType[0][0] = "VALIDITY";
        self::$arrayCaseType[0][1] = "On Time, On Risk, Overdue";
        self::$arrayCaseType[1][0] = "STATUS";
        self::$arrayCaseType[1][1] = "Inbox, Draft, Participated, Unassigned, Paused";

        self::$arrayCategory[0][0] = "PROCESS";
        self::$arrayCategory[0][1] = "Processes";
        self::$arrayCategory[1][0] = "USER";
        self::$arrayCategory[1][1] = "Users";
        self::$arrayCategory[2][0] = "GROUP";
        self::$arrayCategory[2][1] = "Groups";
        self::$arrayCategory[3][0] = "DEPARTMENT";
        self::$arrayCategory[3][1] = "Departments";
        self::$arrayCategory[4][0] = "ACCOMPLISHMENT";
        self::$arrayCategory[4][1] = "Overdue vs. On Schedule";

        self::$arrayOptionUser[0][0] = 1;
        self::$arrayOptionUser[0][1] = "Current User";
        self::$arrayOptionUser[1][0] = 0;
        self::$arrayOptionUser[1][1] = "Users";

        self::$arrayDateFilter[0][0] = "ALL";
        self::$arrayDateFilter[0][1] = "All";
        self::$arrayDateFilter[1][0] = "TODAY";
        self::$arrayDateFilter[1][1] = "Today";
        self::$arrayDateFilter[2][0] = "YESTERDAY";
        self::$arrayDateFilter[2][1] = "Yesterday";
        self::$arrayDateFilter[3][0] = "THIS_WEEK";
        self::$arrayDateFilter[3][1] = "This Week";
        self::$arrayDateFilter[4][0] = "PREVIOUS_WEEK";
        self::$arrayDateFilter[4][1] = "Previous Week";
        self::$arrayDateFilter[5][0] = "THIS_MONTH";
        self::$arrayDateFilter[5][1] = "This Month";
        self::$arrayDateFilter[6][0] = "PREVIOUS_MONTH";
        self::$arrayDateFilter[6][1] = "Previous Month";
        self::$arrayDateFilter[7][0] = "THIS_YEAR";
        self::$arrayDateFilter[7][1] = "This Year";
        self::$arrayDateFilter[8][0] = "PREVIOUS_YEAR";
        self::$arrayDateFilter[8][1] = "Previous Year";
        self::$arrayDateFilter[9][0] = "CUSTOM";
        self::$arrayDateFilter[9][1] = "Custom";

        $additionalFields = array();

        //Stores
        $storeChartType = new stdclass();
        $storeChartType->xtype = "arraystore";
        $storeChartType->idIndex = 0;
        $storeChartType->fields = array("value", "text");
        $storeChartType->data = self::$arrayChartType;

        $storeCaseType = new stdclass();
        $storeCaseType->xtype = "arraystore";
        $storeCaseType->idIndex = 0;
        $storeCaseType->fields = array("value", "text");
        $storeCaseType->data = self::$arrayCaseType;

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

        $storeDateFilter = new stdclass();
        $storeDateFilter->xtype = "arraystore";
        $storeDateFilter->idIndex = 0;
        $storeDateFilter->fields = array("value", "text");
        $storeDateFilter->data = self::$arrayDateFilter;

        //Fields
        //Subtitle
        $txtSubTitle = new stdclass();
        $txtSubTitle->xtype = "hidden";
        $txtSubTitle->id    = "DAS_INS_SUBTITLE";
        $txtSubTitle->name  = "DAS_INS_SUBTITLE";

        $txtSubTitle->value = " - " . self::$arrayCategory[0][1] . " - @@USR_USERNAME";
        $additionalFields[] = $txtSubTitle;

        //Chart type
        $listeners = new stdclass();
        $listeners->select = "
        function (combo, record, index)
        {
            Ext.getCmp(\"DAS_CASBYDDXLABEL\").container.parent().child(\"label\").dom.innerHTML = \"" . "Label for Axis X" . "\";

            Ext.getCmp(\"DAS_CASBYDDYLABEL\").show();

            var chartType = Ext.getCmp(\"DAS_CHART_TYPE\").getValue();

            if (chartType == \"PIE\" || chartType == \"FUN\") {
                Ext.getCmp(\"DAS_CASBYDDXLABEL\").container.parent().child(\"label\").dom.innerHTML = \"Label\";

                Ext.getCmp(\"DAS_CASBYDDYLABEL\").hide();
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
        $cboChartType->fieldLabel = "Chart Type";
        $cboChartType->listeners = $listeners;
        $additionalFields[] = $cboChartType;

        //Label X
        $listeners = new stdclass();
        $listeners->blur = "
        function (text)
        {
            Ext.getCmp(\"DAS_CASBYDDXLABEL\").setValue(Ext.util.Format.trim(Ext.getCmp(\"DAS_CASBYDDXLABEL\").getValue()));
        }
        ";

        $txtXLabel = new stdclass();
        $txtXLabel->xtype = "textfield";
        $txtXLabel->id    = "DAS_CASBYDDXLABEL";
        $txtXLabel->name  = "DAS_CASBYDDXLABEL";

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
            Ext.getCmp(\"DAS_CASBYDDYLABEL\").setValue(Ext.util.Format.trim(Ext.getCmp(\"DAS_CASBYDDYLABEL\").getValue()));
        }
        ";

        $txtYLabel = new stdclass();
        $txtYLabel->xtype = "textfield";
        $txtYLabel->id    = "DAS_CASBYDDYLABEL";
        $txtYLabel->name  = "DAS_CASBYDDYLABEL";

        $txtYLabel->fieldLabel = "Label for Axis Y";
        $txtYLabel->width = 320;
        $txtYLabel->value = null;
        $txtYLabel->listeners = $listeners;
        $additionalFields[] = $txtYLabel;

        //Cases according
        $listeners = new stdclass();
        $listeners->select = "
        function (combo, record, index)
        {
            if (Ext.getCmp(\"ADA_CASBYDD_CASE_TYPE\").getValue() != \"VALIDITY\") {
                Ext.getCmp(\"ADA_CASBYDD_ON_RISK_REMAININGDAY\").allowBlank = true;

                document.getElementById(\"containerOnRiskRemainingDay1\").style.display = \"none\";
                document.getElementById(\"containerOnRiskRemainingDay2\").style.display = \"none\";
            } else {
                Ext.getCmp(\"ADA_CASBYDD_ON_RISK_REMAININGDAY\").allowBlank = false;

                document.getElementById(\"containerOnRiskRemainingDay1\").style.display = \"inline\";
                document.getElementById(\"containerOnRiskRemainingDay2\").style.display = \"inline\";
            }
        }
        ";

        $cboCaseType = new stdclass();
        $cboCaseType->xtype = "combo";
        $cboCaseType->id    = "ADA_CASBYDD_CASE_TYPE";
        $cboCaseType->name  = "ADA_CASBYDD_CASE_TYPE";

        $cboCaseType->valueField = "value";
        $cboCaseType->displayField = "text";
        $cboCaseType->value = self::$arrayCaseType[0][0];
        $cboCaseType->store = $storeCaseType;

        $cboCaseType->triggerAction = "all";
        $cboCaseType->mode = "local";
        $cboCaseType->editable = false;

        $cboCaseType->width = 320;
        $cboCaseType->fieldLabel = "Cases Status Group By";
        $cboCaseType->listeners = $listeners;
        $additionalFields[] = $cboCaseType;

        //On Risk - Label1
        $strEmpty1 = new stdclass();
        $strEmpty1->columnWidth = 0.25;
        $strEmpty1->xtype = "label";
        $strEmpty1->html = "&nbsp;";

        $strOnRisk = new stdclass();
        $strOnRisk->columnWidth = 0.75;
        $strOnRisk->xtype = "label";
        $strOnRisk->html = "Cases Are On Risk, They Have Only";

        //On Risk 1
        $containerOnRiskRemainingDay = new stdclass();
        $containerOnRiskRemainingDay->layout = "column";
        $containerOnRiskRemainingDay->id     = "containerOnRiskRemainingDay1";
        $containerOnRiskRemainingDay->name   = "containerOnRiskRemainingDay1";
        $containerOnRiskRemainingDay->bodyStyle = "margin-bottom: 0.45em; border-width: 0px;";
        $containerOnRiskRemainingDay->items = array($strEmpty1, $strOnRisk);
        $additionalFields[] = $containerOnRiskRemainingDay;

        //On Risk - Label2
        $txtOnRiskRemainingDay = new stdclass();
        $txtOnRiskRemainingDay->columnWidth = 0.07;
        $txtOnRiskRemainingDay->xtype = "numberfield";
        $txtOnRiskRemainingDay->id    = "ADA_CASBYDD_ON_RISK_REMAININGDAY";
        $txtOnRiskRemainingDay->name  = "ADA_CASBYDD_ON_RISK_REMAININGDAY";
        $txtOnRiskRemainingDay->allowNegative = false;
        $txtOnRiskRemainingDay->allowDecimals = false;
        //$txtOnRiskRemainingDay->allowBlank = false;
        $txtOnRiskRemainingDay->minValue = 1;
        $txtOnRiskRemainingDay->value = 1;

        $strOnRiskRemainingDay = new stdclass();
        $strOnRiskRemainingDay->columnWidth = 0.68;
        $strOnRiskRemainingDay->xtype = "label";
        $strOnRiskRemainingDay->html = "&nbsp;&nbsp;" . "Days Before They Expire";

        //On Risk 2
        $containerOnRiskRemainingDay = new stdclass();
        $containerOnRiskRemainingDay->layout = "column";
        $containerOnRiskRemainingDay->id     = "containerOnRiskRemainingDay2";
        $containerOnRiskRemainingDay->name   = "containerOnRiskRemainingDay2";
        $containerOnRiskRemainingDay->bodyStyle = "margin-bottom: 0.65em; border-width: 0px;";
        $containerOnRiskRemainingDay->items = array($strEmpty1, $txtOnRiskRemainingDay, $strOnRiskRemainingDay);
        $additionalFields[] = $containerOnRiskRemainingDay;

        //Category
        $listeners = new stdclass();
        $listeners->select = "
        function (combo, record, index)
        {
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
            } else {
                r = combo2.findRecord(combo2.valueField, combo2.getValue());
                i = combo2.store.indexOf(r);
                str = str + combo2.store.getAt(i).get(combo2.displayField);
            }

            ///////
            Ext.ComponentMgr.get(\"DAS_INS_SUBTITLE\").setValue(str);
        }
        ";

        $cboCategory = new stdclass();
        $cboCategory->xtype = "combo";
        $cboCategory->id    = "DAS_CASBYDDCATEGORY";
        $cboCategory->name  = "DAS_CASBYDDCATEGORY";

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

        //$rdoUser1 = new stdclass();
        //$rdoUser1->name = "DAS_CASBYDDOPTIONUSER";
        //$rdoUser1->inputValue = 1;
        //$rdoUser1->checked = true;
        //$rdoUser1->boxLabel = "Only by this user";

        //$rdoUser0 = new stdclass();
        //$rdoUser0->name = "DAS_CASBYDDOPTIONUSER";
        //$rdoUser0->inputValue = 0;
        //$rdoUser0->boxLabel = "All users";

        //$rdogrpUser = new stdclass();
        //$rdogrpUser->xtype = "radiogroup";
        //$rdogrpUser->fieldLabel = null;
        //$rdogrpUser->columns = 1;
        //$rdogrpUser->items = array($rdoUser1, $rdoUser0);
        //$additionalFields[] = $rdogrpUser;

        //Option user
        $cboOptionUser = new stdclass();
        $cboOptionUser->xtype = "combo";
        $cboOptionUser->id    = "DAS_CASBYDDOPTIONUSER";
        $cboOptionUser->name  = "DAS_CASBYDDOPTIONUSER";

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

        //Date filter
        $listeners = new stdclass();
        $listeners->select = "
        function (combo, record, index)
        {
            if (Ext.getCmp(\"DAS_DATE_FILTER\").getValue() != \"CUSTOM\") {
                Ext.getCmp(\"DAS_DATE_FROM\").setValue(\"\");
                Ext.getCmp(\"DAS_DATE_FROM\").allowBlank = true;
                Ext.getCmp(\"DAS_DATE_TO\").setValue(\"\");
                Ext.getCmp(\"DAS_DATE_TO\").allowBlank = true;

                document.getElementById(\"containerDateCustom\").style.display = \"none\";
            } else {
                Ext.getCmp(\"DAS_DATE_FROM\").allowBlank = false;
                Ext.getCmp(\"DAS_DATE_TO\").allowBlank = false;

                document.getElementById(\"containerDateCustom\").style.display = \"inline\";
            }
        }
        ";

        $cboDateFilter = new stdclass();
        $cboDateFilter->xtype = "combo";
        $cboDateFilter->id    = "DAS_DATE_FILTER";
        $cboDateFilter->name  = "DAS_DATE_FILTER";

        $cboDateFilter->valueField = "value";
        $cboDateFilter->displayField = "text";
        $cboDateFilter->value = self::$arrayDateFilter[0][0];
        $cboDateFilter->store = $storeDateFilter;

        $cboDateFilter->triggerAction = "all";
        $cboDateFilter->mode = "local";
        $cboDateFilter->editable = false;

        $cboDateFilter->width = 320;
        $cboDateFilter->fieldLabel = "Date";
        $cboDateFilter->listeners = $listeners;
        $additionalFields[] = $cboDateFilter;

        //Date from
        $strEmpty1 = new stdclass();
        $strEmpty1->columnWidth = 0.25;
        $strEmpty1->xtype = "label";
        $strEmpty1->html = "&nbsp;";

        $strDateFrom = new stdclass();
        $strDateFrom->columnWidth = 0.10;
        $strDateFrom->xtype = "label";
        $strDateFrom->html = "From";

        $txtDateFrom = new stdclass();
        $txtDateFrom->columnWidth = 0.25;
        $txtDateFrom->xtype = "datefield";
        $txtDateFrom->id    = "DAS_DATE_FROM";
        $txtDateFrom->name  = "DAS_DATE_FROM";
        $txtDateFrom->allowBlank = false;
        //$txtDateFrom->value = null;
        $txtDateFrom->format = "Y-m-d";
        $txtDateFrom->editable = false;

        //Date to
        $strEmpty2 = new stdclass();
        $strEmpty2->columnWidth = 0.05;
        $strEmpty2->xtype = "label";
        $strEmpty2->html = "&nbsp;";

        $strDateTo = new stdclass();
        $strDateTo->columnWidth = 0.07;
        $strDateTo->xtype = "label";
        $strDateTo->html = "To";

        $txtDateTo = new stdclass();
        $txtDateTo->columnWidth = 0.27;
        $txtDateTo->xtype = "datefield";
        $txtDateTo->id    = "DAS_DATE_TO";
        $txtDateTo->name  = "DAS_DATE_TO";
        $txtDateTo->allowBlank = false;
        //$txtDateTo->value = null;
        $txtDateTo->format = "Y-m-d";
        $txtDateTo->editable = false;

        //Date custom
        $containerDateCustom = new stdclass();
        $containerDateCustom->layout = "column";
        $containerDateCustom->id     = "containerDateCustom";
        $containerDateCustom->name   = "containerDateCustom";
        $containerDateCustom->bodyStyle = "margin: 0.40em 0 0.50em 0; border-width: 0px;";
        $containerDateCustom->items = array($strEmpty1, $strDateFrom, $txtDateFrom, $strEmpty2, $strDateTo, $txtDateTo);
        $additionalFields[] = $containerDateCustom;

        //Field afterRender
        $compAuxRender1 = new stdclass();
        $compAuxRender1->_afterRender = "
        Ext.getCmp(\"DAS_CASBYDDXLABEL\").allowBlank = false;
        document.getElementById(\"DAS_CASBYDDYLABEL\").setAttribute(\"maxlength\", 38);
        ";
        $additionalFields[] = $compAuxRender1;

        $compAuxRender2 = new stdclass();
        $compAuxRender2->_afterRender = "
        //Set initial values

        //Show hide DAS_CASBYDDYLABEL
        if (dashletInstance.DAS_CHART_TYPE && dashletInstance.DAS_CHART_TYPE != \"\") {
            Ext.getCmp(\"DAS_CASBYDDXLABEL\").container.parent().child(\"label\").dom.innerHTML = \"" . "Label for Axis X" . "\";

            Ext.getCmp(\"DAS_CASBYDDYLABEL\").show();

            if (dashletInstance.DAS_CHART_TYPE == \"PIE\" || dashletInstance.DAS_CHART_TYPE == \"FUN\") {
                Ext.getCmp(\"DAS_CASBYDDXLABEL\").container.parent().child(\"label\").dom.innerHTML = \"Label\";

                Ext.getCmp(\"DAS_CASBYDDYLABEL\").hide();
            }

            dashletInstance.DAS_CHART_TYPE = \"\";
        }

        //Set ADA_CASBYDD_ON_RISK_REMAININGDAY
        var swContainerOnRiskRemainingDay = 1;

        if (typeof(dashletInstance.SW_CASE_TYPE) == \"undefined\" && dashletInstance.DAS_INS_UID && !dashletInstance.ADA_CASBYDD_CASE_TYPE) {
            Ext.getCmp(\"ADA_CASBYDD_CASE_TYPE\").setValue(\"STATUS\");

            swContainerOnRiskRemainingDay = 0;
        }

        dashletInstance.SW_CASE_TYPE = 1;

        if (dashletInstance.ADA_CASBYDD_CASE_TYPE) {
            if (dashletInstance.ADA_CASBYDD_CASE_TYPE != \"VALIDITY\") {
                swContainerOnRiskRemainingDay = 0;
            } else {
                swContainerOnRiskRemainingDay = 1;
            }
        }

        dashletInstance.ADA_CASBYDD_CASE_TYPE = \"VALIDITY\";

        if (swContainerOnRiskRemainingDay == 0) {
            Ext.getCmp(\"ADA_CASBYDD_ON_RISK_REMAININGDAY\").allowBlank = true;

            document.getElementById(\"containerOnRiskRemainingDay1\").style.display = \"none\";
            document.getElementById(\"containerOnRiskRemainingDay2\").style.display = \"none\";
        } else {
            Ext.getCmp(\"ADA_CASBYDD_ON_RISK_REMAININGDAY\").allowBlank = false;

            document.getElementById(\"containerOnRiskRemainingDay1\").style.display = \"inline\";
            document.getElementById(\"containerOnRiskRemainingDay2\").style.display = \"inline\";
        }

        if (dashletInstance.ADA_CASBYDD_ON_RISK_REMAININGDAY && dashletInstance.ADA_CASBYDD_ON_RISK_REMAININGDAY != \"\") {
            Ext.getCmp(\"ADA_CASBYDD_ON_RISK_REMAININGDAY\").setValue(dashletInstance.ADA_CASBYDD_ON_RISK_REMAININGDAY);

            dashletInstance.ADA_CASBYDD_ON_RISK_REMAININGDAY = \"\";
        }

        //Set DAS_DATE_FROM DAS_DATE_TO
        var swContainerDateCustom = 0;

        if (dashletInstance.DAS_DATE_FILTER) {
            if (dashletInstance.DAS_DATE_FILTER != \"CUSTOM\") {
                swContainerDateCustom = 0;
            } else {
                swContainerDateCustom = 1;
            }

            dashletInstance.DAS_DATE_FILTER = \"ALL\";
        }

        if (swContainerDateCustom == 0) {
            Ext.getCmp(\"DAS_DATE_FROM\").setValue(\"\");
            Ext.getCmp(\"DAS_DATE_FROM\").allowBlank = true;
            Ext.getCmp(\"DAS_DATE_TO\").setValue(\"\");
            Ext.getCmp(\"DAS_DATE_TO\").allowBlank = true;

            document.getElementById(\"containerDateCustom\").style.display = \"none\";
        } else {
            Ext.getCmp(\"DAS_DATE_FROM\").allowBlank = false;
            Ext.getCmp(\"DAS_DATE_TO\").allowBlank = false;

            document.getElementById(\"containerDateCustom\").style.display = \"inline\";
        }

        if (dashletInstance.DAS_DATE_FROM && dashletInstance.DAS_DATE_FROM != \"\") {
            Ext.getCmp(\"DAS_DATE_FROM\").setValue(dashletInstance.DAS_DATE_FROM.substring(0, 10));
            Ext.getCmp(\"DAS_DATE_TO\").setValue(dashletInstance.DAS_DATE_TO.substring(0, 10));

            dashletInstance.DAS_DATE_FROM = \"\";
            dashletInstance.DAS_DATE_TO = \"\";
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
        self::$arrayChartType[0][0] = "BAR";
        self::$arrayChartType[0][1] = "Bar";
        self::$arrayChartType[1][0] = "PIE";
        self::$arrayChartType[1][1] = "Pie";
        self::$arrayChartType[2][0] = "FUN";
        self::$arrayChartType[2][1] = "Funnel";

        self::$arrayCaseType[0][0] = "VALIDITY";
        self::$arrayCaseType[0][1] = "On Time, On Risk, Overdue";
        self::$arrayCaseType[1][0] = "STATUS";
        self::$arrayCaseType[1][1] = "Inbox, Draft, Participated, Unassigned, Paused";

        self::$arrayDateFilter[0][0] = "ALL";
        self::$arrayDateFilter[0][1] = "All";
        self::$arrayDateFilter[1][0] = "TODAY";
        self::$arrayDateFilter[1][1] = "Today";
        self::$arrayDateFilter[2][0] = "YESTERDAY";
        self::$arrayDateFilter[2][1] = "Yesterday";
        self::$arrayDateFilter[3][0] = "THIS_WEEK";
        self::$arrayDateFilter[3][1] = "This Week";
        self::$arrayDateFilter[4][0] = "PREVIOUS_WEEK";
        self::$arrayDateFilter[4][1] = "Previous Week";
        self::$arrayDateFilter[5][0] = "THIS_MONTH";
        self::$arrayDateFilter[5][1] = "This Month";
        self::$arrayDateFilter[6][0] = "PREVIOUS_MONTH";
        self::$arrayDateFilter[6][1] = "Previous Month";
        self::$arrayDateFilter[7][0] = "THIS_YEAR";
        self::$arrayDateFilter[7][1] = "This Year";
        self::$arrayDateFilter[8][0] = "PREVIOUS_YEAR";
        self::$arrayDateFilter[8][1] = "Previous Year";
        self::$arrayDateFilter[9][0] = "CUSTOM";
        self::$arrayDateFilter[9][1] = "Custom";


        $this->chartType = (isset($config["DAS_CHART_TYPE"]))? $config["DAS_CHART_TYPE"] : self::$arrayChartType[0][0];
        $this->xLabel = (isset($config["DAS_CASBYDDXLABEL"]))? $config["DAS_CASBYDDXLABEL"] : null;
        $this->yLabel = (isset($config["DAS_CASBYDDYLABEL"]))? $config["DAS_CASBYDDYLABEL"] : null;
        $this->caseType = (isset($config["ADA_CASBYDD_CASE_TYPE"]))? $config["ADA_CASBYDD_CASE_TYPE"] : self::$arrayCaseType[1][0];
        $this->onRiskRemainingDay = (isset($config["ADA_CASBYDD_ON_RISK_REMAININGDAY"]))? (int)($config["ADA_CASBYDD_ON_RISK_REMAININGDAY"]) : 1;
        $this->category   = $config["DAS_CASBYDDCATEGORY"];
        $this->optionUser = $config["DAS_CASBYDDOPTIONUSER"];
        $this->dateFilter = (isset($config["DAS_DATE_FILTER"]))? $config["DAS_DATE_FILTER"] : self::$arrayDateFilter[0][0]; //Default all dates
        $this->dateFrom = (isset($config["DAS_DATE_FROM"]))? substr($config["DAS_DATE_FROM"], 0, 10) : null;
        $this->dateTo   = (isset($config["DAS_DATE_TO"]))? substr($config["DAS_DATE_TO"], 0, 10) : null;

        return true;
    }

    public function render($width = 300)
    {
        $title = null;

        $array = self::$arrayCategory;

        for ($i = 0; $i <= count($array) - 1; $i++) {
            if ($array[$i][0] == $this->category) {
                $title = $array[$i][1];
            }
        }

        $title = (!empty($this->xLabel))? $this->xLabel : $title;

        $processUid = null;
        $taskUid = null;
        $userUid = ($this->optionUser == 1)? $_SESSION["USER_LOGGED"] : null;
        $groupUid = null;
        $departmentUid = null;

        list($dateIni, $dateEnd) = Library::getDateIniEnd($this->dateFilter, $this->dateFrom, $this->dateTo);

        //Set html
        $htmlNoRecords = "<div style='font-size: 0.80em;'>" . G::LoadTranslation("ID_NO_RECORDS_FOUND") . "</div>";

        $javascriptChart = null;
        $htmlChart = $htmlNoRecords;

        $arrayStatus = array();
        $arrayStatusColor = array();

        switch ($this->caseType) {
            case "STATUS":
                $arrayStatus = array(
                    array("TO_DO", G::LoadTranslation("ID_INBOX")),
                    array("DRAFT", G::LoadTranslation("ID_DRAFT")),
                    array("ALL",   G::LoadTranslation("ID_SENT")),
                    array("UNASSIGNED", G::LoadTranslation("ID_UNASSIGNED")),
                    array("PAUSED",     G::LoadTranslation("ID_PAUSED"))
                );

                $arrayStatusColor = array();
                break;
            case "VALIDITY":
                $arrayStatus = array(
                    array("ON_TIME", "On Time"),
                    array("ON_RISK", "On Risk"),
                    array("OVERDUE", "Overdue")
                );

                $arrayStatusColor = array(
                    "ON_TIME" => "#008800", //Green
                    "ON_RISK" => "#FF9900", //Yellow
                    "OVERDUE" => "#DC3912"  //Red
                );
                break;
        }

        $arrayConfigData = array(
            "caseType" => $this->caseType,
            "status"   => $arrayStatus,
            "statusColor" => $arrayStatusColor,
            "onRiskRemainingDay" => $this->onRiskRemainingDay
        );

        $zoom = (!isset($_REQUEST["z"]))? "<a href=\\\"javascript:;\\\" onclick=\\\"window.open(\'" . Library::getUrl() . "&z=1\', \'_blank\'); return false;\\\" title=\\\"Zoom\\\"><img src=\\\"/plugin/advancedDashboards/icons/zoom25x25.png\\\" alt=\\\"\\\" /></a>" : null;

        switch ($this->chartType) {
            case "BAR":
                $caseData = ChartLibrary::casesByDrillDownData($title, 0, $this->chartType, $this->category, "ALL", "ALL", $dateIni, $dateEnd, $arrayConfigData, $processUid, $taskUid, $userUid, $groupUid, $departmentUid);

                if ($caseData != null) {
                    $arrayAux = ChartLibrary::getScript(
                        $this->chartType,
                        array(
                            "data" => $caseData,
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
                $configData = serialize($arrayConfigData);
                $configData = str_replace("\"", "@@doubleQuote", $configData);
                $configData = str_replace("'",  "@@singleQuote", $configData);

                $arrayAux = ChartLibrary::getScript(
                    $this->chartType,
                    array(
                        "zoom" => $zoom,
                        "htmlNoRecords" => $htmlNoRecords,
                        "paramsName" => "node, index, chartType, category, status, delStatus, dateIni, dateEnd, configData, processUid, taskUid, userUid, groupUid, departmentUid",
                        "paramsUrlAjax" => "../advancedDashboards/dashletCasesByDrillDownAjax.php",
                        "paramsAjax" => "
                            \"option\": \"CHARTDATA\",
                            \"node\": node,
                            \"index\": index,
                            \"chartType\": chartType,
                            \"category\": category,
                            \"appStatus\": status,
                            \"appDelStatus\": delStatus,
                            \"dateIni\": dateIni,
                            \"dateEnd\": dateEnd,
                            \"configData\": configData,
                            \"processUid\": processUid,
                            \"taskUid\": taskUid,
                            \"userUid\": userUid,
                            \"groupUid\": groupUid,
                            \"departmentUid\": departmentUid
                        ",
                        "paramsNodeEvent" => "'\" + node + \"', \" + index + \", '\" + chartType + \"', '\" + category + \"', '\" + status + \"', '\" + delStatus + \"', '\" + dateIni + \"', '\" + dateEnd + \"', '\" + configData + \"', '\" + processUid + \"', '\" + taskUid + \"', '\" + userUid + \"', '\" + groupUid + \"', '\" + departmentUid + \"'",
                        "paramsOnloadEvent" => "\"$title\", 0, \"" . $this->chartType . "\", \"" . $this->category . "\", \"ALL\", \"ALL\", \"$dateIni\", \"$dateEnd\", \"$configData\", \"$processUid\", \"$taskUid\", \"$userUid\", \"$groupUid\", \"$departmentUid\""
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

            function caseGrid(grdTitle, category, appStatus, dateIni, dateEnd, configData, processUid, taskUid, userUid, groupUid, departmentUid)
            {
                if (typeof(dataTableCase) != \"undefined\") {
                    dataTableCase.fnDestroy();
                }

                \$.fn.dataTableExt.oPagination.iFullNumbersShowPages = 2;

                dataTableCase = \$(\"#dataTableCase\").dataTable({
                    \"bProcessing\": true,
                    \"bServerSide\": true,
                    \"sAjaxSource\": \"../advancedDashboards/dashletCasesByDrillDownAjax.php\",
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
                        aoData.push({\"name\": \"option\", \"value\": \"GRIDDATA\"});
                        aoData.push({\"name\": \"category\", \"value\": category});
                        aoData.push({\"name\": \"appStatus\", \"value\": appStatus});
                        aoData.push({\"name\": \"dateIni\", \"value\": dateIni});
                        aoData.push({\"name\": \"dateEnd\", \"value\": dateEnd});
                        aoData.push({\"name\": \"configData\", \"value\": configData});
                        aoData.push({\"name\": \"processUid\", \"value\": processUid});
                        aoData.push({\"name\": \"taskUid\", \"value\": taskUid});
                        aoData.push({\"name\": \"userUid\", \"value\": userUid});
                        aoData.push({\"name\": \"groupUid\", \"value\": groupUid});
                        aoData.push({\"name\": \"departmentUid\", \"value\": departmentUid});

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
            <table id=\"dataTableCase\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
                <thead>
                    <tr>
                        <th>appUid</th>
                        <th>appDelIndex</th>
                        <th>#</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_STATUS"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_CASE"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_PROCESS"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_TASK"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_SENT_BY"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_CURRENT_USER"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_DUE_DATE"))) . "</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                      <td colspan=\"10\" class=\"dataTables_empty\">" . G::LoadTranslation("ID_LOADING_GRID") . "</td>
                    </tr>
                </tbody>
                <!--
                <tfoot>
                    <tr>
                        <th>appUid</th>
                        <th>appDelIndex</th>
                        <th>#</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_STATUS"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_CASE"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_PROCESS"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_TASK"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_SENT_BY"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_CURRENT_USER"))) . "</th>
                        <th>" . str_replace(" ", "&nbsp;", htmlentities(G::LoadTranslation("ID_DUE_DATE"))) . "</th>
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

