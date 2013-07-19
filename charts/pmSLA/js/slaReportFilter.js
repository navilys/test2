var storeFirstReport;

var reportValues = {
    SLA_UID: '',
    TYPE_DATE: '',
    DATE_START: '',
    DATE_END: '',
    TYPE_EXCEEDED: '',
    EXC_NUMBER: '',
    EXC_DURATION_TYPE: '',
    EXC_STATUS: ''
};

var storeSla = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/slaProxy.php'
    }),
    root: 'data',
    baseParams: {
        functionExecute: 'listSlaName'
    },
    fields: ['SLA_UID', 'SLA_NAME']
});

var comboSla = new Ext.form.ComboBox({
    id: 'VAL_SLA',
    valueField: 'SLA_UID',
    displayField: 'SLA_NAME',
    fieldLabel: _TRANS("ID_SLA"),
    typeAhead: true,
    triggerAction: 'all',
    width: 330,
    editable: false,
    value: '- All -',
    store: storeSla
});

var comboDates = new Ext.form.ComboBox({
    id: 'VAL_DATES',
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    width: 155,
    editable: false,
    value: '- All -',
    store: [
        ['ALL', _TRANS("ID_ALL")],
        ['>', _TRANS("ID_GREATER_THAN")],
        ['>=', _TRANS("ID_GREATER_EQUAL_THAN")],
        ['<', _TRANS("ID_LESS_THAN")],
        ['<=', _TRANS("ID_LESS_EQUAL_THAN")],
        ['between', _TRANS("ID_BETWEEN")]
    ],
    listeners: {
        select: function (combo, record, index) {
            switch (combo.getValue()) {
                case 'ALL':
                    dateStart.hide();
                    Ext.getCmp('label_and').hide();
                    dateEnd.hide();
                    break;
                case 'between':
                    dateStart.show();
                    Ext.getCmp('label_and').show();
                    dateEnd.show();
                    break;
                default:
                    dateStart.show();
                    Ext.getCmp('label_and').hide();
                    dateEnd.hide();
                    break;
            }
        }
    }
});

var dateStart = new Ext.form.DateField({
    id: 'VAL_DATE_START',
    xtype: 'datefield',
    format: 'Y-m-d',
    forceSelection: true,
    width: 120,
    emptyText: _TRANS("ID_SELECT_DATE"),
    editable: true
});

var dateEnd = new Ext.form.DateField({
    id: 'VAL_DATE_END',
    xtype: 'datefield',
    format: 'Y-m-d',
    forceSelection: true,
    width: 120,
    emptyText: _TRANS("ID_SELECT_DATE"),
    editable: true
});

var fieldsDates = new Ext.form.CompositeField({
    fieldLabel: _TRANS("ID_DATES"),
    items: [
    comboDates,
    dateStart,
    {
        id: 'label_and',
        html: _TRANS("ID_AND")
    },
    dateEnd]
});

var numberDuration = new Ext.form.NumberField({
    id: 'VAL_DURATION_NUMBER',
    allowBlank: false,
    decimalPrecision: 0,
    width: 51,
    value: 1,
    minValue: 1
});

var comboDuration = new Ext.form.ComboBox({
    id: 'VAL_DURATION_TYPE',
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    mode: 'local',
    width: 90,
    editable: false,
    value: 'HOURS',
    store: [
        ['HOURS', _TRANS("ID_HOURS")],
        ['DAYS', _TRANS("ID_DAYS")]
    ]
});

var comboExceeded = new Ext.form.ComboBox({
    id: 'VAL_EXCEEDED',
    fieldLabel: _TRANS("ID_EXCEEDED"),
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    mode: 'local',
    width: 155,
    editable: false,
    value: 'ALL',
    store: [
        ['ALL', _TRANS("ID_ALL")],
        ['NO_EXCEEDED', _TRANS("ID_NO_EXCEEDED")],
        ['EXCEEDED', _TRANS("ID_EXCEEDED")],
        ['EXCEEDED_LESS', _TRANS("ID_EXCEEDED_LESS")],
        ['EXCEEDED_MORE', _TRANS("ID_EXCEEDED_MORE")]
    ],
    listeners: {
        select: function (combo, record, index) {
            switch (combo.getValue()) {
                case 'EXCEEDED_LESS':
                case 'EXCEEDED_MORE':
                    numberDuration.show();
                    comboDuration.show();
                    break;
                default:
                    numberDuration.hide();
                    comboDuration.hide();
                    break;
            }
        }
    }
});

var fieldsDuration = new Ext.form.CompositeField({
    items: [comboExceeded, numberDuration, comboDuration]
});

var comboTypeCases = new Ext.form.ComboBox({
    id: 'VAL_CASE_TYPE',
    fieldLabel: _TRANS("ID_STATUS_CASE"),
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    mode: 'local',
    width: 155,
    editable: false,
    value: 'ALL',
    store: [
        ['ALL', _TRANS("ID_ALL")],
        ['OPEN', _TRANS("ID_OPEN")],
        ['COMPLETED', _TRANS("ID_COMPLETED")]
    ]
});

var panelReportFilter = new Ext.Panel({
    frame: true,
    title: _TRANS("ID_FILTERS"),

    items: [{
        layout: 'column',
        items: [{
            columnWidth: .32,
            labelWidth: 40,
            layout: 'form',
            items: [{
                html: '&nbsp;'
            }]
        }, {
            columnWidth: .68,
            labelWidth: 100,
            layout: 'form',
            items: [
            comboSla, fieldsDates, fieldsDuration, comboTypeCases]
        }],

        buttonAlign: 'center',
        buttons: [{
            iconCls: 'button_menu_ext ss_sprite ss_report',
            text: _TRANS("ID_GENERATE_REPORT"),
            handler: function() {

                buttonReportSlas.show();
                buttonReportSlas.toggle();
                buttonReportCases.hide();
                buttonReportHistory.hide();

                storeFirstReport.setBaseParam('SLA_UID', comboSla.getValue());

                storeFirstReport.setBaseParam('TYPE_DATE', comboDates.getValue());
                storeFirstReport.setBaseParam('DATE_START', dateStart.getRawValue());
                storeFirstReport.setBaseParam('DATE_END', dateEnd.getRawValue());

                storeFirstReport.setBaseParam('TYPE_EXCEEDED', comboExceeded.getValue());
                storeFirstReport.setBaseParam('EXC_NUMBER', numberDuration.getValue());
                storeFirstReport.setBaseParam('EXC_DURATION_TYPE', comboDuration.getValue());
                storeFirstReport.setBaseParam('EXC_STATUS', comboTypeCases.getValue());

                reportValues.SLA_UID = comboSla.getValue();
                reportValues.TYPE_DATE = comboDates.getValue();
                reportValues.DATE_START = dateStart.getValue();
                reportValues.DATE_END = dateEnd.getValue();
                reportValues.TYPE_EXCEEDED = comboExceeded.getValue();
                reportValues.EXC_NUMBER = numberDuration.getValue();
                reportValues.EXC_DURATION_TYPE = comboDuration.getValue();
                reportValues.EXC_STATUS = comboTypeCases.getValue();

                storeFirstReport.load({params:{start:0, limit: parseInt(ID_PAGESIZE) }});
            }
        }, {
            icon: '/plugin/pmSLA/images/broom.png',
            text: _TRANS("ID_CLEAR_FILTERS"),
            handler: function() {
                comboSla.reset();
                dateStart.reset();
                dateEnd.reset();

                numberDuration.reset();
                comboDuration.reset();
                comboExceeded.reset();
                comboTypeCases.reset();
                comboDates.reset();

                dateStart.hide();
                Ext.getCmp('label_and').hide();
                dateEnd.hide();

                numberDuration.hide();
                comboDuration.hide();

                buttonReportSlas.hide();
                buttonReportCases.hide();
                buttonReportHistory.hide();
            }
        }]
    }]
});