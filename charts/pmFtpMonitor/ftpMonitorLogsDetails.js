Ext.namespace("pmFtpMonitor");

var pageSize;
var storeListProcess, storeSettings;
var smodel;
pmFtpMonitor.application = {
    init:function(){
        //CONFIG params
        pageSize = parseInt(CONFIG.pageSize);
        storeListProcess = function (n, r, i, uid, action) {
            var myMask = new Ext.LoadMask(Ext.getBody(), {
                msg:"Load pmFtpMonitor log detail list..."
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
                    Ext.MessageBox.alert("Alert", "Failure load log detail list");
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
                    name: "FTP_LOG_DET_UID"
                },{
                    name: "EXECUTION_DATETIME"
                },{
                    name: "FULL_PATH"
                },{
                    name: "HAVE_XML"
                },{
                    name: "VARIABLES"
                },{
                    name: "STATUS"
                },{
                    name: "DESCRIPTION"
                },{
                    name: "CASE"
                }]
            }),     
            listeners:{
                beforeload:function (store) {
                    this.baseParams = {
                        "action": "showLogDetails",
                        "uid": FTP_LOG_UID,
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
                id: "FTP_LOG_DET_UID", 
                dataIndex: "FTP_LOG_DET_UID", 
                hidden: true
            },{
                header: "Case", 
                dataIndex: "CASE"
            },{
                header: "Execution Date Time", 
                dataIndex: "EXECUTION_DATETIME"
            },{
                header: "Path", 
                dataIndex: "FULL_PATH"
            },{
                header: "Have XML", 
                dataIndex: "HAVE_XML" 
            },{
                header: "Variables", 
                dataIndex: "VARIABLES"
            },{
                header: "Status", 
                dataIndex: "STATUS" 
            },{
                header: "Description", 
                dataIndex: "DESCRIPTION"
            }]
        });
        smodel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners:{
                rowselect: function(sm){
                    rowSelected = grdpnlSettings.getSelectionModel().getSelected();                        
                },
                rowdeselect: function(sm){
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
            title : "FTP Monitor Log Details",
            sm: smodel,
            enableColumnResize: true,
            enableHdMenu: true, //Menu of the column
      
            tbar: [/*, "-" ,txtSearch, btnTextClear, btnSearch*/],
            /*bbar: pagingUser,*/
            frame: true,
            height: 400,      
            renderTo: "divMain"
        });
              
        //Initialize events
        storeListProcess(pageSize, pageSize, 0, FTP_LOG_UID, 'showLogDetails');
    }
}

Ext.onReady(pmFtpMonitor.application.init, pmFtpMonitor.application);


