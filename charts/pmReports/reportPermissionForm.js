// Declare global variables
var storePmrOwnerType;
var storePmrOwnerUID;
var hiddenPmrUID;
var txtPmrTitle;
var cboPmrOwnerType;
var cboPmrOwnerUID;
var formFields;
var PMR_UID;
var PMR_OWNER_UID;
var reportPermissionsFrm;

// On ready
Ext.onReady(function() {
    // Stores
    storePmrOwnerType = new Ext.data.ArrayStore({
        idIndex: 0,
        fields: ['id', 'value'],
        data:   [['USER', 'User'], ['DEPARTMENT', 'Department'], ['GROUP', 'Group'], ['EVERYBODY', 'Everybody']]
    });

    storePmrOwnerUID = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url:    '../dashboard/getOwnersByType',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            totalProperty: 'total',
            root:          'owners',
            fields:        [{name: 'OWNER_UID',  type: 'string'}, {name: 'OWNER_NAME', type: 'string'}]
        }),
        autoLoad: true,
        listeners: {
            beforeload: function (store) {
                storePmrOwnerUID.baseParams = {'option': 'OWNERTYPE', 'type': cboPmrOwnerType.getValue()};
            },
            load: function (store, record, option) {
                if (PMR_UID) {
                    cboPmrOwnerUID.setValue(PMR_OWNER_UID);
                }
                else {
                    if (store.getAt(0)) {
                      cboPmrOwnerUID.setValue(store.getAt(0).get(cboPmrOwnerUID.valueField));
                    }
                }
                if (cboPmrOwnerType.getValue() == 'EVERYBODY') {
                    cboPmrOwnerUID.clearValue();
                    cboPmrOwnerUID.disable(true);
                    cboPmrOwnerUID.hide();
                    PMR_OWNER_UID='';
                }
                else {
                    cboPmrOwnerUID.enable(true);
                    cboPmrOwnerUID.show();
                }
            }
        }
    });

    // Fields
    hiddenPmrUID = new Ext.form.Hidden({
        id:   'hiddenPmrUID',
        name: 'PMR_UID'
    });
    hiddenAddTabUID = new Ext.form.Hidden({
        id:   'hiddenAddTabUID',
        name: 'ADD_TAB_UID'
    });

    txtPmrTitle = new Ext.form.TextField({
        id:         'txtPmrTitle',
        name:       'ADD_TAB_NAME',
        fieldLabel: _('ID_SIMPLE_REPORT'),
        readOnly:   true,
        allowBlank: false,
        width:      320,
        listeners:  {
            blur: function() {
                this.setValue(this.getValue().trim());
            }
        }
    });

    cboPmrOwnerType = new Ext.form.ComboBox({
        id:            'cboPmrOwnerType',
        name:          'PMR_TYPE',
        fieldLabel:    _('ID_ASSIGN_TO'),
        editable:      false,
        width:         320,
        store:         storePmrOwnerType,
        triggerAction: 'all',
        mode:          'local',
        value:         'USER',
        valueField:    'id',
        allowBlank:    false,
        displayField:  'value',
        listeners:     {
            select: function (combo, record, index) {
                cboPmrOwnerUID.store.removeAll();
                cboPmrOwnerUID.clearValue();
                storePmrOwnerUID.baseParams = {'option': 'OWNERTYPE', 'type': combo.getValue()};
                cboPmrOwnerUID.store.reload();
                cboPmrOwnerUID.setValue(null);
                cboPmrOwnerUID.store.on("load", function (store) {
                    PMR_OWNER_UID = '';
                    if (storePmrOwnerUID.getAt(0)) {
                        cboPmrOwnerUID.setValue(storePmrOwnerUID.getAt(0).get(cboPmrOwnerUID.valueField));
                    }
                });
            }
        }
    });

    cboPmrOwnerUID = new Ext.form.ComboBox({
        id:            'cboPmrOwnerUID',
        name:          'PMR_OWNER_UID',
        fieldLabel:    _('ID_NAME'),
        editable:      false,
        width:         320,
        store:         storePmrOwnerUID,
        triggerAction: 'all',
        mode:          'local',
        allowBlank:    false,
        valueField:    'OWNER_UID',
        displayField:  'OWNER_NAME'
    });

  formFields = [
    new Ext.form.FieldSet({
      id:    'general',
      title: '',
      items: [hiddenPmrUID, hiddenAddTabUID, txtPmrTitle, cboPmrOwnerType, cboPmrOwnerUID]
    })
  ];

  // Form
    reportPermissionsFrm = new Ext.form.FormPanel({
        id:  'reportPermissionsFrm',
        labelWidth: 100,
        border: true,
        width: 460,
        height: 150,
        frame: true,
        title: '',
        items: formFields,
        buttonAlign: 'right',
        buttons: [
          new Ext.Action({
           id:      'btnSubmit',
           text:    _('ID_SAVE'),
           handler: function () {
             if (reportPermissionsFrm.getForm().isValid()) {
                var myMask = new Ext.LoadMask(Ext.getBody(), {msg: 'Saving. Please wait...'});
                myMask.show();
                
                Ext.Ajax.request({
                  url:      '../pmReports/reportsAjax?action=verifyDate',
                  method:   'POST',
                  params:   reportPermissionsFrm.getForm().getFieldValues(),
                  success: function (result, request) {
                                myMask.hide();
                                var dataResponse = Ext.util.JSON.decode(result.responseText)
                                if (dataResponse.status == 'OK') {
                                   Ext.Ajax.request({
                                        url:      '../pmReports/reportsAjax?action=saveReportPermissions',
                                        method:   'POST',
                                        params:   reportPermissionsFrm.getForm().getFieldValues(),
                                        success: function (result, request) {
                                                     myMask.hide();
                                                     var dataResponse = Ext.util.JSON.decode(result.responseText)
                                                     if (dataResponse.status == 'OK') {
                                                         newin.hide();
                                                        store.reload();
                                                     } else {
                                                         Ext.MessageBox.alert( _('ID_ALERT'), _('ID_FAILED_SAVE_PERMISSIONS') );
                                                     }
                                                 },
                                        failure: function (result, request) {
                                                   myMask.hide();
                                                   Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
                                                 }
                                    });
                                } else {
                                    Ext.MessageBox.alert( _('ID_ALERT'), _('ID_PERMISSION_ALREADY_EXIST'));
                                }
                           },
                  failure: function (result, request) {
                             myMask.hide();
                             Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
                           }
                });
            }
            else {
              Ext.MessageBox.alert(_('ID_INVALID_DATA'), _('ID_CHECK_FIELDS_MARK_RED'));
            }
           }
          }),
          {
            xtype:   'button',
            id:      'btnCancel',
            text:    _('ID_CANCEL'),
            handler: function () {
              newin.hide();
            }
         }
        ]
    });

    newin = new Ext.Window({
        title: '',//_('ID_PERMISSIONS'),
        autoHeight: true,
        id: 'newin',
        modal: true,
        width: 470,
        items: [reportPermissionsFrm]
    });

});
