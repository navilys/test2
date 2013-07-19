Ext.onReady(function() 
{
	var tooltipRenderer = function(data, metadata, record, rowIndex, columnIndex,store) 
	{
		metadata.attr = 'ext:qtip="' + data + '" style="white-space: normal; "';
		return data;
	};
	
	var store = new Ext.data.JsonStore({
		url           : 'ajaxListRoles.php',
		root          : 'data',
		totalProperty : 'total', 
		remoteSort    : true,
		autoWidth     : true,
		fields        : ['ROL_UID', 'ROL_CODE', 'STATUS', 'PER_CODE']
	});
	store.load();
	 
	var grid = new Ext.grid.GridPanel({
		store : store,
		cm : new Ext.grid.ColumnModel({
			defaults : {
				width    : 20,
				sortable : true
		    },
		    columns : [
		    {
				header    : "#",
				width     : 5,
				sortable  : true,
				hidden    : true,
				dataIndex : 'ROL_UID'
		    }, {
				header    : "Rol Name",
				width     : 15,
				sortable  : true,
				dataIndex : 'ROL_CODE'
		    }, {
				header    : "Status",
				width     : 15,
				sortable  : true,
				renderer  : tooltipRenderer,
				dataIndex : 'STATUS'
		    }, {
				header    : "Field Name",
				width     : 15,
				sortable  : true,
				renderer  : tooltipRenderer,
				dataIndex : 'PER_CODE'
		    }]
		}),
		border       : false,
		loadMask     : true,
		stripeRows   : true,
		autoShow     : true,
		title        : 'Liste des Autorisations',
		autoFill     : true,
		nocache      : true,
		autoWidth    : true,
		height       : 800,
		stateful     : true,
		animCollapse : true,
		stateId      : 'grid',
		listeners    : {
			rowdblclick : OpenListPermission
		},
		viewConfig : {
			forceFit     : true,
			scrollOffset : 0
		}
	});
		 
	var viewport = new Ext.Viewport({
		layout : 'fit',
		items : [grid]
	});
	
	
	function OpenListPermission()
	{
		var rowModel = grid.getSelectionModel().getSelected();
		if(rowModel){
			var sRolID = rowModel.data.ROL_UID;
			var ROL_CODE = rowModel.data.ROL_CODE;
			var win = new Ext.Window({
				id          : 'new_options',
				closable    : true,
				maximizable : true,
				title       : 'Permissions Role: <b>' + ROL_CODE + '</b>',			
				width       :650,
				height      : 440,
				loadMask    : true,
				items       : [{
					xtype    : "component",
					id       : 'iframe-win1',  // Add id	
					loadMask : true,
					autoEl   : {
						tag         : "iframe",
						frameborder : '0',
						width       : '100%',
						height      : '100%',
						loadMask    : true			            			            
			        }
			    }],
			    buttons:[{
					iconCls :'boton-guardar',
					text    :'Close Panel',
					handler : function(){
						win.destroy();
					}
				}]
			});
			
			win.show();
			Ext.getDom('iframe-win1').src = "../convergenceList/permissionsRol.php?rolID="+sRolID;			
		}			
	}
	


});