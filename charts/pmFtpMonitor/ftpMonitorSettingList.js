Ext.namespace("pmFtpMonitor");

var pageSize;
var newButton, editButton;
var deactivateButton, activateButton;
var storeListProcess, storeSettings;
var smodel;
pmFtpMonitor.application = {
    init:function(){
        //CONFIG params
        pageSize = parseInt(CONFIG.pageSize);
        newButton = new Ext.Action({
            text: _('ID_NEW'),
            iconCls: 'button_menu_ext ss_sprite  ss_add',
            disabled: false,
            handler: newSetting
        });
        editButton = new Ext.Action({
            text: _('ID_EDIT'),
            iconCls: 'button_menu_ext ss_sprite  ss_pencil',
            disabled: true,
            handler: editSetting
        }); 
        deactivateButton = new Ext.Action({
            text: "Disable",
            iconCls: "button_menu_ext ss_sprite ss_tag_red",
            disabled: true,
            handler: switchSettingStatus
        });
        
        activateButton = new Ext.Action({
            text: "Enable",
            iconCls: 'button_menu_ext ss_sprite ss_tag_green',
            disabled: true,
            handler: switchSettingStatus
        });

        storeListProcess = function (n, r, i, uid, action) {
            var myMask = new Ext.LoadMask(Ext.getBody(), {
                msg:"Load pmFtpMonitor setting list..."
            });
            myMask.show();

            Ext.Ajax.request({
                url: "ftpMonitorAjax",
                method: "POST",
                params: {
                    "action": action,
                    "uid": uid,
                    "pageSize": n, 
                    "limit": r, 
                    "start": i
                },
                success:function (result, request) {
                    storeSettings.loadData(Ext.util.JSON.decode(result.responseText));
                    myMask.hide();
                },
                failure:function (result, request) {
                    myMask.hide();
                    Ext.MessageBox.alert("Alert", "Failure load setting list");
                }
            });
        };
        //store
        storeSettings = new Ext.data.Store({
            proxy:new Ext.data.HttpProxy({
                url:    "ftpMonitorAjax",
                method: "POST"
            }),          
            reader:new Ext.data.JsonReader({
                root: "resultRoot",
                totalProperty: "resultTotal",
                fields: [{
                    name: "FTP_UID"
                },{
                    name: "CONNECTION_TYPE"
                },{
                    name: "HOST"
                },{
                    name: "PORT"
                },{
                    name: "USER"
                },{
                    name: "FTP_PATH"
                },{
                    name: "SEARCH_PATTERN"
                },{
                    name: "PRO_TITLE"
                },{
                    name: "TAS_TITLE"
                },{
                    name: "USR_USERNAME"
                },{
                    name: "FTP_STATUS"
                }]
            }),     
            listeners:{
                beforeload:function (store) {
                    this.baseParams = {
                        "action": "showSettingsList",
                        "pageSize": pageSize
                    };
                }
            }
        });
        //cmodel
        var cmodelSettings = new Ext.grid.ColumnModel({
            defaults: {
                width:50,
                sortable:true
            },
            columns:[{
                id: "FTP_UID", 
                dataIndex: "FTP_UID", 
                hidden: true
            },{
                header: "Connection Type", 
                dataIndex: "CONNECTION_TYPE"
            },{
                header: "Host", 
                dataIndex: "HOST" 
            },{
                header: "Port", 
                dataIndex: "PORT" 
            },{
                header: "User", 
                dataIndex: "USER"
            },{
                header: "Path", 
                dataIndex: "FTP_PATH"
            },{
                header: "Search Pattern", 
                dataIndex: "SEARCH_PATTERN"
            },{
                header: "Process", 
                dataIndex: "PRO_TITLE"
            },{
                header: "Task", 
                dataIndex: "TAS_TITLE"
            },{
                header: "Case User", 
                dataIndex: "USR_USERNAME"
            },{
                header: "Status", 
                dataIndex: "FTP_STATUS"
            }]
        });
        smodel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners:{
                rowselect: function(sm){
                    editButton.enable();
                    rowSelected = grdpnlSettings.getSelectionModel().getSelected();
                    if (rowSelected.data.FTP_STATUS == "ACTIVE"){
                        deactivateButton.enable();
                        activateButton.disable();
                    } else {
                        deactivateButton.disable();
                        activateButton.enable();
                    }
                        
                },
                rowdeselect: function(sm){
                    editButton.disable();
                    deactivateButton.disable();
                    activateButton.disable();
                }
            }
        });

        var grdpnlSettings = new Ext.grid.GridPanel({
            id: "grdpnlSettings",
            hidden: false,
            store: storeSettings,
            colModel: cmodelSettings,     
            columnLines: true,
            viewConfig: {
                forceFit: true
            },
            title : "FTP Monitor Settings",
            sm: smodel,
            enableColumnResize: true,
            enableHdMenu: true, //Menu of the column
      
            tbar: [newButton, '-', editButton, deactivateButton, activateButton/*, "-" ,txtSearch, btnTextClear, btnSearch*/],
            /*bbar: pagingUser,*/    
            height: 400, 
            frame: true,
            renderTo: "divMain",
      
            listeners:{
                dblclick: editSetting
            }
        });
              
        //Initialize events
        storeListProcess(pageSize, pageSize, 0, '', 'showSettingsList');
    }
}
// handlers
newSetting= function() {
    location.href = 'ftpMonitorSettingEdit';
};
editSetting= function() {
    location.href = 'ftpMonitorSettingEdit?uid=' + rowSelected.data.FTP_UID;
};
switchSettingStatus= function() {
    if (confirm('Are you sure?')){
        Ext.Ajax.request({
            url: "ftpMonitorAjax",
            method: "POST",
            params: {
                "action": "switchSettingsStatus",
                "uid": rowSelected.data.FTP_UID
            },
            success:function (result, request) {
                Ext.MessageBox.alert("Alert", Ext.util.JSON.decode(result.responseText).message);
                storeListProcess(pageSize, pageSize, 0, '', 'showSettingsList');
                deactivateButton.disable();
                activateButton.disable();
                
            },
            failure:function (result, request) {
                Ext.MessageBox.alert("Alert", "Failure switching settings status.");
            }
        });
    }
};

Ext.onReady(pmFtpMonitor.application.init, pmFtpMonitor.application);
