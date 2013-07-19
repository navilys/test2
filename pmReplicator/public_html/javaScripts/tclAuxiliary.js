var tclAuxiliary = function (){
	
	var additionalObjectData={};
	additionalObjectData.domData=[];
	/**
	 * 
	 */
	var insertElemntInAdditionalData=function(oDom,sDataType,oValue,position){
		var iPosition = searchDom(oDom);
		var iElementId = (iPosition==-1) ? additionalObjectData.domData.length : iPosition;
		if (additionalObjectData.domData[iElementId]){
			if (position===undefined)
				position = additionalObjectData.domData[iElementId][sDataType].length;
			additionalObjectData.domData[iElementId][sDataType][position]=oValue;
		}else
		  createNewStruct(additionalObjectData.domData,iElementId,oDom,sDataType,oValue,0);
		return additionalObjectData.domData[iElementId][sDataType].length;
	};
	var searchDom = function(oDom){
		if (additionalObjectData.domData.length>0){
			var iCounter=0;
			var bFound=false;
			var bFinish=false;
			while (!bFound && !bFinish){
				bFound=(additionalObjectData.domData[iCounter].dom===oDom);
				iCounter++;
				bFinish=(iCounter==additionalObjectData.domData.length);
			}
			if (bFound)
				return --iCounter;
		}
		return -1;
	};
	var deleteDataFromStructure=function(oDom,sTypeData,sDataPosition){
		var iPosition = searchDom(oDom);
		if (iPosition!=-1){
			additionalObjectData.domData[iPosition][sTypeData].splice(sDataPosition,1);
		}
	};
	var createNewStruct= function(aCurrentElement,iElementId,oDom,sDataType,oValue,position){
		aCurrentElement[iElementId]=[];
		aCurrentElement[iElementId]["dom"]=oDom;
		aCurrentElement[iElementId][sDataType]=[];
		aCurrentElement[iElementId][sDataType][position]=oValue;
	};
	var oUtilities = {
			datesDifference:function (sStarDate, sEndDate, sFormat){
				var oStarDate = this.convertToDate(sStarDate,sFormat);
				var oEndDate = this.convertToDate(sEndDate,sFormat);
				var iDifference = oStarDate.getTime() - oEndDate.getTime();
				//in days
				iDifference = (iDifference/(86400*1000));
				return  iDifference;
			},
			//converts a string to date, may also recieve a format
			convertToDate:function (sDate, sFormat){
				//get Year
				var sYearFormat = sFormat.replace(/[^y]/g,'*');
				var sYear = this.extractString(sDate,sYearFormat);
				//get Month
				var sMonthFormat = sFormat.replace(/[^m]/g,'*');
				var sMonth = this.extractString(sDate,sMonthFormat);
				//get Day
				var sDayFormat = sFormat.replace(/[^d]/g,'*');
				var sDay = this.extractString(sDate,sDayFormat);
				//validate if no problem was found when formating date
				if(!(sYear && sMonth && sDay)){
					return false;
				}
				//create date, months start in 0 zero for january
				var oDateObject = new Date(sYear, parseFloat(sMonth)-1, sDay);
				return oDateObject;
			},
			//extracts a string from a mask
			extractString:function (sString, sMask){
				//string to be returned
				var sStringReturned = "";
				//validates the string an format have the same length
				if(sString.length != sMask.length){
					return false;
				}
				//transform strings in arrays
				aMask = sMask.split('');
				aString = sString.split('');
				//navigates mask array
				for(var iCount = 0; aMask.length > iCount; iCount++){
					//if * is not in character position, character from string is stored
					if(aMask[iCount] != "*"){
						sStringReturned += aString[iCount];
					}
				}
				return sStringReturned;
			},
			//trims leading and trailing whitespaces on a string
			trimString:function (sString)
			{
				return sString.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			},
			//extracts a string from a mask
			deleteGridRows:function (sGrid, sField){
				//get rows count
				iRows = Number_Rows_Grid(sGrid,sField);
				//delete always the second row
				for(var iRow = 1; iRows > iRow; iRow++){
			  	try{
			  	getObject(sGrid).deleteGridRow('[2]', true);
			  	}catch(err){
						//alert(err);
					}
			 	}
			},
			//converts integer day of week to literal
			dayOfWeekLiteral:function (iDay){
				//array that stores days of week
				var aDaysLiteral=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
				if(iDay>=0 && iDay<8){
					return aDaysLiteral[iDay];
				}else{
					return false;
				}
			},
			//change a field from edit mode to view
			changeToView:function (oField){
				oField.style.display='none';
				var newT = document.createElement("span");
			  if(oField.nodeName=='SELECT'){
			  	//var newT = document.createTextNode(oField.options[oField.selectedIndex].innerHTML);
			  	newT.innerHTML=oField.options[oField.selectedIndex].innerHTML;
			  	//oField.parentNode.appendChild(newT);
			  	///return;
			  }
			  else{
			  	//var newT = document.createTextNode(sText1 + oField.value + sText2);
			  	newT.innerHTML=oField.value;
			  }
			  var i = 0;
			  try{
			    for(i=0;i<oField.parentNode.childNodes.length+1; i++){
			    	if (oField.parentNode.childNodes[i].type == 'span'){
			      	oField.parentNode.removeChild(oField.parentNode.childNodes[i]);
				  	}
			 	  }
			 	}
			 	catch(e){
			 		if(oField.parentNode.childNodes[i-1].type!="text" && oField.parentNode.childNodes[i-1].type!="hidden")
			 	  	oField.parentNode.removeChild(oField.parentNode.childNodes[i-1]);
			 	}
			  oField.parentNode.appendChild(newT);
			},
			/**
			 * This function binds a click event to a given dom element
			 * 
			 * @param sElement: a string with the name of the dom to attach the event.
			 * @param oAction: the action to be executed when the click function is trigered.
			 */
			tclBindClick:function (sElement,fAction){
				var oElement = (typeof sElement=="string") ? document.getElementById(sElement) : sElement;
				var aElements= (oElement.tagName!=undefined) ? Array(oElement):oElement;
				var oSingleElement;
				var aFunctionIds=[];
				for (var i=0;(oSingleElement=aElements[i])!=undefined;i++){
				    if (oSingleElement.addEventListener){
				    	oSingleElement.addEventListener('click',fAction);
					} else if (oSingleElement.attachEvent) {
						var oContextElement=oSingleElement;
						oSingleElement.attachEvent('onclick',function(){
								fAction.call(oContextElement);
							});
					}
				    aFunctionIds[aFunctionIds.length]=insertElemntInAdditionalData(oSingleElement,"events",fAction) -1;
				}
				return aFunctionIds;
			},
			/**
			 * 
			 * @param sEvent
			 * @param sElement
			 * @param fAction
			 */
			bindEvent:function(sEvent,sElement,fAction){
				var oElement = (typeof sElement=="string") ? document.getElementById(sElement) : sElement;
				var aElements= (oElement.tagName!=undefined) ? Array(oElement): (typeof oElement=="object")? oElement : Array(oElement);
				var oSingleElement;
				var oEvent;
				var aFunctionIds=[];
				for (var i=0;(oSingleElement=aElements[i])!=undefined;i++){
				    if (oSingleElement.addEventListener){
				    	oEvent=oSingleElement.addEventListener(sEvent,fAction,false);
					} else if (oSingleElement.attachEvent) { 
						var oContextElement=oSingleElement;
						oEvent=oSingleElement.attachEvent('on'+sEvent,function(){
							 fAction.call(oContextElement);
						});
					}
				    aFunctionIds[aFunctionIds.length]=insertElemntInAdditionalData(oSingleElement,"events",fAction) - 1;
				}
				return aFunctionIds;
			},
			/**
			 * This function will return an array containing all the elements that match a given class
			 * 
			 * @param className : an string that defines the class name to be fetched
			 * @returns an array with the result
			 */
			getClassesCompatible:function (sClassName,oDomToSearch){
				oDomParent =  oDomToSearch || document;
				if (document.getElementsByClassName==undefined){
					var hasClass=new RegExp('(\\s|^)' + sClassName + '(\\s|$)');
					var aAllElements = oDomParent.getElementsByTagName("*");
					var result=[];
					var element;
					for (var i=0; (element=aAllElements[i]) != null; i++){
						var sAuxClass=element.sClassName;
						if (sAuxClass && sAuxClass.indexOf(sClassName) != -1 && hasClass.test(sAuxClass)){
							result.push(element);
						}
					}
					return result;
				} else {
					return oDomParent.getElementsByClassName(sClassName);
				}
			},
			/**
			 * This function will fire any event
			 * 
			 * @param oDom: the dom object to be fired;
			 * @param sEvent: an string with then name of the event;
			 * @returns
			 */
			executeEvent:function(oDom,sEvent){
				   //IE
				   if(oDom.fireEvent){
				       return oDom.fireEvent("on"+sEvent);
				   }
				   //OTHERS
				   if(document.createEvent){
				       var evt = document.createEvent('HTMLEvents');
				     if(evt.initEvent){
				         evt.initEvent(sEvent, true, true);
				     }            
				     if(oDom.dispatchEvent){
				         return oDom.dispatchEvent(evt);
				     }
				   }
				},
			/**
			 * this function will allow the creati—n of any Attribute to given dom
			 * @param oDom = the dom object
			 * @param sAttr = an string with the new attribute name
			 * @param sValue = an string with the value
			 */
			createAttribute:function (oDom,sAttr,sValue){
				var oNewAttribute = document.createAttribute(sAttr);
				oNewAttribute.nodeValue=sValue;
				oDom.setAttributeNode(oNewAttribute);
			},
			/**
			 * 
			 */
			addClass: function(dom,classToAdd){
				var dom = (typeof dom=="string") ? document.getElementById(dom) : dom;
				if (!this.hasClass(dom,classToAdd)) dom.className += " "+classToAdd;
			},
			/**
			 * 
			 */
			hasClass: function(dom,classToSearch){
				var dom = (typeof dom=="string") ? document.getElementById(dom) : dom;
				return dom.className.match(new RegExp('(\\s|^)'+classToSearch+'(\\s|$)'));
			},
			/**
			 * 
			 */
			removeClass: function(dom,classToSearch){
				var dom = (typeof dom=="string") ? document.getElementById(dom) : dom;
				if(this.hasClass(dom,classToSearch)){
					var reg = new RegExp('(\\s|^)'+classToSearch+'(\\s|$)');
					dom.className=dom.className.replace(reg,' ');
				}
			},
			/**
			 * this function will return an array with all the selected items value in the selectbox
			 * @param string or dom object sDomId :the dom or id name of the selectbox
			 * @returns {Array} with selected values;
			 */
			getSelectedOptions: function (sDomId){
				var oDomId=(typeof sDomId=="string") ? document.getElementById(sDomId):sDomId;
				var aElements=oDomId.getElementsByTagName("option");
				var aResult = new Array();
				var oOption;
				for (var i=0;(oOption=aElements[i])!=undefined;i++){
					if (oOption.selected){
						aResult.push(oOption);
					}
				}
				return aResult; 
			},
			/**
			 * 
			 * @param target
			 */
			disableSelection: function (target){
				if (typeof target.onselectstart!="undefined")
					target.onselectstart=function(){return false;};
				else if (typeof target.style.MozUserSelect!="undefined")
					target.style.MozUserSelect="none";
				else 
					target.onmousedown=function(){return false;};
				target.style.cursor = "default";
			},
			/**
			 * 
			 * @param oDom
			 * @param sEventNumber
			 */
			unbindEvent:function(event,oDom,sEventNumber){
				var iPosition = searchDom(oDom);
				if (iPosition!=-1){
					var func=additionalObjectData.domData[iPosition]["events"][sEventNumber];
					if (oDom.removeEventListener)
						oDom.removeEventListener(event,func,false);
					else
						oDom.detachEvent('on'+event,func);
					deleteDataFromStructure(oDom,"events",sEventNumber);
				}
			},
			/**
			 * 
			 * @param sUrl
			 * @param oData
			 * @param fToExecute
			 * @param sMethod
			 */
			ajaxCallJson:function(sUrl,oData,fToExecute,sMethod){
				var sNewMethod=sMethod || "GET";
				var sSendData=this.transformAnobjectToStringLine(oData,"&");
				var oAjaxRequest= (window.XMLHttpRequest()) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
				var sNewUrl=sNewMethod==="GET" ? sUrl+"?"+ sSendData: sUrl;
				var sPostParams=null;
				oAjaxRequest.open(sMethod, sNewUrl, true);
				if (sNewMethod==="POST"){
					oAjaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					oAjaxRequest.setRequestHeader("Content-length",sSendData.length);
					oAjaxRequest.setRequestHeader("Connection", "close");
					sPostParams=sSendData;
				}
				oAjaxRequest.onreadystatechange = function(){
					if (oAjaxRequest.readyState==4 || oAjaxRequest.readyState==200){
						var oResp=JSON.parse(oAjaxRequest.responseText)
						fToExecute(oResp);
					}
				};
				oAjaxRequest.send(sPostParams);
			},
			/**
			 * 
			 * @param oData
			 * @param glue
			 * @returns
			 */
			transformAnobjectToStringLine:function(oData,glue){
				var aNewData = [];
				var counter = 0;
				var property;
				for (property in oData){
					if (typeof oData[property]!=='function' && property !="isObject" && property !="isArray"){
						aNewData[counter]=property+"="+oData[property];
						counter++;
					}
				}
				return aNewData.join(glue);
			},
			/**
			 * 
			 * @param oElement
			 * @param oData
			 */
			fillDropBox:function(oElement,oData){
				oElement=(typeof oElement=="string") ? document.getElementById(oElement):oElement;
				oElement.innerHTML="";
				var proeprty;
				for (property in oData){
					if (typeof oData[property]!=='function' && property !="isObject" && property !="isArray"){
						var newOption=document.createElement("option");
						newOption.value=oData[property][0];
						newOption.innerHTML=oData[property][1];
						oElement.appendChild(newOption);
					}
				}
			}
		};
	return oUtilities;
}();
