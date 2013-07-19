window.onload = function(){
	var domObject=document.getElementById("tclCloseButton");
	//create click handler
	if (domObject!=null){
		domObject.onclick=tclClickHandler;
	}
	var domCronPressed = document.getElementById("EditRuteOPT");
	if (domCronPressed!=null){
		domCronPressed.onclick=tclCronClick;
	}
	var newPanel;
};
/*
* This function will perform a "close window"
* when the item becomes clicked
*/
function tclClickHandler(){
	var domMessageWindow=document.getElementById("tclCustomMessageWindow");
	domMessageWindow.style.display="none";
}

function tclOpenPopUp (sPageName,sValueName,sValue)
	{
		newPanel =new leimnud.module.panel();
		newPanel.options={
			size	:{w:630,h:274},			
		position:{x:60,y:60,center:true},
			control	:{close:true,resize:false},fx:{modal:true},
		statusBar:false,
			fx	:{shadow:false,modal:true}
		};
		newPanel.make();
		newPanel.loader.show();
		var r = new leimnud.module.rpc.xmlhttp({
											   url:sPageName,
											   method:"POST",
											   args:sValueName+"="+sValueName
											   });
		r.callback=function(rpc)
		{
			newPanel.loader.hide();
			newPanel.addContent(rpc.xmlhttp.responseText);
			var scs = rpc.xmlhttp.responseText.extractScript();
			
			scs.evalScript();
		};
		r.make();
		
	}
function tclCronClick(){
	var RuteText=document.getElementById("RuteText");
	tclOpenPopUp("EditCronRute.php","CRON_RUTE",RuteText.innerHTML);
}