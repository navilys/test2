document.write('<link rel="stylesheet" type="text/css" href="/plugin/firstmedical/icons.css" />');

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

function fn_add_tab(sName,sUrl,sIcon)
{
	
	var TabPanel = Ext.getCmp('caseTabPanel');
	
	TabPanel.add({
		id: 'ID_' + sName,
	    title: sName,
	    frameConfig:{name: sName + 'Frame', id: sName + 'Frame'},
	    defaultSrc : '../firstmedical/' + sUrl,
	    iconCls: sIcon,
	    loadMask:{msg:'Loading...'},
	    autoWidth: true,
	    closable:true,
	    enableTabScroll: true,
	    autoScroll: true,
	    bodyStyle:{height: (PMExt.getBrowser().screen.height-60) + 'px', overflow:'auto'}
	}).show();
	
	TabPanel.doLayout();
	fn_open_frames();
}

function fn_open_tab()
{
	var TabPanel = Ext.getCmp('caseTabPanel');
	  TabPanel.add({
		id: 'ID_HISTORIAL',
	    title: 'Historial ',
	    iconCls: 'icon-historial',
	    html : '<iframe id="id_frame" frameborder="0" style="width: 100%; height: 100%;" src="../firstmedical/cases_HistorialJS.php"></iframe>',            
	    closable:false
	  }).show();
	  TabPanel.doLayout();
}

function fn_tabchange()
{
	var TabPanel = Ext.getCmp('caseTabPanel');
	TabPanel.activate('ID_HISTORIAL');	
}

Ext.onReady(function() 
{
  var TabPanel = Ext.getCmp('caseTabPanel');
  TabPanel.add({
		id: 'ID_HISTORIAL',
	    title: 'Historial',
	    frameConfig:{name: 'HISTORIAL' + 'Frame', id: 'HISTORIAL' + 'Frame'},
	    defaultSrc : '../firstmedical/cases_HistorialJS.php',
	    iconCls: 'icon-historial',
	    loadMask:{msg:'Loading...'},
	    autoWidth: true,
	    closable:false,
	    autoScroll: true,
	    bodyStyle:{height: (PMExt.getBrowser().screen.height-60) + 'px', overflow:'auto'}
	});
	
  TabPanel.doLayout();
 
});

