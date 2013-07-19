Ext.onReady(function() {

    savePmrlFile = new Ext.Action({
        text: "Save file",
        iconCls: 'button_menu_ext ss_sprite ss_disk',
        handler: function () {
            rowSelected = gridPmrlFiles.getSelectionModel().getSelected();
            if (rowSelected) {
                var conent = document.getElementById('fieldTextarea').value;
                Ext.Ajax.request({
                    url: 'controllers/businessRulesProxy.php',
                    params: {
                        'functionExecute': 'editFilePmrl',
                        'CONTENT': conent,
                        'NAME_FILE': rowSelected.data.NAME_FILE
                    },
                    success: function(r,o) {
                        //storeGlobalVariables.load();
                        PMExt.notify('Information','Pmrl file pmrl');
                    },
                    failure: function() {
                    }
                });
            }
        },
        disabled: true
    });

    panelShowPmrlFile = new Ext.Panel({
        region: 'east',
        split: true,
        autoScroll: false,
        width: "50%",
        minSize: 400,
        maxSize: 700,
        title: 'Show File',
        border : true,
        margins:'0 0 0 0',
        tbar:[savePmrlFile],
        loader: {
            autoLoad:true
        }
    });

    var panelPmrlFiles = new Ext.Panel({
        region:'center',
        layout:'border',
        split: true,
        border : false,
        margins:'5 0 0 0',
        items:[gridPmrlFiles, panelShowPmrlFile]
    });

    var panelCenterBusinessRules = new Ext.Panel({
        region:'center',
        layout:'border',
        border : false,
        margins:'0 0 5 0',
        items:[gridExcelFiles, panelPmrlFiles]
    });

    new Ext.Viewport({
        layout:'border',
        border: false,
        autoScroll: false,
        items:[panelCenterBusinessRules]
    });

    storePmrlFiles.load();
    storeExcelFiles.load();
});