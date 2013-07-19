/////  GRID IZQ BUSINESS RULES EXECUTED  /////
var comboProcess;
var nameFileLoad;
var panelShowPmrlFile;
var savePmrlFile;

var newButtonExcelFiles = new Ext.Action({
    text:_('ID_NEW'),
    icon:'/images/import.gif',
    handler: function () {
        uploadFileXls();
    }
});

var downloadButtonExcelFiles = new Ext.Action({
    text: _('ID_DOWNLOAD'),
    icon: '/images/export.png',
    handler: function () {
        rowSelected = gridExcelFiles.getSelectionModel().getSelected();
        if (rowSelected) {
            urlPage = '../pmBusinessRules/downloadFile.php?type=excel&name=' + rowSelected.data.NAME_FILE;
            redirectPage(urlPage);
        }
    },
    disabled: true
});

var deleteButtonExcelFiles = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite ss_delete',
    handler: function () {
        rowSelected = gridExcelFiles.getSelectionModel().getSelected();
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
                            'functionExecute': 'deleteExcelFile',
                            'NAME_FILE': rowSelected.data.NAME_FILE
                        },
                        success: function(r,o) {
                            Ext.MessageBox.hide();
                            storePmrlFiles.load();
                            storeExcelFiles.load();
                            PMExt.notify('Information','Delete excel file');
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

var generatedButtonExcelFiles = new Ext.Action({
    text: 'Generar Pmrl',
    iconCls: 'button_menu_ext ss_sprite ss_page_gear',
    handler: function () {
        rowSelected = gridExcelFiles.getSelectionModel().getSelected();
        if (rowSelected) {
            Ext.Msg.confirm(_('ID_CONFIRM'), 'Do you want to generate prmls?',
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
                            'functionExecute': 'generateFilesPmrl',
                            'NAME_FILE': rowSelected.data.NAME_FILE
                        },
                        success: function(r,o) {
                            Ext.MessageBox.hide();
                            storePmrlFiles.load();
                            PMExt.notify('Information','Generate pmrl files');
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

var storeExcelFiles = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        method: 'POST',
        url: 'controllers/businessRulesProxy'
    }),
    root: 'data',
    autoDestroy: true,
    totalProperty: 'total',
    remoteSort: true,
    baseParams: {functionExecute: 'listExcelFiles'},
    fields: ['NAME_FILE','DATE_FILE','SIZE_FILE'],
    listeners :{
        load: function(combo, record) {
            gridExcelFiles.enable();

            downloadButtonExcelFiles.disable();
            deleteButtonExcelFiles.disable();
            generatedButtonExcelFiles.disable();
        }
    }
});

var gridExcelFiles = new Ext.grid.GridPanel({
    disabled: true,
    region: 'north',
    split: true,
    height: 150,
    minSize: 100,
    maxSize: 250,
    store: storeExcelFiles,
    margins: '0 0 0 0',
    border: true,
    title: 'Excel Files of Rules Business',
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
    //sm: smodel,
    tbar:[newButtonExcelFiles, downloadButtonExcelFiles, deleteButtonExcelFiles, '->', generatedButtonExcelFiles],
    viewConfig: {
        forceFit:true,
        scrollOffset: 2,
        emptyText: '<div align="center"><b>No exist excel files</b></div>'
    },
    listeners: {
        click : function () {
            rowSelected = gridExcelFiles.getSelectionModel().getSelected();
            if (rowSelected) {
                downloadButtonExcelFiles.enable();
                deleteButtonExcelFiles.enable();
                generatedButtonExcelFiles.enable();
            }
        }
    }
});
