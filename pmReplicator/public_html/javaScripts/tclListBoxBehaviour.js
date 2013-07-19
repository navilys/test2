/**
 * 
 */
function tclListBox(sListBoxId,bCheckBoxInSelect,sOneClickFunctionId,sClassName){
	var that=this;
	var sListName=sListBoxId;
	var bWillHaveCheckBox=(bCheckBoxInSelect===undefined) ? true : bCheckBoxInSelect; 
	var oListBx=document.getElementById(sListBoxId);
	var sClsName = sClassName || "tclRow";
	var aRowClasses=new Array(sClsName, "tclCursorDefault", "tclElementTable");
	var aTextFieldClasses= new Array("tclWidth245","tclBoxView","tclInlineAlignLeft","tclTopPadding1");
	var aControlAreaClasses = new Array("tclInlineAlignLeft");
	var aSelectedItems=new Array();
	var oBodForKeyStroke = document.getElementsByTagName("body");
	var bCtrlPressed = false;
	var bShiftPressed = false;
	var oneClickMultiSelect=false;
	var oUnselectedTables = sOneClickFunctionId!=undefined ? new tclListBehaviour(sOneClickFunctionId):undefined;
	var iCurrentMax=0;
	tclAuxiliary.disableSelection(oListBx);
	/**
	 * key down behaviour
	 */
	tclAuxiliary.bindEvent("keydown",oBodForKeyStroke, function(eventArgs){
		var iKeyStroke = eventArgs.keyCode;
		bCtrlPressed=(iKeyStroke===17 || iKeyStroke===224);
		bShiftPressed=(iKeyStroke===16);
	});
	tclAuxiliary.bindEvent("keyup",oBodForKeyStroke, function(){
		bCtrlPressed =false;
		bShiftPressed =false;
	});
	if (oUnselectedTables==undefined){
		tclAuxiliary.bindEvent("mouseup",oBodForKeyStroke,function(){
			oneClickMultiSelect=false;
		});
	}
	/**
	 * this function will create all the structrue needed in order allow row interaction
	 * @param iRowIndex
	 * @param sTextPart
	 */
	var createRow = function (iRowIndex,oOption){
		var oNewDiv=document.createElement("div");
		oNewDiv.id="tclRow["+iRowIndex+"]";
		tclAuxiliary.createAttribute(oNewDiv,"tclRow",iRowIndex);
		var sClassName;
		for (var i=0; (sClassName=aRowClasses[i])!=undefined;i++){
			tclAuxiliary.addClass(oNewDiv,sClassName);
		}
		oNewDiv.appendChild(createTextArea(iRowIndex,oOption.innerHTML));
		oNewDiv.appendChild(createHidden(iRowIndex,oOption.value));
		if (bWillHaveCheckBox){
			oNewDiv.appendChild(createInputArea(iRowIndex,oOption.innerHTML));
		}
		return oNewDiv;
	};
	/**
	 * 
	 * @param iRowIndex
	 * @param sTextPart
	 */
	var createTextArea = function (iRowIndex,sTextPart){
		var oNewTextArea=document.createElement("div");
		oNewTextArea.id="tclTextArea["+iRowIndex+"]";
		var sClassName;
		for (var i=0; (sClassName=aTextFieldClasses[i])!=undefined;i++){
			tclAuxiliary.addClass(oNewTextArea,sClassName);
		}
		oNewTextArea.innerHTML=sTextPart;
		attachEvent(oNewTextArea);
		return oNewTextArea;
	};
	/**
	 * 
	 * @param iRowIndex
	 */
	var createInputArea = function (iRowIndex,sTextPart){
		var oNewCotrolArea=document.createElement("div");
		oNewCotrolArea.id="tclTextArea["+iRowIndex+"]";
		var sClassName;
		for (var i=0; (sClassName=aControlAreaClasses[i])!=undefined;i++){
			tclAuxiliary.addClass(oNewCotrolArea,sClassName);
		}
		oNewCotrolArea.appendChild(createCheckBox(iRowIndex,sTextPart,"STRUCTURE"));
		oNewCotrolArea.appendChild(createCheckBox(iRowIndex,sTextPart,"DATA"));
		return oNewCotrolArea;
	};
	/**
	 * 
	 * @param iRowIndex
	 * @param sIdText
	 */
	var createCheckBox = function (iRowIndex,sTextPart,sIdText){
		var oNewCheckBox = document.createElement("input");
		oNewCheckBox.type = "checkbox";
		oNewCheckBox.name = sListName+"[OPTIONS]["+sTextPart+"]["+sIdText+"]";
		oNewCheckBox.value=true;
		oNewCheckBox.title=sIdText;
		tclAuxiliary.createAttribute(oNewCheckBox, "checked", "true");
		return oNewCheckBox;
	};
	/**
	 * 
	 * @param iRowIndex
	 * @param sTextPart
	 * @returns
	 */
	var createHidden = function(iRowIndex,sTextPart){
		var oNewHidden = document.createElement("input");
		oNewHidden.type="hidden";
		oNewHidden.name=sListName+"[VALUES]["+iRowIndex+"]";
		oNewHidden.value = sTextPart;
		return oNewHidden;
	};
	/**
	 * 
	 * @param domToBeAttached
	 * @returns
	 */
	var attachEvent = function (domToBeAttached){
		tclAuxiliary.bindEvent("mousedown",domToBeAttached, function(){
			var oRow = this.parentNode;
			if (oUnselectedTables===undefined){
				var shiftBehaviour = (bShiftPressed) ? shiftSelect(oRow) : undefined;
				if (shiftBehaviour===undefined){
					if (tclAuxiliary.hasClass(oRow, "tclListSelected"))
						that.unselectItem(oRow);
					else{
						that.selectItem(oRow);
						oneClickMultiSelect=true;
					}
				}
			} else {
				oneClickBehaviour(this.innerHTML,oRow);
			}
		});
		if (oUnselectedTables===undefined){
			tclAuxiliary.bindEvent("mouseover",domToBeAttached,function(){
				if (oneClickMultiSelect){
					var oRow = this.parentNode;
					if (tclAuxiliary.hasClass(oRow, "tclListSelected"))
						that.unselectItem(oRow);
					else
						that.selectItem(oRow);
				}
			});
		}
	};
	/**
	 * 
	 * @param sValue
	 * @param oRow
	 * @returns
	 */
	var oneClickBehaviour = function(sValue,oRow){
		oUnselectedTables.displayHideSingle(sValue,'block');
		that.removeRow(oRow);
	};
	/**
	 * 
	 * @returns
	 */
	var shiftSelect = function(oRowSelected){
		var lastSelection=getCurrentLastSelect();
		if (lastSelection===undefined) return undefined;
		selectBatchBetweenTwoElements(lastSelection,oRowSelected);
		return true;
	};
	/**
	 * 
	 * @param oRowOrigin
	 * @param oRowSelected
	 * @returns
	 */
	var selectBatchBetweenTwoElements = function(oRowOrigin,oRowFinal){
		var iInitialPosition = parseInt(oRowOrigin.getAttribute("tclRow"),10);
		var iFinalPosition = parseInt(oRowFinal.getAttribute("tclRow"),10);
		if (iInitialPosition>iFinalPosition)
			selectAllBetweenElements(iFinalPosition,iInitialPosition,oRowFinal);
		else if (iInitialPosition<iFinalPosition)
			selectAllBetweenElements(iInitialPosition,iFinalPosition,oRowFinal);
		else if (iInitialPosition=iFinalPosition)
			that.selectItem(oRowFinal);
	};
	/**
	 * 
	 * @param iInitialPosition
	 * @param iFinalPosition
	 * @returns
	 */
	var selectAllBetweenElements = function (iInitialPosition,iFinalPosition,oRowFinal){
		var aNodes = oRowFinal.parentNode.children;
		var oNode;
		that.unselectAllSelected(oRowFinal);
		for (var i=0;(oNode=aNodes[i])!=undefined;i++){
			var iCurrentRow=parseInt(oNode.getAttribute("tclRow"),10);
			if (iInitialPosition<=iCurrentRow && iFinalPosition>=iCurrentRow ){
				that.selectItem(oNode);
			}
		}
	};
	/**
	 * 
	 * @returns
	 */
	var getCurrentLastSelect = function(){
		if (aSelectedItems.length===0)
			return undefined;
		return aSelectedItems[aSelectedItems.length-1];
	};
	/**
	 * this function will add a new row inside the list box
	 * 
	 * @param sTextPart: the string that will held the information to be display
	 */
	this.addNewRow = function (oElementPart){
		iCurrentMax++;
		oListBx.appendChild(createRow(iCurrentMax,oElementPart));
	};
	/**
	 * 
	 */
	this.maxNumRows = function (){
		return oListBx.childNodes.length;
	};
	/**
	 * 
	 */
	this.removeRow = function (oRowToRemove){
		this.unselectItem(oRowToRemove);
		oListBx.removeChild(oRowToRemove);
	};
	/**
	 * 
	 */
	this.clearAll = function (){
		var aRows=tclAuxiliary.getClassesCompatible(sClsName,oListBx);
		while (aRows[0]!==undefined){
			this.removeRow(aRows[0]);
		}
		aSelectedItems = new Array();
	};
	/**
	 * 
	 */
	this.selectItem = function(oRow){
		var iNewSize=aSelectedItems.length;
		if (!bCtrlPressed && !oneClickMultiSelect && !bShiftPressed) this.unselectAllSelected(oRow);
		aSelectedItems.splice(iNewSize, 0, oRow);
		tclAuxiliary.addClass(oRow,"tclListSelected");
	};
	/**
	 * 
	 */
	this.unselectAllSelected = function(oRow){
		var aChild = oRow.parentNode.children;
		var oNode;
		for (var i=0; (oNode=aChild[i])!=undefined;i++){
			if (tclAuxiliary.hasClass(oNode,"tclListSelected")) 
				this.unselectItem(oNode);
		}
	};
	/**
	 * 
	 */
	this.unselectItem = function(oRow){
		var iIndexOfItem=aSelectedItems.indexOf(oRow);
		aSelectedItems.splice(iIndexOfItem, 1);
		tclAuxiliary.removeClass(oRow, "tclListSelected");
	};
	/**
	 * 
	 */
	this.getSelectedItems = function(){
		return aSelectedItems;
	};
	/**
	 * 
	 */
	this.getValueOfRow=function(oRow){
		return oRow.children[1].value;
	};
}