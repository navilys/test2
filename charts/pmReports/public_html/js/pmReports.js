var bPMReportPanel = true;
//var goToPMReportPermissionsPanel = function()
//{
//	oPanelAddPermissions.remove();
//	showPMReportPanelPermissions();
//};
var refreshPMReportPanel = function()
{
	oPanel.remove();
	showPMReportPanel();
};
var refreshPMReportPermissionsPanel = function(sPmrUid)
{
	bPMReportPanel = false;
	//return false;
	oPanelPermissions.remove();
	showPMReportPanelPermissions(sPmrUid);
	bPMReportPanel = true;
};
var oPanel;
var showPMReportPanel = function()
{
  baseURL=baseURLFunction();  
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:550,h:400},
  	position:{x:0,y:0,center:true},
  	title	:"PM Simple Reports",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  //oPanel.events = {
  //	remove: function() { delete(oPanel); }.extend(this)
  //};
  
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'pmReports/rptList',
  	args: "action=ktDmsConf"
  });
  oPanel.make();
  oPanel.loader.show();
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var oPanelNew;
var showPMReportPanelConfig = function(sPmrUid)
{
	if(!sPmrUid)
		sPmrUid = 0;
	oPanel.remove();
  baseURL=baseURLFunction();  
  oPanelNew = new leimnud.module.panel();
  oPanelNew.options = {
  	size	:{w:550,h:400},
  	position:{x:0,y:0,center:true},
  	title	:"New PM Simple Reports",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanelNew.events = {
  	remove: function() { showPMReportPanel(); }.extend(this)
 	};
  
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'pmReports/rptConfig',
  	args: "sPmrUid="+sPmrUid
  });
  oPanelNew.make();
  oPanelNew.loader.show();
  oRPC.callback = function(rpc){
  	oPanelNew.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanelNew.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var oPanelPermissions;
var showPMReportPanelPermissions = function(sPmrUid)
{
	if(!sPmrUid)
		sPmrUid = 0;
	if(oPanel && bPMReportPanel)
		oPanel.remove();
	bPMReportPanel = true;
  baseURL=baseURLFunction();  
  oPanelPermissions = new leimnud.module.panel();
  oPanelPermissions.options = {
  	size	:{w:550,h:400},
  	position:{x:0,y:0,center:true},
  	title	:"PM Simple Reports Permissions",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanelPermissions.events = {
  	remove: function() { if(bPMReportPanel){showPMReportPanel();} }.extend(this)
 	};
  
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'pmReports/rptPermissions',
  	args: "sPmrUid="+sPmrUid
  });
  oPanelPermissions.make();
  oPanelPermissions.loader.show();
  oRPC.callback = function(rpc){
  	oPanelPermissions.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanelPermissions.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var oPanelAddPermissions;
var showPMReportPanelAddPermissions = function(sPmrUid)
{
	bPMReportPanel = false;
	if(!sPmrUid)
		sPmrUid = 0;
	oPanelPermissions.remove();
	bPMReportPanel = true;
  baseURL=baseURLFunction();  
  oPanelAddPermissions = new leimnud.module.panel();
  oPanelAddPermissions.options = {
  	size	:{w:550,h:400},
  	position:{x:0,y:0,center:true},
  	title	:"PM Simple Reports Add Permissions",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanelAddPermissions.events = {
  	remove: function() { showPMReportPanelPermissions(sPmrUid); }.extend(this)
 	};
  
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'pmReports/rptAddPermissions',
  	args: "sPmrUid="+sPmrUid
  });
  oPanelAddPermissions.make();
  oPanelAddPermissions.loader.show();
  oRPC.callback = function(rpc){
  	oPanelAddPermissions.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanelAddPermissions.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
function baseURLFunction(){
	currentUrlArray=window.location.href.split("/");
	newURL="";
	if((currentUrlArray[0]=="http:")||(currentUrlArray[0]=="https:")){
		newURL=newURL+currentUrlArray[0]+"//";
		delete currentUrlArray[0];
		delete currentUrlArray[1];
	}
	countA=0;
	for(i=0;i<currentUrlArray.length;i++){
		if(currentUrlArray[i]){
			countA++;
			if(countA<5){
				newURL=newURL+currentUrlArray[i]+"/";
				//alert(currentUrlArray[i]);
			}
		}

	}

 return newURL;
}
/*
var oPanel;
var showPMReportPanel=function(uid)
{
	  newPanel =new leimnud.module.panel();
						newPanel.options={
							size	:{w:900,h:500},			
							position:{x:60,y:60,center:true},
							control	:{close:true,resize:false},fx:{modal:true},
							statusBar:false,
							fx	:{shadow:false,modal:true}
						};
						newPanel.events={
							remove	:closeEvents
						};
						newPanel.make();
						newPanel.loader.show();
						var r = new leimnud.module.rpc.xmlhttp({
							url:"callViewLogDetails.php",
							method:"POST",
							args:"PRO_UID="+uid
						});
						r.callback=function(rpc)
						{
							newPanel.loader.hide();
							newPanel.addContent(rpc.xmlhttp.responseText);
var scs = rpc.xmlhttp.responseText.extractScript();

	scs.evalScript();
						}.extend(this);;
						r.make();
						
};
*/