function startDateRender (v)
{
    var dateString = "-";
    if (v != "-" && v != null) {
        dateString = _DF(v,"Y/d/d H:i:s");
    }
    return dateString;
}

storeFirstReport = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/slaProxy.php'
    }),
    root: 'data',
    autoDestroy: true,
    totalProperty: 'total',
    remoteSort: true,
    baseParams: {functionExecute: 'reportSlaFirstLevel'},
    fields: [
        {name: 'SLA_UID', type: 'string'},
        {name: 'SLA_NAME', type: 'string'},
        {name: 'SUM_DURATION', type: 'float'},
        {name: 'SUM_EXCEEDED', type: 'float'},
        {name: 'AVG_SLA', type: 'float'},
        {name: 'SUM_PEN_VALUE', type: 'float'},
        {name: 'SLA_PEN_VALUE_UNIT', type: 'string'}
    ]
});

// var pageSize = parseInt(CONFIG.pageSize);
var pageSize = parseInt(ID_PAGESIZE);
var storePageSize = new Ext.data.SimpleStore({
    autoLoad: true,
    fields: ['size'],
    data:[['20'], ['30'], ['40'], ['50'], ['100']]
});

var comboPageSize = new Ext.form.ComboBox({
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
            pagingFirstRepList.pageSize = parseInt(d.data['size']);
            pagingFirstRepList.moveFirst();
        }
    }
});

comboPageSize.setValue(pageSize);

var pagingFirstRepList = new Ext.PagingToolbar({
    pageSize : pageSize,
    store : storeFirstReport,
    displayInfo : true,
    autoHeight : true,
    displayMsg : 'Report {0} - {1} Of {2}',
    emptyMsg : _TRANS("ID_NO_REPORT_SHOW"),
    items: [
        comboPageSize
    ]
});

var expExcel = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_excel',
    handler: function () {
        exportReport('reportXls','firstReport');
    }
});

var expPdf = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_acrobat',
    handler: function () {
        exportReport('reportPdf','firstReport');
    }
});

var gridFirstReport = new Ext.grid.GridPanel({
    store: storeFirstReport,
    margins: '0 0 0 0',
    height: 130,
    autoScroll: true,
    loadMask : true,
    border:true,
    autoShow: true,
    autoFill:true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    animCollapse:true,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [
            {header: _TRANS("ID_SLA"), width: 8, sortable: true, dataIndex: 'SLA_NAME'},
            {header: _TRANS("ID_TIMES_EXECUTED"), width: 8, sortable: true, dataIndex: 'SUM_DURATION',
                align: 'right', renderer: function (v) {return (v > 1) ? formatNumber(v, false) + ' Cases' : '1 Case'; }},
            {header: _TRANS("ID_TIME_EXCEEDED"), width: 8, sortable: true, dataIndex: 'SUM_EXCEEDED',
                align: 'right', renderer: function (v) {return convertLabelTime(v); }},
            {header: _TRANS("ID_AVERAGE_EXCEED"), width: 8, sortable: true, dataIndex: 'AVG_SLA',
                align: 'right', renderer : function(v) { return convertLabelTime(v); } },
            {header: _TRANS("ID_PENALTY"), width: 6, sortable: true, dataIndex: 'SUM_PEN_VALUE',
                align: 'right', renderer : function(v, param, data) {return formatNumber(v, true) + ' ' + data.data.SLA_PEN_VALUE_UNIT; }}
        ]
    }),
    tbar:[
        new Ext.form.Label({
            id: 'executeCron1'
        }),
        '->',
        expExcel,
        expPdf
    ],
    viewConfig: {
        forceFit:true,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
    },
    bbar: pagingFirstRepList,
    listeners: {
        rowdblclick : function() {
            rowSelected = gridFirstReport.getSelectionModel().getSelected();
            if (rowSelected) {
                buttonReportCases.show();
                buttonReportCases.toggle();
                buttonReportHistory.hide();

                buttonReportCases.setText(rowSelected.data.SLA_NAME);
                document.getElementById('button3').style.width = "150px";

                storeSecondReport.setBaseParam('SLA_UID', rowSelected.data.SLA_UID);
                storeSecondReport.setBaseParam('TYPE_DATE', reportValues.TYPE_DATE);
                storeSecondReport.setBaseParam('DATE_START', reportValues.DATE_START);
                storeSecondReport.setBaseParam('DATE_END', reportValues.DATE_END);

                storeSecondReport.setBaseParam('TYPE_EXCEEDED', reportValues.TYPE_EXCEEDED);
                storeSecondReport.setBaseParam('EXC_NUMBER', reportValues.EXC_NUMBER);
                storeSecondReport.setBaseParam('EXC_DURATION_TYPE', reportValues.EXC_DURATION_TYPE);
                storeSecondReport.setBaseParam('EXC_STATUS', reportValues.EXC_STATUS);

                secondReportData = new Array();
                secondReportData['SLA_UID'] = rowSelected.data.SLA_UID;
                secondReportData['SLA_NAME'] = rowSelected.data.SLA_NAME;
                secondReportData['TYPE_DATE'] = rowSelected.data.TYPE_DATE;
                secondReportData['DATE_START'] = reportValues.DATE_START;
                secondReportData['DATE_END'] = reportValues.DATE_END;
                secondReportData['TYPE_EXCEEDED'] = reportValues.TYPE_EXCEEDED;
                secondReportData['EXC_NUMBER'] = reportValues.EXC_NUMBER;
                secondReportData['EXC_DURATION_TYPE'] = reportValues.EXC_DURATION_TYPE;
                secondReportData['EXC_STATUS'] = reportValues.EXC_STATUS;

                storeSecondReport.load({params:{start:0, limit: parseInt(ID_PAGESIZE) }});
            }
        }
    },
    bbar : pagingFirstRepList
});