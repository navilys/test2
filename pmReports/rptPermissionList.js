new Ext.KeyMap(document, [{
    key: Ext.EventObject.F5,
    fn: function(keycode, e) {
        if (! e.ctrlKey) {
            if (Ext.isIE) {
                e.browserEvent.keyCode = 8;
            }
            e.stopEvent();
            document.location = document.location;
        } else {
          Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
        }
    }
}
]);

var store;
var cmodel;
var permissionsGrid;
var actions;
var filterStatus = '';

Ext.onReady(function(){
    Ext.QuickTips.init();
    var resultTpl = new Ext.XTemplate(
      '<tpl for="."><div class="x-combo-list-item" style="white-space:normal !important;word-wrap: break-word;">',
          '<span> {APP_PRO_TITLE}</span>',
      '</div></tpl>'
    );
    
    var columnRenderer = function(data, metadata, record, rowIndex,columnIndex, store) {
        var new_text = metadata.style.split(';');
        var style = '';
        for (var i = 0; i < new_text.length -1 ; i++) {
          var chain = new_text[i] +";";
          if (chain.indexOf('width') == -1) {
            style = style + chain;
          }
        }
        metadata.attr = 'ext:qtip="' + data + '" style="'+ style +' white-space: normal; "';
        return data;
    };
    
    render_status = function(v){
        switch(v){
            case '1': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
            case '0': return '<font color="red">' + _('ID_INACTIVE') + '</font>'; break;
        }
        return 1;
    };

    var newButton = new Ext.Action({
        text: _('ID_NEW'),
        iconCls: 'button_menu_ext ss_sprite  ss_add',
        handler: NewPermission
    });
    var editButton = new Ext.Action({
        id:'editButton',
        text: _('ID_EDIT'),
        iconCls: 'button_menu_ext ss_sprite  ss_pencil',
        disabled: true,
        handler: EditPermission
    });

    var deleteButton = new Ext.Action({
        id:'deleteButton',
        text: _('ID_DELETE'),
        iconCls: 'button_menu_ext ss_sprite  ss_delete',
        disabled: true,
        handler: DeletePermission
    });

    var activeButton = new Ext.Action({
        text: _('ID_STATUS'),
        id:'activator',
        icon: '',
        iconCls: 'silk-add',
        handler: activeDeactive,
        disabled:true
    });

    var backButton = new Ext.Action({
        text: _('ID_BACK'),
        iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
        handler: BackToPmTables
    });

    actions = _addPluginActions([ newButton, '-', editButton, '-', activeButton, deleteButton, {xtype: 'tbfill'}, backButton]);

    var stepsFields = Ext.data.Record.create([
        {name : 'PMR_UID',          type: 'string'},
        {name : 'ADD_TAB_UID',      type: 'string'},
        {name : 'PMR_TYPE',         type: 'string'},
        {name : 'PMR_OWNER_UID',    type: 'string'},
        {name : 'PMR_CREATE_DATE',  type: 'string'},
        {name : 'PMR_UPDATE_DATE',  type: 'string'},
        {name : 'PMR_TYPE_TITLE',  type: 'string'},
        {name : 'PMR_STATUS',       type: 'string'}
  ]);

    store = new Ext.data.Store( {
        proxy : new Ext.data.HttpProxy({
            url: '../pmReports/reportsAjax?action=getListPermissions'
          }),
        remoteSort  : true,
        sortInfo    : stepsFields,
        reader : new Ext.data.JsonReader( {
        root: 'data',
        totalProperty: 'totalCount',
        fields : [
            
            {name : 'PMR_UID'},
            {name : 'ADD_TAB_UID'},
            {name : 'ASSIGNED'},
            {name : 'PMR_TYPE'},
            {name : 'PMR_OWNER_UID'},
            {name : 'PMR_TYPE_TITLE'},
            {name : 'APP_EVN_ATTEMPTS'},
            {name : 'PMR_CREATE_DATE'},
            {name : 'PMR_UPDATE_DATE'},
            {name : 'PMR_STATUS'}
            ]
      })
    });
    store.setDefaultSort('PMR_CREATE_DATE', 'desc');
    store.setBaseParam("ADD_TAB_UID",ADD_TAB_UID);

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50
        },
        columns: [
            {id:'PMR_UID',                  dataIndex: 'PMR_UID',           hidden:true, hideable:false},
            {header: 'ADD_TAB_UID',         dataIndex: 'ADD_TAB_UID',       hidden:true, hideable:false},
            {header: _('ID_ASSIGNED_TO'),   dataIndex: 'ASSIGNED',          width: 150, hidden: false, renderer: columnRenderer},
            {header: _('ID_TYPE'),          dataIndex: 'PMR_TYPE',          width: 150, hidden: true, hideable:false,renderer: columnRenderer},
            {header: _('ID_OWNER'),         dataIndex: 'PMR_TYPE_TITLE',    width: 150, hidden: true, hideable:false,renderer: columnRenderer},
            {header: _('ID_CREATE'),        dataIndex: 'PMR_CREATE_DATE',   width: 90,hidden:false,renderer: columnRenderer},
            {header: _('ID_UPDATE'),        dataIndex: 'PMR_UPDATE_DATE',   width: 90, hidden: false,renderer: columnRenderer},
            {header: _('ID_STATUS'),        dataIndex: 'PMR_STATUS',        width: 50, hidden: false, renderer: render_status}
        ]
    });

    bbarpaging = new Ext.PagingToolbar({
      pageSize      : 25,
      store         : store,
      displayInfo   : true,
      displayMsg    : _('ID_GRID_PAGE_DISPLAYING_REPORT_PERMISSIONS_MESSAGE') + '&nbsp; &nbsp; ',
      emptyMsg      : _('ID_GRID_PAGE_NO_PERMISSIONS_MESSAGE')
    });

    permissionsGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'permissionsGrid',
        height:100,
        autoWidth : true,
        stateful : true,
        stateId : 'grid',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        columnLines: false,
        viewConfig: {
            forceFit:true
        },
        title : _('ID_ASSIGNED_PERMISSIONS_FOR') + ': ' + ADD_TAB_NAME,
        store: store,
        cm: cmodel,
        sm: new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
              //rowselect: function(smObj, rowIndex, record) {
              //      Ext.getCmp('activator').setDisabled(false);
              //      Ext.getCmp('editButton').setDisabled(false);
              //      Ext.getCmp('deleteButton').setDisabled(false);
              //      Ext.getCmp('activator').setDisabled(false);
              //  }
                rowselect: function(sm){
                    deleteButton.enable();
                    editButton.enable();
                    activeButton.enable();
                },
                rowdeselect: function(sm){
                    deleteButton.disable();
                    editButton.disable();
                    activeButton.disable();
                }
             }
          }),
        tbar: actions,
        bbar: bbarpaging,
        //singleSelect: true,
        listeners: {
            render: function(){
                this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
                permissionsGrid.getSelectionModel().on('rowselect', function(){
                    var rowSelected = permissionsGrid.getSelectionModel().getSelected();
                    var activator = Ext.getCmp('activator');
                    Ext.getCmp('activator').setDisabled(false);
                    Ext.getCmp('editButton').setDisabled(false);
                    Ext.getCmp('deleteButton').setDisabled(false);
                    activator.setDisabled(false);

                    if( rowSelected.data.PMR_STATUS == '1' ) {
                        activator.setIcon('/images/deactivate.png');
                        activator.setText( _('ID_DEACTIVATE') );
                    } else {
                        //Ext.getCmp('activator').setDisabled(true);
                        Ext.getCmp('editButton').setDisabled(true);
                        Ext.getCmp('deleteButton').setDisabled(true);
                        activator.setIcon('/images/activate.png');
                        activator.setText( _('ID_ACTIVATE') );
                    }
                    if (store.totalLength<=1) {
                        Ext.getCmp('deleteButton').setDisabled(true);
                        if( rowSelected.data.PMR_STATUS == '1' ) {
                            activator.setDisabled(true);
                            activator.setIcon('/images/deactivate.png');
                            activator.setText( _('ID_DEACTIVATE') );
                        }
                    }
                });
            }
        }
    });
    //onMessageContextMenu = function (grid, rowIndex, e) {
    //    e.stopEvent();
    //    var coords = e.getXY();
    //    contextMenu.showAt([coords[0], coords[1]]);
    //};
    permissionsGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
        //grid.singleSelect(true);
        var sm = grid.getSelectionModel();
        sm.selectRow(rowIndex, sm.isSelected(rowIndex));
      },
      this
    );

    //permissionsGrid.on('contextmenu', function(evt){evt.preventDefault();}, this);
    //permissionsGrid.addListener('rowcontextmenu',onMessageContextMenu, this);

    permissionsGrid.store.load();

    viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        singleSelect:true,
        items: [
           permissionsGrid
        ]
    });
});

var _addPluginActions = function(defaultactions) {
    try {
        if (Ext.isArray(_pluginactions)) {
            if (_pluginactions.length > 0) {
                var positionToInsert = _tbfillPosition(defaultactions);
                var leftactions = defaultactions.slice(0, positionToInsert);
                var rightactions = defaultactions.slice(positionToInsert, defaultactions.length - 1);
                return leftactions.concat(_pluginactions.concat(rightactions));
            }
            else {
                return defaultactions;
            }
        }
        else {
            return defaultactions;
        }
    }
    catch (error) {
        return defaultactions;
    }
};

var _tbfillPosition = function(actions) {
    try {
        for (var i = 0; i < actions.length; i++) {
            if (Ext.isObject(actions[i])) {
                if (actions[i].xtype == 'tbfill') {
                    return i;
                }
            }
        }
        return i;
    }
    catch (error) {
        return 0;
    }
};

function messageSelect() {
    Ext.Msg.show({
        title:'',
        msg: _('ID_NO_SELECTION_WARNING'),
        buttons: Ext.Msg.INFO,
        fn: function(){},
        animEl: 'elId',
        icon: Ext.MessageBox.INFO,
        buttons: Ext.MessageBox.OK
    });
}

NewPermission = function(){
    reportPermissionsFrm.getForm().reset();
    reportPermissionsFrm.getForm().setValues({
        PMR_UID : '',
        ADD_TAB_UID : ADD_TAB_UID,
        ADD_TAB_NAME : ADD_TAB_NAME,
        PMR_TYPE : '',
        PMR_OWNER_UID: ''
    });
    newin.setTitle('Add Permission');
    
    cboPmrOwnerType.setValue("USER");
    storePmrOwnerUID.baseParams = {'option': 'OWNERTYPE', 'type': cboPmrOwnerUID.valueField};
    cboPmrOwnerUID.store.reload();
    cboPmrOwnerUID.store.on("load", function (store) {
        if (storePmrOwnerUID.getAt(0)) {
            cboPmrOwnerUID.setValue(storePmrOwnerUID.getAt(0).get(cboPmrOwnerUID.valueField));
        }
    });
    newin.show();
};

EditPermission = function(){
    reportPermissionsFrm.getForm().reset();
    var rowsSelected = permissionsGrid.getSelectionModel().getSelections();
    if (rowsSelected.length > 0) {
        reportPermissionsFrm.getForm().setValues({
            ADD_TAB_UID : ADD_TAB_UID,
            ADD_TAB_NAME : ADD_TAB_NAME,
            PMR_UID : rowsSelected[0].get('PMR_UID'),
            PMR_TYPE : rowsSelected[0].get('PMR_TYPE')
        });
        newin.setTitle(_('ID_EDIT_PERMISSIONS'));
        if (rowsSelected[0].get('PMR_TYPE') != 'EVERYBODY'){
            storePmrOwnerUID.baseParams = {'option': 'OWNERTYPE', 'type': rowsSelected[0].get('PMR_TYPE')};
            cboPmrOwnerUID.store.reload();
            cboPmrOwnerUID.store.on("load", function (store) {
                cboPmrOwnerUID.setValue(rowsSelected[0].get('PMR_OWNER_UID'));
            });
        } else {
            //cboPmrOwnerUID.store.removeAll();
            //cboPmrOwnerUID.setValue('');
            reportPermissionsFrm.getForm().setValues({
                PMR_OWNER_UID : ''
            });
        }

        newin.show();
    } else {
        messageSelect();
    }
};

DeletePermission = function(){
    var rowsSelected = permissionsGrid.getSelectionModel().getSelections();
    if( rowsSelected.length > 0 ) {
        
        PMExt.confirm(_('ID_CONFIRM'), _('ID_DELETE_PERMISSION', rowsSelected[0].get('ASSIGNED')), function(){
            Ext.Ajax.request({
                url : '../pmReports/reportsAjax?action=deletePermission',
                params : {
                    PMR_UID : rowsSelected[0].get('PMR_UID'),
                    ADD_TAB_UID : rowsSelected[0].get('ADD_TAB_UID')
                },
                success: function ( result, request ) {
                    store.reload();
                },
                failure: function ( result, request) {
                    Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
                }
            });
        });
    } else {
        messageSelect();
    }
};

function activeDeactive(){
    var rowsSelected = permissionsGrid.getSelectionModel().getSelections();
    if( rowsSelected.length > 0 ) {
      Ext.Ajax.request({
        url : '../pmReports/reportsAjax?action=changeStatus',
        params : {
            PMR_UID : rowsSelected[0].get('PMR_UID'),
            PMR_STATUS : rowsSelected[0].get('PMR_STATUS'),
            ADD_TAB_UID : rowsSelected[0].get('ADD_TAB_UID')
        },
        success: function ( result, request ) {
          store.reload();
          var activator = Ext.getCmp('activator');
          activator.setDisabled(true);
          activator.setText('Status');
          activator.setIcon('');
        },
        failure: function ( result, request) {
          Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);
        }
      });
    } else {
        messageSelect();
    }
}

BackToPmTables = function(){
    location.href = '../pmTables?PRO_UID='+PRO_UID;
};
