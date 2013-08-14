var APP_UID;
var valueChbox;
var STATUS;
var MENBER_ID;
var  ArrayCoverages = [['All Coverages', 'All Coverages'],['DANDOFF', 'D&O'],['EPL', 'EPL'],['FIDUCIARY', 'Fiduciary']];

Ext.chart.Chart.CHART_URL = '/images/charts.swf';
Ext.onReady(function() {
	
	  var tooltipRenderer = function(data, metadata, record, rowIndex, columnIndex, store) {
	        metadata.attr = 'ext:qtip="' + data + '" style="text-align: justify; white-space: normal; "';
	        return data;
	      };
			//This loop loads columns 	
     
      _fields     = new Array();// Dynamic Assignation
	    _idProperty = '__index__';  
	   
	    _fields.push({name: _idProperty});

    
	   function renderIcon(val) 
	   {
		   var app_uid = val;
		   return '<img src="/images/edit-row.png">';
	    	 
	   }

      
      function redirect(href) {
        parent.location.href = href;
      }
      
      parent.swFrame = '1';    
      // list dynaforms grid    
      /*var dynaformStore = new Ext.data.JsonStore({
        url : 'listDynaforms.php?APP_UID=' + APP_UID,
        root : 'data',
        totalProperty : 'total', // <--- total de registros a paginar
        autoWidth : true,
        remoteSort: true,
        fields : ['DYN_TITLE', 'APP_UID', 'DYN_UID', 'EDIT','PRO_UID','APP_UID','TAS_UID','CURRENTDATETIME', 'TYPEFORM'
        ]
      });
      dynaformStore.load();   
      
      var pageDynaform = new Ext.PagingToolbar({
        store : dynaformStore,
        displayInfo : true,
        autoHeight : true,
        displayMsg : 'Forms {0} - {1} Of {2}',
        emptyMsg : 'No record to show',
        pageSize : 20
      });            
      
      /// --------- Filters ----------- /// 
      
      var screenheigth = (PMExt.getBrowser().screen.height-8).toString();    
      
      
      var dynaformListgrid = new Ext.grid.GridPanel({
        id : 'dynaformListgrid',
        store : dynaformStore,
        region:'center',
    	margins: '0 0 0 0',
    	border: true, 
        cm : new Ext.grid.ColumnModel({
          defaults : {
            
            sortable : true
          },
          columns:[{id: "ID", dataIndex: "APP_UID", hidden: true},
                   {header: "Dynaform", dataIndex: "DYN_TITLE", width: 500, align: "left"},
                   {header: "", width: 50, sortable: true, dataIndex: 'DYN_UID' ,renderer: renderIcon}
                  ]
        }),

        bbar : pageDynaform,
        //tbar : ReceivedPanelToolBars,
        border : false,
        loadMask : true,
        stripeRows : true,
        autoShow : true,
        autoFill : true,
        nocache : true,
        autoWidth : true,
        height : screenheigth,
        stateful : true,
        animCollapse : true,
        stateId : 'dynaformListgrid',
        listeners : {       
    	  rowclick : function( grid, rowIndex, columnIndex, e ){
    	  		
    	         	 __addTabFrame();
    	        	
            }
            
          },
        viewConfig : {
        	  	
              scrollOffset : 2,
              emptyText: '<div align="center"><b>** No Cases to show **</b></div>'
        }
      });            
*/

      // / ----------- end list dynaforms grid   -------------------///      

      var tabpanel = {
        id : 'tabpanel1',
        region: 'center',   
        activeTab : 0,
        flex: 3,
        //width:1150,
        items : [ subtabs ]
     }
       
        //subtabs
	var subtabs = new Ext.TabPanel({
        autoWidth:true,
        id:'subtabsDynaforms',                     
        deferredRender:false,                   
        defaults:{autoScroll: true},
        defaultType:"iframepanel",
        activeTab: 0,
        enableTabScroll: true,
        listeners: {
        tabchange: function(panel){
          panel.ownerCt.doLayout();
        }
      }
       
    });
    
    for(var i=0;i<DYNAFORMSLIST.length;i++){
        var tabTitle = DYNAFORMSLIST[i]['DYN_TITLE'];
        var DYN_UID=DYNAFORMSLIST[i]['DYN_UID'];  
        var ACTIONTYPE = DYNAFORMSLIST[i]['TYPEFORM']; 
        var PRO_UID  = DYNAFORMSLIST[i]['PRO_UID'];
    	var CURRENTDATETIME = DYNAFORMSLIST[i]['CURRENTDATETIME'];
        var url = 'casesHistoryDynaformPage_Ajax.php?ACTIONTYPE='+ACTIONTYPE+'&actionAjax=historyDynaformGridPreview'+'&DYN_UID='+DYN_UID+'&APP_UID='+APP_UID+'&PRO_UID='+PRO_UID+'&CURRENTDATETIME=' + CURRENTDATETIME +'&ACTIONSAVE=0';
        
        fn_add_tab(tabTitle,url,subtabs);          
      }   
     	setTimeout(function(){
      	
      		subtabs.setActiveTab(0);
  		}, 500);
      
      var viewport = new Ext.Viewport({
        layout: {
    	  type: 'fit',  
    	  autoScroll: true,        
          align: 'center'
        },
        items : [ subtabs ]
      });
      
     // dynaformStore.reload();
      
    /*  var __addTabFrame = function() {
    	  rowSelected = Ext.getCmp('dynaformListgrid').getSelectionModel().getSelected();  
    	  var tabTitle = rowSelected.data.DYN_TITLE;
    	  var ACTIONTYPE = rowSelected.data.TYPEFORM;
    	  var PRO_UID  = rowSelected.data.PRO_UID;
    	  var CURRENTDATETIME = rowSelected.data.CURRENTDATETIME;
    	  var DYN_UID=rowSelected.data.DYN_UID;   
          var url = 'casesHistoryDynaformPage_Ajax.php?ACTIONTYPE='+ACTIONTYPE+'&actionAjax=historyDynaformGridPreview'+'&DYN_UID='+DYN_UID+'&APP_UID='+APP_UID+'&PRO_UID='+PRO_UID+'&CURRENTDATETIME=' + CURRENTDATETIME +'&ACTIONSAVE=0';
          //fn_add_tab(tabTitle,url,TabPanel);
    	  tabId = 'plugin-grid-' + rowSelected.data.APP_NUMBER;
    	  
    	  fn_add_tab(tabTitle,url,'');
    	  
    	} */
    
      function fn_open_frames()
      {
        var aIframes = document.getElementsByTagName('iframe');  
        for(var con = 0; con <= aIframes.length ; con++)
        {
          if(aIframes[con] != undefined)
          {
            document.getElementById(aIframes[con].id).style.width = '100%';
            document.getElementById(aIframes[con].id).style.height = '100%';      
          } 
        } 
      } 
      var screenWidth = (PMExt.getBrowser().screen.width-140).toString() + 'px';
      function fn_add_tab(sName,sUrl,TabPanel)
      {   
      	var TabPanel = Ext.getCmp('subtabsDynaforms');      
      	TabPanel.add({
      		id: 'iframe-' + sName,      
      		title: sName,
      		frameConfig:{name: sName + 'Frame', id: sName + 'Frame'},
      		defaultSrc : '../convergenceList/' + sUrl,    
      		loadMask:{msg:'Chargement ...'},
      		autoHeigth: true,
      		closable:false,
      		autoScroll: true,       
      		bodyStyle:{height: ADAPTIVEHEIGHT+'px'},
      		width:screenWidth
        }).show();
        
        TabPanel.doLayout();      
        fn_open_frames();

      }
      function reloadGrid()
      {
      	dynaformStore.reload();
      }

    });
