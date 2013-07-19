Ext.onReady(function () {
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

  /*//*****
  var tablesStore = new Ext.data.JsonStore({
    root: 'data',
    totalProperty: 'totalCount',
    idProperty: 'id',
    autoLoad  : false,
    remoteSort: false,
    fields: [
        'id',
        'name',
        {name: 'cant', type: 'float'}
    ],
    proxy: new Ext.data.HttpProxy({
      url: 'mainAjax',
      method: 'POST'
    })
  });
  */

  //tablesStore.setDefaultSort('id', 'asc'); //*****

  var tablesStore2 = new Ext.data.JsonStore({
    root: 'data',
    totalProperty: 'totalCount',
    idProperty: 'id',
    autoLoad  : false,
    remoteSort: false,
    fields: [
        'id',
        'name',
        {name: 'cant', type: 'float'}
    ],
    proxy: new Ext.data.HttpProxy({
      url: 'mainAjax',
      method: 'POST'
    })
  });

  tablesStore2.setDefaultSort('id', 'asc');

  var logStore = new Ext.data.JsonStore({
    root: 'data',
    totalProperty: 'totalCount',
    idProperty: 'pid',
    //autoLoad  : true,
    remoteSort: false,
    fields: [
        'pid',
        'date',
        'workspace',
        {name: 'count', type: 'float'},
        {name: 'duration', type: 'float'},
        'method',
        'uri',
        'fields'
    ],
    proxy: new Ext.data.HttpProxy({
      url: 'mainAjax',
      method: 'POST'
    })
  });

  logStore.setBaseParam('xaction', 'logByIp');
  logStore.setDefaultSort('pid', 'date');

  var logStoreTotalIp = new Ext.data.JsonStore({
    root: 'data',
    totalProperty: 'totalCount',
    idProperty: 'id',
    autoLoad  : true,
    remoteSort: false,
    fields: [
      'ip',
      'cant'
    ],
    proxy: new Ext.data.HttpProxy({
      url: 'mainAjax',
      method: 'POST'
    })
  });

  logStoreTotalIp.setBaseParam('xaction', 'logTotalIp')
  logStoreTotalIp.setDefaultSort('ip', 'asc');

  function newTablePanel(grid, n, e) {
    var gridItems = grid.store.data.items[n].data;
    var gridTable = Ext.getCmp('grid-' + gridItems.name);
    if (gridTable) {
      gridTable.store.load();
      return;
    }

    //Ext.Msg.alert(gridTable);
    //centerPanel = Ext.getCmp('center-panel'); //*****

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function(response) {
        var data = Ext.util.JSON.decode(  response.responseText );
        var tempStore = new Ext.data.JsonStore({
          id: 'store-' +gridItems.name,
          root: 'data',
          totalProperty: 'totalCount',
          idProperty: 'id',
          autoLoad  : true,
          remoteSort: false,
          fields: data.fields,
          proxy: new Ext.data.HttpProxy({
            url: 'mainAjax'
          })
        });

        tempStore.setBaseParam('xaction', 'tableRows');
        tempStore.setBaseParam('table', gridItems.name);

        var tempGrid = new Ext.grid.EditorGridPanel({
          id: 'grid-' +gridItems.name,
          title: gridItems.name,
          columns: data.columns,
          editable: true,
          iconCls: 'tabs',
          store: tempStore,
          closable:true,
          listeners: {
            //rowclick: newTablePanel
          }
        });

        centerPanel.add(tempGrid);
        centerPanel.doLayout();
        tempGrid.show();
      },
      failure: function () {},
      params: {xaction: 'table', table: gridItems.name}
    });
  }

  /*//*****
  //grid with tables, this grid is showed at the left
  var tablesGrid = new Ext.grid.GridPanel({
    columns : [
      {header: "#",    width: 20,  dataIndex: 'id',   sortable: true},
      {header: "Name", width: 150, dataIndex: 'name', sortable: true},
      {header: "rows", width: 50,  dataIndex: 'cant', sortable: true, align: 'right'}
    ],

    store: tablesStore,
    autoHeight: true,
    listeners: {
      rowclick: newTablePanel
    }
  });

  //create some portlet tools using built in Ext tool ids
  var tools = [
    {
      id:'gear',
      handler: function () {
        Ext.Msg.alert('Message', 'The Settings tool was clicked.');
      }
    },
    {
      id:'close',
      handler: function (e, target, panel) {
        panel.ownerCt.remove(panel, true);
      }
    }
  ];
  */

  var logToolbar = new Ext.Toolbar({
    //height: 33,
    items: [
      {
        xtype  : 'tbbutton',
        text   : 'Refresh log',
        icon: '/images/refresh.gif',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onLogRefresh
      },
      '-',
      {
        xtype: 'tbbutton',
        text   : 'Disable/Enable',
        icon: '/images/icon-pmupgrade.png',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onLogDisableEnable
      },
      '-',
      {
        xtype: 'tbbutton',
        text   : 'Clear log',
        icon: '/images/icon-pmclear-cache.png',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onLogClear
      },
      '-',
      new Ext.form.ComboBox({
        id: 'ipComboBox',
        editable: false,
        store: logStoreTotalIp,
        valueField    :'ip',
        displayField  :'cant',
        typeAhead     : true,
        autocomplete  : true,
        forceSelection: true,
        mode          : 'local',
        triggerAction : 'all',
        selectOnFocus :true,
        width:200,
        listeners: {
          select: function (record, index) {
            onLogSelectIp(record, index);
          }
        }
      }),
      '-',
      new Ext.Toolbar.TextItem({id: 'statusLog', text: ''}),
      ' '
    ]
  });

  var logExpander = new Ext.ux.grid.RowExpander({
    tpl: new Ext.Template(
      '<p>{fields}</p>'
    )
  });

  var logPanel = new Ext.grid.GridPanel({
      id: 'logPanel',
      border: false,
      margins:'2 2 2 2',
      //title: 'Query Log Analyzer',
      height: 350,
      //autoHeight: true,
      iconCls: 'tabs',
      columns: [
        logExpander,
        {header: "Date",      width: 90,  dataIndex: 'date',      sortable: true},
        {header: "Workspace", width: 90,  dataIndex: 'workspace', sortable: true},
        {header: "Count",     width: 35,  dataIndex: 'count',     sortable: true, align: 'right', renderer: renderCount},
        {header: "Duration",  width: 60,  dataIndex: 'duration',  sortable: true, align: 'right', renderer: renderAccum},
        {header: "Method",    width: 60,  dataIndex: 'method',    sortable: true},
        {header: "Uri",       width: 300, dataIndex: 'uri',       sortable: true}
      ],
      store: logStore,
      tbar : logToolbar,
      plugins : logExpander,
      viewConfig: {
        forceFit:true
      },
      listeners: {
        rowdblclick: function (grid, n, e) {
          showNewLogPanel(grid, n, e, tabQueryLog);
        }
      }
  });

  var logTimePanel = new Ext.grid.GridPanel({
      id: 'logTimePanel',
      margins:'2 2 2 2',
      title: 'Time Log Analyzer',
      height: 200,
      iconCls: 'tabs',
      columns: [
        logExpander,
        {header: "date",      width: 90,  dataIndex: 'date',      sortable: true},
        {header: "workspace", width: 90,  dataIndex: 'workspace', sortable: true},
        {header: "count",     width: 35,  dataIndex: 'count',     sortable: true, align: 'right', renderer: renderCount},
        {header: "duration",  width: 60,  dataIndex: 'duration',  sortable: true, align: 'right', renderer: renderAccum},
        {header: "method",    width: 60,  dataIndex: 'method',    sortable: true},
        {header: "uri",       width: 300, dataIndex: 'uri',       sortable: true }
      ],
      //store: logStore,
      //tbar : logToolbar,
      //plugins : logExpander,
      viewConfig: {
        forceFit:true
      },
      listeners: {
        rowdblclick: function (grid, n, e) {
          //showNewLogPanel(grid, n, e, tabXXX); //*****
        }
      }
  });

  function renderDuration(value, p, r) {
    var v = Ext.util.Format.number(value, '0.0000');
    if (v > 0.1)
      var fmt = "<font color='red'>{0}</font>";
    else
      var fmt = "<font color='green'>{0}</font>";
    return String.format(fmt, v);
  }

  function renderAccum(value, p, r) {
    var v = Ext.util.Format.number(value, '0.0000');
    if (v > 1)
      var fmt = "<font color='red'>{0}</font>";
    else
      var fmt = "<font color='green'>{0}</font>";
    return String.format(fmt, v);
  }

  function renderCount(value, p, r) {
    var v = Ext.util.Format.number(value, '0000');
    if (v > 10)
      var fmt = "<font color='red'>{0}</font>";
    else
      var fmt = "<font color='green'>{0}</font>";
    return String.format(fmt, v);
  }

  function renderSqlTime(value, p, r) {
    var v = Ext.util.Format.number(value, '0.0000');
    if (v > 0.1)
      var fmt = "<font color='red'>{0}</font>";
    else
      var fmt = "<font color='blue'>{0}</font>";
    return String.format(fmt, v);
  }

  function renderPhpTime(value, p, r) {
    var v = Ext.util.Format.number(r.data['duration'] - r.data['sqlTime'], '0.0000') ;
    if (v > 0.01)
      var fmt = "<font color='red'>{0}</font>";
    else
      var fmt = "<font color='blue'>{0}</font>";
    return String.format(fmt, v);
  }

  function showNewLogPanel(grid, n, e, tabPanel) {
    var ipComboBox = Ext.getCmp('ipComboBox');
    var gridItems = grid.store.data.items[n].data;
    var gridTable = Ext.getCmp('grid-' + gridItems.pid);

    if (gridTable) {
      gridTable.store.load();
      return;
    }

    //var tabPanel = Ext.getCmp('center-panel'); //*****

    var tempToolbar = new Ext.Toolbar({
      items: [
        {
           pressed: true,
           enableToggle:true,
           text: 'Show Preview',
           cls: 'x-btn-text-icon details',
           toggleHandler: function (btn, pressed) {
             var logPanel = Ext.getCmp('grid-' + gridItems.pid );
             var view = logPanel.getView();
             view.showPreview = pressed;
             view.refresh();
           }
         }
      ]
    });

    var tempStore = new Ext.data.JsonStore({
      id : 'store-' + gridItems.pid,
      root: 'data',
      totalProperty: 'totalCount',
      idProperty: 'id',
      autoLoad  : true,
      remoteSort: false,
      fields: [
        'id',
        {name: 'duration', type: 'float'},
        {name: 'accum', type: 'float'},
        {name: 'sqlTime', type: 'float'},
        'sql',
        'sqlBold',
        'backtrace'
      ],
      proxy: new Ext.data.HttpProxy({
        url: 'mainAjax'
      })
    });

    tempStore.setBaseParam('xaction', 'logByIpDetails');
    tempStore.setBaseParam('pid', gridItems.pid);
    tempStore.setBaseParam('ip', ipComboBox.getValue());

    var tempExpander = new Ext.ux.grid.RowExpander({
      tpl: new Ext.Template(
        '<p>{sqlBold}</p><br />',
        '<p><b>Backtrace:</b><br />{backtrace}</p>'
      )
    });

    var tempGrid = new Ext.grid.EditorGridPanel ({
      id : 'grid-' +gridItems.pid,
      title: gridItems.date,
      columns : [
        tempExpander,
        {header: "id",         width: 35,  dataIndex: 'id',       sortable: true},
        {header: "sqlTime",    width: 60,  dataIndex: 'sqlTime',  sortable: true, align: 'right', renderer: renderSqlTime},
        {header: "phpTime",    width: 60,  dataIndex: 'sqlTime',  sortable: true, align: 'right', renderer: renderPhpTime},
        {header: "duration",   width: 60,  dataIndex: 'duration', sortable: true, align: 'right', renderer: renderDuration},
        {header: "accum",      width: 60,  dataIndex: 'accum',    sortable: true, align: 'right', renderer: renderAccum},
        {header: "sql",        width: 400, dataIndex: 'sql',      sortable: true}
        //{header: "backtrace'", width: 300, dataIndex: 'uri',      sortable: true}
      ],
      iconCls: 'tabs',
      store: tempStore,
      closable:true,
      //tbar : tempToolbar,
      plugins: tempExpander,
      listener: {
        rowclick: function(grid, n, e){
          var record = grid.getStore().getAt(n);
          var sqlBold = record.get('sqlBold');
          var gridItems = grid.store.data.items[n].data;
          Ext.Msg.alert('Query', sqlBold);
        },
        contextmenu: function (a, b, c, d) {
          Ext.Msg.alert('works');
        }
       },
      viewConfig: {
        forceFit: true
      }
    });

    //tempGrid.addListener('rowcontextmenu', onMessageContextMenu,this);
    tempGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
      e.stopEvent();
      var rowSelected = grid.store.data.items[rowIndex];
      messageContextMenu.sqlBold   = rowSelected.data.sqlBold;
      Ext.Msg.alert ('Query ' + rowSelected.data.id, rowSelected.data.sqlBold);
    }, this);

    tabPanel.add(tempGrid);
    tabPanel.doLayout();
    tempGrid.show();
  }

  function onMessageContextMenu(grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    var rowSelected = grid.store.data.items[rowIndex];
    messageContextMenu.sqlBold   = rowSelected.data.sqlBold;
    messageContextMenu.backtrace = rowSelected.data.backtrace;
    Ext.Msg.alert('sql', messageContextMenu.sqlBold);
    messageContextMenu.showAt([coords[0], coords[1]]);
  }

  var messageContextMenu = new Ext.menu.Menu({
    id: 'messageContextMenu',
    items: [{
        text: 'Show Sql',
        icon: '/images/pencil_beta.png',
        handler: function () {
          //var rowSelected = processesGrid.getSelectionModel().getSelected();
          Ext.Msg.alert('sql', this.sqlBold);
        }
      },{
        text: 'Backtrace',
        handler: function () { Ext.Msg.alert ('backtrace', this.backtrace);}
      }
    ]
  });

  function onLogSelectIp(record, item) {
    logStore.setBaseParam('ip', record.getValue());
    logStore.load();
  }

  function onLogRefresh(item, pressed) {
    var ipComboBox = Ext.getCmp("ipComboBox");
    ipComboBox.clearValue();

    Ext.getCmp("statusLog").setText("Refreshing...");

    Ext.Ajax.request({
      url: "mainAjax",
      method: "POST",
      params: {xaction: "logRefresh"},

      success: function(response) {
        var data = eval("(" + response.responseText + ")"); //json

        var formItems = formQueryLog.form.items;
        var message = "";

        if (data.error == 1) {
          message = "Error:" + data.message;
          formItems.items[0].setValue(data.enabled);
          formItems.items[1].setValue("..");
          formItems.items[2].setValue("..");
        }
        else {
          message = data.message;
          formItems.items[0].setValue(data.enabled);
          formItems.items[1].setValue(data.ipsPages);
          formItems.items[2].setValue(data.posSize);
        }

        Ext.getCmp("statusLog").setText(message);
        Ext.getCmp("ipComboBox").store.load();
        logPanel.store.removeAll(); //Set true to not fire clear event
      },
      failure: function () {}
    });
  }

  function onLogClear(item, pressed) {
    Ext.getCmp("statusLog").setText("Clearing....");

    Ext.Ajax.request({
      url: "mainAjax",
      method: "POST",
      success: function (response) {
        var data = eval("(" + response.responseText + ")"); //json

        if (data.error == 1) {
          var sqlMessage= "Error:" + data.message;
        }
        else {
          var sqlMessage= data.message;
        }

        Ext.getCmp("statusLog").setText(sqlMessage);
        Ext.getCmp("ipComboBox").store.removeAll();
        Ext.getCmp("ipComboBox").clearValue();
        logPanel.store.removeAll();
      },
      failure: function () {},
      params: {xaction: "logClear"}
    });
  }

  function onLogDisableEnable(item, pressed) {
    Ext.getCmp('statusLog').setText('Changing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formQueryLog.form.items;
        formItems.items[0].setValue(data.enabled);

        if (data.error == 1) {
          var sqlMessage = 'Error:' + data.message;
        }
        else {
          var sqlMessage = data.message;
        }
        Ext.getCmp('statusLog').setText(sqlMessage);
      },
      failure: function () {},
      params: {xaction: 'logDisableEnable'}
    });
  }


  function onMemcachedDisableEnable(item, pressed) {
    Ext.getCmp('statusMemcached').setText('Changing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formMemcached.form.items;
        formItems.items[1].setValue(data.enabled);
        onMemcachedRefresh(item, pressed);
      },
      failure: function () {},
      params: {xaction: 'memcachedDisableEnable'}
    });
  }

  var formQueryLog = new Ext.FormPanel({
    frame: true,
    border: false,
    labelAlign: 'right',
    labelWidth: 70,
    //autoWidth : true,
    //autoHeight: true,
    height: 350,

    items: [
      {
        fieldLabel: 'Enabled',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Ips/Pages',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Log Size',
        xtype: 'displayfield'
      }
    ]
  });

  var formTimeLog = new Ext.FormPanel({
    frame: true,
    border: false,
    autoHeight: true,
    labelAlign: 'right',
    labelWidth: 70,

    items: [
      {
        fieldLabel: 'Enabled',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Ips/Pages',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Log Size',
        xtype: 'displayfield'
      }
    ],
    buttons: [
      {
        text   : 'Clear',
        handler: onLogClear
      },
      {
        text   : 'Disable/Enable',
        handler: onLogDisableEnable
      }
    ]
  });

  var memcachedBar = new Ext.Toolbar({
    items: [
      {
        xtype: 'tbbutton',
        text:    'Status',
        icon: '/images/icon-email-settings.png',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onMemcachedRefresh
      },
      '-',
      {
        xtype: 'tbbutton',
        text:    'Disable/Enable',
        icon: '/images/icon-pmupgrade.png',//loading.gif',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onMemcachedDisableEnable
      },
      '-',
      {
        xtype: 'tbbutton',
        text:    'Edit Server',
        icon: '/images/database-tool.png',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onMemcachedEditServer
      },
      '-',
      {
        xtype: 'tbbutton',
        text:    'Clear Memcached',
        icon: '/images/icon-pmclear-cache.png',
        style : {
          padding: "5px 5px 5px 5px"
        },
        handler: onMemcachedClear
      },
      '-',
      new Ext.Toolbar.TextItem({id: 'statusMemcached', text: ''})
    ]
  });

  var formMemcached = new Ext.FormPanel({
    frame: true,
    //title:'Memcached Properties',
    labelAlign: 'right',
    labelWidth: 80,
    autoHeight: true,

    items: [
      {
        fieldLabel: 'Supported',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Enabled',
        xtype: 'displayfield'
      }
    ]
  });

  var memcachedPanel = new Ext.FormPanel({
    frame: true,
    border: false,
    id: 'memcachedPanel',
    bodyStyle: 'padding: 0.25em; font-weight: bold; font-size: 1.3em;',
    //title: 'Memcached',
    labelAlign: 'center',
    labelWidth: 200,
    autoHeight: true,
    tbar: memcachedBar,
    items: [
      {
        fieldLabel: 'Supported',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Enabled',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Version',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Uptime',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Total Items',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Current Connections',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Retrieval requests',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Storage requests',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Successful hits',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Missing hits',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'MB read from network',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'MB written to network',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'MB for storage',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Items removed from cache',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Memcached Server',
        xtype: 'displayfield'
      }
    ]
  });

  function onMemcachedRefresh(item, pressed) {
    Ext.getCmp('statusMemcached').setText('Updating....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        if (data.error == 1) {
          var sqlMessage = 'Error:' + data.message;
        }
        else {
          var sqlMessage = data.message;
          var formItems = memcachedPanel.form.items;
          formItems.items[0].setValue(data.supported);
          formItems.items[1].setValue(data.enabled);
          formItems.items[2].setValue(data.version);
          formItems.items[3].setValue(data.uptime);
          formItems.items[4].setValue(data.total_items);
          formItems.items[5].setValue(data.curr_connections);
          formItems.items[6].setValue(data.cmd_get);
          formItems.items[7].setValue(data.cmd_set);
          formItems.items[8].setValue(data.get_hits);
          formItems.items[9].setValue(data.get_misses);
          formItems.items[10].setValue(data.mbytes_read);
          formItems.items[11].setValue(data.mbytes_write);
          formItems.items[12].setValue(data.total_mbytes);
          formItems.items[13].setValue(data.evictions);
          formItems.items[14].setValue(data.memcached_server);

          var formItems = formMemcached.form.items;
          formItems.items[0].setValue(data.supported);
          formItems.items[1].setValue(data.enabled);
        }
        Ext.getCmp('statusMemcached').setText(sqlMessage);
      },
      failure: function () {},
      params: {xaction: 'refreshMemcached'}
    });
  }

  function onMemcachedEditServer(item, pressed) {
    Ext.getCmp('statusMemcached').setText('Changing server');
    // Prompt for user server
    Ext.Msg.prompt('Memcached', 'Please enter your memcached server:', function(btn, text){
      if (btn == 'ok'){
        Ext.Ajax.request({
          url: 'mainAjax',
          method: 'POST',
          success: function (response) {
            var data = Ext.util.JSON.decode(response.responseText);
            var formItems = formMemcached.form.items;
            formItems.items[1].setValue( data.enabled);
            onMemcachedRefresh(item, pressed);
          },
          failure: function () {},
          params: {xaction: 'memcachedEditServer', server: text}
        });
      }
    });
  }

  function onMemcachedClear(item, pressed) {
    Ext.getCmp('statusMemcached').setText('Clearing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function(response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formMemcached.form.items;
        formItems.items[1].setValue( data.enabled);
        onMemcachedRefresh(item, pressed);
      },
      failure: function () {},
      params: {xaction: 'memcachedClear'}
    });
  }

  var sphinxBar = new Ext.Toolbar({
    items: [
      {
        xtype  : 'tbbutton',
        text   : 'Status',
        handler: onSphinxRefresh
      },
      '-',
      {
        xtype  : 'tbbutton',
        text   : 'Disable/Enable',
        handler: onSphinxDisableEnable
      },
      '-',
      {
        xtype  : 'tbbutton',
        text   : 'Edit Server',
        handler: onSphinxEditServer
      },
      ' ',
        new Ext.Toolbar.TextItem({id:'statusSphinx',text:''}),
      ' '
    ]
  });

  var formSphinx = new Ext.FormPanel({
    frame: true,
    labelAlign: 'right',
    labelWidth: 80,
    autoHeight: true,

    items: [
      {
        fieldLabel : 'Enabled',
        xtype      : 'displayfield'
      }
    ]
  });

  var sphinxPanel = new Ext.FormPanel({
    frame: true,
    id: 'sphinxPanel',
    bodyStyle: 'background:none;padding:10px;;font-weight:bold;font-size:1.3em;',
    title: 'Sphinx',
    labelAlign: 'center',
    labelWidth: 200,
    autoHeight: true,
    tbar: sphinxBar,
    items: [
      {
        fieldLabel: 'Uptime',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Current Connections',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Search',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Excerpt',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Update',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Keywords',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Persist',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Status',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Sphinx Server',
        xtype: 'displayfield'
      }
    ]
  });

  function onSphinxDisableEnable(item, pressed) {
    Ext.getCmp('statusSphinx').setText('Changing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function(response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formSphinx.form.items;
        formItems.items[0].setValue(data.enabled);
        onSphinxRefresh(item, pressed);
      },
      failure: function () {},
      params: {xaction: 'sphinxDisableEnable'}
    });
  }

  function onSphinxRefresh(item, pressed) {
    Ext.getCmp('statusSphinx').setText('Updating....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        if (data.error == 1) {
          var sqlMessage= 'Error:' + data.message;
        }
        else {
          var sqlMessage = data.message;
          var formItems = sphinxPanel.form.items;
          formItems.items[0].setValue(data.uptime);
          formItems.items[1].setValue(data.connections);
          formItems.items[2].setValue(data.command_search);
          formItems.items[3].setValue(data.command_excerpt);
          formItems.items[4].setValue(data.command_update);
          formItems.items[5].setValue(data.command_keywords);
          formItems.items[6].setValue(data.command_persist);
          formItems.items[7].setValue(data.command_status);
          formItems.items[8].setValue(data.sphinx_server);

          var formItems = formSphinx.form.items;
          formItems.items[0].setValue(data.enabled);
        }
        Ext.getCmp('statusSphinx').setText(sqlMessage);
      },
      failure: function () {},
      params: {xaction: 'refreshSphinx'}
    });
  }

  function onSphinxEditServer(item, pressed) {
    Ext.getCmp('statusSphinx').setText('Changing server');
    //Prompt for user server
    Ext.Msg.prompt('Sphinx', 'Please enter your Sphinx server:', function(btn, text){
      if (btn == 'ok') {
        Ext.Ajax.request({
          url: 'mainAjax',
          method: 'POST',
          success: function (response) {
            var data = Ext.util.JSON.decode(response.responseText);
            var formItems = formSphinx.form.items;
            formItems.items[1].setValue(data.enabled);
            Ext.getCmp('statusSphinx').setText(data.message);
            onSphinxRefresh(item,pressed);
          },
          failure: function () {},
          params: {xaction: 'sphinxEditServer', server: text}
        });
      }
    });
  }

  function onSphinxClear(item, pressed) {
    Ext.getCmp('statusSphinx').setText('Clearing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formMemcached.form.items;
        formItems.items[1].setValue(data.enabled);
        onSphinxRefresh(item,pressed);
      },
      failure: function () {},
      params: {xaction: 'sphinxClear'}
    });
  }

  var gearmanBar = new Ext.Toolbar({
    items: [
      {
        xtype  : 'tbbutton',
        text   : 'Status',
        handler: onGearmanRefresh
      },
      '-',
      {
        xtype  : 'tbbutton',
        text   : 'Disable/Enable',
        handler: onGearmanDisableEnable
      },
      '-',
      {
        xtype  : 'tbbutton',
        text   : 'Edit Server',
        handler: onGearmanEditServer
      },
      ' ',
        new Ext.Toolbar.TextItem({id: 'statusGearman', text: ''}),
      ' '
    ]
  });

  var formGearman = new Ext.FormPanel({
    frame: true,
    labelAlign: 'right',
    labelWidth: 80,
    autoHeight: true,

    items: [
      {
        fieldLabel: 'Supported',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Enabled',
        xtype: 'displayfield'
      }
    ]
  });

  var gearmanPanel = new Ext.FormPanel({
    frame: true,
    id: 'gearmanPanel',
    bodyStyle: 'background:none;padding:10px;font-weight:bold;font-size:1.3em;',
    title: 'Gearman',
    labelAlign: 'center',
    labelWidth: 200,
    autoHeight: true,
    tbar: gearmanBar,
    items: [
      /*
      {
        fieldLabel: 'Uptime',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Current Connections',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Search',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Excerpt',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Update',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Keywords',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Persist',
        xtype: 'displayfield'
      },
      {
        fieldLabel: 'Command Status',
        xtype: 'displayfield'
      },
      */
      {
        fieldLabel: 'Gearman Server',
        xtype: 'displayfield'
      }
    ]
  });

  function onGearmanDisableEnable(item, pressed) {
    Ext.getCmp('statusGearman').setText('Changing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formGearman.form.items;
        formItems.items[0].setValue(data.enabled);
        onGearmanRefresh(item,pressed);
      },
      failure: function () {},
      params: {xaction: 'gearmanDisableEnable'}
    });
  }

  function onGearmanRefresh(item, pressed) {
    Ext.getCmp('statusGearman').setText('Updating....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        if (data.error == 1) {
          var sqlMessage = 'Error:' + data.message;
        }
        else {
          var sqlMessage = data.message;
          var formItems = gearmanPanel.form.items;
          formItems.items[0].setValue(data.gearman_server);
/*
          formItems.items[0].setValue(data.uptime);
          formItems.items[1].setValue(data.connections);
          formItems.items[2].setValue(data.command_search);
          formItems.items[3].setValue(data.command_excerpt);
          formItems.items[4].setValue(data.command_update);
          formItems.items[5].setValue(data.command_keywords);
          formItems.items[6].setValue(data.command_persist);
          formItems.items[7].setValue(data.command_status);
*/
          var formItems = formGearman.form.items;
          formItems.items[0].setValue(data.supported);
          formItems.items[1].setValue(data.enabled);
        }
        Ext.getCmp('statusGearman').setText(sqlMessage);
      },
      failure: function () {},
      params: {xaction: 'refreshGearman'}
    });
  }

  function onGearmanEditServer(item, pressed) {
    Ext.getCmp('statusGearman').setText('Changing server');
    // Prompt for user server
    Ext.Msg.prompt('Sphinx', 'Please enter your Sphinx server:', function (btn, text) {
      if (btn == 'ok') {
        Ext.Ajax.request({
          url: 'mainAjax',
          method: 'POST',
          success: function (response) {
            var data = Ext.util.JSON.decode(response.responseText);
            var formItems = formGearman.form.items;
            formItems.items[1].setValue(data.enabled);
            Ext.getCmp('statusGearman').setText( data.message );
            onGearmanRefresh(item,pressed);
          },
          failure: function () {},
          params: {xaction: 'gearmanEditServer', server: text}
        });
      }
    });
  }

  function onGearmanClear(item, pressed) {
    Ext.getCmp('statusGearman').setText('Clearing....');

    Ext.Ajax.request({
      url: 'mainAjax',
      method: 'POST',
      success: function (response) {
        var data = Ext.util.JSON.decode(response.responseText);
        var formItems = formGearman.form.items;
        formItems.items[1].setValue(data.enabled);
        onGearmanRefresh(item,pressed);
      },
      failure: function () {},
      params: {xaction: 'gearmanClear'}
    });
  }

    function onSqlExecute(item, pressed) {
      //var centerPanel = Ext.getCmp('center-panel'); //*****
      var sqlSentence = Ext.getCmp('sqlSentence').getValue();

      var sqlPanel = Ext.getCmp('sqlPanel');

      Ext.Ajax.request({
        url: 'mainAjax',
        method: 'POST',
        success: function (response) {
          var data = Ext.util.JSON.decode(response.responseText);
          var sqlMessage= Ext.getCmp('sqlMessage');
          if (data.error == 1) {
            sqlMessage.setValue( data.message);
          }
          else {
            sqlMessage.setValue(data.message);
//            var tempStore = new Ext.data.JsonStore({
//  //          id: 'store-' + gridItems.name,
//              root: 'data',
//              totalProperty: 'totalCount',
//              idProperty: 'id',
//              autoLoad  : true,
//              remoteSort: false,
//              fields: data.fields,
//              proxy: new Ext.data.HttpProxy({
//                url: 'mainAjax',
//              })
//            });
          }
          //tempStore.setBaseParam('xaction', 'tableRows');
          //tempStore.setBaseParam('table', sqlSentence);

          //var tempGrid = new Ext.grid.GridPanel({
          //  //id: 'grid-' + gridItems.name,
          //  title: 'Query',
          //  columns: data.columns,
          //  iconCls: 'tabs',
          //  store: tempStore,
          //  closable:true,
          //  listeners: {
          //  }
          //});
          //centerPanel.add(tempGrid);
          //centerPanel.doLayout();
          //tempGrid.show();
      },
      failure: function () {},
      params: {xaction: 'query', query: sqlSentence}
    });

      //storeServer.setBaseParam('filter', item.id);
      //storeServer.load();
    }

    function onQueryLogCollapse(panel) {
    };

    /*//*****
    function onQueryLogExpand(panel) {
      //centerPanel = Ext.getCmp('center-panel'); //*****
      //centerPanel.add(logPanel); //*****
      //logPanel.doLayout();
      //centerPanel.doLayout();
      onLogRefresh();
      //logPanel.show(); //*****

    };

    function onMemcachedExpand(panel) {
      //centerPanel = Ext.getCmp('center-panel'); //*****
      //centerPanel.add(memcachedPanel); //*****
      //centerPanel.doLayout(); //*****
      onMemcachedRefresh();
      //memcachedPanel.show(); //*****
    };
    */

    function onSphinxExpand(panel) {
      //centerPanel = Ext.getCmp('center-panel'); //*****
      //centerPanel.add(sphinxPanel); //*****
      //centerPanel.doLayout(); //*****
      sphinxPanel.show();
    };

    function onGearmanExpand(panel) {
      //centerPanel = Ext.getCmp('center-panel'); //*****
      //centerPanel.add(gearmanPanel); //*****
      //centerPanel.doLayout(); //*****
      gearmanPanel.show();
    };

    /*
    var viewport = new Ext.Viewport({
      layout: 'border',
      items: [
        {
         id: 'app-header',
         xtype: 'box',
         region: 'north',
         height: 20,
         html: '<b>ProcessMaker Monitor tool</b>'
        },
        {
          region:'west',
          id:'west-panel',
          //title:'Workspaces',
          split:true,
          width: 240,
          minSize: 175,
          maxSize: 400,
          collapsible: true,
          margins:'5 0 5 5',
          cmargins:'5 5 5 5',
          layout:'anchor',
          layoutConfig:{
            animate:true
          },
          items: [
            {
              title: 'Query Log Analyzer',
              border: true,
              autoScroll: true,
              iconCls: 'settings',
              collapsible: true,
              collapsed: true,
              items: [
                formQueryLog
              ],
              listeners: {
                beforeexpand: function (pnl, animate) {
                  //function onLogRefresh
                  Ext.Ajax.request({
                    url: "mainAjax",
                    method: "POST",
                    params: {xaction: "logRefresh"},

                    success: function(response) {
                      var data = eval("(" + response.responseText + ")"); //json

                      var formItems = formQueryLog.form.items;
                      var message = "";

                      if (data.error == 1) {
                        message = "Error:" + data.message;
                        formItems.items[0].setValue(data.enabled);
                        formItems.items[1].setValue("..");
                        formItems.items[2].setValue("..");
                      }
                      else {
                        message = data.message;
                        formItems.items[0].setValue(data.enabled);
                        formItems.items[1].setValue(data.ipsPages);
                        formItems.items[2].setValue(data.posSize);
                      }

                      Ext.getCmp("statusLog").setText(message);
                    },
                    failure: function () {}
                  });
                },

                expand: onQueryLogExpand
              }
            },
            {
              title: 'Time Log Analyzer',
              border: false,
              autoScroll: true,
              iconCls: 'settings',
              collapsible: true,
              collapsed: true,
              items: [
                formTimeLog
              ]
            },
            {
              title: 'Memcached',
              border: false,
              autoScroll: true,
              iconCls: 'settings',
              collapsible: true,
              collapsed: true,
              items: [
                formMemcached
              ],
              listeners: {
                expand: onMemcachedExpand
              }
            }
            //,
            //{
            //  title: 'Sphinx',
            //  border: false,
            //  autoScroll: true,
            //  iconCls: 'settings',
            //  collapsible: true,
            //  collapsed: true,
            //  items: [
            //    formSphinx
            //  ],
            //  listeners: {
            //    expand: onSphinxExpand
            //  }
            //},
            //{
            //  title: 'Gearman',
            //  border: false,
            //  autoScroll: true,
            //  iconCls: 'settings',
            //  collapsible: true,
            //  collapsed: true,
            //  items: [
            //    formGearman
            //  ],
            //  listeners: {
            //    expand: onGearmanExpand
            //  }
            //}
          ]
        },
        {
          id:'center-panel',
          //xtype:'portal',
          activeTab: 0,
          xtype: 'tabpanel',
          region:'center',
          margins:'5 5 5 0',
          resizeTabs:true, // turn on tab resizing
          minTabWidth: 115,
          tabWidth: 135,
          enableTabScroll: true,
          defaults: {autoScroll: true},
          plugins: new Ext.ux.TabCloseMenu(),
          items: []
        }
      ]
    });
    */

    //centerPanel = Ext.getCmp('center-panel'); //*****

    var sqlPanel = new Ext.grid.GridPanel({
      id: 'sqlPanel',
      margins:'2 2 2 2',
      title: 'sql',
      split: true,
      height: 200,
      iconCls: 'tabs',
      columns: [
        {header: "#",    width: 20,  dataIndex: 'id',   sortable: true},
        {header: "Name", width: 150, dataIndex: 'name', sortable: true},
        {header: "rows", width: 50,  dataIndex: 'cant', sortable: true, align: 'right'}
      ],
      store: tablesStore2,
      region:'center'
    });

    var sqlToolbar = new Ext.Toolbar({
      height: 33,
      items: [
        new Ext.Button ({
          id: 'execute',
          text: 'Execute',
          handler: onSqlExecute
        })
      ]
    });

    var sqlviewport = new Ext.Panel({
      title: 'Query',
      layout: 'border',
      border: false,
      items: [{
        region: 'center',
        height: '65%',
        split: true,
        tbar : sqlToolbar,
        items: [{
          xtype: 'textarea',
          id : 'sqlSentence',
          hideLabel: true,
          labelSeparator: '',
          height: '100%',
          width: '100%'
        }]
      },
      //sqlPanel,
      {
        margins:'2 2 2 2',
        region: 'south',
        height: 100,
        split: true,
        items: [{
          id : 'sqlMessage',
          xtype: 'displayfield',
          height: '100%',
          width: '100%'
        }]
      }]
    });

    //centerPanel.add(logPanel);
    //centerPanel.add(sqlviewport );
    //centerPanel.doLayout();

  ///////
  function eAcceleratorProcessAjax(option)
  {  switch (option) {
       case "INFO":
       case "CACHEED":
       case "OPTIMIZERED":
       case "CACHECLEAR":
       case "CACHECLEAN":
       case "CACHEPURGE":
         Ext.ComponentMgr.get("loadIndicator").setValue("<img src=\"/images/documents/_indicator.gif\" />");
         break;

       case "INSTALLED":
         break;
     }

     var p = {
       "option": option
     };

     Ext.Ajax.request({
       url: "eAcceleratorAjax",
       method: "POST",
       params: p,

       success: function (response, opts) {
         var dataResponse = eval("(" + response.responseText + ")"); //json

         switch (option) {
           case "INFO":
           case "CACHEED":
           case "OPTIMIZERED":
           case "CACHECLEAR":
           case "CACHECLEAN":
           case "CACHEPURGE":
             if (dataResponse.status && dataResponse.status == "OK") {
               var frm = frmEAcceleratorInfo.form.items;

               frm.items[0].setValue(dataResponse.version);
               frm.items[1].setValue(dataResponse.cache);
               frm.items[2].setValue(dataResponse.optimizer);
               frm.items[3].setValue(dataResponse.memoryUsage);
               frm.items[4].setValue(dataResponse.memoryFree);
               frm.items[5].setValue(dataResponse.cachedScripts);
               frm.items[6].setValue(dataResponse.removedScripts);
               frm.items[7].setValue(dataResponse.cachedKeys);
             }
             else {
               Ext.MessageBox.alert("Warning", "Error.<br />" + dataResponse.message);
             }

             Ext.ComponentMgr.get("loadIndicator").setValue("");
             break;

           case "INSTALLED":
             if (dataResponse.status && dataResponse.status == "ERROR") {
               tabMain.remove("tabMainEAccelerator", true);
             }
             break;
         }
       },

       failure: function (response, opts) {
         //
       }
     });
  }

  function eAcceleratorListProcessAjax(option, n, r, i)
  {  var p = {
       "option": option,
       "pageSize": n,
       "limit": r,
       "start": i
     };

     Ext.Ajax.request({
       url: "eAcceleratorAjax",
       method: "POST",
       params: p,

       success: function (response, opts) {
         var dataResponse = eval("(" + response.responseText + ")"); //json

         if (dataResponse.status && dataResponse.status == "OK") {
           switch (option) {
             case "CACHEDSCRIPTLST":  storeCachedScript.loadData(dataResponse); break;
             case "REMOVEDSCRIPTLST": storeRemovedScript.loadData(dataResponse); break;
           }
         }
         else {
           Ext.MessageBox.alert("Warning", "Error.<br />" + dataResponse.message);
         }
       }
       //,

       //failure: function (response, opts) {
       //}
     });
  }

  ///////
  function scriptStore(option, ps)
  {  var component = new Ext.data.Store({
       proxy: new Ext.data.HttpProxy({
         url: "eAcceleratorAjax",
         method: "POST"
       }),

       reader: new Ext.data.JsonReader({
         totalProperty: "total",
         root: "root",
         fields: scriptField
       }),

       //autoLoad: true,

       listeners:{
         beforeload:function (store) {
           this.baseParams = {"option": option, "pageSize": pageSize[ps]};
         }
       }
     });

     return (component);
  }

  function scriptComboBox(comboBoxId, pagtb, ps)
  {  var component = new Ext.form.ComboBox({
       id: comboBoxId,

       mode: "local",
       triggerAction: "all",
       store: storePageSize,
       valueField: "size",
       displayField: "size",
       width: 50,
       editable: false,

       listeners:{
         select: function (combo, record, index) {
           pageSize[ps] = parseInt(record.data["size"]);

           Ext.ComponentMgr.get(pagtb).pageSize = pageSize[ps];
           Ext.ComponentMgr.get(pagtb).moveFirst();
         }
       }
     });

     return (component);
  }

  function scriptPagingToolbar(pagingtbId, pagingtbStore, pagingtbComboBox, ps)
  {  var component = new Ext.PagingToolbar({
       id: pagingtbId,

       pageSize: pageSize[ps],
       store: pagingtbStore,
       displayInfo: true,
       displayMsg: "Displaying results " + "{" + "0" + "}" + " - " + "{" + "1" + "}" + " of " + "{" + "2" + "}",
       emptyMsg: "No results to display",
       items: ["-", "Page size:", pagingtbComboBox]
     });

     return (component);
  }

  ///////
  var scriptField  = [
    {name: "file",    type: "string"},
    {name: "mtime",   type: "string"},
    {name: "size",    type: "string"},
    {name: "reloads", type: "string"},
    {name: "hits",    type: "string"}
  ];

  var scriptColumn = [
    {dataIndex: "file",    header: "Filename", width: 150, sortable: true},
    {dataIndex: "mtime",   header: "MTime", sortable: true},
    {dataIndex: "size",    header: "Size", sortable: true},
    {dataIndex: "reloads", header: "Reloads", sortable: true},
    {dataIndex: "hits",    header: "Hits", sortable: true}
  ];

  var pageSize = [];
  pageSize["cached"] = 25;
  pageSize["removed"] = 25;

  var swCached = 1;
  var swRemoved = 1;

  ///////
  var storeCachedScript = scriptStore("CACHEDSCRIPTLST", "cached");
  var storeRemovedScript = scriptStore("REMOVEDSCRIPTLST", "removed");

  var storePageSize = new Ext.data.SimpleStore({
    fields: ["size"],
    data: [["25"], ["35"], ["50"], ["100"]],
    autoLoad: true
  });

  ///////
  var cboCachedScriptPageSize  = scriptComboBox("cboCachedScriptPageSize", "pagtbCachedScript", "cached");
  var cboRemovedScriptPageSize = scriptComboBox("cboRemovedScriptPageSize", "pagtbRemovedScript", "removed");

  var pagtbCachedScript = scriptPagingToolbar("pagtbCachedScript", storeCachedScript, cboCachedScriptPageSize, "cached");
  var pagtbRemovedScript = scriptPagingToolbar("pagtbRemovedScript", storeRemovedScript, cboRemovedScriptPageSize, "removed");

  var grdCachedScript = new Ext.grid.GridPanel({
    id: "grdCachedScript",

    columnLines: true,
    //stripeRows: true,
    bbar: pagtbCachedScript,
    height: 350,
    columns: scriptColumn,
    store: storeCachedScript
  });

  var grdRemovedScript = new Ext.grid.GridPanel({
    id: "grdRemovedScript",

    columnLines: true,
    bbar: pagtbRemovedScript,
    height: 350,
    columns: scriptColumn,
    store: storeRemovedScript
  });

  var tbarEAcceleratorInfo = new Ext.Toolbar({
    items: [
      new Ext.Toolbar.TextItem({text: "Caching:"}),
      {
        xtype: "button",
        //disabled: true,
        //iconCls: "button_menu_ext ss_sprite ss_tag_green",
        text: "Enable/Disable",
        handler: function () {
          eAcceleratorProcessAjax("CACHEED");
        }
      },
      "-",
      new Ext.Toolbar.TextItem({text: "Optimizer:"}),
      {
        xtype: "button",
        text: "Enable/Disable",
        handler: function () {
          eAcceleratorProcessAjax("OPTIMIZERED");
        }
      },
      "-",
      {
        xtype: "button",
        text: "Clear cache",
        //tooltip: "Remove all unused scripts and data from shared memory and disk cache",
        handler: function () {
          eAcceleratorProcessAjax("CACHECLEAR");
        }
      },
      "-",
      {
        xtype: "button",
        text: "Clean cache",
        //tooltip: "Remove all expired scripts and data from shared memory and disk cache",
        handler: function () {
          eAcceleratorProcessAjax("CACHECLEAN");
        }
      },
      "-",
      {
        xtype: "button",
        text: "Purge cache",
        //tooltip: "Remove all 'removed' scripts from shared memory",
        handler: function () {
          eAcceleratorProcessAjax("CACHEPURGE");
        }
      },
      "->",
      {
        xtype: "displayfield",
        id: "loadIndicator"
      }
    ]
  });

  ///////
  var frmEAcceleratorInfo = new Ext.FormPanel({
    id: "frmEAcceleratorInfo",

    frame: true,
    border: false,

    bodyStyle: "padding: 0.25em; font-weight: bold; font-size: 1.3em;",
    //labelAlign: "center",
    labelWidth: 200,
    autoHeight: true,
    tbar: tbarEAcceleratorInfo,
    items: [
      {
        xtype: "displayfield",
        fieldLabel: "Version"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Caching enabled"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Optimizer enabled"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Memory usage"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Free memory"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Cached scripts"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Removed scripts"
      },
      {
        xtype: "displayfield",
        fieldLabel: "Cached keys"
      }
    ]
  });

  ///////
  var tabQueryLog = new Ext.TabPanel({
    border: false,
    enableTabScroll: true,
    activeTab: 0,
    //style: {
    //  border: "5px solid yellow"
    //},
    items: [
      {
        title: "Information",
        items: [formQueryLog]
      },
      {
        title: "Log",
        //defaults: {
        //  layout: "fit"
        //},
        items: [logPanel]
      }
    ]
  });

  var tabTimeLog = new Ext.TabPanel({
    border: false,
    activeTab: 0,
    items: [
      {
        title: "Information",
        items: [formTimeLog]
      }
    ]
  });

  var tabWorkspaceManagement = new Ext.TabPanel({
    border: false,
    activeTab: 0,
    items: [
      {
        title: "Workspace Management",
        html: "<iframe src=\"workspaceManagement\" width=\"99%\" height=\"100%\" style=\"border: 0;\"></iframe>"
      },
      {
        title: "New Workspace",
        html: "<iframe src=\"../install/newSite?type=blank\" width=\"99%\" height=\"100%\" style=\"border: 0;\"></iframe>"
      }
    ]
  });

  var tabEAccelerator = new Ext.TabPanel({
    border: false,
    activeTab: 0,
    items: [
      {
        title: "Information",
        items: [frmEAcceleratorInfo]
      }
      /*
      ,
      {
        title: "Cached scripts",
        items: [grdCachedScript],
        listeners: {
          activate: function () {
            //storeCachedScript.load();
            if (swCached == 1) {
              eAcceleratorListProcessAjax("CACHEDSCRIPTLST", pageSize["cached"], pageSize["cached"], 0);
              swCached = 0;
            }
          }
        }
      },
      {
        title: "Removed scripts",
        items: [grdRemovedScript],
        listeners: {
          activate: function () {
            if (swRemoved == 1) {
              eAcceleratorListProcessAjax("REMOVEDSCRIPTLST", pageSize["removed"], pageSize["removed"], 0);
              swRemoved = 0;
            }
          }
        }
      }
      */
    ]
  });

  /*
  if (EACCELERATOR_CACHEKEYS == 1) {
    tabEAccelerator.add({
      title: "Cached keys",
      items: []
    });
  }
  */

  var tabMainItem = [];

  tabMainItem.push(
    {
      //layout: "fit",
      //closable: true,
      title: "Query Log Analyzer",
      items: [tabQueryLog],

      listeners: {
        activate: function () {
          onLogRefresh();
        }
      }
    }
  );
  /*
  tabMainItem.push(
    {
      title: "Time Log Analyzer",
      items: [tabTimeLog]
    }
  );
  */
  tabMainItem.push(
    {
      title: "Memcached",
      items: [memcachedPanel],

      listeners: {
        activate: function () {
          onMemcachedRefresh();
        }
      }
    }
  );
  tabMainItem.push(
    {
      title: "Multitenant Management",
      items: [tabWorkspaceManagement]
    }
  );
  if (EACCELERATOR_INSTALLED == 1) {
    tabMainItem.push(
      {
        id: "tabMainEAccelerator",

        title: "eAccelerator",
        items: [tabEAccelerator],

        listeners: {
          activate: function () {
            eAcceleratorProcessAjax("INFO");
          }
        }
      }
    );
  }

  var tabMain = new Ext.TabPanel({
    border: false,
    enableTabScroll: true,
    activeTab: 0,
    //style: {
    //  border: "5px solid black"
    //},

    //plugins: new Ext.ux.TabCloseMenu(),

    defaults: {
      layout: "fit"
    },

    items: [tabMainItem]
  });

  ///////
  cboCachedScriptPageSize.setValue(pageSize["cached"]);
  cboRemovedScriptPageSize.setValue(pageSize["removed"]);

  if (EACCELERATOR_INSTALLED == 1) {
    eAcceleratorProcessAjax("INSTALLED");
  }

  ///////
  //LOAD ALL PANELS
  var viewport = new Ext.Viewport({
    layout: "fit",
    //renderTo: Ext.getBody(),

    //autoScroll: true,
    //defaults: {autoScroll: true},

    items: [tabMain]
  });
});