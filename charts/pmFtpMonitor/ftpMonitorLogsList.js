Ext.namespace("pmFtpMonitor");

var pageSize;
var viewButton;
var storeListProcess, storeSettings;
var smodel;
pmFtpMonitor.application = {
    init:function(){
        //CONFIG params
        pageSize = parseInt(CONFIG.pageSize);
        viewButton = new Ext.Action({
            text: "View",
            iconCls: 'button_menu_ext ss_sprite ss_eye',
            disabled: true,
            handler: viewDetails
        });
        storeListProcess = function (n, r, i, uid, action) {
            var myMask = new Ext.LoadMask(Ext.getBody(), {
                msg:"Load pmFtpMonitor log list..."
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
                    Ext.MessageBox.alert("Alert", "Failure load log list");
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
                    name: "FTP_LOG_UID"
                },{
                    name: "EXECUTION_DATETIME"
                },{
                    name: "FAILED"
                },{
                    name: "SUCCEEDED"
                },{
                    name: "PROCESSED"
                },{
                    name: "CONNECTION_TYPE"
                },{
                    name: "HOST"
                },{
                    name: "FTP_PATH"
                }]
            }),     
            listeners:{
                beforeload:function (store) {
                    this.baseParams = {
                        "action": "showLogList",
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
                id: "FTP_LOG_UID", 
                dataIndex: "FTP_LOG_UID", 
                hidden: true
            },{
                header: "Execution Date Time", 
                dataIndex: "EXECUTION_DATETIME"
            },{
                header: "Connection Type", 
                dataIndex: "CONNECTION_TYPE"
            },{
                header: "Path", 
                renderer: function(v,params,record){
                    return (record.data.HOST.length>0) ? "/" + record.data.HOST + "/" + record.data.FTP_PATH : record.data.FTP_PATH;
                }
            },{
                header: "Failed", 
                dataIndex: "FAILED" 
            },{
                header: "Succeeded", 
                dataIndex: "SUCCEEDED" 
            },{
                header: "Processed", 
                dataIndex: "PROCESSED"
            }]
        });
        smodel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners:{
                rowselect: function(sm){
                    rowSelected = grdpnlSettings.getSelectionModel().getSelected();
                    viewButton.enable();
                },
                rowdeselect: function(sm){
                    viewButton.disable();
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
      
            tbar: [ viewButton/*, "-" ,txtSearch, btnTextClear, btnSearch*/],
            /*bbar: pagingUser,*/
            frame: true,
            height: 400,      
            renderTo: "divMain",
            listeners:{
                dblclick: viewDetails
            }
        });
              
        //Initialize events
        storeListProcess(pageSize, pageSize, 0, '', 'showLogList');
    }
}
viewDetails = function() {
    location.href = 'ftpMonitorLogsList?uid=' + rowSelected.data.FTP_LOG_UID;
};

Ext.onReady(pmFtpMonitor.application.init, pmFtpMonitor.application);


