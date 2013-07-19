/**
 * pentahoReportsList.js
 * The main javascript functions used by the reports list are stored within this javascript file.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @package plugins.pentahoreports.javascript
 */

/**
 * open a report folder loading their respective content via an Ajax request
 * @param uid
 * @param rootfolder
 * @return void
 */
function openReportFolder( uid, rootfolder ){
    currentFolder = uid;
    if((document.getElementById('child_'+uid).innerHTML!="")&&(uid!=rootfolder)){
      document.getElementById('child_'+uid).innerHTML="";
      getPMFolderContent(uid);
      return;
    }
    document.getElementById('child_'+uid).innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : 'pentahoReportsListAjax',
      async : true,
      method: 'POST',
      args  : 'action=openReportFolder&folderID=' + uid+'&rootfolder='+rootfolder
    });
    oRPC.callback = function(rpc) {
        document.getElementById('child_'+uid).innerHTML = rpc.xmlhttp.responseText;
        var scs = rpc.xmlhttp.responseText.extractScript();
        scs.evalScript();
    }.extend(this);
    oRPC.make();

}

/**
 * checking if an element is undefined or not
 * @param element 
 * @return boolean
 */
function elementExists(element){
	if (element!=undefined)	{
		return true;	
	} else {
		return false;
	}
}

/**
 * open or load a report in the div element
 * @param reportFilePath
 * @param reportFileName
 */
function openReport( reportFilePath, reportFileName ){
    
		reportFullFilePath = reportFilePath+reportFileName;

  	
    parent.document.getElementById('spanFolderContent').innerHTML = "<img src='/js/maborak/core/images/loader_B.gif' >";
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : 'pentahoReportsListAjax',
      async : true,
      method: 'POST',
      args  : 'action=openReport&filePath=' + reportFilePath + '&fileName=' + reportFileName
    });
    oRPC.callback = function(rpc) {
    		divWidth = 'width:'+(screen.width-420)+';';
        
        parent.document.getElementById('spanFolderContent').innerHTML = oRPC.xmlhttp.responseText;
        
        if (navigator.appName=='Microsoft Internet Explorer'){
          document.getElementById('spanFolderContent').style.width = (screen.width-420);
        } else {
          document.getElementById('spanFolderContent').setAttribute('style',divWidth);
        }
        
        var scs = oRPC.xmlhttp.responseText.extractScript();
        scs.evalScript();
    }.extend(this);
    oRPC.make();
}

/**
 * @deprecated
 * older function that collapse or uncollapse the
 * report browser
 */
function toggleShowFolderContent( folderName, rootfolder ){
	uid = "child_"+folderName;
	arrowId = "arrow_"+folderName;
	folderElement = document.getElementById(uid);
	arrowElement  = document.getElementById(arrowId);
	
	hiddenStatus = folderElement.style.display;

	if (hiddenStatus==''){
		folderElement.setAttribute('style','display:none;');		
		folderElement.style.display='none';
		  if (elementExists(arrowElement))
		    document.getElementById(arrowId).innerHTML='&#9658;';
	}
  else { 	
    folderElement.setAttribute('style','');
  	folderElement.style.display='';
  	  if (elementExists(arrowElement))
  	    document.getElementById(arrowId).innerHTML='&#9660;';
  }

}

/**
 * @deprecated
 * collapse an uncollapse folder tree
 */
function toggleTree(){
	divWidth = screen.width-420;
	divWidthAlt = screen.width-50;

	if (document.getElementById('publisherContent[0]').style.display!='none'||document.getElementById('publisherContent[0]').style.display==undefined){
	  document.getElementById('publisherContent[0]').parentNode.style.width="0px"; 
  	  document.getElementById('publisherContent[0]').style.display='none';
  	  document.getElementById('spanFolderContent').style.width=divWidthAlt;
  	}	else { 		
  	  document.getElementById('publisherContent[0]').parentNode.style.width="270px";	 
  	  document.getElementById('publisherContent[0]').style.display='inline';
  	  document.getElementById('spanFolderContent').style.width=divWidth;
  	} 	  
}

 /**
  * Loading the reports List when the page is loaded by the browser
  */
window.onload = startPentahoReportsDir;
function startPentahoReportsDir(){
	openReportFolder('0','0');
}
