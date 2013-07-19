Ext.namespace("pmFtpMonitor");

var hdnFTP_UID;
var cmbCONNECTION_TYPE, cmbPRO_UID, cmbTAS_UID, cmbDEL_USER_UID, cmbINPUT_DOCUMENT_UID;
var txtHOST, txtPORT, txtUSER, txtPASS, txtSEARCH_PATTERN, txtFTP_PATH;
var rdoXML_SEARCH, rdoFTP_STATUS;
var btnSubmit;
var frmEdit;
pmFtpMonitor.application = {
    init:function(){
        Ext.QuickTips.init();
        hdnFTP_UID = new Ext.form.Hidden({
            id: "hdnFTP_UID",
            name: "FTP_UID",
            value: FTP_UID
        });
        cmbCONNECTION_TYPE = new Ext.form.ComboBox({
            id: "cmbCONNECTION_TYPE",
            name: "CONNECTION_TYPE",
            mode: "local",
            store: new Ext.data.SimpleStore({
                fields: ["conntype"],
                data:[["FTP"],["SHARED"]] // ,["SFTP"]
            }),
            displayField: "conntype",
            valueField: "conntype",
            fieldLabel: "Connection Type",
            editable: false,
            triggerAction: 'all',
            width: 500,
            allowBlank: false,
            listeners: {
                select: function(){
                    if(cmbCONNECTION_TYPE.getValue() == "SHARED") {
                        hideField(txtHOST);
                        hideField(txtPORT);
                        hideField(txtUSER);
                        hideField(txtPASS);                        
                        txtFTP_PATH.allowBlank = false;
                    } else {
                        showField(txtHOST);
                        showField(txtPORT);
                        showField(txtUSER);
                        showField(txtPASS);                        
                        txtFTP_PATH.allowBlank = true;
                    }
                }
            }
        });
        cmbCONNECTION_TYPE.setValue((CONNECTION_TYPE == "") ? "FTP":CONNECTION_TYPE);
        txtHOST = new Ext.form.TextField({
            id: "txtHOST",
            name: "HOST",
            value: HOST,
            fieldLabel: "Host",
            editable: true,
            allowBlank: false,
            width: 500,
            listeners: {
                blur: validateCnn
            }
        });
        txtPORT = new Ext.form.TextField({
            id: "txtPORT",
            name: "PORT",
            originalValue: PORT,
            fieldLabel: "Port",
            editable: true,
            width: 500,
            vtype: "port",
            listeners: {
                blur: validateCnn
            }
        });
        txtUSER = new Ext.form.TextField({
            id: "txtUSER",
            name: "USER",
            value: USER,
            fieldLabel: "User",
            editable: true,
            width: 500,
            listeners: {
                blur: validateCnn
            }
        });
        txtPASS = new Ext.form.TextField({
            id: "txtPASS",
            name: "PASS",
            fieldLabel: "Password",
            inputType: "password",
            editable: true,
            width: 500,
            hiddenValue: PASS,
            listeners: {
                blur: validateCnn
            }
        });
        txtSEARCH_PATTERN = new Ext.form.TextField({
            id: "txtSEARCH_PATTERN",
            name: "SEARCH_PATTERN",
            value: SEARCH_PATTERN,
            fieldLabel: "Search Pattern",
            editable: true,
            width: 500
        });
        txtFTP_PATH = new Ext.form.TextField({
            id: "txtFTP_PATH",
            name: "FTP_PATH",
            fieldLabel: "Path",
            editable: true,
            width: 500,
            validationEvent: "blur",
            listeners: {
                blur: validateCnn
            }
        });
        rdoXML_SEARCH = new Ext.form.Checkbox({
            id: "rdoXML_SEARCH",
            name: "XML_SEARCH",
            checked: XML_SEARCH == "TRUE",
            fieldLabel: "Pass variables in an XML file",
            inputValue: "TRUE"
        });
        cmbPRO_UID = new Ext.form.ComboBox({
            id: "cmbPRO_UID",
            name: "PRO_UID",
            fieldLabel: "Process",
            hiddenName: "PRO_UID",
            hiddenValue: PRO_UID,
            store: new Ext.data.Store( {
                proxy: new Ext.data.HttpProxy( {
                    url: 'ftpMonitorAjax',
                    method: 'POST'
                }),
                baseParams: {
                    action: 'showProcessList'
                },
                reader: new Ext.data.JsonReader( {
                    fields: [ {
                        name: 'value'
                    }, {
                        name: 'name'
                    } ]
                }),
                listeners: {
                    load: function() {
                        cmbPRO_UID.setValue(PRO_UID);
                        cmbTAS_UID.store.load({
                            params: {
                                action: 'showTaskList',
                                proUid: cmbPRO_UID.getValue()
                            }
                        });          
                        cmbINPUT_DOCUMENT_UID.store.load({
                            params: {
                                action: 'showInputDocumentList',
                                proUid: cmbPRO_UID.getValue()
                            }
                        });
                    }
                },
                autoLoad: true
            }),    
            valueField: "value",
            displayField: "name",
            emptyText: TRANSLATIONS.ID_SELECT,
            width: 500,
            allowBlank: false,
            selectOnFocus: true,
            editable: false,
            triggerAction: "all",
            mode: "local",
            listeners: {
                select: function(){
                    cmbINPUT_DOCUMENT_UID.store.load({
                        params: {
                            action: 'showInputDocumentList',
                            proUid: cmbPRO_UID.getValue()
                        }
                    });
                    cmbTAS_UID.store.load({
                        params: {
                            action: "showTaskList",
                            proUid: cmbPRO_UID.getValue()
                        }
                    });          
                }                 
            }
        });
        cmbTAS_UID = new Ext.form.ComboBox({
            id: "cmbTAS_UID",
            name: "TAS_UID",
            fieldLabel: "Task",
            hiddenName: "TAS_UID",
            hiddenValue: TAS_UID,
            store: new Ext.data.Store( {
                proxy: new Ext.data.HttpProxy( {
                    url: 'ftpMonitorAjax',
                    method: 'POST'
                }),
                baseParams: {
                    action: 'showTaskList',
                    proUid: cmbPRO_UID.getValue()
                },
                reader: new Ext.data.JsonReader( {
                    fields: [ {
                        name: 'TAS_UID'
                    }, {
                        name: 'TAS_TITLE'
                    } ]
                }),
                listeners:{
                    load: function(){
                        cmbTAS_UID.setValue(TAS_UID);
                        cmbDEL_USER_UID.store.load({
                            params: {
                                action: 'showCaseUserList',
                                tasUid: cmbTAS_UID.getValue()
                            }
                        });
                    }
                },
                autoLoad: false
            }),    
            valueField: 'TAS_UID',
            displayField: 'TAS_TITLE',
            emptyText: TRANSLATIONS.ID_SELECT,
            width: 500,
            allowBlank: false,
            selectOnFocus: true,
            editable: false,
            triggerAction: 'all',
            mode: 'local',
            listeners: {
                select: function(){
                    cmbDEL_USER_UID.store.load({
                        params: {
                            action: 'showCaseUserList',
                            tasUid: cmbTAS_UID.getValue()
                        }
                    });          
                }                 
            }

        });
        cmbDEL_USER_UID = new Ext.form.ComboBox({
            id: "cmbDEL_USER_UID",
            name: "DEL_USER_UID",
            fieldLabel: "User",
            hiddenName: "DEL_USER_UID",
            hiddenValue: DEL_USER_UID,
            store: new Ext.data.Store( {
                proxy: new Ext.data.HttpProxy( {
                    url: 'ftpMonitorAjax',
                    method: 'POST'
                }),
                baseParams: {
                    action: 'showCaseUserList',
                    tasUid: cmbTAS_UID.getValue()
                },
                reader: new Ext.data.JsonReader( {
                    fields: [ {
                        name: 'USR_UID'
                    }, {
                        name: 'USR_USERNAME'
                    } ]
                }),
                listeners:{
                    load: function(){
                        cmbDEL_USER_UID.setValue(DEL_USER_UID);
                    }
                },
                autoLoad: false
            }),    
            valueField: 'USR_UID',
            displayField: 'USR_USERNAME',
            emptyText: TRANSLATIONS.ID_SELECT,
            width: 500,
            allowBlank: false,
            selectOnFocus: true,
            editable: false,
            triggerAction: 'all',
            mode: 'local'
        });
        cmbINPUT_DOCUMENT_UID = new Ext.form.ComboBox({
            id: "cmbINPUT_DOCUMENT_UID",
            name: "INPUT_DOCUMENT_UID",
            fieldLabel: "Input Document",
            hiddenName: "INPUT_DOCUMENT_UID",
            hiddenValue: INPUT_DOCUMENT_UID,
            store: new Ext.data.Store( {
                proxy: new Ext.data.HttpProxy( {
                    url: 'ftpMonitorAjax',
                    method: 'POST'
                }),
                baseParams: {
                    action: 'showInputDocumentList',
                    proUid: cmbPRO_UID.getValue()
                },
                reader: new Ext.data.JsonReader( {
                    fields: [ {
                        name: 'INP_DOC_UID'
                    }, {
                        name: 'CON_VALUE'
                    } ]
                }),
                listeners:{
                    load: function(){
                        cmbINPUT_DOCUMENT_UID.setValue(INPUT_DOCUMENT_UID);
                    }
                },
                autoLoad: false
            }),    
            valueField: 'INP_DOC_UID',
            displayField: 'CON_VALUE',
            emptyText: TRANSLATIONS.ID_SELECT,
            width: 500,
            allowBlank: false,
            selectOnFocus: true,
            editable: false,
            triggerAction: 'all',
            mode: 'local'
        });
        rdoFTP_STATUS = new Ext.form.Checkbox({
            id: "rdoFTP_STATUS",
            name: "FTP_STATUS",
            checked: FTP_STATUS == "ACTIVE",
            fieldLabel: "Active",
            inputValue: "ACTIVE"
        });
        btnSubmit = new Ext.Button({
            id: "btnSubmit",
            text: "Submit",
            handler: function () {
                if (frmEdit.getForm().isValid())
                    Ext.Ajax.request({
                        url: "ftpMonitorAjax",
                        method: "POST",
                        params: {
                            "formData": Ext.util.JSON.encode(frmEdit.getForm().getValues()),
                            "action": "editSettings"
                        },
                         
                        success:function (result, request) {
                            Ext.MessageBox.alert("Alert",Ext.util.JSON.decode(result.responseText)["message"]);
                            location.href = 'ftpMonitorSettingList';
                        },
                        failure:function (result, request) {
                            myMask.hide();
                            Ext.MessageBox.alert("Alert", "Failure saving settings");
                        }
                    });
            }
        });
        frmEdit = new Ext.FormPanel({
            id: "frmEdit",
            hidden: false,   
            labelWidth: 170, //The width of labels in pixels
            //bodyStyle: "padding:0.5em;",
            title : "&nbsp; ",
            border: false,
                     
            items: [hdnFTP_UID, cmbCONNECTION_TYPE, txtHOST, txtPORT, txtUSER, txtPASS, 
            txtFTP_PATH, txtSEARCH_PATTERN, rdoXML_SEARCH, cmbPRO_UID, cmbTAS_UID, cmbDEL_USER_UID,
            cmbINPUT_DOCUMENT_UID, rdoFTP_STATUS],
            
            //renderTo: "divMain",    
            width: 700,
            frame: true,
            buttonAlign: "right",
            buttons: [btnSubmit,{
                text:"Reset",
                handler: function () {
                    frmEdit.getForm().reset();
                }
            }]
        });
        frmEdit.render(document.body);
        
        Ext.apply(Ext.form.VTypes, {
            "port": function(val, field){
                var portVal = /^(0|[1-9][0-9]{0,3}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$/;
                return portVal.test(val);
            },
            "portText": "0 - 65535"
        });
        Ext.form.VTypes.port(PORT, txtPORT);
        txtPORT.setValue(PORT);
        if(CONNECTION_TYPE == "SHARED") {
            hideField(txtHOST);
            hideField(txtPORT);
            hideField(txtUSER);
            hideField(txtPASS);                        
        } else {
            showField(txtHOST);
            showField(txtPORT);
            showField(txtUSER);
            showField(txtPASS);                        
        }                    
        txtFTP_PATH.setValue(FTP_PATH);
        validateCnn();
    }
}

Ext.onReady(pmFtpMonitor.application.init, pmFtpMonitor.application);

validateCnn = function(){
    Ext.Ajax.request({
        url: "ftpMonitorAjax",
        method: "POST",
        params: {
            "action": "isCnn",
            "formData": Ext.util.JSON.encode(frmEdit.getForm().getValues())
        },    
        success: function (response) {
            var res = Ext.util.JSON.decode(response.responseText);
            var field;
            if (res["success"] != "true")
                if (field = frmEdit.getForm().findField(res["field"]))
                    field.markInvalid(res["message"]);
        }
    });
    
}

/*
 * hideField
 */
hideField = function(field)
{
    if(field) {
        field.disable();// for validation
        field.hide();
        var labelEl = field.getEl();
        if(labelEl) {
            labelEl.up('.x-form-item').setDisplayed(false); // hide label
        }
    }
}
/*
 * showField
 */
showField = function(field)
{
    if(field) {
        field.enable();
        field.show();
        var labelEl = field.getEl();
        if(labelEl) {
            field.getEl().up('.x-form-item').setDisplayed(true); // show label
        }
    }
}