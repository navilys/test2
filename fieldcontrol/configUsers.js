var editor;
var fieldNameData;

Ext.onReady(function() 
{
	var tooltipRenderer = function(data, metadata, record, rowIndex, columnIndex,store) 
	{
		metadata.attr = 'ext:qtip="' + data + '" style="white-space: normal; "';
		return data;
	};
	
	function redirect(href) {
	        parent.location.href = href;
	}
	
	function addTooltip(value, metadata, record, rowIndex, colIndex, store){
	    metadata.attr = 'ext:qtip="' + value + '"';
	    return value;
	}
	
	ConfigListUserPage = function(value){
		//location.href = 'configListUsers?uUID=' + value + '&type=auth';
		var win = new Ext.Window({
			//iconCls:'boton-nuevo-exportador',
			closable: true,
			maximizable: true,
			title: 'Config list users',			
			bodyStyle:'padding:8px',
			width:900,
			height: 480,
			
			loadMask : true,
			items : [{
		        xtype : "component",
		        id    : 'iframe-win1',  // Add id	
		        loadMask : true,
		        autoEl : {
		            tag : "iframe",
		            frameborder : '0',
			        width: '100%',
			        height: '100%',				        
		            loadMask : true			            			            
		        }
		    }],
		    buttons:[{
				iconCls:'boton-guardar',
				text:'Close Panel',
				handler: function(){
					win.destroy();
				}
			}]
		});
		
		win.show();		
		
		Ext.getDom('iframe-win1').src = '../fieldcontrol/configListUsers?uUID=' + value + '&type=auth';	
	};


	var ConfigUsers_store = new Ext.data.JsonStore({
	        url : 'ajaxConfigUsers.php',
	        root : 'data',
	        totalProperty : 'total',
	        autoWidth : true,
	         fields : [ 'CONFIG_USERS_ID', 'FIELD_NAME', 'DESCRIPTION','TYPE','PARAMETERS','TYPE_ACTION','STATUS']
	    });
	ConfigUsers_store.load();  
		
	var ConfigUsers_cm = new Ext.grid.ColumnModel([
		       {
		            header: "Name",
		            dataIndex: 'FIELD_NAME'
				} , {
		            header: "Description",
		            dataIndex: 'DESCRIPTION'
				} , { 
					header: "Type",
					dataIndex: 'TYPE'
				} , {
					header: "Type Action",
					dataIndex: 'TYPE_ACTION'
				} , {
					header: "Parameters",
					dataIndex: 'PARAMETERS'
				} ,
				{
					header: "Status",
					dataIndex: 'STATUS'
				} 
			]);
	ConfigUsers_cm.defaultSortable= true;	
		
	var ConfigUsers_grid = new Ext.grid.GridPanel({
				store			: ConfigUsers_store,
				cm				: ConfigUsers_cm,
				stripeRows		: true,
				autoScroll		: true,
				id			 	:'ConfigUsers_grid',
				ddGroup		   	:'gridDDactions',
				enableDragDrop 	: true, 
				viewConfig 		: {
		          forceFit 		: true,
		          scrollOffset 	: 0,
		          emptyText		: 'There are no actions to display'
		       },
				bbar			: new Ext.PagingToolbar({
			          pageSize: 50,
			          store: ConfigUsers_store,
			          displayInfo: true,
			          displayMsg: 'Displaying {0} - {1} of {2}',
			          emptyMsg: "No actions to display"
				}),
				tbar 			: [{
							text: 'Add ',
							cls : 'x-btn-text-icon',
							icon : '/images/ext/default/tree/drop-add.gif',
							handler: function(){
								add_ConfigUsers_popup();
							}
						}, {
							text	: 'Edit ',
							cls 	: 'x-btn-text-icon',
							icon 	: '/images/edit-table.png',
							id		: 'editConfigUsers',
							disabled: true,
							handler	: function() {
								edit_ConfigUsers_popup(ConfigUsers_grid);
							}		
						} , {
							text	: 'Remove ',
							cls 	: 'x-btn-text-icon',
							icon 	: '/images/delete-16x16.gif',
							id		: 'removeConfigUsers',
							disabled: true,
							handler	: function(){
								remove_ConfigUsers_popup(ConfigUsers_grid);
							}
						} , {
							text: 'Save Config ',
							cls : 'x-btn-text-icon',
							icon : '/images/ok.png',
							tooltip  : 'Add drag and drop',
							handler: function(){
								saveConfigUsers_DragAndDrop(ConfigUsers_store);
							}
						} , {
							text:('Config List Users'),
							  iconCls :'button_menu_ext ss_configListUsers',
							  handler: ConfigListUserPage,
							  disabled: false 
						}],
				listeners      : {  //drag and drop
					"render": {
				  		scope: this,
				  		fn: function(grid) {
							var ddrow = new Ext.dd.DropTarget(grid.container, {
								ddGroup : 'gridDDactions',
								copy:false,
								notifyDrop : function(dd, e, data){
									var ds = grid.store;
									var sm = ConfigUsers_grid.getSelectionModel();
				                    var rows = sm.getSelections();
				                    if(dd.getDragData(e)) {
				                    	var cindex=dd.getDragData(e).rowIndex;
				                        if(typeof(cindex) != "undefined") {
				                        	for(i = 0; i <  rows.length; i++) {
				                        		ds.remove(ds.getById(rows[i].id));
				                            }
				                            ds.insert(cindex,data.selections);
				                            sm.clearSelections();
				                         }
				                    }
								}
				         }) 
						//store.load();
				      }
				   }
				},
				sm: new Ext.grid.RowSelectionModel({
				      selectSingle: false,
				      listeners:{
				        selectionchange: function(sm){
							buttonEditAction = Ext.getCmp('editConfigUsers');
							buttonEditAction.setDisabled(false);
							buttonEditRemove = Ext.getCmp('removeConfigUsers');
							buttonEditRemove.setDisabled(false);
				        }
				      }
				})
		});	
	
	// back users
	BackToUsers = function(){
		  location.href = 'users_List';
	};
	
	backButton = new Ext.Action({
		text : _('ID_BACK'),
		iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
		handler: BackToUsers
	});
	  
 

	var configUsersPanel = new Ext.Panel({
		autoWidth    : true,
		height       : 550,
		layout       : 'fit',
		autoScroll	 : true,
		items        : [
		                ConfigUsers_grid
		],
		tbar: ['',{xtype: 'tbfill'},backButton]
		
	});
	
	  //TABS PANEL
	
	var viewport = new Ext.Viewport({
		layout : 'fit',
		items  : [configUsersPanel]
	});

	/////////////////////////////Config Users //////////////////////////////////////////////////
	
	
	var idOption =  new Ext.form.TextField ({
		allowBlank : true,
		height     : 50,
		disabled   : false,
		anchor     : '100%'
    });
    
	var description =  new Ext.form.TextField ({
		allowBlank : true,
		height     : 50,
		disabled   : false,
		anchor     : '100%'
    });
 
	Ext.apply(Ext.form.VTypes, {
	    idField : function(val,field)
	    {
			return /^(?=.*[a-zA-Z])\w{4,}$/.test(val);
		},
		idFieldText:'id incorrect',
		fieldDescription : function(val,field)
	    {
			return /^[A-Z0-9 _a-z]*$/.test(val);
		},
		fieldDescriptionText:'Description incorrect'
		
	});
		 
	function add_ConfigUsers_popup()
	{			
		  //row editor for table columns grid
		  editor = new Ext.ux.grid.RowEditor({
		    saveText: _("ID_UPDATE"),
		    listeners: {
		      canceledit: function(grid,obj){
		        if ( grid.record.data.field_label == '' && grid.record.data.field_name == '') {
		        	gridOptionsConfig_store.remove(grid.record);
		        }
		      }
		    }
		  });

		  editor.on({
		    afteredit: function(roweditor, changes, record, rowIndex) {
		      //
		    },
		    afteredit: function(roweditor, rowIndex) {
		      row = gridOptionsConfig.getSelectionModel().getSelected();
		      if (row.get('field_key') == true) {
		        row.data.field_null = false;

		      }
		      row.commit();
		    }
		  });
		  
		var gridOptionsConfig_store = new Ext.data.JsonStore({
	        url : 'SaveConfigUsers.php?method=listOptions&fieldName=' ,
	        root : 'data',
	        totalProperty : 'total',
	        autoWidth : true,
	         fields : [ 'ID_OPTION', 'DESCRIPTION']
	    });
		gridOptionsConfig_store.load();  
	    
	    var gridOptionsConfig_cm = new Ext.grid.ColumnModel([
	       {
	            header: "ID_OPTION",
	            id :"ID_OPTION",
	            dataIndex: 'ID_OPTION',
	            editor	  : idOption
		   }, {
	            header: "DESCRIPTION",
	            id :"DESCRIPTION",
	            dataIndex: 'DESCRIPTION',
	            editor	  : description
		   } 
		]);
	    gridOptionsConfig_cm.defaultSortable = true;	
		
	    var gridOptionsConfig = new Ext.grid.EditorGridPanel({
			store: gridOptionsConfig_store,
			cm:gridOptionsConfig_cm,
			plugins: [editor],
			align : 'center',
			sm: new Ext.grid.RowSelectionModel({
			      selectSingle: false,
			      listeners:{
			        selectionchange: function(sm){
			         // console.log('select');
			        }
			      }
			    }),
			stripeRows: true,
			autoScroll:true,
			width : 450,
			height : 200,
			id:'gridOptionsConfig',
			hidden : true,
			viewConfig : {
	          forceFit : true,
	          scrollOffset : 0,
	          emptyText: 'There are no options to display'
	       },
	       bbar			: new Ext.PagingToolbar({
		          pageSize: 50,
		          store: gridOptionsConfig_store,
		          displayInfo: true,
		          displayMsg: 'Displaying {0} - {1} of {2}',
		          emptyMsg: "No actions to display"
			}),
	       tbar : [{
				text: 'Add Options',
				cls : 'x-btn-text-icon',
				icon : '/images/ext/default/tree/drop-add.gif',
				handler: function(){
					addColumn(gridOptionsConfig, gridOptionsConfig_store, editor);
				}	
	       }, {
				text: 'Edit Options',
				cls : 'x-btn-text-icon',
				icon : '/images/edit-table.png',
				handler: function(){
	    	   		editColumn(gridOptionsConfig, gridOptionsConfig_store, editor);
				}	
	       }, {
				text: 'Remove Options',
				cls : 'x-btn-text-icon',
				icon : '/images/delete-16x16.gif',
				handler: function(){
	    	   		removeColumn();
				}	
	       }/*, {
				text: 'Save Options',
				cls : 'x-btn-text-icon',
				icon : '/images/ok.png',
				handler: function(){
	    	   		saveOptions(gridOptionsConfig, gridOptionsConfig_store);
				}	
	       }*/],
	       listeners: {}
		});
		
		add_ConfigUsers_popup_form = new Ext.FormPanel({
			id: 'add_ConfigUsers_popup_form',								  
			labelAlign: 'center',
			bodyStyle:'padding:5px 5px 5px 10px',
			autoScroll:true,
			items: [
				{
					xtype: 'textfield',
		            id:'idFieldName', 
		            width: 350,
					fieldLabel: 'Field Name',
					name: 'first',
					allowBlank:false,
					autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '30'},
	            	enableKeyEvents: true, 
	            	vtype : 'idField',
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
					xtype: 'textfield',
		            id:'idDescription', 
		            width: 350,
					fieldLabel: 'Description',
					name: 'first',
					vtype : 'fieldDescription',
					allowBlank:false
				} , new Ext.form.ComboBox({
                    fieldLabel: 'Status',
                    hiddenName:'Status',
                    store: new Ext.data.ArrayStore({
                    	fields: ['ID', 'NAME'],
                        data: [['ACTIVE',' ACTIVE '], 
                               ['INACTIVE',' INACTIVE ']],
                        autoLoad: true 
                    }),
                    valueField:'ID',                    
                    displayField:'NAME',
                    id:'idStatus',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText:'Select a type...',
                    selectOnFocus:true,
                    width:350
                }), new Ext.form.ComboBox({
                    fieldLabel: 'Type',
                    hiddenName:'type',
                    store: new Ext.data.ArrayStore({
                    	fields: ['ID', 'NAME'],
                        data: [['DROPDOWN',' DROPDOWN '], 
                               ['TEXTFIELD',' TEXTFIELD ']],
                        autoLoad: true 
                    }),
                    valueField:'ID',                    
                    displayField:'NAME',
                    id:'idType',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText:'Select a type...',
                    selectOnFocus:true,
                    width:350,
                    listeners:
   		    	 	{ select: { 
							fn:function(cmb,record,index)
							{
								var item = cmb.getValue();
								//console.log(item);
								if(item == 'DROPDOWN')
								{
									var visible = Ext.getCmp('idTypeAction');
									visible.show();
									visible.label.show();
									var visible = Ext.getCmp('parametersfield');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.hide();
								}
								else
								{
									var visible = Ext.getCmp('idTypeAction');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('parametersfield');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.hide();
								}
							}
						}
   		    	 	}
                }),  new Ext.form.ComboBox({
                    fieldLabel: 'Type Action',
                    hiddenName:'typeAction',
                    store: new Ext.data.ArrayStore({
                    	fields: ['ID', 'NAME'],
                        data: [['QUERY',' QUERY '], 
                               ['SELECT OPTIONS',' SELECT OPTIONS ']],
                        autoLoad: true 
                    }),
                    valueField:'ID',
                    displayField:'NAME',
                    id:'idTypeAction',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText:'Select a type action...',
                    selectOnFocus:true,
                    hidden:true,
                    width:350,
                    listeners:
   		    	 	{ select: { 
							fn:function(cmb,record,index)
							{
								var item = cmb.getValue();
								//console.log(item);
								if(item == 'QUERY')
								{
									var visible = Ext.getCmp('parametersfield');
									visible.show();
									visible.label.show();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.hide();
								}
								else
								{
									var visible = Ext.getCmp('parametersfield');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.show();
								}
							}
						}
   		    	 	}
                }), {                                                                                              
					xtype: 'textarea',
					id:'parametersfield', 
					fieldLabel: "Parameters Select", 
					name: 'parameters', 
					width: 350, 
					allowBlank: true,
					disabled: false,
					hidden: true
			   } , gridOptionsConfig
			]							
		});		
		
	    add_ConfigUsers_popup_window = new Ext.Window({
			title: 'Add Config Users',
			id:'add_ConfigUsers_popup_window',
			width: 500,
			autoHeight: true,
			autoScroll:true,
			closable:true,
			modal:true,
			constrain:true,
			plain:true,
			layout: 'form',
			items: [add_ConfigUsers_popup_form],
			buttons: [{
	            text: 'Save',
	            type: 'submit',
	            scope: this,
	            handler: function() {   					
					var fieldName = Ext.getCmp('idFieldName').getValue();
					var description = Ext.getCmp('idDescription').getValue();
					var type = Ext.getCmp('idType').getValue();
					var status = Ext.getCmp('idStatus').getValue();
					var typeAction = Ext.getCmp('idTypeAction').getValue();
					var parameterField = Ext.getCmp('parametersfield').getValue();
					if(typeAction == 'SELECT OPTIONS')
						saveOptions(gridOptionsConfig, gridOptionsConfig_store);
					if(fieldName != '' && description != '' )
					{	
						//var dataHidden = editDataFields(gridSelectConfigUsers_store);
						
						Ext.getCmp('add_ConfigUsers_popup_form').form.submit({
							method: 'POST',
							url: 'SaveConfigUsers.php?method=add',
							params : {
								fieldName		: fieldName,
								description 	: description,
								type			: type,
								status			: status,
								typeAction		: typeAction,
								parameterField 	: parameterField
	                    	},
	                    	success: function(f, a) {                                                
	                    		var data = Ext.decode(a.response.responseText);                        
	                    		if(data.success == true){ 
	                    			Ext.MessageBox.show({                            
	                    				msg : 'The data was saved sucessfully!',
	                    				buttons : Ext.MessageBox.OK,
	                    				icon : Ext.MessageBox.INFO
	                    			});
	                    			Ext.getCmp('add_ConfigUsers_popup_window').close();
	                    			Ext.getCmp('ConfigUsers_grid').getStore().reload();
	                          
	                    		}                        
	                    	},            
	                    	failure: function(f, a) { 
	                    		f.markInvalid(a.result.errors);
	                    	}            
						})
					}
					else
						alert("Register parameters select");
	            }
	        },{
	            text: 'Cancel',            
	            handler: function (){                
	                Ext.getCmp('add_ConfigUsers_popup_window').close();
	            }
	        }]
			});	
			
			add_ConfigUsers_popup_window.show();
			add_ConfigUsers_popup_window.toFront();
			
	}

     
     // edit Actions repeat
     function edit_ConfigUsers_popup(ConfigUsers_grid)
     {
    	 editor = new Ext.ux.grid.RowEditor({
 		    saveText: _("ID_UPDATE"),
 		    listeners: {
 		      canceledit: function(grid,obj){
 		        if ( grid.record.data.field_label == '' && grid.record.data.field_name == '') {
 		        	gridOptionsConfig_store.remove(grid.record);
 		        }
 		      }
 		    }
 		  });

 		  editor.on({
 		    afteredit: function(roweditor, changes, record, rowIndex) {
 		      //
 		    },
 		    afteredit: function(roweditor, rowIndex) {
 		      row = gridOptionsConfig.getSelectionModel().getSelected();
 		      if (row.get('field_key') == true) {
 		        row.data.field_null = false;

 		      }
 		      row.commit();
 		    }
 		  });
 		  
    	 var gridConfigUsers = Ext.getCmp('ConfigUsers_grid');
		 var rowSelected = gridConfigUsers.getSelectionModel().getSelected();
		 fieldNameData = rowSelected.data.FIELD_NAME;
    	
		 var gridOptionsConfig_store = new Ext.data.JsonStore({
		        url : 'SaveConfigUsers.php?method=listOptions&fieldName=' + fieldNameData,
		        root : 'data',
		        totalProperty : 'total',
		        autoWidth : true,
		         fields : [ 'ID_OPTION', 'DESCRIPTION']
		    });
			gridOptionsConfig_store.load();  
		    
		    var gridOptionsConfig_cm = new Ext.grid.ColumnModel([
		       {
		            header: "ID_OPTION",
		            id :"ID_OPTION",
		            dataIndex: 'ID_OPTION',
		            editor	  : idOption
			   }, {
		            header: "DESCRIPTION",
		            id :"DESCRIPTION",
		            dataIndex: 'DESCRIPTION',
		            editor	  : description
			   } 
			]);
		    gridOptionsConfig_cm.defaultSortable = true;	
			
		    var gridOptionsConfig = new Ext.grid.EditorGridPanel({
				store: gridOptionsConfig_store,
				cm:gridOptionsConfig_cm,
				plugins: [editor],
				align : 'center',
				sm: new Ext.grid.RowSelectionModel({
				      selectSingle: false,
				      listeners:{
				        selectionchange: function(sm){
				          //console.log('select');
				        }
				      }
				    }),
				stripeRows: true,
				autoScroll:true,
				width : 450,
				height : 200,
				id:'gridOptionsConfig',
				hidden : true,
				viewConfig : {
		          forceFit : true,
		          scrollOffset : 0,
		          emptyText: 'There are no options to display'
		       },
		       bbar			: new Ext.PagingToolbar({
			          pageSize: 50,
			          store: gridOptionsConfig_store,
			          displayInfo: true,
			          displayMsg: 'Displaying {0} - {1} of {2}',
			          emptyMsg: "No actions to display"
				}),
		       tbar : [{
					text: 'Add Options',
					cls : 'x-btn-text-icon',
					icon : '/images/ext/default/tree/drop-add.gif',
					handler: function(){
						addColumn(gridOptionsConfig, gridOptionsConfig_store, editor);
					}	
		       }, {
					text: 'Edit Options',
					cls : 'x-btn-text-icon',
					icon : '/images/edit-table.png',
					handler: function(){
		    	   		editColumn(gridOptionsConfig, gridOptionsConfig_store, editor);
					}	
		       }, {
					text: 'Remove Options',
					cls : 'x-btn-text-icon',
					icon : '/images/delete-16x16.gif',
					handler: function(){
		    	   		removeColumn();
					}	
		       }/*, {
					text: 'Save Options',
					cls : 'x-btn-text-icon',
					icon : '/images/ok.png',
					handler: function(){
		    	   		saveOptions(gridOptionsConfig, gridOptionsConfig_store);
					}	
		       }*/],
		       listeners: {}
			});
		    
    	 edit_ConfigUsers_popup_form = new Ext.FormPanel({
    		 id: 'edit_ConfigUsers_popup_form',								  
    		 labelAlign: 'center',
    		 bodyStyle:'padding:5px 5px 5px 10px',
    		 autoScroll:true,
    		 items: [
    		     {
    		    	 xtype: 'textfield',
    		    	 id:'idFieldName', 
    		    	 width: 350,
    		    	 fieldLabel: 'Field Name',
    		    	 name: 'first',
    		    	 allowBlank:false,
    		    	 vtype : 'idField',
    		    	 disabled: true,
    		    	 autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '30'},
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
    		    	 xtype: 'textfield',
    		    	 id:'idDescription', 
    		    	 width: 350,
    		    	 fieldLabel: 'Description',
    		    	 name: 'first',
    		    	 vtype : 'fieldDescription',
    		    	 allowBlank:false
    		     } , new Ext.form.ComboBox({
                     fieldLabel: 'Status',
                     hiddenName:'Status',
                     store: new Ext.data.ArrayStore({
                     	fields: ['ID', 'NAME'],
                         data: [['ACTIVE',' ACTIVE '], 
                                ['INACTIVE',' INACTIVE ']],
                         autoLoad: true 
                     }),
                     valueField:'ID',                    
                     displayField:'NAME',
                     id:'idStatus',
                     typeAhead: true,
                     mode: 'local',
                     triggerAction: 'all',
                     emptyText:'Select a type...',
                     selectOnFocus:true,
                     width:350
                 }), new Ext.form.ComboBox({
    		    	 fieldLabel: 'Type',
    		    	 hiddenName:'type',
    		    	 store: new Ext.data.ArrayStore({
    		    		 fields: ['ID', 'NAME'],
    		    		 data: [['DROPDOWN',' DROPDOWN '], 
    		    		        ['TEXTFIELD',' TEXTFIELD ']],
    		    		 autoLoad: true 
    		    	 }),
    		    	 valueField:'ID',                    
    		    	 displayField:'NAME',
    		    	 id:'idType',
    		    	 typeAhead: true,
    		    	 mode: 'local',
    		    	 triggerAction: 'all',
    		    	 emptyText:'Select a type...',
    		    	 selectOnFocus:true,
    		    	 width:350,
    		    	 listeners:
    		    	 { select: { 
 							fn:function(cmb,record,index)
 							{
 								var item = cmb.getValue();
 								//console.log(item);
 								if(item == 'DROPDOWN')
 								{
 									var visible = Ext.getCmp('idTypeAction');
 									visible.show();
 									visible.label.show();
 									var visible = Ext.getCmp('parametersfield');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.hide();
 								}
 								else
 								{
 									var visible = Ext.getCmp('idTypeAction');
 									visible.hide();
 									visible.label.hide();
 									var visible = Ext.getCmp('parametersfield');
									visible.hide();
									visible.label.hide();
									var visible = Ext.getCmp('gridOptionsConfig');
									visible.hide();
 								}
 							}
 						}
    		    	 }
    		     }),  new Ext.form.ComboBox({
    		    	 fieldLabel: 'Type Action',
    		    	 hiddenName:'typeAction',
    		    	 store: new Ext.data.ArrayStore({
    		    		 fields: ['ID', 'NAME'],
    		    		 data: [['QUERY',' QUERY '], 
    		    		        ['SELECT OPTIONS',' SELECT OPTIONS ']],
    		    		 autoLoad: true 
    		    	 }),
    		    	 valueField:'ID',
    		    	 displayField:'NAME',
    		    	 id:'idTypeAction',
    		    	 typeAhead: true,
    		    	 mode: 'local',
    		    	 triggerAction: 'all',
    		    	 emptyText:'Select a type action...',
    		    	 selectOnFocus:true,
    		    	 width:350,
    		    	 listeners:
    		    	 { select: { 
 							fn:function(cmb,record,index)
 							{
 								var item = cmb.getValue();
 								//console.log(item);
 								if(item == 'QUERY')
 								{
 									var visible = Ext.getCmp('parametersfield');
 									visible.show();
 									visible.label.show();
 									var visible = Ext.getCmp('gridOptionsConfig');
 									visible.hide();
 								}
 								else
 								{
 									var visible = Ext.getCmp('parametersfield');
 									visible.hide();
 									visible.label.hide();
 									var visible = Ext.getCmp('gridOptionsConfig');
 									visible.show();
 								}
 							}
 						}
    		    	 }
    		     }), {                                                                                              
    		    	 xtype: 'textarea',
    		    	 id:'parametersfield', 
    		    	 fieldLabel: "Parameters Select", 
    		    	 name: 'parameters', 
    		    	 width: 350, 
    		    	 allowBlank: true,
    		    	 disabled: false,
    		    	 hidden: false
    		     },   gridOptionsConfig
    		     ]							
    	 });		
    	 		
    	 edit_ConfigUsers_popup_window = new Ext.Window({
    		 title: 'Add Config Users',
    		 id:'edit_ConfigUsers_popup_window',
    		 width: 500,
    		 autoHeight: true,
    		 autoScroll:true,
    		 closable:true,
    		 modal:true,
    		 constrain:true,
    		 plain:true,
    		 layout: 'form',
    		 items: [edit_ConfigUsers_popup_form],
    		 buttons: [{
    			 text: 'Save',
    			 type: 'submit',
    			 scope: this,
    			 handler: function() {   
    			 	var gridConfigUsers = Ext.getCmp('ConfigUsers_grid');
    			 	var rowSelected = gridConfigUsers.getSelectionModel().getSelected();
    			 	var idConfigUsers = rowSelected.data.CONFIG_USERS_ID;
    			 	var fieldName = Ext.getCmp('idFieldName').getValue();
    			 	var description = Ext.getCmp('idDescription').getValue();
    			 	var type = Ext.getCmp('idType').getValue();
    			 	var status = Ext.getCmp('idStatus').getValue();
    			 	var typeAction = Ext.getCmp('idTypeAction').getValue();
    			 	var parameterField = Ext.getCmp('parametersfield').getValue();
    			 	if(typeAction == 'SELECT OPTIONS')
						saveOptions(gridOptionsConfig, gridOptionsConfig_store);
    			 	if(fieldName != '' && description != '' )
    			 	{	
    	 				Ext.getCmp('edit_ConfigUsers_popup_form').form.submit({
    	 					method: 'POST',
    	 					url: 'SaveConfigUsers.php?method=edit',
    	 					params : {
    	 						fieldName		: fieldName,
    	 						description 	: description,
    	 						type			: type,
    	 						typeAction		: typeAction,
    	 						parameterField 	: parameterField,
    	 						idConfigUsers	: idConfigUsers,
    	 						status			: status
    	 					},
    	 					success: function(f, a) {                                                
    	 						var data = Ext.decode(a.response.responseText);                        
    	 						if(data.success == true){ 
    	 							Ext.MessageBox.show({                            
    	 								msg : 'The data was saved sucessfully!',
    	 								buttons : Ext.MessageBox.OK,
    	 								icon : Ext.MessageBox.INFO
    	 							});
    	 							Ext.getCmp('edit_ConfigUsers_popup_window').close();
    	 							Ext.getCmp('ConfigUsers_grid').getStore().reload();
    	 							
    	 						}                        
    	 					},            
    	 					failure: function(f, a) { 
    	 						f.markInvalid(a.result.errors);
    	 					}            
    	 				})
    			 	}
    			 	else
    			 		alert("Register parameters select");
    		 	}
    		 },{
    			 text: 'Cancel',            
    			 handler: function (){                
    			 	Ext.getCmp('edit_ConfigUsers_popup_window').close();
    		 	}
    		 }]
    	 });	
    	 			
    	 edit_ConfigUsers_popup_window.show();
    	 edit_ConfigUsers_popup_window.toFront();
    	 var gridConfigUsers = Ext.getCmp('ConfigUsers_grid');
    	 var rowSelected = gridConfigUsers.getSelectionModel().getSelected();
    	 Ext.getCmp('idFieldName').setValue(rowSelected.data.FIELD_NAME);
    	 Ext.getCmp('idDescription').setValue(rowSelected.data.DESCRIPTION);
    	 Ext.getCmp('idType').setValue(rowSelected.data.TYPE);
    	 Ext.getCmp('idType').setValue(rowSelected.data.TYPE);
    	 Ext.getCmp('idTypeAction').setValue(rowSelected.data.TYPE_ACTION);
    	 Ext.getCmp('idStatus').setValue(rowSelected.data.STATUS);
    	 Ext.getCmp('parametersfield').setValue(rowSelected.data.PARAMETERS);
    	 if(rowSelected.data.TYPE == 'TEXTFIELD')
    	 {
    		 var visible = Ext.getCmp('idTypeAction');
    		 visible.hide();
    		 visible.label.hide();
    		 var visible = Ext.getCmp('parametersfield');
    		 visible.hide();
    		 visible.label.hide();
    		 var visible = Ext.getCmp('gridOptionsConfig');
    		 visible.hide();
    	 }
    	 if(rowSelected.data.TYPE_ACTION == 'SELECT OPTIONS')
    	 {
    		 var visible = Ext.getCmp('parametersfield');
    		 visible.hide();
    		 visible.label.hide();
    		 var visible = Ext.getCmp('gridOptionsConfig');
    		 visible.show();
    	 }
    	 
    }

    function add_ParametersAction()
    {
    	 var casesGrid_ = Ext.getCmp('gridOptionsConfig');
    	 var rowSelected = casesGrid_.getSelectionModel().getSelected();
    	 var textParameters = "";
    	 parameters = rowSelected.data.FIELD_NAME;
    	 textParameters = Ext.getCmp('parametersfield').getValue();
    	 if(textParameters.length)
    		 textParameters = textParameters+","+parameters;
    	 else
    		 textParameters = parameters;
		  		 
		 Ext.getCmp('parametersfield').setValue(textParameters);
	}

	function remove_ConfigUsers_popup(ConfigUsers_grid)
	{
		if(ConfigUsers_grid.selModel.getCount() == 1) {
			var rowModel = ConfigUsers_grid.getSelectionModel().getSelected();
			
	  	    if (rowModel) {
	  	    	var sm = ConfigUsers_grid.getSelectionModel();
	            var sel = sm.getSelected();
	            if (sm.hasSelection()) {
	            	
	            	  Ext.Msg.show({
			                title : 'Remove Action of Inbox',
			                buttons : Ext.MessageBox.YESNOCANCEL,
			                msg : 'Remove Action of Inbox : ' + rowModel.data.NAME + ' ?',
			                fn : function(btn) {
			                  if (btn == 'yes') {
			                      var ID = rowModel.data.CONFIG_USERS_ID;
			          			
			                      Ext.Ajax.request({
			                    	  url : '../fieldcontrol/SaveConfigUsers.php?method=remove',
									  params : {
			                    	  		ID : ID
			                      		},
			                      		success: function(f, a) {                                                
					                        var data = Ext.decode(f.response.responseText);
					                        var url = data.success; 
					                          if (url == true) {
					                            Ext.MessageBox.show({                            
						                            msg : 'The data was removed sucessfully!',
						                            buttons : Ext.MessageBox.OK,
						                            icon : Ext.MessageBox.INFO
						                         });    
					                            Ext.getCmp('ConfigUsers_grid').getStore().reload();                   
					                          } else {
					                            Ext.MessageBox.alert("Error");
					                          }                       
					                    }, 
			                      		success : function(resp) {
					                          var data = Ext.decode(resp.responseText);
					                          var url = data.success; 
					                          if (url == true) {
					                            Ext.MessageBox.show({                            
						                            msg : 'The data was removed sucessfully!',
						                            buttons : Ext.MessageBox.OK,
						                            icon : Ext.MessageBox.INFO
						                         });    
					                            Ext.getCmp('ConfigUsers_grid').getStore().reload();                   
					                          } else {
					                            Ext.MessageBox.alert("Error");
					                          }
					                        }
			                        
			                      });
			                  }
			                }
			              });
	            
	            } else {
	            	Ext.MessageBox.alert('Error');
	            }
		
	  	    }else {
	  	    	Ext.MessageBox.alert('Sorry...','You must select a Vendor to Remove.');
	  	    }
		}

	}
	
	function saveConfigUsers_DragAndDrop(ConfigUsers_store)
	{
		var i  = 0;
		var arrayConfigUsers = new Array ();
		var myJSON  = '';
			
		ConfigUsers_store.each(function(record)  
		{  
			var fieldName = record.get('FIELD_NAME');
			var description = record.get('DESCRIPTION');
			var type = record.get('TYPE');
			var typeAction = record.get('TYPE_ACTION');
			var parameterField = record.get('PARAMETERS');
				
			var item = {
					"value"         	: i,
					"fieldName"			: fieldName,
					"description"		: description,
					"type"				: type,
					"typeAction" 		: typeAction,
					"parameterField" 	: parameterField
					
			};
			i++;
			arrayConfigUsers.push(item);
		});
			
		if(arrayConfigUsers.length != 0){
			myJSON= Ext.util.JSON.encode(arrayConfigUsers);
			saveDataConfigUsers(myJSON);
		}
			
	} 
	
	function saveDataConfigUsers(arrayConfigUsers)
	{  
		Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
	      
		Ext.Ajax.request({
			url: '../fieldcontrol/SaveConfigUsers.php?method=dragdrop',
	        params: {
				arrayConfigUsers : arrayConfigUsers
			},
	        success: function(r,o){
				Ext.MessageBox.hide();
				Ext.MessageBox.show({                            
					msg : 'The operation completed sucessfully!',
					buttons : Ext.MessageBox.OK,
					icon : Ext.MessageBox.INFO
                }); 
	         },
	        failure: function(){
	        	Ext.MessageBox.alert('Error','The operation was not completed sucessfully!');
	        	Ext.MessageBox.hide();
	        }
		});
		Ext.getCmp('ConfigUsers_grid').getStore().reload(); 
	       
	}
	

	function addColumn(gridOptionsConfig,gridOptionsConfig_store,editor) 
	{
	  var PMRow = gridOptionsConfig.getStore().recordType;
	  var row = new PMRow({
		  ID_OPTION : '',
	      DESCRIPTION  : ''
	  });
	  var len = gridOptionsConfig.getStore().data.length;

	  editor.stopEditing();
	  gridOptionsConfig_store.insert(len, row);
	  gridOptionsConfig.getView().refresh();
	  gridOptionsConfig.getSelectionModel().selectRow(len);
	  editor.startEditing(len);
	}
 
	function editColumn(gridOptionsConfig, gridOptionsConfig_store,editor)
	{
	  var row = Ext.getCmp('gridOptionsConfig').getSelectionModel().getSelected();
	  var selIndex = gridOptionsConfig_store.indexOfId(row.id);
	  editor.stopEditing();
	  gridOptionsConfig.getView().refresh();
	  gridOptionsConfig.getSelectionModel().selectRow(selIndex);
	  editor.startEditing(selIndex);
	}

	function removeColumn()
	{
	  PMExt.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_REMOVE_FIELD'), function(){
	    var records = Ext.getCmp('gridOptionsConfig').getSelectionModel().getSelections();
	    Ext.each(records, Ext.getCmp('gridOptionsConfig').store.remove, Ext.getCmp('gridOptionsConfig').store);
	  });
	}
			
	function saveOptions(gridOptionsConfig, gridOptionsConfig_store)
	{
		var fieldTable = '';
		var swD     = 0;
		var i       = 0;
		var miArray = new Array ();
		var myJSON  = '';
		gridOptionsConfig_store.each(function(record)  
		{  
			
				if(record.get('ID_OPTION') != '' &&  record.get('DESCRIPTION') != '')
				{					
						var idOption  = record.get('ID_OPTION');
						var description = record.get('DESCRIPTION');
						swD ++;
						var j = 0;
						var item = {
							"value"        : i,
							"idOption"     : idOption,
							"description"  : description
	    				};
						i++;
						miArray.push(item);	    			
	    		}
	    
	    });
		//var idInbox 	 = Ext.getCmp('idInboxCombo').getValue();	
		if(miArray.length != 0){
			myJSON= Ext.util.JSON.encode(miArray);
			//console.log(myJSON);
			var fieldName = Ext.getCmp('idFieldName').getValue();
			saveDataField(myJSON, fieldName, gridOptionsConfig);
			
		}
		else
		{
			alert('Add options please');
		}
		
	}
	
	function saveDataField(myArray, fieldName, gridOptionsConfig)
	{  
		
	    var sw = 0;  	 
	        Ext.MessageBox.show({
				msg          : 'Save data..',
				progressText : 'send...',
				width        : 300,
				wait         : true,
				waitConfig   : {
	                interval : 200
	            }
	        });

	        Ext.Ajax.request({
	            params : {        
	        		myArray : myArray,
	        		fieldName : fieldName
	            },
	            url : '../fieldcontrol/SaveConfigUsers.php?method=options',
	            success : function(save) {
	                var data = Ext.decode(save.responseText);
	                var url  = data.success;
	                gridOptionsConfig.getStore().commitChanges();
	                gridOptionsConfig.on({
	                    beforeload: function (store, operation, opts) {
	                        Ext.apply(operation, {
	                            params: {
	                        	fieldName: fieldName
	                            }
	                       });
	                    }
	                });
	                //gridOptionsConfig.getStore().reload();
	                Ext.MessageBox.hide();
	                
	            },
	            failure : function() {
	            	Ext.MessageBox.alert('Error', 'The operation was not completed sucessfully!');
	            	
	            }
	     });
	};
		    
});
