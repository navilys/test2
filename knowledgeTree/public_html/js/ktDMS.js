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
var showDMS_Metadata = function()
{
  baseURL=baseURLFunction();  
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:750,h:500},
  	position:{x:0,y:0,center:true},
  	title	:"DMS Configuration",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:true,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:true}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'knowledgeTree/kt_Ajax',
  	args: "action=ktDmsConf"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showDMS_UserConf = function()
{
  baseURL=baseURLFunction();  
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:650,h:400},
  	position:{x:0,y:0,center:true},
  	title	:"DMS User Configuration",
  	theme	:"processmaker",  	
  	statusBar:false,
  	control	:{resize:true,roll:false,close:true},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:true}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'knowledgeTree/kt_Ajax',
  	args: "action=ktDmsUserConf"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);  	
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
  
  function assignedDocuments( documentType ){
    currentGroup = documentType;
    baseURL=baseURLFunction();
    document.getElementById('spanDocumentTypeContent').innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";//"Loading list";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : false,
      method: 'POST',
      args  : 'action=showDocumentTypeAssigned&documentType=' + documentType
    });
    oRPC.make();
    document.getElementById('spanDocumentTypeContent').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
  
var assignDocument1 = function()
{
  baseURL=baseURLFunction();  
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:350,h:200},
  	position:{x:0,y:0,center:true},
  	title	:"Assign Document",
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'knowledgeTree/kt_Ajax',
  	args: "action=ktAssignDocument"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

function assignDocument( documentType ){
    currentGroup = documentType;
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";//"Loading options...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : false,
      method: 'POST',
      args  : 'action=ktAssignDocument&documentType=' + documentType
    });
    oRPC.make();
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
  
  function assignDocumentSave( documentType, documentId ){    
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "Adding document...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : true,
      method: 'POST',
      args  : 'action=ktAssignDocumentSave&documentType=' + documentType+"&documentId="+documentId
    });
    oRPC.callback = function(rpc){
      	assignedDocuments( documentType );
      }.extend(this);
    oRPC.make();
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
  
  function assignDocumentDelete( documentType,documentId ){    
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "Deleting document...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : true,
      method: 'POST',
      args  : 'action=ktDeleteDocumentSave&documentId='+documentId
    });
    oRPC.callback = function(rpc){
      	assignedDocuments( documentType );
      }.extend(this);
    oRPC.make();
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
  
  
var mapPanel;

var mapFields = function(documentType)
{
  baseURL=baseURLFunction();  
  mapPanel = new leimnud.module.panel();
  mapPanel.options = {
  	size	:{w:650,h:450},
  	position:{x:0,y:0,center:true},
  	title	:"Map Fields between DMS Metadata and ProcessMaker Variables",
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  mapPanel.events = {
  	remove: function() { delete(mapPanel); }.extend(this)
  };
  mapPanel.make();
  mapPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : baseURL+'knowledgeTree/kt_Ajax',
  	args: "action=ktDmsMapFields&documentType="+documentType
  });
  oRPC.callback = function(rpc){
  	mapPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	mapPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

function mapFieldsSave( documentType, formObj ){        
    howManyElements=getObject('COUNT_ELEMENTS').value();
    
    str="";
    for(i=1;i<=howManyElements;i++){
        str+="&"+getGridField("MAPFIELDS",i,"DMSFIELDSET").value+"--"+getGridField("MAPFIELDS",i,"FIELDNAME").value;
        str+="="+getGridField("MAPFIELDS",i,"PMVAR").value;
    }    
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "Saving map...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : true,
      method: 'POST',
      args  : 'action=ktMapFieldSave&documentType=' + documentType+str
    });
    oRPC.callback = function(rpc){
    //  	assignedDocuments( documentType );    
        mapPanel.remove();
      }.extend(this);
    oRPC.make();
    //alert(oRPC.xmlhttp.responseText);
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
  
  function destinationPath( documentType ){
    currentGroup = documentType;
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";//"Loading options...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : false,
      method: 'POST',
      args  : 'action=ktdestinationPath&documentType=' + documentType
    });
    oRPC.make();
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
  }
 function destinationPathSave(documentType, destinationPath){
    baseURL=baseURLFunction();
    document.getElementById('spanHeaderEditor').innerHTML = "Saving path...";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : baseURL+'knowledgeTree/kt_Ajax',
      async : true,
      method: 'POST',
      args  : 'action=ktDestinationPathSave&documentType=' + documentType+"&destinationPath="+destinationPath
    });
    oRPC.callback = function(rpc){
      	assignedDocuments( documentType );
      }.extend(this);
    oRPC.make();
    document.getElementById('spanHeaderEditor').innerHTML = oRPC.xmlhttp.responseText;
    var scs = oRPC.xmlhttp.responseText.extractScript();
    scs.evalScript();
    
}

function kt_toggleFolder( uid ){
    //alert(uid);
	currentGroup = uid;
    document.getElementById('child_'+uid).innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";//<div style="background: transparent url(http://hugo.opensource.colosa.net/js/maborak/core/images/loader_B.gif) no-repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: 32px; height: 32px; position: absolute; display: none; top: 514.5px; left: 609.5px;" class="panel_loader___processmaker"></div><div style="" class="panel_statusBar___processmaker"><div style="position: relative; text-align: center; display: none;" class="panel_statusButtons___processmaker"></div>';
    url   = 'kt_Ajax';
    async = true;
    method = 'POST';
    args  = 'action=toggleFolder&folderID=' + uid;
    //alert(args);
    callback = function(rpc) { 
    	//alert(rpc);
        document.getElementById('child_'+uid).innerHTML = rpc;
        //var scs = rpc.xmlhttp.responseText.extractScript();
        //scs.evalScript();
    
        getFolderContent(uid);
    }
    //alert(callback);
    ajax_post(url, args, method, callback, async );
    


}
function getFolderContent(uid){
  document.getElementById('spanFolderContent').innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";//"Loading..";
  
  url   = 'kt_Ajax';
  async = false;
  method= 'POST';
  args  = 'action=getFolderContent&folderID=' + uid;
  callback = null;
  rpc=ajax_post(url, args, method, callback, async );
	
    document.getElementById('spanFolderContent').innerHTML = rpc;
    //var scs = oRPC.xmlhttp.responseText.extractScript();
    //scs.evalScript();
    
  }
  
  
var _oVarsPanel_;
var showDynaformsFormVars = function(sFieldName, sAjaxServer, sProcess, sSymbol) {
	_oVarsPanel_ = new leimnud.module.panel();
	_oVarsPanel_.options = {
    limit    : true,
    size     : {w:350,h:400},
    position : {x:0,y:0,center:true},
    title    : '',
    theme    : 'processmaker',
    statusBar: false,
    control  : {drag:false,resize:true,close:true},
    fx       : {opacity:true,rolled:false,modal:true}
  };
  _oVarsPanel_.make();
  _oVarsPanel_.events = {
    remove:function() {
      delete _oVarsPanel_;
    }.extend(this)
  };
  _oVarsPanel_.loader.show();
  oRPC = new leimnud.module.rpc.xmlhttp({
    url   : sAjaxServer,
    method: 'POST',
    args  : 'sFieldName=' + sFieldName + '&sProcess=' + sProcess + '&sSymbol=' + sSymbol
  });
  oRPC.callback = function(oRPC) {
    _oVarsPanel_.loader.hide();
    var scs = oRPC.xmlhttp.responseText.extractScript();
    _oVarsPanel_.addContent(oRPC.xmlhttp.responseText);
    scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var insertFormVar = function(sFieldName, sValue) {
	oAux = document.getElementById(sFieldName);
	if (oAux.setSelectionRange) {
		var rangeStart = oAux.selectionStart;
    var rangeEnd   = oAux.selectionEnd;
    var tempStr1   = oAux.value.substring(0,rangeStart);
    var tempStr2   = oAux.value.substring(rangeEnd);
    oAux.value     = tempStr1 + sValue + tempStr2;
	}
	else {
	  if (document.selection) {
	    oAux.focus();
      document.selection.createRange().text = sValue;
	  }
	}
	_oVarsPanel_.remove();
};

var ktUserConfSubmit = function(formObj,oPanel){
	 if (!G.getObject(formObj).verifyRequiredFields()){
   return;
 }
 nextStep=document.getElementById("form[NEXT_STEP]");
 //alert(nextStep);
  var res=ajax_post( formObj.action, formObj, 'POST' , null , false );	
	oPanel.remove();
	window.location=nextStep.value;
}