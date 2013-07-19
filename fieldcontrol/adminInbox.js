
//Keyboard Events
new Ext.KeyMap(document, [
    {
        key : Ext.EventObject.F5,
        fn  : function(keycode, e) {
        if (! e.ctrlKey) {
            if (Ext.isIE) {
                // IE6 doesn't allow cancellation of the F5 key, so trick it into
                // thinking some other key was pressed (backspace in this case)
                e.browserEvent.keyCode = 8;
            }
            e.stopEvent();
            document.location = document.location;
            } else {
                Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
        }
    }
},
{
    key: Ext.EventObject.DELETE,
    fn: function(k,e){
        iGrid = Ext.getCmp('infoGrid');
        rowSelected = iGrid.getSelectionModel().getSelected();
    }
}
]);

var store;
var cmodel;
var infoGrid;
var viewport;
var smodel;
var newButton;
var editButton;
var deleteButton;
var groupsButton;
var contextMenu;
var user_admin = '00000000000000000000000000000001';
var pageSize;
var dateFormat;
var comboAuthSources;

Ext.onReady(function(){
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

    newButton = new Ext.Action({
        text      : _('ID_NEW'),
        iconCls   : 'button_menu_ext ss_sprite  ss_add',
        handler   : NewInboxAction
    });
    
    editButton = new Ext.Action({
        text     : _('ID_EDIT'),
        iconCls  : 'button_menu_ext ss_sprite  ss_pencil',
        handler  : EditInboxWindow,
        disabled : true
    });

    deleteButton = new Ext.Action({
        text     : _('ID_DELETE'),
        iconCls  : 'button_menu_ext ss_sprite  ss_delete',
        handler  : DeleteInboxAction,
        disabled : true
    });

    searchText = new Ext.form.TextField ({
        id         : 'searchTxt',
        ctCls      :'pm_search_text_field',
        allowBlank : true,
        width      : 100,
        emptyText  : _('ID_ENTER_SEARCH_TERM'),
        listeners  : {
            specialkey: function(f,e){
                if (e.getKey() == e.ENTER) {
                    DoSearch();
                }
            },
            focus: function(f,e) {
                var row = infoGrid.getSelectionModel().getSelected();
                infoGrid.getSelectionModel().deselectRow(infoGrid.getStore().indexOf(row));
            }
        }
    });

    smodel = new Ext.grid.RowSelectionModel({
        singleSelect : true,
        listeners    :{
            rowselect: function(sm){
    			editButton.enable();
                deleteButton.enable();
            },
            rowdeselect: function(sm){
            	editButton.disable();
                deleteButton.disable();
            }
        }
    });
    var stepsFields = Ext.data.Record.create([{
        name : 'ID',
        type : 'integer'
    },{
        name : 'INBOX',
        type : 'string'
    },{
        name : 'DESCRIPTION',
        type : 'string'
    }]);

    store = new Ext.data.GroupingStore( {
    remoteSort  : true,
    sortInfo    : stepsFields,
    groupField  :'',
    proxy       : new Ext.data.HttpProxy({
        url       : 'admininbox_ajax?inbox=listinbox'
    }),
    reader        : new Ext.data.JsonReader( {
        root          : 'data',
        totalProperty : 'root',
        fields        : [
            {name : 'ID'},
            {name : 'INBOX'},
            {name : 'DESCRIPTION'}
        ]
    })
    });

    storePageSize = new Ext.data.SimpleStore({
        fields   : ['size'],
        data     : [['20'],['30'],['40'],['50'],['100']],
        autoLoad : true
    });

    comboPageSize = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store         : storePageSize,
    valueField    : 'size',
    displayField  : 'size',
    width         : 50,
    editable      : false,
    listeners     :{
        select: function(c,d,i){
            UpdatePageConfig(d.data['size']);
            bbarpaging.pageSize = parseInt(d.data['size']);
            bbarpaging.moveFirst();
        }
    }
    });

  comboPageSize.setValue(pageSize);

    bbarpaging = new Ext.PagingToolbar({
        store       : store, // <--grid and PagingToolbar using same store (required)
        displayInfo : true,
        autoHeight  : true,
        displayMsg  : 'Inboxes {0} - {1} Of {2}',
        emptyMsg    : 'No Inboxes to show',
        pageSize    : 20
    }); 

    cmodel = new Ext.grid.ColumnModel({
        defaults : {
            width: 50
        },
        columns : [
            {id     : 'ID', dataIndex: 'ID', hidden: true, hideable: false},
            {header : 'Inbox Name', dataIndex: 'INBOX', width: 90, align: 'left', sortable: true, renderer: "Inbox Name"},
            {header : 'Description', dataIndex: 'DESCRIPTION', width: 175, align: 'left', renderer: "Description"}
        ]
    });

    infoGrid = new Ext.grid.GridPanel({
        region             : 'center',
        layout             : 'fit',
        id                 : 'infoGrid',
        height             : 100,
        autoWidth          : true,
        stateful           : true,
        stateId            : 'grid',
        enableColumnResize : true,
        enableHdMenu       : true,
        frame              : false,
        columnLines        : false,
        viewConfig         : {
            forceFit:true
        },
        title : "Admin Inbox",
        store : store,
        cm    : cmodel,
        sm    : smodel,
        tbar  : [newButton, '-', editButton ,'-', deleteButton],
        bbar  : bbarpaging,
        listeners : {
      		rowdblclick: EditInboxWindow
    },
        view  : new Ext.grid.GroupingView({
            forceFit     : true,
            groupTextTpl : '{text}'
        })
    });

    infoGrid.on('rowcontextmenu',
        function (grid, rowIndex, evt) {
            var sm = grid.getSelectionModel();
            sm.selectRow(rowIndex, sm.isSelected(rowIndex));
        },
        this
    );

    infoGrid.on('contextmenu',
        function (evt) {
            evt.preventDefault();
        },
        this
    );

    infoGrid.store.load();

    viewport = new Ext.Viewport({
        layout     : 'fit',
        autoScroll : false,
        items      : [infoGrid]
    });
});



//Do Nothing Function
DoNothing = function(){};

//Open Edit Group Form
EditInboxWindow = function(){  
  var rowSelected = infoGrid.getSelectionModel().getSelected();
  editForm.getForm().reset();
  editForm.getForm().findField('inboxnamefield1').setValue(rowSelected.data.INBOX);
  editForm.getForm().findField('inboxdescfield1').setValue(rowSelected.data.DESCRIPTION);
  win = new Ext.Window({
    autoHeight: true,
    width: 440,
    title: 'EDIT INBOX',
    closable: false,
    modal: true,
    autoDestroy : true,
    id: 'w',
    items: [editForm]
  });
  win.show();
  

};

//Open New User Form
NewInboxAction = function(){
    newForm.getForm().reset();
    newForm.getForm().items.items[0].focus('',500);
    wInbox = new Ext.Window({
        autoHeight : true,
        width      : 400,
        title      : "Create new inbox",
        closable   : false,
        autodestroy: true,
        modal      : true,
        id         : 'w',
        items      : [newForm]
    });
    wInbox.show();
};

//Close Popup Window
CloseWindow = function(){
    Ext.getCmp('w').hide();
};

editForm = new Ext.FormPanel({
    url   : 'admininbox_ajax?inbox=editInbox',
    frame : true,
    items :[
        {
        	 id:'inboxnamefield1', 
	         xtype: 'textfield', 
	         fieldLabel: "Inbox Name", 
	         name: 'name', 
	         width: 200, 
	         //allowBlank: false,
	         disabled: true
	    } , {
	    	id:'inboxdescfield1', 
	    	xtype: 'textarea', 
	    	fieldLabel: "Inbox Description", 
	    	name: 'name', 
	    	width: 200, 
	    	autoCreate: {tag: 'textarea', type: 'text', style: "width:300px;height:60px;", autocomplete: "off" , maxlength: '90'},
	    	maxLength: 90
	    	//allowBlank: false
	    }
    ],
    buttons: [
        {text : "Save" , handler:  saveEditInbox},
        {text : "Cancel", handler: CloseWindow}
    ]
});
newForm = new Ext.FormPanel({
    url   : 'admininbox_ajax?inbox=newinbox',
    frame : true,
    items :[
            {
            	id:'inboxnamefield', 
            	xtype: 'textfield', 
            	fieldLabel: "Inbox Name", 
            	name: 'name', 
            	width: 220,
            	maxLength: 30,
            	autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '25'},
            	//allowBlank: false,
            	enableKeyEvents: true, 
                listeners: {
                   keyup: function(val, e){
                       result = val.getValue();
                       text = Ext.util.Format.uppercase(result);
                       text =  text.replace(/(^\s*)|(\s*$)/g,""); // delete spaces in text
                       val.setValue(text);
                   },
                   keypress: function(val, e){
                       result = val.getValue();
                       text = Ext.util.Format.uppercase(result);
                       text =  text.replace(/(^\s*)|(\s*$)/g,""); // delete spaces in text
                       val.setValue(text);
                   }
                 } 
             } , {
            	id:'inboxdescfield', 
            	xtype: 'textarea',
            	fieldLabel: "Inbox Description", 
            	name: 'name', 
            	width: 220, 
            	autoCreate: {tag: 'textarea', type: 'text', style: "width:300px;height:60px;", autocomplete: "off" , maxlength: '90'},
            	//allowBlank: false,
            	maxLength: 90
            	}
    ],
    buttons: [
        {text : "Save", handler: saveNewInbox},
        {text : "Cancel", handler: CloseWindow}
    ]
});

function saveNewInbox() {
    var inbox = newForm.getForm().findField('inboxnamefield').getValue();
    var description = newForm.getForm().findField('inboxdescfield').getValue();
    if(!inbox.length)
    {
    	Ext.Msg.alert(_('ID_ERROR'),'Please Enter a Name Inbox');
    	return 0;
    }	
    if(!description.length)
    {
    	Ext.Msg.alert(_('ID_ERROR'),'Please Enter a Description Inbox');
    	return 0;
    }
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
        url    : 'admininbox_ajax.php',
        method : 'POST',
        params : {
            action : 'saveNewInbox',
            inbox  : inbox,
            desc   : description
        },
        success: function(xhr,params) {
        	
        	if (xhr.responseText == 'true'){
        		message = '';
        		DoSearch();
                deleteButton.disable(); //Disable Delete Button
                editButton.disable();
                PMExt.notify("New Inbox","The inbox was created succesfully!");
                viewport.getEl().unmask();
                CloseWindow();
			}
			else{
				message = xhr.responseText;
				viewport.getEl().unmask();
				newForm.getForm().findField('inboxnamefield').setValue("");
				Ext.Msg.alert(
						_('ID_ERROR'),
						'Already exists with that code inbox : '+inbox);
			}
        },
        failfure: function(xhr,params) {
        	alert('Failure!\n'+xhr.responseText);
            viewport.getEl().unmask();
        }
    });
}

 function saveEditInbox() {
    viewport.getEl().mask(_('ID_PROCESSING'));
    Ext.Ajax.request({
        url    : 'admininbox_ajax.php',
        method : 'POST',
        params : {
            action : 'saveEditInbox',
            inbox  :editForm.getForm().findField('inboxnamefield1').getValue(),
            desc   :editForm.getForm().findField('inboxdescfield1').getValue()
        },
        success: function(xhr,params) {
            DoSearch();
            deleteButton.disable(); //Disable Delete Button
            editButton.disable();
            PMExt.notify("Edit Inbox","The inbox was updated succesfully!");
            viewport.getEl().unmask();
            CloseWindow();
            editForm.getForm().reset(); //Set empty form to next use
            newForm.getForm().reset(); //Set empty form to next use
        },
        failfure: function(xhr,params) {
            alert('Failure!\n'+xhr.responseText);
            viewport.getEl().unmask();
        }
    });
};

//Delete User Action
DeleteInboxAction = function(){
    Ext.Msg.confirm(_('ID_CONFIRM'), "Do you want to remove this inbox?",
    function(btn, text){
        if (btn=="yes"){
        rowSelected = infoGrid.getSelectionModel().getSelected();
        viewport.getEl().mask(_('ID_PROCESSING'));
        Ext.Ajax.request({
            url     : 'admininbox_ajax.php',
            params  : {action: 'deleteinbox', ID: rowSelected.data.ID , ID_INBOX: rowSelected.data.INBOX},
            success : function(r,o){
                viewport.getEl().unmask();
                DoSearch();
                deleteButton.disable(); //Disable Delete Button
                editButton.disable();
                PMExt.notify("Delete Inbox", "The inbox was removed succesfully!");
            },
                failure : function(){
                viewport.getEl().unmask();
            }
        });

        }
        }
    );
};

//Do Search Function
DoSearch = function(){
  infoGrid.store.load({params: {textFilter: searchText.getValue()}});
};

//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
    Ext.Ajax.request({
        url    : 'admininbox_ajax',
        params : {'function':'listinbox', size: pageSize}
    });
};
