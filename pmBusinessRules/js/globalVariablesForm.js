var storeGlobalVariables;

var baseGlobalVariables = new Ext.form.Hidden({
    name: 'ACCION',
});

var nameGlobalVariables = new Ext.form.TextField({
    fieldLabel: 'Name',
    name: 'GLOBAL_UID',
    width: 180,
    allowBlank: false
});

var valueGlobalVariables = new Ext.form.TextArea({
    fieldLabel: 'Value',
    name: 'GLOBAL_VALUE',
    width: 180,
    height: 70,
    allowBlank: false
});

var typeGlobalVariables = new Ext.form.ComboBox({
    valueField: 'UID',
    displayField: 'VALUE',
    value: 'String',

    fieldLabel: 'Type',
    forceSelection: true,
    triggerAction: 'all',  
    editable: false,
    name: 'GLOBAL_TYPE',
    width: 180,
    allowBlank: false,
    store: [['String','String'],['Int','Int'],['Date','Date'],['Boolean','Boolean']],
    listeners: {
        select : function (combo) {
            if (combo.getValue() == 'Date') {
                formartGlobalVariables.setVisible(true);
                if (formartGlobalVariables.getValue() == 'vacio') {
                    formartGlobalVariables.setValue('Y-m-d H:i:s');
                }
            } else {
                formartGlobalVariables.setVisible(false);
            }
        }
    }
});

var formartGlobalVariables = new Ext.form.TextField({
    fieldLabel: 'Format',
    name: 'GLOBAL_FORMAT',
    width: 180,
    allowBlank: false
});

var formGlobalVariables = new Ext.FormPanel({
    labelWidth : 50,
    url : 'controllers/globalVariablesProxy',
    frame : true,
    autoScroll: true,
    monitorValid : true,
    baseParams: {'functionExecute': 'saveGlobalVariables'},
    items:[
        baseGlobalVariables,
        nameGlobalVariables,
        valueGlobalVariables,
        typeGlobalVariables,
        formartGlobalVariables
    ],

    buttons:[{
        text : 'Guardar',
        iconCls: 'ss_diskG',
        formBind : true,
        handler : function(){
            formGlobalVariables.getForm().submit({
                method : 'POST',
                waitTitle : 'Conectando',
                waitMsg : 'Procesando datos, espere un momento por favor...',
                success : function () {
                    winGlobalVariables.hide();
                    storeGlobalVariables.load();
                },
                failure : function (form, action) {
                    if (action.failureType == 'server'){
                        obj = Ext.util.JSON.decode(action.response.responseText);
                        Ext.Msg.alert('Login Fallido !', obj.reason);
                    } else {
                        Ext.Msg.alert('Warning!', 'Authentication server is unreachable : ' + action.response.responseText);
                    }
                }
            });
        }
    },
    {
        text : 'Cancelar',
        iconCls: 'ss_crossG',
        handler : function(){
            winGlobalVariables.hide();
        }
    }]
});

var winGlobalVariables = new Ext.Window({
    layout: 'fit',
    title : 'Ventana',
    width : 270,
    height: 228,
    closeAction: 'hide',
    resizable: false,
    plain: true,
    modal: true,
    border: false,
    items: [formGlobalVariables]
});