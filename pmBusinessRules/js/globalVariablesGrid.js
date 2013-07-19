Ext.onReady(function() {
    
    storeGlobalVariables = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'controllers/globalVariablesProxy'
        }),
        root: 'data',
        autoDestroy: true,
        totalProperty: 'total',
        remoteSort: true,
        baseParams: {functionExecute: 'listGlobalVariables'},
        fields: ['GLOBAL_UID','GLOBAL_VALUE','GLOBAL_TYPE','GLOBAL_FORMAT'],
        listeners: {
            load : function () {
                editButtonGlobalVariables.disable();
                deleteButtonGlobalVariables.disable();
            }
        }
    });

    var newButtonGlobalVariables = new Ext.Action({
        text:_('ID_NEW'),
        iconCls:'button_menu_ext ss_sprite ss_add',
        handler: function () {
            baseGlobalVariables.setValue('new');
            nameGlobalVariables.setValue('');
            valueGlobalVariables.setValue('');
            typeGlobalVariables.setValue('String');
            formartGlobalVariables.setValue('vacio');
            formartGlobalVariables.setVisible(false);

            winGlobalVariables.setTitle('New global variable');
            winGlobalVariables.show();
        }
    });

    var editButtonGlobalVariables = new Ext.Action({
        text: _('ID_EDIT'),
        iconCls:'button_menu_ext ss_sprite ss_pencil',
        handler: function () {
            rowSelected = gridGlobalVariables.getSelectionModel().getSelected();
            if (rowSelected) {
                baseGlobalVariables.setValue(rowSelected.data.GLOBAL_UID);
                nameGlobalVariables.setValue(rowSelected.data.GLOBAL_UID);
                typeGlobalVariables.setValue(rowSelected.data.GLOBAL_TYPE);
                valueGlobalVariables.setValue(rowSelected.data.GLOBAL_VALUE);

                if (rowSelected.data.GLOBAL_TYPE == 'Date') {
                    formartGlobalVariables.setValue(rowSelected.data.GLOBAL_FORMAT);
                    formartGlobalVariables.setVisible(true);
                } else {
                    formartGlobalVariables.setValue('vacio');
                    formartGlobalVariables.setVisible(false);
                }

                winGlobalVariables.setTitle('Edit global variable : ' + rowSelected.data.GLOBAL_UID);
                winGlobalVariables.show();
            }
        },
        disabled: true
    });

    var deleteButtonGlobalVariables = new Ext.Action({
        text: _('ID_DELETE'),
        iconCls:'button_menu_ext ss_sprite ss_delete',
        handler: function () {
            rowSelected = gridGlobalVariables.getSelectionModel().getSelected();
            if (rowSelected) {
                Ext.Msg.confirm(_('ID_CONFIRM'), 'Do you want to delete selected field?',
                function(btn, text) {
                    if (btn == "yes") {
                        Ext.MessageBox.show({
                            msg: 'Load Data... Wait please..',
                            progressText: 'Saving...',
                            width:300,
                            wait:true,
                            waitConfig: {interval:200},
                            animEl: 'mb7'
                        });

                        Ext.Ajax.request({
                            url: 'controllers/globalVariablesProxy.php',
                            params: {
                                'GLOBAL_UID': rowSelected.data.GLOBAL_UID,
                                'functionExecute': 'deleteGlobalVariables'
                            },
                            success: function(r,o) {
                                Ext.MessageBox.hide();
                                storeGlobalVariables.load();
                                PMExt.notify('Information','Delete global variable');
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
    
    var searchGlobalVariables = new Ext.ux.form.SearchField({
        store: storeGlobalVariables,
        width: 250
    });

    var storePageSize = new Ext.data.SimpleStore({
        autoLoad: true,
        fields: ['size'],
        data:[['20'],['30'],['40'],['50'],['100']]
    });

    var comboPageSize = new Ext.form.ComboBox({
        typeAhead : false,
        mode : 'local',
        triggerAction : 'all',
        value : 20,
        store : storePageSize,
        valueField : 'size',
        displayField : 'size',
        width : 50,
        editable : false,
        listeners : {
            select : function(c,d,i){
                pagingList.pageSize = parseInt(d.data['size']);
                pagingList.moveFirst();
            }
        }
    });

    var pagingList = new Ext.PagingToolbar({
        pageSize : 100,
        store : storeGlobalVariables,
        displayInfo : true,
        autoHeight : true,
        displayMsg : 'Modelos mostrados {0} - {1} de {2}',
        emptyMsg : 'No hay modelos para mostrar',
        items: [
            comboPageSize
        ]
    });

    var gridGlobalVariables = new Ext.grid.GridPanel({
        store: storeGlobalVariables,
        border: true,
        title: 'Global Variables',
        loadMask : true,
        cm: new Ext.grid.ColumnModel({
            defaults: {
                width: 50,
                sortable: true
            },
            columns: [
                {header: "Name",   dataIndex: 'GLOBAL_UID'},
                {header: "Value",  dataIndex: 'GLOBAL_VALUE'},
                {header: "Type",   width: 10, dataIndex: 'GLOBAL_TYPE'},
                {header: "Format", width: 10, dataIndex: 'GLOBAL_FORMAT'}
            ]
        }),
        autoShow: true,
        autoFill:true,
        nocache: true,
        autoWidth: true,
        stripeRows: true,
        stateful: true,
        animCollapse: true,
        tbar: [newButtonGlobalVariables, editButtonGlobalVariables, deleteButtonGlobalVariables, '->', searchGlobalVariables],
        bbar: pagingList,
        viewConfig: {
                  forceFit:true,
                  scrollOffset: 1,
                  emptyText: '<div align="center"><b>Cochalo</b></div>'
        },
        listeners: {
            rowdblclick : function () {
                rowSelected = gridGlobalVariables.getSelectionModel().getSelected();
                if (rowSelected) {
                    baseGlobalVariables.setValue(rowSelected.data.GLOBAL_UID);
                    nameGlobalVariables.setValue(rowSelected.data.GLOBAL_UID);
                    typeGlobalVariables.setValue(rowSelected.data.GLOBAL_TYPE);
                    valueGlobalVariables.setValue(rowSelected.data.GLOBAL_VALUE);

                    if (rowSelected.data.GLOBAL_TYPE == 'Date') {
                        formartGlobalVariables.setValue(rowSelected.data.GLOBAL_FORMAT);
                        formartGlobalVariables.setVisible(true);
                    } else {
                        formartGlobalVariables.setValue('vacio');
                        formartGlobalVariables.setVisible(false);
                    }

                    winGlobalVariables.setTitle('Edit global variable : ' + rowSelected.data.GLOBAL_UID);
                    winGlobalVariables.show();
                }
            },
            click : function () {
                rowSelected = gridGlobalVariables.getSelectionModel().getSelected();
                if (rowSelected) {
                    editButtonGlobalVariables.enable();
                    deleteButtonGlobalVariables.enable();
                }
            }
        }
    });

    new Ext.Viewport({
        layout:'fit',
        border: false,
        autoScroll: false,
        items:[gridGlobalVariables]
    });

    storeGlobalVariables.load();
    
});