var externalRegistrationGrid;
var store;
var storeViewLog;
var win ;
var viewportLog;
var ER_UID;
var formGeneral;
var PRO_UID;
var ER_TITLE;
var storeDynaForm;
var storeTrigger;
var comboResources;
var comboDynaForm;
var comboTemplates;
var comboAssignUser;
var comboNameObject;
var comboTasStart;
var comboTrigger;
var theTitle;

Ext.onReady(function(){
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();
//External Registration List
  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: '../externalRegistration/externalRegistrationAjax',
      method: 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      totalProperty: 'totalCount',
      root: 'data',
      fields : [
        {name : 'ER_UID'},
        {name : 'ER_TITLE'},
        {name : 'PRO_UID'},
        {name : 'ER_TEMPLATE'},
        {name : 'DYN_UID'},
        {name : 'ER_ACTION_ASSIGN'},
        {name : 'ER_OBJECT_UID'},
        {name : 'ER_ACTION_START_CASE'},
        {name : 'TAS_UID'},
        {name : 'ER_ACTION_EXECUTE_TRIGGER'},
        {name : 'TRI_UID'},
        {name : 'ER_CREATE_DATE'},
        {name : 'ER_UPDATE_DATE'},
        {name : 'DYN_TITLE'},
        {name : 'REQ_RECEIVED'},
        {name : 'VIEW_FORM'},
        {name : 'REQ_COMPLETED'}
      ]
    })
  });
  store.setBaseParam( 'action', 'listExternalRegistration' );

  externalRegistrationGrid = new Ext.grid.GridPanel( {
    region            : 'center',
    layout            : 'fit',
    id                : 'externalRegistrationGrid',
    title             : '',
    stateful          : true,
    stateId           : 'grid',
    enableColumnResize: true,
    enableHdMenu      : true,
    frame             :false,
    columnLines       : true,
    cm: new Ext.grid.ColumnModel({
      defaults: {
          sortable: true
      },
      columns: [
        {id:      'ER_UID',                                  dataIndex: 'ER_UID',              hidden:true, hideable:false},
        {header:  "Title",                      width:  100, dataIndex: 'ER_TITLE',            sortable: true  },
        {header:  "Additional Dynaform",        width:  150, dataIndex: 'DYN_TITLE',           sortable: true  },
        {header:  "E-mail Template",            width:  100, dataIndex: 'ER_TEMPLATE',         sortable: true  },
        {header:  "Requests Received",          width:  90,  dataIndex: 'REQ_RECEIVED',        sortable: true  },
        {header:  "Requests Completed",         width:  90,  dataIndex: 'REQ_COMPLETED',       sortable: true  },
        {header:  "",    dataIndex: 'ER_UID',   width:  50,  sortable: false , hideable:false, renderer: function(){return '<a href="#" onclick="openViewUid()">View UID</a>'}   , align:'center'},
        {header:  "",                           width:  50,  dataIndex: 'VIEW_FORM', hideable:false, sortable: true  },
        {header:  "",    dataIndex: 'ER_UID',   width:  50,  sortable: false , hideable:false, renderer: function(){return '<a href="#" onclick="openLog()">View Log</a>'}       , align:'center'},
        {header:  "",    dataIndex: 'ER_UID',   width:  25,  sortable: false , hideable:false, renderer: function(){return '<a href="#" onclick="editForm()">Edit</a>'}          , align:'center'},
        {header:  "",    dataIndex: 'ER_UID',   width:  30,  sortable: false , hideable:false, renderer: function(){return '<a href="#" onclick="removeRecord()">Delete</a>'}    , align:'center'}
      ]
    }),
    store: store,
    tbar:[
      {
        text    :'New External Registration Form',
        iconCls: 'button_menu_ext ss_sprite ss_application_form',
        handler :NewExternalRegistration
      }
    ],
    // paging bar on the bottom
    bbar: new Ext.PagingToolbar({
      pageSize    : 25,
      store       : store,
      displayInfo : true,
      displayMsg  : 'Displaying External Registration Forms{0} - {1} of {2}'
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

  externalRegistrationGrid.addListener('rowcontextmenu', onMessageContextMenu,this);
  externalRegistrationGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
    var sm = grid.getSelectionModel();
    sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    var rowSelected = Ext.getCmp('externalRegistrationGrid').getSelectionModel().getSelected();
  }, this);
  externalRegistrationGrid.on('contextmenu', function (evt) {
    evt.preventDefault();
  }, this);

  //View Log
  storeViewLog = new Ext.data.GroupingStore( {
    proxy   : new Ext.data.HttpProxy({
      url   : '../externalRegistration/externalRegistrationAjax',
      method: 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      totalProperty: 'totalCount',
      root: 'data',
      fields : [
        {name : 'ER_REQ_UID'},
        {name : 'ER_UID'},
        {name : 'ER_REQ_DATA'},
        {name : 'ER_REQ_DATE'},
        {name : 'FULL_NAME'},
        {name : 'EMAIL'},
        {name : 'ER_REQ_COMPLETED'},
        {name : 'ER_REQ_COMPLETED_DATE'}
      ]
    })
  });
  storeViewLog.setBaseParam( 'action', 'listExternalRegistrationLogs' );

  viewLogGrid = new Ext.grid.GridPanel( {
    region            : 'center',
    //layout            : 'fit',
    id                : 'viewLogGrid',
    height            : 300,
    title             : '',
    stateful          : true,
    enableColumnResize: true,
    enableHdMenu      : true,
    columnLines       : true,

    cm: new Ext.grid.ColumnModel({
      defaults: {
        sortable: true
      },
      columns: [
        {id:      'ER_REQ_UID',                       dataIndex: 'ER_REQ_UID',                               hidden:true, hideable:false},
        {header:  "Date",               width:  110,  dataIndex: 'ER_REQ_DATE',             sortable: true  },
        {header:  "Full Name",          width:  120,  dataIndex: 'FULL_NAME',               sortable: true  },
        {header:  "E-Mail",             width:  140,  dataIndex: 'EMAIL',                   sortable: true  },
        {header:  "Registration Completed", width:  110,   dataIndex: 'ER_REQ_COMPLETED',       sortable: true  },
        {header:  "Registration Date ", width:  110,   dataIndex: 'ER_REQ_COMPLETED_DATE',  sortable: true  },
        {header:  "",                   width:  80,   dataIndex: 'DYN_UID',                 sortable: false , renderer: function(){return '<a href="#" onclick="openViewLog()">View filled form</a>'}    , align:'center'}
      ]
    }),
    store: storeViewLog,
    bbar: new Ext.PagingToolbar({
        pageSize    : 25,
        store       : storeViewLog,
        displayInfo : true,
        displayMsg  : 'Displaying {0} - {1} of {2}'
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

  theTitle = new Ext.form.TextField({
    fieldLabel    :'Title',
    name          :'ER_TITLE',
    emptyText     :'Enter a title...',
    id            :'ER_TITLE',
    anchor        : '60%',
    allowBlank    : true,
    listeners     :{
      blur: function() {
        this.setValue(this.getValue().trim());
      }
    }
  });

  storeResources = new Ext.data.Store( {
    proxy   : new Ext.data.HttpProxy({
      url   : '../externalRegistration/externalRegistrationAjax',
      method: 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'data',
      fields : [
        {name : 'PRO_UID'},
        {name : 'PRO_TITLE'}
      ]
    })
  });
  storeResources.setBaseParam( 'action', 'loadResources' );

  comboResources = new Ext.form.ComboBox({
    fieldLabel          : 'Use the resources from process',
    id                  : 'PRO_UID',
    name                : 'PRO_UID',
    hiddenName          : 'PRO_UID',
    forceSelection      : true,
    store               : storeResources,
    emptyText           : '-- Select a Process --',
    triggerAction       : 'all',
    allowBlank          : false,
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'PRO_TITLE',
    valueField          : 'PRO_UID'
  });

  storeDynaForm = new Ext.data.ArrayStore({
    storeId: 'storeDynaForm',
    fields:[
      {name : 'DYN_UID',    type: 'string'},
      {name : 'DYN_TITLE',  type: 'string'}
    ]
  });

  comboDynaForm = new Ext.form.ComboBox({
    fieldLabel          :'Additional DynaForm',
    name                :'DYN_UID',
    hiddenName          :'DYN_UID',
    id                  :'comboDynaForm',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    typeAhead           : true,
    store               : storeDynaForm,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'DYN_TITLE',
    valueField          : 'DYN_UID'
  });

  storeTemplates = new Ext.data.ArrayStore( {
    storeId: 'storeTemplates',
    fields:[
      {name : 'FILE'},
      {name : 'NAME'}
    ]
  });

  comboTemplates = new Ext.form.ComboBox({
    fieldLabel          :'E-Mail Template',
    name                :'ER_TEMPLATE',
    id                  :'ER_TEMPLATE',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    allowBlank          : false,
    store               : storeTemplates,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'FILE',
    valueField          : 'NAME'
  });

  storeAssignUser = new Ext.data.ArrayStore( {
    storeId: 'storeAssignUser',
    fields:[
      {name : 'LABEL'},
      {name : 'VALUE'}
    ]
  });
  comboAssignUser = new Ext.form.ComboBox({
    fieldLabel          :'Assign User To',
    name                :'ASSIGN_USER',
    id                  :'ASSIGN_USER',
    hiddenName          :'ER_ACTION_ASSIGN',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    store               : storeAssignUser,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'LABEL',
    valueField          : 'VALUE'
  });

  storeNameObject = new Ext.data.GroupingStore( {
    proxy   : new Ext.data.HttpProxy({
      url   : '../externalRegistration/externalRegistrationAjax',
      method: 'POST'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'objectName',
      fields:[
        {name : 'LABEL'},
        {name : 'VALUE'}
      ]
    })
  });
  storeNameObject.setBaseParam( 'action', 'loadElements' );

  comboNameObject = new Ext.form.ComboBox({
    fieldLabel          :'Name / Title',
    name                :'ER_OBJECT_UID',
    id                  :'ER_OBJECT_UID',
    hiddenName          :'ER_OBJECT_UID',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    allowBlank          : true,
    store               : storeNameObject,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'LABEL',
    valueField          : 'VALUE'
  });

  storeTasStart = new Ext.data.ArrayStore( {
    fields:[
      {name : 'TAS_UID'},
      {name : 'TAS_TITLE'}
    ]
  });
  comboTasStart = new Ext.form.ComboBox({
    fieldLabel          :'Start a Case on the Task',
    name                :'TAS_UID',
    id                  :'TAS_UID',
    hiddenName          :'TAS_UID',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    store               : storeTasStart,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'TAS_TITLE',
    valueField          : 'TAS_UID'
  });

  storeTrigger = new Ext.data.ArrayStore( {
    fields:[
      {name : 'TRI_UID'},
      {name : 'TRI_TITLE'}
    ]
  });
  comboTrigger = new Ext.form.ComboBox({
    fieldLabel          :'Execute the Trigger',
    name                :'TRI_UID',
    id                  :'TRI_UID',
    hiddenName          :'TRI_UID',
    mode                : 'local',
    disabled            : true,
    forceSelection      : true,
    store               : storeTrigger,
    emptyText           :'-- None --',
    triggerAction       : 'all',
    editable            : false,
    border              : false,
    anchor              : '90%',
    displayField        : 'TRI_TITLE',
    valueField          : 'TRI_UID'
  });

  comboResources.on('select',function(cmb,record,index){
    PRO_UID = record.get('PRO_UID');
    Ext.Ajax.request({
      url: '../externalRegistration/externalRegistrationAjax',
      params: {
        action   : 'loadResources',
        PRO_UID:PRO_UID
      },
      success: function(resp){
        //var dataResult = Ext.util.JSON.decode(resp.responseText);
        dataResult = eval('('+resp.responseText+')');
        Ext.MessageBox.hide();
        if (dataResult.success) {
          comboDynaForm.enable();
          comboDynaForm.clearValue();
            var dataComplete = [];
            for ( var i = 0, c = dataResult.dynaforms.length; i < c; i++ ) {
                dataComplete.push([dataResult.dynaforms[i].DYN_UID, dataResult.dynaforms[i].DYN_TITLE]);
            }
          storeDynaForm.loadData(dataComplete);
          comboTemplates.enable();
          comboTemplates.clearValue();
          var dataTemplates = [];
          for ( var i = 0, c = dataResult.templates.length; i < c; i++ ) {
              dataTemplates.push([dataResult.templates[i].FILE, dataResult.templates[i].NAME]);
          }
          storeTemplates.loadData(dataTemplates);
          comboAssignUser.enable();
          comboAssignUser.clearValue();
          var dataAssignUser = [];
          for ( var i = 0, c = dataResult.AssignUser.length; i < c; i++ ) {
              dataAssignUser.push([dataResult.AssignUser[i].LABEL, dataResult.AssignUser[i].VALUE]);
          }
          storeAssignUser.loadData(dataAssignUser);
          comboTasStart.enable();
          comboTasStart.clearValue();
          var dataTasStart = [];
          for ( var i = 0, c = dataResult.TasStart.length; i < c; i++ ) {
              dataTasStart.push([dataResult.TasStart[i].TAS_UID, dataResult.TasStart[i].TAS_TITLE]);
          }
          storeTasStart.loadData(dataTasStart);
          comboTrigger.enable();
          comboTrigger.clearValue();
          var dataTriggers = [];
          for ( var i = 0, c = dataResult.triggers.length; i < c; i++ ) {
              dataTriggers.push([dataResult.triggers[i].TRI_UID, dataResult.triggers[i].TRI_TITLE]);
          }
          storeTrigger.loadData(dataTriggers);
          comboNameObject.hide();
          comboNameObject.container.up('div.x-form-item').setStyle('display', 'none');
        }
        comboDynaForm.setValue('');
        comboTemplates.setValue('');
        comboAssignUser.setValue('');
        comboNameObject.setValue('');
        comboDynaForm.store.on('loadData',function(store) {
          comboDynaForm.setValue('');
        });
        comboTemplates.store.on('load',function(store) {
          comboTemplates.setValue('');
        });
        comboAssignUser.store.on('load',function(store) {
          comboAssignUser.setValue('');
        });
        comboTasStart.store.on('load',function(store) {
          comboTasStart.setValue('');
        });
        comboTrigger.store.on('load',function(store) {
          comboTrigger.setValue('');
        });
        comboNameObject.store.on('load',function(store) {
          comboNameObject.setValue('');
        });
      },
      failure: function(obj, resp){
        Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
      }
    });
  },this);

  comboAssignUser.on('select',function(cmb,record,index){
    storeNameObject.load({
      params:{
        OBJECT_VALUE:record.get('VALUE'),
        PRO_UID:PRO_UID
      }
    });
    if(record.get('LABEL') != '-- None --')
    {
      comboNameObject.enable();
      comboNameObject.clearValue();
      comboNameObject.allowBlank = false;
      comboNameObject.show();
      comboNameObject.container.up('div.x-form-item').setStyle('display', 'block');
    }
    else
    {
      comboNameObject.allowBlank = true;
      comboNameObject.disable();
      comboNameObject.hide();
      comboNameObject.container.up('div.x-form-item').setStyle('display', 'none');
    }
    comboNameObject.store.on('load',function(store) {
      comboNameObject.setValue('');
    });
  },this);

  var general = {
    xtype: 'fieldset',
    defaultType: 'textfield',
    title: 'General',
    items:
    [
      {
        xtype:'hidden',
        name:'ER_UID',
        id:'ER_UID'
      },
      theTitle,
      comboResources,
      comboTemplates,
      comboDynaForm
    ]
  };

  var actionsAfterRegister = {
    xtype: 'fieldset',
    defaultType: 'textfield',
    title: 'Actions After Registrer',
    items:
    [
      comboAssignUser,
      comboNameObject,
      comboTasStart,
      comboTrigger
    ]
  };

  formGeneral = new Ext.FormPanel({
    height: 350,
    title:'',
    labelWidth:200,
    defaults:{xtype:'textfield'},
    bodyStyle:'padding: 10px',
    items:[
      general,
      actionsAfterRegister
    ]
  });

  win = new Ext.Window({
    id: 'win',
    title: '',
    width: 750,
    floatable: true,
    autoHeight:true,
    modal: true,
    resizable: false,
    closeAction : 'hide',
    items: [formGeneral],
    plain: true,
    buttons: [{
      id: 'btnSave',
      text: 'Save',
      handler : function() {
        var erros = 0;
        if(theTitle.getValue().trim() == '')
        {
          erros++;
          Ext.MessageBox.show({
            title: 'Error',
            msg: 'Must enter a title.',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
          });
        }
        else if (comboResources.getValue() == '')
        {
          erros++;
          Ext.MessageBox.show({
            title: 'Error',
            msg: 'You must select a process.',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
          });
        }
        else if (comboTemplates.getValue()== '')
        {
          erros++;
          Ext.MessageBox.show({
            title: 'Error',
            msg: 'You must select a Template.',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
          });
        }
        else if (comboAssignUser.getValue() != '')
        {
          if (comboNameObject.getValue() == '')
          {
            erros++;
            Ext.MessageBox.show({
              title: 'Error',
              msg: 'You must select.',
              buttons: Ext.MessageBox.OK,
              icon: Ext.MessageBox.ERROR
            });
          }
        }
        if(erros == 0)
        {
          formGeneral.getForm().submit({
            url : '../externalRegistration/externalRegistrationAjax',
            params : {action:'saveExternalRegistration'},
            waitMsg : 'Saving data, please wait...',
            failure: function (form, action) {
              Ext.MessageBox.show({
                title: 'Error',
                msg: 'Error saving the data. Please try again later.',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
              });
              formGeneral.getForm().reset();
            },
            success: function (form, request) {
              Ext.MessageBox.show({
                title: 'Success',
                msg: 'Data saved correctly.',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.INFO
              }
              );
              formGeneral.getForm().reset();
              store.reload({params:{ start : 0 , limit : 25 }});
            }
          });
          win.hide();
          }
      }
    },
    {
    id: 'btn',
    text: 'Cancel',
    handler: function() {
      formGeneral.getForm().reset();
      win.hide();
    }
    }]
  });

  function onMessageContextMenu(grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    messageContextMenu.showAt([coords[0], coords[1]]);
  }

  var viewport = new Ext.Viewport({
    layout: 'border',
    autoScroll: true,
    items: [
      externalRegistrationGrid
    ]
  });

});

function openLog(){
  var rows = externalRegistrationGrid.getSelectionModel().getSelections();
  var ids = '';
  for(i=0; i<rows.length; i++) {
    if(i != 0 ) ids += ',';
    ids += rows[i].get('ER_TITLE');
    ER_UID =rows[i].get('ER_UID');

  }
  storeViewLog.setBaseParam( 'ER_UID', ER_UID );
  storeViewLog.load({params:{ start : 0 , limit : 25 }});
  winLog = new Ext.Window({
    id: 'winLog',
    title: ids,
    width: 800,
    resizable: false,
    floatable: true,
    autoHeight:true,
    closeAction : 'hide',
    modal: true,
    items: viewLogGrid,
    plain: true,
    buttons: [{
      id: 'btnLogClose',
      text: 'Close',
      handler: function() {
        winLog.hide();
      }
    }]
  });
  winLog.show();
  winLog.center();
}

function openViewLog(){
  var rows = viewLogGrid.getSelectionModel().getSelections();
  var ids = '';
  var ER_REQ_UID = '';
  for(i=0; i<rows.length; i++) {
    if(i != 0 ) ids += ',';
    ids += rows[i].get('FULL_NAME');
    ER_REQ_UID = rows[i].get('ER_REQ_UID');
  }
  winViewLog = new Ext.Window({
    id: 'winViewLog',
    title: ids,
    width: 850,
    height:400,
    modal: true,
    resizable: false,
    closeAction : 'hide',
    //autoScroll: true,
    autoLoad : {
      url : '../externalRegistration/externalRegistrationAjax',
      params : {
        action:'viewRequestForm',
        ER_REQ_UID: ER_REQ_UID
      },
      scripts: true
    },
    buttons: [{
      id: 'btnwinViewLog',
      text: 'Close',
      handler: function() {
        winViewLog.hide();
      }
    }]
  });
  winViewLog.show();
  winViewLog.center();
}

function NewExternalRegistration(){
  ER_UID = ''
  ER_TITLE = '';
  theTitle.setValue('');
  PRO_UID= '';
  DYN_UID= '';
  ER_TEMPLATE='';
  ER_ACTION_ASSIGN= '';
  ER_OBJECT_UID= '';
  TAS_UID= '';
  TRI_UID= '';
  Ext.getCmp('ER_UID').setValue('');
  comboResources.enable();
  comboResources.clearValue();
  comboDynaForm.disable();
  comboDynaForm.clearValue();
  comboTemplates.disable();
  comboTemplates.clearValue();
  comboAssignUser.disable();
  comboAssignUser.clearValue();
  comboNameObject.disable();
  comboNameObject.clearValue();
  comboTasStart.disable();
  comboTasStart.clearValue();
  comboTrigger.disable();
  comboTrigger.clearValue();

  win.setTitle('New External Registration Form');
  win.show();
  win.center();

  comboNameObject.hide();
  comboNameObject.container.up('div.x-form-item').setStyle('display', 'none');
}

function editForm(){
  formGeneral.getForm().reset();
  var rows = externalRegistrationGrid.getSelectionModel().getSelections();
  for(i=0; i<rows.length; i++) {
    formGeneral.getForm().setValues({
      ER_UID:rows[i].get('ER_UID'),
      ER_TITLE: rows[i].get('ER_TITLE')
    });
    PRO_UID = rows[i].get('PRO_UID');
    DYN_UID = rows[i].get('DYN_UID');
    ER_TEMPLATE = rows[i].get('ER_TEMPLATE');
    ER_ACTION_ASSIGN = rows[i].get('ER_ACTION_ASSIGN');
    ER_OBJECT_UID = rows[i].get('ER_OBJECT_UID');
    TAS_UID = rows[i].get('TAS_UID');
    TRI_UID = rows[i].get('TRI_UID');

    storeResources.load();
    comboResources.store.on('load',function(store) {
      comboResources.setValue(PRO_UID);
    });
    Ext.Ajax.request({
      url: '../externalRegistration/externalRegistrationAjax',
      params: {
        action   : 'loadResources',
        PRO_UID:PRO_UID
      },
      success: function(resp){
        dataResult = eval('('+resp.responseText+')');
        Ext.MessageBox.hide();
         //console.dir(dataResult.dynaforms['0']);
        if (dataResult.success) {
          comboDynaForm.enable();
          comboDynaForm.clearValue();
          var dataComplete = [];
          for ( var i = 0, c = dataResult.dynaforms.length; i < c; i++ ) {
              dataComplete.push([dataResult.dynaforms[i].DYN_UID, dataResult.dynaforms[i].DYN_TITLE]);
          }
          storeDynaForm.loadData(dataComplete);
          comboTemplates.enable();
          comboTemplates.clearValue();
          var dataTemplates = [];
          for ( var i = 0, c = dataResult.templates.length; i < c; i++ ) {
              dataTemplates.push([dataResult.templates[i].FILE, dataResult.templates[i].NAME]);
          }
          storeTemplates.loadData(dataTemplates);
          comboAssignUser.enable();
          comboAssignUser.clearValue();
          var dataAssignUser = [];
          for ( var i = 0, c = dataResult.AssignUser.length; i < c; i++ ) {
              dataAssignUser.push([dataResult.AssignUser[i].LABEL, dataResult.AssignUser[i].VALUE]);
          }
          storeAssignUser.loadData(dataAssignUser);
          if (ER_ACTION_ASSIGN == '') {
            comboNameObject.hide();
            comboNameObject.container.up('div.x-form-item').setStyle('display', 'none');
          }
          else {
            comboNameObject.show();
            comboNameObject.container.up('div.x-form-item').setStyle('display', '');
          }
          comboTasStart.enable();
          comboTasStart.clearValue();
          var dataTasStart = [];
          for ( var i = 0, c = dataResult.TasStart.length; i < c; i++ ) {
              dataTasStart.push([dataResult.TasStart[i].TAS_UID, dataResult.TasStart[i].TAS_TITLE]);
          }
          storeTasStart.loadData(dataTasStart);
          comboTrigger.enable();
          comboTrigger.clearValue();
          var dataTriggers = [];
          for ( var i = 0, c = dataResult.triggers.length; i < c; i++ ) {
              dataTriggers.push([dataResult.triggers[i].TRI_UID, dataResult.triggers[i].TRI_TITLE]);
          }
          storeTrigger.loadData(dataTriggers);
        }
      },
      failure: function(obj, resp){
        Ext.Msg.alert( _('ID_ERROR'), resp.result.msg);
      }
    });

    if(ER_OBJECT_UID != '')
    {
      comboNameObject.enable();
      comboNameObject.clearValue();
      storeNameObject.load({
        params:{
            OBJECT_VALUE:ER_ACTION_ASSIGN,
            PRO_UID:PRO_UID
        }
      });
      comboNameObject.store.on('load',function(store) {
        comboNameObject.setValue(ER_OBJECT_UID);
      });
    }
    else
    {
      comboNameObject.allowBlank = true;
      comboNameObject.disable();
    }
    comboDynaForm.store.on('load',function(store) {
      comboDynaForm.setValue(DYN_UID);
    });
    comboTemplates.store.on('load',function(store) {
      comboTemplates.setValue(ER_TEMPLATE);
    });
    comboAssignUser.store.on('load',function(store) {
      comboAssignUser.setValue(ER_ACTION_ASSIGN);
    });
    comboTasStart.store.on('load',function(store) {
      if (storeTasStart.find('TAS_UID', TAS_UID) == -1) {
        TAS_UID = '';
      }
      comboTasStart.setValue(TAS_UID);
    });
    comboTrigger.store.on('load',function(store) {
      if (storeTrigger.find('TRI_UID', TRI_UID) == -1) {
        TRI_UID = '';
      }
      comboTrigger.setValue(TRI_UID);
    });
  }
  win.setTitle('Edit External Registration Form');
  win.show();
  win.center();

  comboNameObject.hide();
  comboNameObject.container.up('div.x-form-item').setStyle('display', 'none');
}
function removeRecord(){
  var rows = externalRegistrationGrid.getSelectionModel().getSelections();
  var ER_UID = '';
  var ER_TITLE = '';
  for(i=0; i<rows.length; i++) {
    if(i != 0 ) ids += ',';
    ER_UID = rows[i].get('ER_UID');
    ER_TITLE = rows[i].get('ER_TITLE');
  }
  winDelete = new Ext.Window({
    id        : 'winDelete',
    title     : 'Confirm',
    width     : 300,
    floatable : true,
    resizable : false,
    modal     : true,
    closeAction : 'hide',
    icon      : '/images/ext/default/window/icon-question.gif',
    html      :'<table><tr><td width="30%" align="center"><img src="/images/ext/default/window/icon-question.gif" /></td><td><span class="ext-mb-text">Do you want to delete the external registration "'+ ER_TITLE +'" form?</span></td></tr></table>',
    plain     : true,
    buttons: [
    {
      id: 'btnwinDelete',
      text: 'Delete',
      handler : function() {
        Ext.Ajax.request({
          url     : '../externalRegistration/externalRegistrationAjax',
          params  : {action:'deleteExternalRegistration' , ER_UID:ER_UID},
          waitMsg : 'deleting data...',
          failure : function (form, action) {
           Ext.MessageBox.show({
              title   : 'Error',
              msg     : 'Error could not completely remove the data please try later..',
              buttons : Ext.MessageBox.OK,
              icon    : Ext.MessageBox.ERROR
           });
          },
          success: function (form, request) {
            Ext.MessageBox.show({
              title   : 'Success',
              msg     : 'Deleted data.',
              buttons : Ext.MessageBox.OK,
              icon    : Ext.MessageBox.INFO
            });
            store.reload({params:{ start : 0 , limit : 25 }});
            winDelete.hide();
          }
        });
      }
    },
    {
      id      : 'btnwinCancel',
      text    : 'Cancel',
      handler : function() {
        winDelete.hide();
      }
    }]
  });
  winDelete.show();
  winDelete.center();
}

function openViewUid(){
  var rows = externalRegistrationGrid.getSelectionModel().getSelections();
  var ER_UID = '';
  var ids = '';
  for(i=0; i<rows.length; i++) {
    ER_UID += rows[i].get('ER_UID');
    ids = rows[i].get('ER_TITLE');
  }
  Ext.Msg.show({
      title   : ids,
      msg     : 'UID: ' + ER_UID,
      buttons : Ext.Msg.INFO,
      fn      : function(){},
      animEl  : 'elId',
      icon    : Ext.MessageBox.INFO,
      buttons : Ext.MessageBox.OK
    });
}