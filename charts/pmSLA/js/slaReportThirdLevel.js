var expExcelThi = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_excel',
    handler: function () {
        exportReport('reportXls','thirdReport');
    }
});

var expPdfThi = new Ext.Action({
    iconCls:'button_menu_ext ss_sprite ss_page_white_acrobat',
    handler: function () {
        exportReport('reportPdf','thirdReport');
    }
});

var textSla = new Ext.form.Label({
    fieldLabel: _TRANS("ID_SLA"),
    name: 'SLA_NAME'
});

var textProcess = new Ext.form.Label({
    fieldLabel: _TRANS("ID_PROCESS"),
    name: 'PRO_NAME'
});

var textTypeSla = new Ext.form.Label({
    fieldLabel: _TRANS("ID_TYPE"),
    name: 'SLA_TYPE_NAME'
});

var textTasks = new Ext.form.Label({
    fieldLabel: _TRANS("ID_TASKS"),
    name: 'TASKS'
});

var textCasesNumber = new Ext.form.Label({
    fieldLabel: _TRANS("ID_CASE"),
    name: 'APP_NUMBER'
});

var textDuration = new Ext.form.Label({
    fieldLabel: _TRANS("ID_DURATION"),
    name: 'APP_SLA_DURATION'
});

var textDurationExceeded = new Ext.form.Label({
    fieldLabel: _TRANS("ID_DURATION_EXCEEDED"),
    name: 'APP_SLA_EXCEEDED'
});

var textPenalty = new Ext.form.Label({
    fieldLabel: _TRANS("ID_PENALTY"),
    name: 'APP_SLA_PEN_VALUE'

});

var panelFilters = new Ext.Panel({
    height: 120,
    margins: '0 0 0 0',
    frame: true,
    labelAlign: 'left',
    align: 'center',
    labelStyle: 'font-weight:bold;',
    flex: 1,
    items: [{
        layout: 'column',
        items: [{
            columnWidth: 0.45,
            labelWidth: 60,
            layout: 'form',
            items: [textSla, textProcess, textTypeSla, textTasks]
        }, {
            columnWidth: 0.55,
            labelWidth: 120,
            layout: 'form',
            items: [textCasesNumber, textDuration, textDurationExceeded, textPenalty]
        }]
    }]
});

var storeReportCase = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/slaProxy.php'
    }),
    root: 'data',
    autoDestroy: true,
    totalProperty: 'total',
    remoteSort: true,
    baseParams: {
        functionExecute: 'reportCase'
    },
    fields: ['TASK_NAME', 'USR_NAME', 'DEL_DELEGATE_DATE',
             'DEL_INIT_DATE', 'DEL_FINISH_DATE', 'VAL_DURATION', 'APP_TYPE']
});

// var pageSize = parseInt(CONFIG.pageSize);
var pageSize = parseInt(20);
var storePageSize = new Ext.data.SimpleStore({
    autoLoad: true,
    fields: ['size'],
    data:[['20'], ['30'], ['40'], ['50'], ['100']]
});

var comboPageSize3 = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: storePageSize,
    valueField: 'size',
    displayField: _TRANS("ID_SIZE"),
    width: 50,
    editable: false,
    listeners: {
        select: function(c,d,i) {
            pagingThirdRepList.pageSize = parseInt(d.data['size']);
            pagingThirdRepList.moveFirst();
        }
    }
});

comboPageSize3.setValue(pageSize);

var pagingThirdRepList = new Ext.PagingToolbar({
    pageSize : pageSize,
    store : storeReportCase,
    displayInfo : true,
    autoHeight : true,
    displayMsg : 'Report {0} - {1} Of {2}',
    emptyMsg : _TRANS("ID_NO_REPORT_SHOW"),
    items: [
        comboPageSize3
    ]
});

var gridReportCase = new Ext.grid.GridPanel({
    store: storeReportCase,
    margins: '0 0 0 0',
    //height: 400,
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
    flex: 2,
    //autoHeight: true,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [{
            header: _("ID_TASK"),
            dataIndex: 'TASK_NAME',
            width: 70
        }, {
            header: _("ID_DELEGATE_USER"),
            dataIndex: 'USR_NAME',
            width: 60,
            hidden: false
        }, {
            header: _("ID_TASK_TRANSFER"),
            dataIndex: 'DEL_DELEGATE_DATE',
            width: 60,
            renderer: startDateRender
        }, {
            header: _("ID_START_DATE"),
            dataIndex: 'DEL_INIT_DATE',
            width: 60,
            renderer: startDateRender
        }, {
            header: _("ID_END_DATE"),
            dataIndex: 'DEL_FINISH_DATE',
            width: 60,
            renderer: startDateRender
        }, {
            header: _TRANS("ID_DURATION"),
            dataIndex: 'VAL_DURATION',
            width: 45,
            align: 'right',
            renderer: function(v) {
                return convertLabelTime(v);
            }
        }, {
            header: _("ID_ACTION"),
            dataIndex: 'APP_TYPE',
            width: 35
        }]
    }),
    viewConfig: {
        forceFit: true,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
    } // ,
    // bbar: pagingThirdRepList
});

var formReportCase = new Ext.Panel({
    labelWidth: 80,
    autoWidth: true,
    frame: true,
    layout: {
        type: 'vbox',
        align: 'stretch',
        pack: 'start'
    },
    bodyStyle: 'padding:5px 5px 0',
    tbar:[
        new Ext.form.Label({
            id: 'executeCron3'
        }),
        '->',
        expExcelThi,
        expPdfThi
    ],
    items: [panelFilters, gridReportCase]
});

function showReportCaseThirdLevel() {
    rowSelected = gridSecondReport.getSelectionModel().getSelected();

    if (rowSelected) {

        Ext.Ajax.request({
            url: 'controllers/slaProxy.php',
            params: {
                functionExecute: 'selectSlaUID',
                SLA_UID: rowSelected.data.SLA_UID,
                APP_NUMBER: rowSelected.data.APP_NUMBER
            },
            success: function(resp) {
                var data;
                data = Ext.decode(resp.responseText);
                if (data.success === true) {
                    thirdReportData['APP_UID'] = data.data.APP_UID;
                    thirdReportData['SLA_UID'] = data.data.SLA_UID;
                    thirdReportData['APP_NUMBER'] = data.data.APP_NUMBER;

                    storeReportCase.setBaseParam('APP_UID', data.data.APP_UID);
                    storeReportCase.setBaseParam('SLA_UID', data.data.SLA_UID);
                    storeReportCase.setBaseParam('APP_NUMBER', data.data.APP_NUMBER);

                    storeReportCase.load();

                    switch (data.data.SLA_TYPE) {
                        case 'RANGE':
//                                comboTaskEnd.show();
//                                Ext.getCmp('labelTo').show();
//                                fieldsTasks.show();

                            textTasks.show();
                            textTasks.setText(data.data.SLA_TASKS_START + ' ' + _TRANS("ID_TO") + ' ' + data.data.SLA_TASKS_END);
                            break;
                        case 'TASK':
//                                comboTaskEnd.hide();
//                                Ext.getCmp('labelTo').hide();
//                                fieldsTasks.show();

                            textTasks.show();
                            textTasks.setText(data.data.SLA_TASKS_START);
                            break;
                        case 'PROCESS':
//                                fieldsTasks.hide();

                            textTasks.hide();
                            break;
                    default:

                    }
                    // textSla.setValue(data.data.SLA_NAME);
                    // textProcess.setValue(data.data.PRO_NAME);

                    // comboTypeSla.setValue(data.data.SLA_TYPE);
                    //textCasesNumber.setValue(data.data.APP_NUMBER);
                    //textDuration.setValue(convertLabelTime(data.data.APP_SLA_DURATION));
                    //textDurationExceeded.setValue(convertLabelTime(data.data.APP_SLA_EXCEEDED));
                    //textPenalty.setValue(data.data.APP_SLA_PEN_VALUE + ' ' + data.data.SLA_PEN_VALUE_UNIT);

                    textSla.setText(data.data.SLA_NAME);
                    textProcess.setText(data.data.PRO_NAME);

                    var sLblType = "";
                    switch(data.data.SLA_TYPE) {
                        case 'PROCESS':
                            sLblType = _TRANS("ID_ENTIRE_PROCESS");
                            break;
                        case 'RANGE':
                            sLblType = _TRANS("ID_MULTIPLE_TASKS");
                            break;
                        case 'TASK':
                            sLblType = _TRANS("ID_TASK");
                            break;
                        default:
                            sLblType = data.data.SLA_TYPE;
                            break;
                    }

                    textTypeSla.setText(sLblType);

//                        comboTaskStart.setValue(data.data.SLA_TASKS_START);
//                        comboTaskEnd.setValue(data.data.SLA_TASKS_END);

                    textCasesNumber.setText(formatNumber(parseFloat(data.data.APP_NUMBER), false));
                    textDuration.setText(convertLabelTime(data.data.APP_SLA_DURATION));
                    textDurationExceeded.setText(convertLabelTime(data.data.APP_SLA_EXCEEDED));
                    textPenalty.setText(formatNumber(parseFloat(data.data.APP_SLA_PEN_VALUE), true) + ' ' + data.data.SLA_PEN_VALUE_UNIT);

                } else {
                    Ext.MessageBox.alert(_("ID_ERROR"), _TRANS("ID_PROBLEM_OCCURRED"));
                }
            },
            failure: function() {
                Ext.MessageBox.alert(_("ID_ERROR"), _TRANS("ID_PROBLEM_OCCURRED"));
            }
        });

    }
}