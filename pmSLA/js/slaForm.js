var timeCron;
var valuesData = {
    VAL_ID_SLA: '',
    VAL_PROCESS_SLA: '',
    VAL_NAME_SLA: '',
    VAL_DESCRIPTION_SLA: '',
    VAL_TYPE_SLA: 'PROCESS',
    VAL_TASK_START_SLA: '',
    VAL_TASK_END_SLA: '',
    VAL_DURATION_NUMBER_SLA: '1',
    VAL_DURATION_TYPE_SLA: 'HOURS',
    VAL_CONDITION_SLA: '',
    VAL_SLA_PEN_ENABLED: '1',
    VAL_PENALITY_TIME_NUMBER_SLA: '1',
    VAL_PENALITY_UNIT_TYPE_SLA: '$US',
    VAL_PENALTY_UNIT_SLA: '',
    VAL_PENALITY_VALUE_NUMBER_SLA: '1',
    VAL_PENALITY_VALUE_TYPE_SLA: 'HOURS',
    VAL_STATUS_SLA: '1',
    VAL_TYPE_FORM: 'NEW' // new = New Item, UPDATE = Update Mode
};

var DISPLAY_NORMAL = 0;
var DISPLAY_EXPANDED = 1;

var storeProcess = new Ext.data.JsonStore({
    url: 'controllers/slaProxy.php',
    root: 'data',
    baseParams: {
        functionExecute: 'processList'
    },
    fields: [{
        name: 'PRO_UID'
    }, {
        name: 'PRO_TITLE'
    }, {
        name: 'PRO_DESCRIPTION'
    }]
});

var hdnUidSla = new Ext.form.Hidden({
    id: 'VAL_ID_SLA',
    value: valuesData.VAL_ID_SLA
});

var comboProcess = new Ext.form.ComboBox({
    id: 'VAL_PROCESS_SLA',
    valueField: 'PRO_UID',
    displayField: 'PRO_TITLE',
    fieldLabel: _TRANS("ID_PROCESS"),
    typeAhead: true,
    triggerAction: 'all',
    width: 320,
    value: valuesData.VAL_PROCESS_SLA,
    store: storeProcess,
    listeners: {
        select: function(combo, record, index) {
            var selVal = storeTaskStart.setBaseParam('PRO_UID', combo.getValue());
            storeTaskStart.reload({
                params: {
                    PRO_UID: selVal
                }
            });
            var selVal2 = storeTaskEnd.setBaseParam('PRO_UID', combo.getValue());
            storeTaskEnd.reload({params: {PRO_UID: selVal2}});

            comboTaskStartSla.setValue('');
            comboTaskEndSla.setValue('');

            changeSettings();
        }
    }
});

var textNameSla = new Ext.form.TextField({
    id: 'VAL_NAME_SLA',
    fieldLabel: _TRANS("ID_NAME"),
    width: 320,
    autoCreate: { tag: "input", type: "text", autocomplete: "off", maxlength: 50},
    value: valuesData.VAL_NAME_SLA,
    allowBlank: true,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var txtareaDescriptionSla = new Ext.form.TextArea({
    id: 'VAL_DESCRIPTION_SLA',
    fieldLabel: _TRANS("ID_DESCRIPTION"),
    allowBlank: true,
    value: valuesData.VAL_DESCRIPTION_SLA,
    width: 320,
    autoCreate: { tag: "textarea", autocomplete: "off", maxlength: 200},
    height: 40,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var comboStatusSla = new Ext.form.ComboBox({
    id: 'VAL_STATUS_SLA',
    fieldLabel: _TRANS("ID_STATUS"),
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    //mode: 'local',
    width: 146,
    editable: false,
    value: valuesData.VAL_STATUS_SLA,
    store: [
        ['1', _TRANS("ID_ACTIVE")],
        ['0', _TRANS("ID_INACTIVE")]
    ],
    listeners: {
        select: function() {
            changeSettings();
        }
    }
});

var checkboxReload = new Ext.form.Checkbox({
    id: 'VAL_RELOAD_SLA',
    name: 'VAL_RELOAD_SLA',
    hidden: true,
    value: false,
    fieldLabel: '',
    boxLabel: _TRANS("ID_RELOAD"),
});

var fieldsBaseInformation = new Ext.form.FieldSet({
    title: _TRANS("ID_SLA_INFORMATION"),
    items: [comboProcess, textNameSla, txtareaDescriptionSla, comboStatusSla, checkboxReload]
});

var comboTypeSla = new Ext.form.ComboBox({
    id: 'VAL_TYPE_SLA',
    valueField: 'ID',
    displayField: 'VAL',
    fieldLabel: _TRANS("ID_TYPE"),
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    //mode: 'local',
    width: 146,
    editable: false,
    value: valuesData.VAL_TYPE_SLA,
    store: [
        ['PROCESS', _TRANS("ID_ENTIRE_PROCESS")],
        ['RANGE', _TRANS("ID_MULTIPLE_TASKS")],
        ['TASK', _TRANS("ID_TASK")]
    ],
    listeners: {
        select: function(combo) {
            switch (combo.getValue()) {
                case 'PROCESS':
                    fieldsTasks.hide();
                    //comboTaskStartSla.fieldLabel = _TRANS("ID_TASK");
                    break;
                case 'RANGE':
                    Ext.getCmp('labelTo').show();
                    comboTaskEndSla.show();
                    fieldsTasks.show();
                    //comboTaskStartSla.fieldLabel = _TRANS("ID_TASK_START");
                    break;
                case 'TASK':
                    Ext.getCmp('labelTo').hide();
                    comboTaskEndSla.hide();
                    fieldsTasks.show();
                    //comboTaskStartSla.fieldLabel = _TRANS("ID_TASK");
                    break;
            }
            changeSettings();
        }
    }
});

var storeTaskStart = new Ext.data.JsonStore({
    url: 'controllers/slaProxy.php',
    root: 'data',
    baseParams: { functionExecute: 'listTasks' },
    fields: [{
        name: 'TAS_UID'
    }, {
        name: 'TAS_TITLE'
    }]
});

var comboTaskStartSla = new Ext.form.ComboBox({
    id: 'VAL_TASK_START_SLA',
    fieldLabel: _TRANS("ID_TASK"),
    valueField: 'TAS_UID',
    displayField: 'TAS_TITLE',
    typeAhead: true,
    //mode: 'local',
    triggerAction: 'all',
    lazyRender: true,
    width: 146,
    value: valuesData.VAL_TASK_START_SLA,
    store: storeTaskStart,
    listeners: {
        select: function(combo) {
            storeTaskEnd.setBaseParam('processId', combo.getValue());
            storeTaskEnd.setBaseParam('taskStart', combo.getValue());
            storeTaskEnd.load();
            //comboTaskEndSla.enable();
            comboTaskEndSla.setValue('');
            changeSettings();
        }
    }
});

var storeTaskEnd = new Ext.data.JsonStore({
    url: 'controllers/slaProxy.php',
    root: 'data',
    baseParams: {
        functionExecute: 'listTasks'
    },
    fields: [{
        name: 'TAS_UID'
    }, {
        name: 'TAS_TITLE'
    }]
});

var comboTaskEndSla = new Ext.form.ComboBox({
    id: 'VAL_TASK_END_SLA',
    valueField: 'TAS_UID',
    displayField: 'TAS_TITLE',
    typeAhead: true,
    //mode: 'local',
    triggerAction: 'all',
    lazyRender: true,
    width: 146,
    value: valuesData.VAL_TASK_END_SLA,
    store: storeTaskEnd,
    listeners: {
        select: function() {
            changeSettings();
        }
    }
});

var fieldsTasks = new Ext.form.CompositeField({
    hidden: true,
    items: [ comboTaskStartSla, { id: 'labelTo', html: _TRANS("ID_TO") }, comboTaskEndSla ]
});

var numberDurationSla = new Ext.form.NumberField({
    id: 'VAL_DURATION_NUMBER_SLA',
    fieldLabel: _TRANS("ID_DURATION"),
    allowBlank: false,
    emptyText: _TRANS("ID_THIS_FIELD_EMPTY"),
    decimalPrecision: 0,
    width: 51,
    value: valuesData.VAL_DURATION_NUMBER_SLA,
    minValue: 1,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var comboDurationSla = new Ext.form.ComboBox({
    id: 'VAL_DURATION_TYPE_SLA',
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    //mode: 'local',
    width: 90,
    editable: false,
    value: valuesData.VAL_DURATION_TYPE_SLA,
    store: [
        ['HOURS', _TRANS("ID_HOURS")],
        ['DAYS', _TRANS("ID_DAYS")]
    ],
    listeners: {
        select: function() {
            changeSettings();
        }
    }
});

var txtareaConditionSla = new Ext.form.TextArea({
    id: 'VAL_CONDITION_SLA',
    fieldLabel: _TRANS("ID_CONDITION"),
    allowBlank: true,
    value: valuesData.VAL_CONDITION_SLA,
    width: 340,
    height: 35,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var btnVariables = new Ext.Button({
    text: '@@',
    disabled: false,
    handler: function() {
        storeAllVariables.load({
            params: { PRO_UID: comboProcess.getValue() }
        });
        storeSystemList.load({
            params: { PRO_UID: comboProcess.getValue() }
        });
        storeProcessList.load({
            params: { PRO_UID: comboProcess.getValue() }
        });
        windowsFormVariable.show();
    }
});

var fieldsCondition = new Ext.form.CompositeField({
    items: [txtareaConditionSla, btnVariables]
});

var fieldsDuration = new Ext.form.CompositeField({
    items: [numberDurationSla, comboDurationSla]
});

var fieldsType = new Ext.form.FieldSet({
    title: _TRANS("ID_SLA_TYPE"),
    items: [comboTypeSla, fieldsTasks, fieldsDuration, fieldsCondition]
});

var checkboxPenalty = new Ext.form.Checkbox({
    id: 'VAL_PENALITY_ACTIVE_SLA',
    name: 'VAL_PENALITY_ACTIVE_SLA',
    fieldLabel: _TRANS("ID_ACTIVE_PENALTY"),
    listeners: {
        check: function(check, checkValue) {
            if (checkValue) {
                Ext.getCmp('VAL_FIELDS_PENALITY_SLA').show();
                changeSizeForm(DISPLAY_EXPANDED);
            } else {
                Ext.getCmp('VAL_FIELDS_PENALITY_SLA').hide();
                changeSizeForm(DISPLAY_NORMAL);
            }
            changeSettings();
        }
    }
});

var numberPenaltyTimeSla = new Ext.form.NumberField({
    id: 'VAL_PENALITY_TIME_NUMBER_SLA',
    fieldLabel: _TRANS("ID_PENALTY"),
    allowBlank: false,
    emptyText: _TRANS("ID_THIS_FIELD_EMPTY"),
    decimalPrecision: 0,
    width: 40,
    value: valuesData.VAL_PENALITY_TIME_NUMBER_SLA,
    minValue: 1,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var storePenaltyUnitSla = new Ext.data.SimpleStore({
    fields: ['ID', 'VAL'],
    data: [
        ['$US', _TRANS("ID_SUS")],
        ['POINTS', _TRANS("ID_POINTS")]
    ]
});


var comboPenaltyUnitSla = new Ext.form.ComboBox({
    id: 'VAL_PENALITY_UNIT_TYPE_SLA',
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    mode: 'local',
    width: 80,
    value: valuesData.VAL_PENALITY_UNIT_TYPE_SLA,
    store: storePenaltyUnitSla,
    listeners: {
        select: function(combo) {
            changeSettings();
        }
    }
});

var numberPenaltyValueSla = new Ext.form.NumberField({
    id: 'VAL_PENALITY_VALUE_NUMBER_SLA',
    allowBlank: false,
    emptyText: _TRANS("ID_THIS_FIELD_EMPTY"),
    decimalPrecision: 0,
    width: 40,
    value: valuesData.VAL_PENALITY_VALUE_NUMBER_SLA,
    minValue: 1,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var comboPenaltyValueSla = new Ext.form.ComboBox({
    id: 'VAL_PENALITY_VALUE_TYPE_SLA',
    valueField: 'ID',
    displayField: 'VAL',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender: true,
    //mode: 'local',
    width: 80,
    editable: false,
    value: valuesData.VAL_PENALITY_VALUE_TYPE_SLA,
    store: [
        ['HOURS', _TRANS("ID_HOURS")],
        ['DAYS', _TRANS("ID_DAYS")]
    ],
    listeners: {
        select: function() {
            changeSettings();
        }
    }
});

var textPenaltyUnitSla = new Ext.form.TextField({
    id: 'VAL_PENALTY_UNIT_SLA',
    width: 40,
    value: valuesData.VAL_PENALTY_UNIT_SLA,
    allowBlank: true,
    flex: 1,
    listeners: {
        change: function() {
            changeSettings();
        }
    }
});

var fieldsPenaltyValues = new Ext.form.CompositeField({
    items: [numberPenaltyValueSla, comboPenaltyUnitSla,
            { id: 'lblForEach', html: _TRANS("ID_FOR_EACH") },
            numberPenaltyTimeSla, comboPenaltyValueSla,
            { id: 'lblExceeded', html: _TRANS("ID_EXCEED") }
    ]
});

var fieldsPenaltys = new Ext.form.FieldSet({
    id: 'VAL_FIELDS_PENALITY_SLA',
    title: _TRANS("ID_ACTIVE_PENALTY"),
    checkboxToggle: true,
    collapsed: true,
    items: [fieldsPenaltyValues],
    onCheckClick: function() {
        var activeInactive = this.checkbox.dom.checked ? '1' : '0';
        if (activeInactive == 1) {
            this.expand();
            changeSizeForm(DISPLAY_EXPANDED);
        } else {
            this.collapse();
            changeSizeForm(DISPLAY_NORMAL);
        }
    }
});

saveButton = new Ext.Action({
    text: _('ID_SAVE'),
    disabled: true,
    handler: function() {
        if (comboTypeSla.getValue() == 'TASK' && comboTaskStartSla.getValue() == '') {
            Ext.Msg.alert(_('ID_ERROR'), _TRANS("ID_THIS_FIELD_TASK_EMPTY"));
            return false;
        }
        if (comboTypeSla.getValue() == 'RANGE' && (comboTaskStartSla.getValue() == '' || comboTaskEndSla.getValue() == '')) {
            Ext.Msg.alert(_('ID_ERROR'), _TRANS("ID_THIS_FIELD_TASK_EMPTY"));
            return false;
        }
        valuesData.VAL_PROCESS_SLA = comboProcess.getValue();
        formSLA.getForm().submit({
            method: 'post',
            params: {
                functionExecute: (valuesData.VAL_TYPE_FORM == 'NEW') ? 'saveSla' : 'updateSla',
                VAL_ID_SLA: valuesData.VAL_ID_SLA,
                PRO_UID: comboProcess.getValue(),
                VAL_TYPE_SLA_ID: comboTypeSla.getValue(),
                VAL_TASK_START_SLA_ID: comboTaskStartSla.getValue(),
                VAL_TASK_END_SLA_ID: comboTaskEndSla.getValue(),
                VAL_PENALITY: fieldsPenaltys.checkbox.dom.checked
            },
            success: function(form, action) {
                storeSlaList.reload();
                gridLisSLA.getView().refresh();
                PMExt.notify('pmSLA', _TRANS("ID_SUCCESSFULLY_SAVED"));
                windowsFormSla.hide();
                saveButton.disable();
            },
            failure: function(obj, resp) {
                Ext.Msg.alert(_('ID_ERROR'), resp.result.msg);
            }
        });
    }
});

changeSettings = function() {
    saveButton.enable();
}
changeSizeForm = function(size) {
    if (size == 0 || typeof(size) == 'undefined') {
        windowsFormSla.setSize(540, 450);
    } else {
        windowsFormSla.setSize(540, 500);
    }
}

var formSLA = new Ext.FormPanel({
    labelWidth: 80,
    url: 'controllers/slaProxy.php',
    frame: true,
    autoWidth: true,
    autoScroll: true,
    bodyStyle: 'padding:5px 5px 0',
    items: [hdnUidSla, fieldsBaseInformation, fieldsType, fieldsPenaltys]
})


var windowsFormSla = new Ext.Window({
    layout: 'fit',
    title: _TRANS("ID_NEW_SLA"),
    icon: '/plugin/pmSLA/images/time_edit.png',
    width: 540,
    height: 450,
    plain: true,
    modal: true,
    closeAction: 'hide',
    maximizable: true,
    items: [formSLA],
    buttons: [
        saveButton,
        {
            text: _('ID_CANCEL'),
            handler: function() {
                windowsFormSla.hide();
                saveButton.disable();
            }
        }
    ]
});