function hideMessageBox() {
    Ext.MessageBox.hide();
}

function convertLabelTime(timeMinutos) {

    timeSeg = parseFloat(timeMinutos * 60);

    timeHrs = Math.floor(timeSeg/3600);
    timeMin = Math.floor((timeSeg-(timeHrs*3600))/60);

    return timeHrs + ' Hrs. - ' + timeMin + ' Min.';
}

function exportReport(typeReport) {
    /*
    Ext.MessageBox.show({
        msg: 'Generating report..',
        progressText: 'Saving...',
        width:300,
        wait:true,
        waitConfig: {interval:200},
        animEl: 'mb7'
    });
     */


    var con = 0;
    var col = new Array();
    var columns = gridReportSLA.colModel.columns;

    for (var i = 0; columns.length > i; i++) {
        if (columns[i].hidden != true) {
            colAux = new Array();
            colAux = {
                'HEADER' : columns[i].header,
                'DATAINDEX' : columns[i].dataIndex
            };
            col.push(colAux);
        }
    }

    col = Ext.encode(col);
    col = col.replace(/#/, "(numeral)")


    var pageReportExcel = 'slaExportReport.php?';
    pageReportExcel += '&SLA_UID=' + comboSla.getValue();
    pageReportExcel += '&DATE_START=' + dateStart.getValue();
    pageReportExcel += '&DATE_END=' + dateEnd.getValue();
    pageReportExcel += '&TYPE_EXCEEDED=' + comboExceeded.getValue();
    pageReportExcel += '&EXC_NUMBER=' + numberDuration.getValue();
    pageReportExcel += '&EXC_DURATION_TYPE=' + comboDuration.getValue();
    pageReportExcel += '&EXC_STATUS=' + comboTypeCases.getValue();
    pageReportExcel += '&COLUMNS=' + Ext.encode(col);
    pageReportExcel += '&TYPE_EXPORT=' + typeReport;

    document.getElementById('exportReport').src = pageReportExcel;
    /*

    Ext.Ajax.request({
        params = {
            SLA_UID: comboSla.getValue(),
            DATE_START: dateStart.getValue(),
            DATE_END: dateEnd.getValue(),
            TYPE_EXCEEDED: comboExceeded.getValue(),
            EXC_NUMBER: numberDuration.getValue(),
            EXC_DURATION_TYPE: comboDuration.getValue(),
            EXC_STATUS: comboTypeCases.getValue(),

            COLUMNS: Ext.encode(col),

            TYPE_EXPORT: typeReport,
            functionExecute: 'exportReport'
        },
        url: 'controllers/slaProxy.php',
        success: function (retorno) {
            var data = Ext.decode(retorno.responseText);
            //Ext.MessageBox.hide();
            console.log(data.data);
            window.location = './account/login';
        },
        failure: function () {
            Ext.MessageBox.alert('Error', 'Error al Guardar');
        }
    });


 /*

    col = col.replace(/#/, "(numeral)");

    var dataStore = Ext.encode(Ext.pluck(storeSlaReport.data.items, 'data'));

    if (typeReport == 'reportXls') {
        var pageReportExcel = 'slaExportReport.php?';
        pageReportExcel += '&TYPE=' + typeReport;
        pageReportExcel += '&COLUMNS=' + col;
        pageReportExcel += '&DATA=' + dataStore;

        document.getElementById('reportExcel').src = pageReportExcel;
    } else {

    }
     */
}

var penaltyUnit;
Ext.onReady(function() {
    var expExcel = new Ext.Action({
        iconCls:'button_menu_ext ss_sprite ss_page_white_excel',
        handler: function () {
            exportReport('reportXls');
        }
    });

    var expPdf = new Ext.Action({
        iconCls:'button_menu_ext ss_sprite ss_page_white_acrobat',
        handler: function () {
            exportReport('reportPdf');
        }
    });

    storeSlaReport = new Ext.data.GroupingStore({
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'controllers/slaProxy.php'
        }),
        reader: new Ext.data.JsonReader({
            root: 'data',
            totalProperty: 'total',
            fields: ['SLA_UID',
                     'SLA_NAME',
                     'PRO_UID',
                     'APP_NUMBER',
                     {name: 'TOTAL_CASES', type: 'float'},
                     {name: 'TOTAL_EXCEEDED', type: 'float'},
                     {name: 'APP_SLA_PEN_VALUE', type: 'float'},
                     {name: 'AVG_CASES', type: 'float'},
                     'APP_SLA_INIT_DATE',
                     'APP_SLA_DUE_DATE',
                     'APP_SLA_FINISH_DATE',
                     'SLA_PEN_VALUE_UNIT',
                     'APP_SLA_STATUS'
                    ]
        }),
        //remoteSort:true,
        baseParams: { functionExecute: 'reportSla'},
        groupField: 'SLA_NAME',
        listeners: {
            load: function() {
                gridReportSLA.view.collapseAllGroups();
            }
        }
    });


    Ext.ux.grid.GroupSummary.Calculations['AverageCase'] = function(v, record, field, sumTotal){
        return (sumTotal.TOTAL_EXCEEDED/sumTotal.TOTAL_CASES);
    };
    var summary = new Ext.ux.grid.GroupSummary();

    gridReportSLA = new Ext.grid.GridPanel({
        store: storeSlaReport,
        margins: '0 0 0 0',
        border: true,
        region: 'center',
        height: 135,
        autoScroll: true,
        //title: 'List SLA',
        loadMask: true,
        cm: new Ext.grid.ColumnModel({
            defaults: {
                width: 20,
                sortable: true
            },
            columns: [
                {header: _TRANS("ID_SLA"), width: 30, dataIndex: 'SLA_NAME', hidden: true,
                    renderer : function(v) { return '<span style=\'color:green;\'>' + v + '</span>';}
                },
                {header: _TRANS("ID_CASES"), width: 20, summaryType: 'count', dataIndex: 'APP_NUMBER',
                    renderer : function(v) { return 'Case # ' + v;},
                    summaryRenderer: function(v, params, data) { return '<b>' + ((v === 0 || v > 1)
                            ? '(' + v +' Cases)' : '(1 Case)') + '</b>'; }
                },
                {header: _TRANS("ID_TOTAL_CASES"), width: 30, dataIndex: 'TOTAL_CASES', hidden: true, summaryType: 'sum',
                    summaryRenderer: function(v, params, data) { return '<b>' + v + '</b>'; } },

                {header: _TRANS("ID_TOTAL_EXCEEDED"), width: 30, align:'right', dataIndex: 'TOTAL_EXCEEDED', summaryType: 'sum',
                    renderer : function(v) { return convertLabelTime(v);},
                    summaryRenderer: function(v, params, data) { return '<b>' + convertLabelTime(v); + '</b>'; } },

                {header: _TRANS("ID_AVERAGE_BY_CASE"), width: 30, align:'right', dataIndex: 'AVG_CASES', summaryType: 'AverageCase',
                    renderer : function(v) { return convertLabelTime(v);},
                    summaryRenderer: function(v, params, data) { return '<b>' + convertLabelTime(v); + '</b>'; } },

                {header: _TRANS("ID_START_DATE"), width: 20, css:'background: #D0E0D2;', align:'right', dataIndex: 'APP_SLA_INIT_DATE',
                    renderer : function(v) { return (v != null) ? v : '<b>Unassigned</b>'}},
                {header: _TRANS("ID_DUE_DATE"), width: 20, css:'background: #EDEBD3;', align:'right', dataIndex: 'APP_SLA_DUE_DATE',
                    renderer : function(v) { return (v != null) ? v : '<b>Unassigned</b>'}},
                {header: _TRANS("ID_FINISH_DATE"), width: 20, css:'background: #DCE2E8;', align:'right', dataIndex: 'APP_SLA_FINISH_DATE',
                    renderer : function(v) { return (v != null) ? v : '<b>Unassigned</b>'}},



                {header: _TRANS("ID_PENALTY"), width: 20, align:'right', dataIndex: 'APP_SLA_PEN_VALUE', summaryType: 'sum',
                    renderer : function(v, params, data) { penaltyUnit = data.data.SLA_PEN_VALUE_UNIT; return v.toFixed(3) + ' ' + data.data.SLA_PEN_VALUE_UNIT;},
                    summaryRenderer: function(v, params, data, x1, x2) { return v.toFixed(3) + ' ' + penaltyUnit; } },

                {header: _TRANS("ID_STATUS"), width: 10, css:'background: #58636E; color: #EFF1F2; font-weight: bold;', align:'center',dataIndex: 'APP_SLA_STATUS',
                    renderer : function(v) { return (v == 'CLOSED') ? 'Closed' : 'In progress'} }

            ]
        }),
        autoShow: true,
        autoFill:true,
        nocache: true,
        autoWidth: true,
        stripeRows: true,
        stateful: true,
        animCollapse: true,
        tbar:[_TRANS("ID_REPORT_GENERATED_ON") + ' : <b>' + EXECUTECRON + '</b>',
            '->',
            expExcel,
            expPdf
        ],
        //bbar : pagingSlaList,
        plugins: summary,

        view: new Ext.grid.GroupingView({
            forceFit:true,
            scrollOffset: 2,
            selectedRowClass : 'class-select-row',
            overCls: 'class-select-row',
            emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>',
            //groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "COUNTRIES" : "COUNTRY"]}) / STATUS : {[ values.rs[0].data["COMPANY_STATUS"] == "ACTIVE" ? "<span style=\'color:green;\'>ACTIVE</span>" : "<span style=\'color:red;\'>INACTIVE</span>"]}'
            //groupTextTpl: '{text} {[values.rs[0].data["COUNTRY_NAME"] == "This company does not have countries" ? "(0 COUNTRIES)" : values.rs.length > 1 ? "(" + values.rs.length + " COUNTRIES)" : "(" + values.rs.length + " COUNTRY)"]} / STATUS : {[ values.rs[0].data["COMPANY_STATUS"] == "ACTIVE" ? "<span style=\'color:green;\'>ACTIVE</span>" : "<span style=\'color:red;\'>INACTIVE</span>"]}'
            groupTextTpl: '{text}'
        }),
        listeners: {
            rowdblclick : showReportCase
        }
    });

    new Ext.Viewport({
        layout:'border',
        border: false,
        items:[panelFilters, gridReportSLA]
    });

});

