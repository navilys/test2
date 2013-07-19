/**
 * 
 */
var dynaformOnload = function(){
	var dateStamp = new Date().getTime();
	setNewScript("tclProcessReplicatorGeneralFunctionality.js?"+dateStamp);
};
/**
 * 
 * @param sJsFileName
 */
function setNewScript(sJsFileName){
	var newVarScript = document.createElement('script');
	newVarScript.type = "text/javascript";
	newVarScript.src = "/plugin/pmReplicator/javaScripts/" + sJsFileName;
	document.getElementsByTagName("head")[0].appendChild(newVarScript);
}