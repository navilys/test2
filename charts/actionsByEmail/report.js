var actionsByEmailGrid;
var store;
var win ;

new Ext.KeyMap(document, {
  key: Ext.EventObject.F5,
  fn: function(keycode, e) {
      if (! e.ctrlKey) {
        if (Ext.isIE)
            e.browserEvent.keyCode = 8;
        e.stopEvent();
        document.location = document.location;
      }
      else
        Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
  }
});


Ext.onReady(function(){
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: '../actionsByEmail/actionsByEmailAjax',
      method: 'POST'
    }),

    reader : new Ext.data.JsonReader( {
      totalProperty: 'totalCount',
      root: 'data',
      fields : [
        {name : 'ABE_UID'},
        {name : 'ABE_REQ_UID'},
        {name : 'APP_UID'},
        {name : 'TAS_UID'},
        {name : 'ABE_REQ_DATE'},
        {name : 'ABE_REQ_SUBJECT'},
        {name : 'APP_NUMBER'},
        {name : 'USER'},
        {name : 'ABE_REQ_SENT_TO'},
        {name : 'ABE_REQ_STATUS'},
        {name : 'ABE_REQ_ANSWERED'},
        {name : 'ABE_RES_MESSAGE'}
      ]
    })
  });
  store.setBaseParam( 'action', 'loadActionByEmail' );

  actionsByEmailGrid = new Ext.grid.GridPanel( {
    region: 'center',
    layout: 'fit',
    id: 'actionsByEmailGrid',
    //width:800,
    title : '',
    stateful : true,
    stateId : 'grid',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: true,

    cm: new Ext.grid.ColumnModel({
      defaults: {
          sortable: true
      },
      columns: [
        {id:      'ABE_UID',                          dataIndex: 'ABE_UID',             hidden:true, hideable:false},
        {header:  "DATE",               width:  100,  dataIndex: 'ABE_REQ_DATE',        sortable: true  },
        {header:  "CASE NUMBER",        width:  70,   dataIndex: 'APP_NUMBER',          sortable: true  },
        {header:  "SUBJECT",            width:  150,  dataIndex: 'ABE_REQ_SUBJECT',     sortable: true  },
        {header:  "FROM",               width:  110,  dataIndex: 'USER',                sortable: true  },
        {header:  "TO",                 width:  110,  dataIndex: 'ABE_REQ_SENT_TO',     sortable: true  },
        {header:  "STATUS",             width:  40,   dataIndex: 'ABE_REQ_STATUS',      sortable: true  },
        {header:  "ANSWERED",           width:  60,   dataIndex: 'ABE_REQ_ANSWERED' },
        {header:  'VIEW RESPONSE',      width:  80,   sortable: false, align: 'center',                 renderer: function(val){ return '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ss_layout_header" unselectable="off" id="extdd-17" onclick="openForm()" '; }, dataIndex: 'somefieldofyourstore'},
        {header:  "ERROR MESSAGE",      width:  130,  dataIndex: 'ABE_RES_MESSAGE',sortable: false }
      ]
    }),
    store: store,
    tbar:[
      {
        text:'Resend Email',
        iconCls: 'button_menu_ext ss_sprite  ss_world',
        handler:ForwardEmail
      }
    ],
    // paging bar on the bottom
    bbar: new Ext.PagingToolbar({
        pageSize: 25,
        store: store,
        displayInfo: true,
        displayMsg: 'Displaying actionsByEmail {0} - {1} of {2}'//,
    }),
    viewConfig: {
      forceFit: true
    },
      listeners: {
      render: function(){
        this.loadMask = new Ext.LoadMask(this.body, {msg:'Loading...'});
      }
    }
  });

  store.load({params:{ start : 0 , limit : 25 }});
  actionsByEmailGrid.addListener('rowcontextmenu', onMessageContextMenu,this);
  actionsByEmailGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
    var sm = grid.getSelectionModel();
    sm.selectRow(rowIndex, sm.isSelected(rowIndex));

    var rowSelected = Ext.getCmp('actionsByEmailGrid').getSelectionModel().getSelected();

  }, this);
  actionsByEmailGrid.on('contextmenu', function (evt) {
      evt.preventDefault();
  }, this);

  function onMessageContextMenu(grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    messageContextMenu.showAt([coords[0], coords[1]]);
  }

  var viewport = new Ext.Viewport({
    layout: 'border',
    autoScroll: true,
    items: [
      actionsByEmailGrid
    ]
  });
});

function openForm(){
  var rows = actionsByEmailGrid.getSelectionModel().getSelections();
  var REQ_UID = '';
  var ids = '';
  for(i=0; i<rows.length; i++) {
    if(i != 0 ) ids += ',';
    ids += rows[i].get('APP_NUMBER') + ', ';
    ids += rows[i].get('ABE_REQ_SUBJECT');
    REQ_UID += rows[i].get('ABE_REQ_UID');
  }
  if( REQ_UID != '' ) {
    win = new Ext.Window({
          id: 'win',
          title: ids,
          pageX: 100 ,
          pageY: 100 ,
          width: 500,
          floatable: true,
          autoHeight:true,
          modal: true,
          layout: 'fit',
          autoLoad : {
                url : '../actionsByEmail/actionsByEmailAjax',
                params : { action:'viewForm',REQ_UID : REQ_UID },
                scripts: true
          },
          plain: true,
          buttons: [{
            id: 'btn',
            text: 'Close',
            handler: function() {
              //var index = this.id.replace('btn', '');
              win.hide();
            }
          }]}).show();
  } else {
     Ext.Msg.show({
      title:'',
      msg: TRANSLATIONS.ID_NO_SELECTION_WARNING,
      buttons: Ext.Msg.INFO,
      fn: function(){},
      animEl: 'elId',
      icon: Ext.MessageBox.INFO,
      buttons: Ext.MessageBox.OK
    });
  }
}
function ForwardEmail(){
  var rows = actionsByEmailGrid.getSelectionModel().getSelections();
  var REQ_UID = '';
  var ids = '';
  for(i=0; i<rows.length; i++) {
    if(i != 0 ) ids += ',';
    REQ_UID += rows[i].get('ABE_REQ_UID');
    ids += rows[i].get('APP_NUMBER') + ', ';
    ids += rows[i].get('ABE_REQ_SUBJECT');
  }
  if( REQ_UID != '' ) {
    win = new Ext.Window({
            id: 'win',
            title: ids,
            pageX: 100 ,
            pageY: 100 ,
            width: 500,
            floatable: true,
            autoHeight:true,
            modal: true,
            layout: 'fit',
            autoLoad : {
                  url : '../actionsByEmail/actionsByEmailAjax',
                  params : { action:'forwardMail',REQ_UID :REQ_UID},
                  scripts: true
            },
            plain: true,
            buttons: [{
              id: 'btn',
              text: 'Close',
              handler: function() {
                win.hide();
              }
            }]}).show();
  } else {
     Ext.Msg.show({
      title:'',
      msg: 'Select an item from the list',
      buttons: Ext.Msg.INFO,
      fn: function(){},
      animEl: 'elId',
      icon: Ext.MessageBox.INFO,
      buttons: Ext.MessageBox.OK
    });
  }
}



