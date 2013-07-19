function startDateRender (v)
{
    var dateString = "-";
    if (v != "-" && v != null) {
        dateString = _DF(v,"Y/d/d H:i:s");
    }
    return dateString;
}

var textSla = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_SLA"),
    name:'SLA_NAME',
    width: 203,
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});

var textProcess = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_PROCESS"),
    width: 203,
    name: 'PRO_NAME',
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});

var comboTypeSla = new Ext.form.ComboBox({
    valueField: 'ID',
    fieldLabel: _TRANS("ID_TYPE"),
    displayField: 'VAL',
    name: 'SLA_TYPE',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    hideTrigger: true,
    disabled: true,
    width: 203,
    editable: false,
    store: [['PROCESS', _TRANS("Entire Process")], ['RANGE', _TRANS("ID_MULTIPLE_TASKS")], ['TASK', _("ID_TASK")]],
    cls: 'input-h'
});

var storeTasks = new Ext.data.JsonStore({
    url: 'controllers/slaProxy.php',
    root: 'data',
    baseParams: {functionExecute: 'listTasks'},
    fields: [{name: 'TAS_UID'}, {name: 'TAS_TITLE'}]
});

var comboTaskStart = new Ext.form.ComboBox({
    valueField: 'SLA_TASKS_START',
    displayField: 'TAS_TITLE',
    name: 'SLA_TASKS_START',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    hideTrigger: true,
    disabled: true,
    width: 90,
    store: storeTasks,
    cls: 'input-h'
});

var comboTaskEnd = new Ext.form.ComboBox({
    valueField: 'SLA_TASKS_END',
    displayField: 'TAS_TITLE',
    name: 'SLA_TASKS_END',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    hideTrigger: true,
    disabled: true,
    width: 90,
    store: storeTasks,
    cls: 'input-h'
});

var fieldsTasks = new Ext.form.CompositeField({
    //hidden: true,
    fieldLabel: _TRANS("ID_TASKS"),
    items: [comboTaskStart, {id:'labelTo', html: _TRANS("ID_TO")}, comboTaskEnd]
});

var textCasesNumber = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_CASE"),
    name: 'APP_NUMBER',
    width: 203,
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});

var textDuration = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_DURATION"),
    name: 'APP_SLA_DURATION',
    width: 203,
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});

var textDurationExceeded = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_DURATION_EXCEEDED"),
    name: 'APP_SLA_EXCEEDED',
    width: 203,
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});

var textPenalty = new Ext.form.TextField({
    fieldLabel: _TRANS("ID_PENALTY"),
    name: 'APP_SLA_PEN_VALUE',
    width: 203,
    allowBlank : true,
    disabled : true,
    cls: 'input-h'
});


var panelFilters = new Ext.Panel({
    height: 120,
    margins: '0 0 0 0',
    frame: true,
    labelAlign: 'left',
    align: 'center',
    //labelStyle: 'font-weight:bold;',

    items: [{
        layout: 'column',
        items: [{
            columnWidth: .45,
            labelWidth: 60,
            layout: 'form',
            items: [
                textSla,
                textProcess,
                comboTypeSla,
                fieldsTasks
            ]
        }, {
            columnWidth: .55,
            labelWidth: 120,
            layout: 'form',
            items: [
                textCasesNumber,
                textDuration,
                textDurationExceeded,
                textPenalty
            ]
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
    baseParams: {functionExecute: 'reportCase'},
    fields: ['TASK_NAME','USR_NAME','DEL_DELEGATE_DATE','DEL_INIT_DATE','DEL_FINISH_DATE','VAL_DURATION','APP_TYPE']
});

var gridReportCase = new Ext.grid.GridPanel({
    store: storeReportCase,
    margins: '0 0 0 0',
    height: 130,
    autoScroll: true,
    loadMask : true,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 20,
            sortable: true
        },
        columns: [
            {header: _("ID_TASK"), dataIndex: 'TASK_NAME', width: 70},
            {header: _("ID_DELEGATE_USER"), dataIndex: 'USR_NAME', width: 60, hidden:false},
            {header: _("ID_TASK_TRANSFER"), dataIndex: 'DEL_DELEGATE_DATE', width: 60, renderer:startDateRender},
            {header: _("ID_START_DATE"), dataIndex: 'DEL_INIT_DATE', width: 60, renderer: startDateRender},
            {header: _("ID_END_DATE"), dataIndex: 'DEL_FINISH_DATE', width: 60, renderer:startDateRender},
            {header: _TRANS("ID_DURATION"), dataIndex: 'VAL_DURATION', width: 45, align: 'right',
                renderer : function(v) { return convertLabelTime(v);}},
            {header: _("ID_ACTION"), dataIndex: 'APP_TYPE', width: 35},
        ]
    }),
    border:true,
    autoShow: true,
    autoFill:true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    animCollapse:true,
    viewConfig: {
        forceFit:true,
        emptyText: '<div align="center"><b> ' + _TRANS("ID_NO_SLA_SHOW") + ' </b></div>'
    }
});

var formReportCase = new Ext.FormPanel({
    labelWidth: 80,
    autoWidth: true,
    frame: true,
    bodyStyle:'padding:5px 5px 0',
    items: [panelFilters,gridReportCase]
})

var windowsFormSla = new Ext.Window({
    layout:'fit',
    title:'',
    icon:'/plugin/pmSLA/images/time_edit.png',
    frame: true,
    width: 830,
    height: 350,
    plain: true,
    modal:true,
    closeAction: 'hide',
    items: [formReportCase],
    buttons: [{
        text:_('ID_CLOSE'),
        handler: function(){
            windowsFormSla.hide();
        }
    }]
});

function showReportCase() {
    rowSelected = gridReportSLA.getSelectionModel().getSelected();

    if (rowSelected) {

            formReportCase.getForm().load({
                url: 'controllers/slaProxy.php',
                method: 'POST',
                params: {
                    functionExecute: 'selectSlaUID',
                    SLA_UID: rowSelected.data.SLA_UID,
                    // APP_UID: rowSelected.data.APP_UID,
                    APP_NUMBER: rowSelected.data.APP_NUMBER
                },
                waitMsg:'Loading',
                success: function(form, action) {
                    data = Ext.decode(action.response.responseText);
                    if (data.success === true) {
                        storeReportCase.setBaseParam('APP_UID', data.data.APP_UID);
                        storeReportCase.setBaseParam('SLA_UID', data.data.SLA_UID);
                        storeReportCase.setBaseParam('APP_NUMBER', data.data.APP_NUMBER);

                        storeReportCase.load();
                        windowsFormSla.show();
                        switch(data.data.SLA_TYPE) {
                            case 'RANGE':
                                comboTaskEnd.show();
                                Ext.getCmp('labelTo').show();
                                fieldsTasks.show();
                                break;
                            case 'TASK':
                                comboTaskEnd.hide();
                                Ext.getCmp('labelTo').hide();
                                fieldsTasks.show();
                                break;
                            case 'PROCESS':
                                fieldsTasks.hide();
                                break;
                            default:

                        }
                        // formatting of the values
                        textDuration.setValue(convertLabelTime(textDuration.getValue()));
                        textDurationExceeded.setValue(convertLabelTime(textDurationExceeded.getValue()));
                        textPenalty.setValue(textPenalty.getValue() + ' ' + data.data.SLA_PEN_VALUE_UNIT);
                    }

                },
                failure:function(form, action) {
                    DoNothing();
                }
            });


    }
}
