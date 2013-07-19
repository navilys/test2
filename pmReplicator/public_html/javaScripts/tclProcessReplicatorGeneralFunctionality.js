/**
 * Globals declaration
 */ 
var processReplicatorGBehaviour = {};
processReplicatorGBehaviour.bReset = false; 
processReplicatorGBehaviour.oButtonManipulation = new tclButtonBehaviour();
processReplicatorGBehaviour.oSliderAnim = new tclConvertToSlider("tclNewSlider",650,15);
processReplicatorGBehaviour.oUnselectedList = new tclListBehaviour("form[LIST_UNSELECTED_WORKSPACES]");
processReplicatorGBehaviour.oSelectedList = new tclListBehaviour("form[SELECTED_WORKSPACES]");
processReplicatorGBehaviour.oSelectedTablesList = new tclListBox("ListBoxLikeTables");
processReplicatorGBehaviour.oSelectedProcessList=new tclListBox("ListBoxLikeProcess",false);
processReplicatorGBehaviour.oUnselectedTables = new tclListBehaviour("form[UNSELECTED_TABLES]");
processReplicatorGBehaviour.oUnselectedProcess = new tclListBehaviour("form[UNSELECTED_PROCESS]");
processReplicatorGBehaviour.oSelect = document.getElementById("form[WORKSPACES_ORIGIN]");

processReplicatorGBehaviour.oSelect.setAttribute("MULTIPLE", "TRUE");
processReplicatorGBehaviour.oSelect.style.height="196px";
processReplicatorGBehaviour.oSelect.style.width="394px";
processReplicatorGBehaviour.aClassElements = tclAuxiliary.getClassesCompatible("tclButtonBehaviour");
processReplicatorGBehaviour.currentDom;

/**
 * Click behaviour
 */
tclAuxiliary.tclBindClick("FirstWindow",function(){
	processReplicatorGBehaviour.oSliderAnim.MovePositions(0);
});
tclAuxiliary.tclBindClick("SecondWindow",function(){
		if (!tclAuxiliary.hasClass(this, "tclDisabled"))
			processReplicatorGBehaviour.oSliderAnim.MovePositions(1);
	});
tclAuxiliary.tclBindClick("ThirdWindow",function(){
	    if (!tclAuxiliary.hasClass(this, "tclDisabled"))
	    	processReplicatorGBehaviour.oSliderAnim.MovePositions(2);
	});
tclAuxiliary.tclBindClick("FourthWindow",function(){
    if (!tclAuxiliary.hasClass(this, "tclDisabled"))
    	processReplicatorGBehaviour.oSliderAnim.MovePositions(3);
});
tclAuxiliary.tclBindClick("form[WORKSPACES_ORIGIN]",function(){
	if (this.value){
		var that=this;
		bReset=true;
		var newData={
				workspace:that.value
		};
		tclAuxiliary.ajaxCallJson("ajaxFunctionality.php",newData,function(response){
			tclAuxiliary.fillDropBox("form[UNSELECTED_TABLES]",response.tables);
			tclAuxiliary.fillDropBox("form[UNSELECTED_PROCESS]",response.process);
		},"POST");
		processReplicatorGBehaviour.oButtonManipulation.disableButton("SecondWindow");
		processReplicatorGBehaviour.oButtonManipulation.disableButton("ThirdWindow");
		processReplicatorGBehaviour.oButtonManipulation.disableButton("FourthWindow");
		tclAuxiliary.addClass("SelectTablesToTransfer","tclDisabled");
		processReplicatorGBehaviour.oSelectedTablesList.clearAll();
		processReplicatorGBehaviour.oSelectedProcessList.clearAll();
		if (tclAuxiliary.hasClass("SelectDestinationWorkspaces","tclDisabled")){
			tclAuxiliary.removeClass("SelectDestinationWorkspaces","tclDisabled");
		}
	}
});
tclAuxiliary.tclBindClick("form[LIST_UNSELECTED_WORKSPACES]",function(){
	if (this.value){
		var sValue=this.value;
		processReplicatorGBehaviour.oUnselectedList.displayHideSingle(sValue, "none");
		processReplicatorGBehaviour.oSelectedList.displayHideSingle(sValue, "block");
		tclAuxiliary.removeClass("SelectTablesToTransfer","tclDisabled");
	}
});
tclAuxiliary.tclBindClick("form[SELECTED_WORKSPACES]",function(){
	if (this.value){
		var sValue=this.value;
		processReplicatorGBehaviour.oUnselectedList.displayHideSingle(sValue, "block");
		processReplicatorGBehaviour.oSelectedList.displayHideSingle(sValue, "none");
	}
});
/*tclBindClick("form[GRID_OF_TABLES][addLink]",function(){
	var iNumRows=Number_Rows_Grid('GRID_OF_TABLES', 'LINK_TO_SELECTED_WORKSPACE');
	reloadElementWithSelection(iNumRows);
	document.getElementById("form[GRID_OF_TABLES]["+iNumRows.toString()+"][EXPORT_ONLY_STRUCTURE]").checked = "checked";
	document.getElementById("form[GRID_OF_TABLES]["+iNumRows.toString()+"][EXPORT_ONLY_DATA]").checked = "checked";
});*/
tclAuxiliary.tclBindClick("submitForm",function(){
	processReplicatorGBehaviour.oSelectedList.selectAllElementsInList();
	document.forms[0].submit();
});
tclAuxiliary.tclBindClick("SelectProcessToTransfer",function(){
	processReplicatorGBehaviour.oSliderAnim.MovePositions(3);
});
/*tclAuxiliary.tclBindClick("form[UNSELECTED_TABLES]", function(){
	if (this.value){
		processReplicatorGBehaviour.oSelectedTablesList.addNewRow(this.value);
		processReplicatorGBehaviour.oUnselectedTables.displayHideSingle(this.value, "none");
	}
});*/
tclAuxiliary.tclBindClick("moveFromLeftToRight",function(){
	var oSelect = document.getElementById("form[UNSELECTED_TABLES]");
	var aSelectedItems=tclAuxiliary.getSelectedOptions(oSelect);
	var oItem;
	for (var i=0;(oItem=aSelectedItems[i])!=undefined;i++){
		processReplicatorGBehaviour.oSelectedTablesList.addNewRow(oItem);
		processReplicatorGBehaviour.oUnselectedTables.displayHideSingle(oItem.value, "none");
	}
});

tclAuxiliary.tclBindClick("moveAllFromLeftToRight",function(){
	var aSelectOptions = document.getElementById("form[UNSELECTED_TABLES]").getElementsByTagName("*");
	var oValue;
	while (aSelectOptions[0]!=undefined){
			processReplicatorGBehaviour.oSelectedTablesList.addNewRow(aSelectOptions[0]);
			processReplicatorGBehaviour.oUnselectedTables.displayHideSingle(aSelectOptions[0].value, "none");
		}
});
tclAuxiliary.tclBindClick("moveFromRightToLeft",function(){
	var aSelectedRows=processReplicatorGBehaviour.oSelectedTablesList.getSelectedItems();
	while (aSelectedRows[0]!==undefined){
		processReplicatorGBehaviour.oUnselectedTables.displayHideSingle(processReplicatorGBehaviour.oSelectedTablesList.getValueOfRow(aSelectedRows[0]), "block");
		processReplicatorGBehaviour.oSelectedTablesList.removeRow(aSelectedRows[0]);
	}	
});
tclAuxiliary.tclBindClick("moveAllFromRightToLeft", function(){
	processReplicatorGBehaviour.oUnselectedTables.showEverything();
	processReplicatorGBehaviour.oSelectedTablesList.clearAll();
});

tclAuxiliary.tclBindClick("moveAllFromLeftToRightProcess",function(){
	var aSelectOptions = document.getElementById("form[UNSELECTED_PROCESS]").getElementsByTagName("option");
	var oValue;
	while (aSelectOptions[0]!=undefined){
			processReplicatorGBehaviour.oSelectedProcessList.addNewRow(aSelectOptions[0]);
			processReplicatorGBehaviour.oUnselectedProcess.displayHideSingle(aSelectOptions[0].value, "none");
		}
});
tclAuxiliary.tclBindClick("moveFromLeftToRightProcess",function(){
	var oSelect = document.getElementById("form[UNSELECTED_PROCESS]");
	var aSelectedItems=tclAuxiliary.getSelectedOptions(oSelect);
	var oItem;
	for (var i=0;(oItem=aSelectedItems[i])!=undefined;i++){
		processReplicatorGBehaviour.oSelectedProcessList.addNewRow(oItem);
		processReplicatorGBehaviour.oUnselectedProcess.displayHideSingle(oItem.value, "none");
	}
});
tclAuxiliary.tclBindClick("moveFromRightToLeftProcess",function(){
	var aSelectedRows=processReplicatorGBehaviour.oSelectedProcessList.getSelectedItems();
	while (aSelectedRows[0]!==undefined){
		processReplicatorGBehaviour.oUnselectedProcess.displayHideSingle(processReplicatorGBehaviour.oSelectedTablesList.getValueOfRow(aSelectedRows[0]), "block");
		processReplicatorGBehaviour.oSelectedProcessList.removeRow(aSelectedRows[0]);
	}	
});
tclAuxiliary.tclBindClick("moveAllFromRightToLeftProcess", function(){
	processReplicatorGBehaviour.oUnselectedProcess.showEverything();
	processReplicatorGBehaviour.oSelectedProcessList.clearAll();
});
for (var i=0; (processReplicatorGBehaviour.currentDom=processReplicatorGBehaviour.aClassElements[i])!=undefined; i++){
	tclAuxiliary.tclBindClick(processReplicatorGBehaviour.currentDom,function(){
		if (!tclAuxiliary.hasClass(this, "tclDisabled")){
			var item=this.getAttribute("tclGoTo");
			processReplicatorGBehaviour.oSliderAnim.MovePositions(parseInt(item));
			manageButtonBehaviour(parseInt(item),processReplicatorGBehaviour.oButtonManipulation);
		}
	});
}
/**
 * this function will enable buttons when needed
 * 
 * @param posToEnable: the position that validates the button to be Enabled
 * @param objButtonBhave:the object that refers the buttonBehaviour class
 */
function manageButtonBehaviour(posToEnable,objButtonBhave){
	switch (posToEnable){
		case 1:
			if (bReset){
				processReplicatorGBehaviour.oUnselectedList.hideElementInSelect(document.getElementById("form[WORKSPACES_ORIGIN]").value);
				processReplicatorGBehaviour.oSelectedList.hideEverything();
				objButtonBhave.enableButton("SecondWindow");
				bReset=false;
				tclAuxiliary.deleteGridRows('GRID_OF_TABLES', 'LINK_TO_SELECTED_WORKSPACE');
			}
			break;
		case 2:
			objButtonBhave.enableButton("ThirdWindow");
			objButtonBhave.enableButton("FourthWindow");
			break;
	}
}