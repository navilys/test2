/**
 * This class will give the slider functionality to a given elenement id
 * 
 * @param sAreaToSlide = an string that contains the id of the slider area
 * @param iSizeToSlide = an integer with slide window size.
 * @param iSlideSpeed = an integer with the speed  
 */
function tclConvertToSlider(sAreaToSlide,iSizeToSlide,iSlideSpeed){
	this.oSlideArea = document.getElementById(sAreaToSlide);
	this.iSpeed = iSlideSpeed || 10;
	this.SlideSize = iSizeToSlide;
	this.iInterval;
	this.iCurrentWindow = 0;
	this.iLimitToSlide;
	this.iCurrentPxPosition;
	/**
	 * This function will move the slider one position to the left 
	 */
	this.MovePositionLeft = function(){
		this.iLimitToSlide=this.SlideSize * (this.iCurrentWindow + 1);
		this.GenerateAnimation();
		this.iCurrentWindow+= 1;
	};
	/**
	 * This function will move the slider one position to the right
	 */
	this.MovePositionRight = function(){
		this.iLimitToSlide=this.SlideSize * (this.iCurrentWindow  - 1);
		this.GenerateAnimation();
		this.iCurrentWindow-= 1;
	};
	/**
	 * This function will move the slider n positions to the left
	 * 
	 * @param iNumberOfPosition = an integer that defines the position in which the slider will be move
	 */
	this.MovePositions= function (iNumberOfPosition){
		this.iLimitToSlide=this.SlideSize * iNumberOfPosition;
		this.GenerateAnimation();
		this.iCurrentWindow=iNumberOfPosition;
	};
	/**
	 * This function will set up a interval to animate the movement of the slider
	 * 
	 */
	this.GenerateAnimation = function (){
	  this.iCurrentPxPosition = this.iCurrentWindow * this.SlideSize;
	  var oOptions = {
	  		iLimitToSlide:this.iLimitToSlide,
	  		iCurrentPxPosition:this.iCurrentPxPosition,
	  		iSpeed:this.iSpeed,
	  		oSlideArea:this.oSlideArea,
	  		sAttribute:"tclAux"
	  	};
		this.CreateAttribute(oOptions.sAttribute,setInterval(function(){
			tclMove(oOptions);
		},1));
	};
	/**
	*
	*/
	this.CreateAttribute = function(sAttr,sVal){
		var oNewAttribute = document.createAttribute(sAttr);
		oNewAttribute.nodeValue=sVal;
		this.oSlideArea.setAttributeNode(oNewAttribute);
	};
}

/**
 * This class will manage the functionality of the buttons
 * 
 * @param oCLassesEnabled: its an optional struct that defines the classes that will give an status of enable to a button 
 * @param oClassesDisabled: its an optional struct that defines the classes that will give an status of disabled to a button 
 * @returns void
 */
function tclButtonBehaviour(aCLassesEnabled,aClassesDisabled){
	if (!aClassesEnabled){
		var aClassesEnabled=["tclTextCorpColor1","tclCorpColor3",""];
	}
	if (!aClassesDisabled){
		var aClassesDisabled=["tclTextCorpColor5","tclCorpColor4","tclDisabled"];
	}
	this.oCEnabled=aClassesEnabled;
	this.oCDisabled=aClassesDisabled;
	/**
	 * 
	 */
	this.disableButton=function(sElement){
		var oElement = (typeof sElement=="string") ? document.getElementById(sElement) : sElement;
		var classTest;
		for (var i=0; (classTest=this.oCEnabled[i]) != null;i++){
			tclAuxiliary.removeClass(oElement,classTest);
			tclAuxiliary.addClass(oElement,this.oCDisabled[i]);
		}
	};
	/**
	 * 
	 */
	this.enableButton=function(sElement){
		var oElement = (typeof sElement=="string") ? document.getElementById(sElement) : sElement;
		var classTest;
		for (var i=0; (classTest=this.oCDisabled[i]) != null;i++){
			tclAuxiliary.removeClass(oElement,classTest);
			tclAuxiliary.addClass(oElement,this.oCEnabled[i]);
		}
	};
}
/**
 * this class will allow the manipulation of elements inside a select
 * @param mainSelect
 * @returns {listBehaviour}
 */
function tclListBehaviour(mainSelect){
	this.mSelect=(typeof mainSelect=="string") ? document.getElementById(mainSelect) : mainSelect;
	this.singleSelected;
	this.aOptions;
	var that=this;
	var aHiddenElements=[];
	/**
	 * this function will Hide or make appear an option inside the select object
	 * 
	 *  @param sElementToHide: the string containing the element to hide.
	 */
	this.hideElementInSelect = function(sElementToHide){
		var bWasFound=false;
		var iCounter=0;
		getCurrentOptions();
		while (!bWasFound){
			if (iCounter<this.aOptions.length){
				if (this.aOptions[iCounter].value==sElementToHide){
					addToHidden(this.aOptions[iCounter]);
					this.mSelect.remove(iCounter);
					bWasFound=true;
				}
			}
			if (!bWasFound && iCounter<aHiddenElements.length){
				if (aHiddenElements[iCounter].value==sElementToHide){
					this.mSelect.add(aHiddenElements[iCounter],null);
					deleteFromHidden(aHiddenElements[iCounter]);
					bWasFound=true;
				}
			}
			iCounter++;
		}
	};
	/**
	 * this  
	 */
	this.hideEverything = function(){
		getCurrentOptions();
		while (this.aOptions[0]!=undefined){
			addToHidden(this.aOptions[0]);
			this.mSelect.remove(0);
		}
	};
	/**
	 * 
	 */
	this.showEverything = function(){
		while (aHiddenElements[0]!=undefined){
			this.mSelect.add(aHiddenElements[0],null);
			deleteFromHidden(aHiddenElements[0]);
		}
	};
	/**
	 * 
	 */
	this.displayHideSingle = function(valueToAppear,sDisplay){
		this.hideElementInSelect(valueToAppear);
	};
	/**
	 * 
	 */
	this.selectAllElementsInList = function(){
		var oOption;
		getCurrentOptions();
		for (var i=0; (oOption=this.aOptions[i])!=null; i++){
				oOption.selected=true;
		}
	};
	/**
	 * 
	 * @returns an array with all current options in select
	 */
	var getCurrentOptions = function(){
		that.aOptions=that.mSelect.getElementsByTagName("option");
	};
	/**
	 * this function will add an element to the hidden array variable
	 * @param oOption: option to be added in array
	 * @returns void
	 */
	var addToHidden = function(oOption){
		var iSize=aHiddenElements.length;
		var oClone = document.createElement("option");
		oClone.value=oOption.value;
		oClone.text=oOption.text;
		aHiddenElements.splice(iSize,0,oClone);
	};
	/**
	 * this function will delete a given element from 
	 * @param oOption: option to be deleted from array
	 * @returns void
	 */
	var deleteFromHidden = function(oOption){
		var iIndexOfItem=aHiddenElements.indexOf(oOption);
		aHiddenElements.splice(iIndexOfItem, 1);
	};
}

/**
 * This function will perform the movement animation that will be displayed
 * 
 * @param iCurrentPxPosition = the current position in pixels
 */
function tclMove (oOptions){
	if (oOptions.iLimitToSlide>oOptions.iCurrentPxPosition){
		oOptions.iCurrentPxPosition+=oOptions.iSpeed;
		if (oOptions.iCurrentPxPosition>oOptions.iLimitToSlide){
			oOptions.iCurrentPxPosition=oOptions.iLimitToSlide;
		}
	} else if (oOptions.iLimitToSlide<oOptions.iCurrentPxPosition){
		oOptions.iCurrentPxPosition-=oOptions.iSpeed;
		if (oOptions.iCurrentPxPosition<oOptions.iLimitToSlide){
			oOptions.iCurrentPxPosition=oOptions.iLimitToSlide;
		}
	} else if (oOptions.iLimitToSlide==oOptions.iCurrentPxPosition){
		clearInterval(oOptions.oSlideArea.getAttribute(oOptions.sAttribute));
		oOptions.oSlideArea.removeAttribute(oOptions.sAttribute);
	}
	oOptions.oSlideArea.style.left = oOptions.iCurrentPxPosition *(-1);
}

/**
 * this function will reload the new created element in the grid
 * @param sIndex:the index of the row you whish to reload;
 */
function reloadElementWithSelection(sIndex){
	var imputToChange=document.getElementById("form[GRID_OF_TABLES]["+sIndex.toString()+"][LINK_TO_SELECTED_WORKSPACE]");
	imputToChange.value=document.getElementById("form[WORKSPACES_ORIGIN]").value;
	tclAuxiliary.executeEvent(imputToChange,"change");
}