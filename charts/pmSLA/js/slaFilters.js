var storeSlaReport;

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
    width: 155,
    editable: false,
    value: '- All -',
    store: storeSla
});

var dateStart = new Ext.form.DateField({
    id : 'VAL_DATE_START',
    xtype: 'datefield',
    format: 'Y-d-m',
    forceSelection : true,
    width : 120,
    emptyText : _TRANS("ID_SELECT_DATE"),
    editable : true
});

var dateEnd = new Ext.form.DateField({
    id : 'VAL_DATE_END',
    xtype: 'datefield',
    format: 'Y-d-m',
    forceSelection : true,
    width : 120,
    emptyText : _TRANS("ID_SELECT_DATE"),
    editable : true
});

var fieldsDates = new Ext.form.CompositeField({
    fieldLabel : _TRANS("ID_DATES"),
    items: [{html: _TRANS("ID_BETWEEN")}, dateStart, { html: _TRANS("ID_AND") }, dateEnd]
});

var numberDuration = new Ext.form.NumberField({
    id: 'VAL_DURATION_NUMBER',
    allowBlank : false,
    decimalPrecision : 0,
    width: 51,
    value: 1,
    minValue : 1
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
    store: [['HOURS', _TRANS("ID_HOURS")], ['DAYS', _TRANS("ID_DAYS")]]
});

var comboExceeded = new Ext.form.ComboBox({
    id: 'VAL_EXCEEDED',
    fieldLabel : _TRANS("ID_EXCEEDED"),
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    width: 155,
    editable: false,
    value: 'ALL',
    store: [
        ['ALL', _TRANS("ID_ALL2")],
        ['NO_EXCEEDED', _TRANS("ID_NO_EXCEEDED")],
        ['EXCEEDED', _TRANS("ID_EXCEEDED")],
        ['EXCEEDED_LESS', _TRANS("ID_EXCEEDED_LESS")],
        ['EXCEEDED_MORE', _TRANS("ID_EXCEEDED_MORE")]
    ],
    listeners: {
        select: function (combo, record, index) {
            switch (combo.getValue())
            {
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
    fieldLabel : _TRANS("ID_STATUS_CASE"),
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    width: 155,
    editable: false,
    value: 'ALL',
    store: [['ALL', _TRANS("ID_ALL2")], ['OPEN', _TRANS("ID_OPEN")], ['COMPLETED', _TRANS("ID_COMPLETED")]]
});

var panelFilters = new Ext.Panel({
    region: 'north',
    height: 180,
    margins: '0 0 0 0',
    frame: true,
    iconCls : 'icon-search-report',
    title: _TRANS("ID_FILTERS"),
    labelAlign: 'left',
    align: 'center',
    labelStyle: 'font-weight:bold;',
    collapsible:true,

    items: [{
        layout:'column',
        items:[{
            columnWidth: .32,
            labelWidth: 40,
            layout: 'form',
            items: [{html: '&nbsp;'}]
        },{
            columnWidth: .68,
            labelWidth: 100,
            layout: 'form',
            items: [
                comboSla,
                fieldsDates,
                fieldsDuration,
                comboTypeCases
            ]
        }],

        buttonAlign: 'center',
        buttons:[{
            iconCls:'button_menu_ext ss_sprite ss_report',
            text: _TRANS("ID_GENERATE_REPORT"),
            handler: function() {
                //Ext.getCmp('Server').
                storeSlaReport.setBaseParam('SLA_UID', comboSla.getValue());
                storeSlaReport.setBaseParam('DATE_START', dateStart.getValue());
                storeSlaReport.setBaseParam('DATE_END', dateEnd.getValue());

                storeSlaReport.setBaseParam('TYPE_EXCEEDED', comboExceeded.getValue());
                storeSlaReport.setBaseParam('EXC_NUMBER', numberDuration.getValue());
                storeSlaReport.setBaseParam('EXC_DURATION_TYPE', comboDuration.getValue());
                storeSlaReport.setBaseParam('EXC_STATUS', comboTypeCases.getValue());
                storeSlaReport.load();
            }
        },{
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

                numberDuration.hide();
                comboDuration.hide();
            }
        }]
    }],
    listeners: {
        afterrender: function (ob1, ob2, ob3) {
            numberDuration.hide();
            comboDuration.hide();
        }
    }
});


