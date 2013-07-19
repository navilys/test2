var storeSecondReport = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/slaProxy.php'
    }),
    root: 'data',
    autoDestroy: true,
    totalProperty: 'total',
    remoteSort: true,
    baseParams: {
        functionExecute: 'reportCaseSecondLevel'
    },
    fields: ['SLA_UID',
             'SLA_NAME',
             'PRO_UID',
             'APP_NUMBER',
             'APP_STATUS',
             { name: 'TOTAL_CASES', type: 'float' },
             { name: 'TOTAL_EXCEEDED', type: 'float' },
             { name: 'APP_SLA_PEN_VALUE', type: 'float' },
             { name: 'AVG_CASES', type: 'float' },
             'APP_SLA_INIT_DATE',
             'APP_SLA_DUE_DATE',
             'APP_SLA_FINISH_DATE',
             'SLA_PEN_VALUE_UNIT',
             'APP_SLA_STATUS'
            ]
});

// var pageSize = parseInt(CONFIG.pageSize);
var pageSize = parseInt(ID_PAGESIZE);
var storePageSize = new Ext.data.SimpleStore({
    autoLoad: true,
    fields: ['size'],
    data:[['20'], ['30'], ['40'], ['50'], ['100']]
});

var comboPageSize2 = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: storePageSize,
    valueField: 'size',
    displayField: 'size',
    width: 50,
    editable: false,
    listeners: {
        select: function(c,d,i) {
            pagingSecondRepList.pageSize = parseInt(d.data['size']);
            pagingSecondRepList.moveFirst();
        }
    }
});

comboPageSize2.setValue(pageSize);

var pagingSecondRepList = new Ext.PagingToolbar({
    pageSize : pageSize,
    store : storeSecondReport,
    displayInfo : true,
    autoHeight : true,
    displayMsg : 'Cases {0} - {1} Of {2}',
    emptyMsg : _TRANS("ID_NO_REPORT_SHOW"),
    items: [
        comboPageSize2
    ]
});

var expExcelSen = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_excel',
    handler: function () {
        exportReport('reportXls','secondReport');
    }
});

var expPdfSen = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_acrobat',
    handler: function () {
        exportReport('reportPdf','secondReport');
    }
});

var gridSecondReport = new Ext.grid.GridPanel({
    store: storeSecondReport,
    margins: '0 0 0 0',
    height: 130,
    autoScroll: true,
    loadMask: true,
    border: true,
    autoShow: true,
    autoFill: true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    animCollapse: true,

    tbar:[
        new Ext.form.Label({
            id: 'executeCron2'
        }),
        '->',
        expExcelSen,
        expPdfSen
    ],
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [{
            header: _TRANS("ID_SLA"),
            width: 30,
            dataIndex: 'SLA_NAME',
            hidden: true,
            sortable: true,
            renderer: function(v) {
                return '<span style=\'color:green;\'>' + v + '</span>';
            }
        }, {
            header: _TRANS("ID_CASES"),
            width: 20,
            summaryType: 'count',
            dataIndex: 'APP_NUMBER',
            sortable: true,
            renderer: function(v) {
                return 'Case # ' + formatNumber(v, false);
            }
        }, {
            header: _TRANS("ID_EXCEEDED"),
            width: 30,
            align: 'right',
            dataIndex: 'TOTAL_EXCEEDED',
            summaryType: 'sum',
            sortable: true,
            renderer: function(v) {
                return convertLabelTime(v);
            }
        }, {
            header: _TRANS("ID_START_DATE"),
            width: 20,
            css: 'background: #D0E0D2;',
            align: 'right',
            dataIndex: 'APP_SLA_INIT_DATE',
            sortable: true,
            renderer: function(v, ob2, cols) {
                if ((v == null) && (cols.data.APP_STATUS == 'COMPLETED' || cols.data.APP_STATUS == 'CANCELED')) {
                    return '<b>' + _TRANS("ID_NOT_APPLY") + '</b>';
                } else if (v == null) {
                    return '<b>' + _TRANS("ID_WAITING_START") + '</b>';
                } else {
                    return v;
                }
            }
        }, {
            header: _TRANS("ID_DUE_DATE"),
            width: 20,
            css: 'background: #EDEBD3;',
            align: 'right',
            dataIndex: 'APP_SLA_DUE_DATE',
            sortable: true,
            renderer: function(v, ob2, cols) {
                if ((cols.data.APP_SLA_INIT_DATE == null) && (cols.data.APP_STATUS == 'COMPLETED' || cols.data.APP_STATUS == 'CANCELED')) {
                    return '<b>' + _TRANS("ID_NOT_APPLY") + '</b>';
                } else if (cols.data.APP_SLA_INIT_DATE == null) {
                    return '<b>' + _TRANS("ID_WAITING_START") + '</b>';
                } else if (v == null) {
                    return '<b>' + _TRANS("ID_UNASSIGNED") + '</b>';
                } else {
                    return v;
                }
            }
        }, {
            header: _TRANS("ID_FINISH_DATE"),
            width: 20,
            css: 'background: #DCE2E8;',
            align: 'right',
            dataIndex: 'APP_SLA_FINISH_DATE',
            sortable: true,
            renderer: function(v, ob2, cols) {
                if ((cols.data.APP_SLA_INIT_DATE == null) && (cols.data.APP_STATUS == 'COMPLETED' || cols.data.APP_STATUS == 'CANCELED')) {
                    return '<b>' + _TRANS("ID_NOT_APPLY") + '</b>';
                } else if (cols.data.APP_SLA_INIT_DATE == null) {
                    return '<b>' + _TRANS("ID_WAITING_START") + '</b>';
                } else if (v == null) {
                    return '<b>' + _TRANS("ID_UNASSIGNED") + '</b>';
                } else {
                    return v;
                }
            }
        }, {
            header: _TRANS("ID_PENALTY"),
            width: 20,
            align: 'right',
            dataIndex: 'APP_SLA_PEN_VALUE',
            summaryType: 'sum',
            sortable: true,
            renderer: function (v, params, data) {
                penaltyUnit = data.data.SLA_PEN_VALUE_UNIT;
                return formatNumber(v, true) + ' ' + data.data.SLA_PEN_VALUE_UNIT;
            }
        }, {
            header: _TRANS("ID_STATUS"),
            width: 10,
            css: 'background: #58636E; color: #EFF1F2; font-weight: bold;',
            align: 'center',
            dataIndex: 'APP_SLA_STATUS',
            sortable: true,
            renderer: function(v) {
                return (v == 'CLOSED') ? _TRANS("ID_CLOSED") : _TRANS("ID_IN_PROGRESS");
            }
        }]
    }),
    viewConfig: {
        forceFit: true,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
    },
    bbar : pagingSecondRepList,
    listeners: {
        rowdblclick: function() {
            rowSelected = gridSecondReport.getSelectionModel().getSelected();
            if (rowSelected) {
                buttonReportHistory.setText('Case # ' + rowSelected.data.APP_NUMBER);
                document.getElementById('button4').style.width = "150px";

                buttonReportHistory.show();
                buttonReportHistory.toggle();

                showReportCaseThirdLevel();
            }
        }
    }
});