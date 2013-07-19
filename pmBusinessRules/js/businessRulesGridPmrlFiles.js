/////  GRID IZQ BUSINESS RULES EXECUTED  /////

var newButtonPmrlFiles = new Ext.Action({
    text:_('ID_NEW'),
    icon:'/images/import.gif',
    handler: function () {
        uploadFilePmrl();
    }
});

var deleteButtonPmrlFiles = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite ss_delete',
    handler: function () {
        rowSelected = gridPmrlFiles.getSelectionModel().getSelected();
        if (rowSelected) {
            Ext.Msg.confirm(_('ID_CONFIRM'), 'Do you want to delete selected file?',
            function(btn, text) {
                if (btn == "yes") {
                    Ext.MessageBox.show({
                        msg: 'Load Data... Wait please...',
                        progressText: 'Saving...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200},
                        animEl: 'mb7'
                    });
                    Ext.Ajax.request({
                        url: 'controllers/businessRulesProxy',
                        params: {
                            'functionExecute': 'deletePmrlFile',
                            'NAME_FILE': rowSelected.data.NAME_FILE
                        },
                        success: function(r,o) {
                            Ext.MessageBox.hide();
                            storePmrlFiles.load();
                            PMExt.notify('Information','Delete pmrl file');
                        },
                        failure: function() {
                            Ext.MessageBox.hide();
                        }
                    });
                }
            });
        }
    },
    disabled: true
});

var downloadButtonPmrlFiles = new Ext.Action({
    text: _('ID_DOWNLOAD'),
    icon: '/images/export.png',
    handler: function () {
        rowSelected = gridPmrlFiles.getSelectionModel().getSelected();
        if (rowSelected) {
            urlPage = '../pmBusinessRules/downloadFile.php?type=pmrl&name=' + rowSelected.data.NAME_FILE;
            redirectPage(urlPage);
        }
    },
    disabled: true
});


var storePmrlFiles = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/businessRulesProxy'
    }),
    root: 'data',
    autoDestroy: true,
    totalProperty: 'total',
    remoteSort: true,
    baseParams: {functionExecute: 'listPmrlFiles'},
    fields: ['NAME_FILE','DATE_FILE','SIZE_FILE'],
    listeners :{
        load: function(combo, record) {
            gridPmrlFiles.enable();
            deleteButtonPmrlFiles.disable();
            downloadButtonPmrlFiles.disable();
        }
    }
});

var gridPmrlFiles = new Ext.grid.GridPanel({
    disabled: true,
    region: 'center',
    width: 350,
    minSize: 300,
    maxSize: 400,
    store: storePmrlFiles,
    margins: '0 0 0 0',
    border: true,
    title: 'Pmrl Files of Rules Business',
    loadMask : true,
    cm: new Ext.grid.ColumnModel({
        defaults: {
            width: 100,
            sortable: true
        },
        columns: [
            {header: "NAME FILE", width: 30, sortable: true, dataIndex: 'NAME_FILE'},
            {header: "SIZE FILE", width: 30, sortable: true, dataIndex: 'SIZE_FILE'},
            {header: "UPDATE FILE", width: 30, sortable: true, dataIndex: 'DATE_FILE'}
        ]
    }),
    autoShow: true,
    autoFill:true,
    nocache: true,
    autoWidth: true,
    stripeRows: true,
    stateful: true,
    animCollapse: true,
    tbar:[newButtonPmrlFiles, downloadButtonPmrlFiles, deleteButtonPmrlFiles],
    viewConfig: {
        forceFit:true,
        scrollOffset: 1,
        emptyText: '<div align="center"><b>No exist pmrl files</b></div>'
    },
    listeners: {
        click : function () {
            rowSelected = gridPmrlFiles.getSelectionModel().getSelected();
            if (rowSelected) {
                savePmrlFile.disable();
                panelShowPmrlFile.load({
                    url: 'controllers/businessRulesProxy.php',
                    params: {
                        'functionExecute': 'showPmrlFile',
                        'NAME_FILE': rowSelected.data.NAME_FILE}
                });

                deleteButtonPmrlFiles.enable();
                downloadButtonPmrlFiles.enable();
            }
        }
    }
});