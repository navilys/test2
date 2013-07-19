var additionalFunc = function() {
    var aTxtFieldObject = [];
    var bEventAttached=false;
    var bKeyHolding=false;
    var bShiftHold=false;
    var oElemntsInShift={oFirstElement:null,
                         oLastElement:null};
    /**
     * Given a input name, this function will search for an object
     * related to that field 
     * 
     * @param field string: the input name
     * @param optional aToSearch array of objects: that have
     * @param optional sField string: that contain the tipe of element to search for in the object
     * 
     * @return -1 if nothing was found, the object if it exists in the array.
     */
    var searchFieldInArray = function(field,aToSearch,sField){
        var aToSearch=aToSearch ||aTxtFieldObject;
        var sField = sField || "field";
        if (aToSearch.length>0){
            var oField=-1;
            $.each(aToSearch,function(key,value){
                if (value[sField]==field){
                    oField=value;
                    return false;
                }
            });
            return oField;
        }
        return -1
    };
    /**
     * retrive data From array of objects
     * 
     * @param field string: the input name
     * 
     * @return an empty string if nothing has been found,
     * the string of the found field
     */
    var getDataFromField = function (field){
        var oField=searchFieldInArray(field);
        if (oField!=-1)
            return oField.data;
        return "";
    }
    /**
     * creates an object for the current input element that contains 3 fields
     * field: field name, data:value to be stored, timeOutId:id of the time out
     * process
     * 
     * @param field string: the input name
     * @param data string: data to be stored  
     */
    var setDataFromField = function (field,Data){
        var oField=searchFieldInArray(field);
        if (oField==-1){
            var index=aTxtFieldObject.legth==undefined? 0:aTxtFieldObject.legth;
            aTxtFieldObject[index]={
                    field:field,
                    data:Data,
                    timeOutId:""
            };
        } else
            oField.data=Data;
    };
     /**
     * creates an object for the current input element that contains 3 fields
     * field: field name, data:value to be stored, timeOutId:id of the time out
     * process
     * 
     * @param field string: the input name
     * @param id integer: time out id to be stored
     */
    var setTimeOutIdFromField = function (field,id){
        var oField=searchFieldInArray(field);
        if (oField==-1){
            aTxtFieldObject[aTxtFieldObject.length]={
                    field:field,
                    data:"",
                    timeOutId:id
            }
        } else
            oField.timeOutId=id;
    };
    /**
     * retrive time out id From array of objects
     * 
     * @param field string: the input name to be search
     * 
     * @return an empty string if nothing has been found,
     * the integer of the found field
     */
    var getTimeOutId = function (field){
        var oField=searchFieldInArray(field);
        if (oField!=-1)
            return oField.timeOutId;
        return "";
    };
    /**
     * Create an ajax call
     * 
     * @param sSource string: the url string location 
     * @param aoDato array of objects: the array of objects that will be sendit to the server
     * @param fnCallback funcion: a function that will be called once the ajax process is complete
     */
    var setPostAjaxCall = function(sSource, aoData, fnCallback){
        $.post(sSource, aoData, fnCallback, "json");
    };
    /**
     * Creates a delay of 600 ms in order to allow the user to type in a input text field.
     * before starting the array
     * 
     * @param sSource string: the url string location 
     * @param aoDato array of objects: the array of objects that will be sendit to the server
     * @param fnCallback funcion: a function that will be called once the ajax process is complete 
     */
    var delaySearch = function(sSource, aoData, fnCallback,sControl){
        window.clearTimeout(getTimeOutId(sControl));
        var timeOutID = window.setTimeout(function(){
           setPostAjaxCall(sSource, aoData, fnCallback); 
        }, 600);
        setTimeOutIdFromField(sControl,timeOutID);
    };
    /**
     * finds an element position inside an array
     * 
     * @param aSearch array: array you want to search element in
     * @param itemToFind: the element you want to search in the array of objects
     * 
     * @return an integer with the element position, if no element is found a -1 will be returned 
     */
    var getItemPositionInArray = function(aSearch,itemToFind){
        var position=-1;
        $.each(aSearch,function(sKey,sValue){
            if (sValue.name==itemToFind){
                position=sKey;
                return false;
            }
        });
        return position;
    }
    /**
     * inserts an object element or edit an existant one, in a given array of obgjects
     * 
     * @param aoData array of objects: array you want edit or insert an element in
     * @param sField string: name of the field to be created / updated
     * @param sValue string: the value to be stored.
     */
    var setKey = function(aoData,sField,sValue){
        var iItemPosition=getItemPositionInArray(aoData,sField);
        var iPosition=iItemPosition!=-1 ? iItemPosition : aoData.length;
        var aoNewElement=new Array();
        sValue=(sValue instanceof Array) ? JSON.stringify(transFormArrayToObject(sValue)):sValue;
        aoNewElement[iPosition]={
            name:sField,
            value:sValue
        };
        $.extend(true,aoData,aoNewElement);
    };
     /**
     * gets the object element from a given array of objects
     * 
     * @param aoData array of objects: array you want to search in
     * @param sField string: name of the field to be found
     * 
     * @return the value of the object, null if nothing has been found
     */
    var getKey = function(aoData,sField){
        var iItemPosition=getItemPositionInArray(aoData,sField);
        return iItemPosition!=-1 ? aoData[iItemPosition].value:null;
    };
    /**
     * A recursive function that transform any multi array into an object.
     * 
     * @param aArrayToTransform array: multi array that will be transformed
     * 
     * @return object
     */
    var transFormArrayToObject=function(aArrayToTransform){
        var aResult={};
        $.each(aArrayToTransform,function(iKey,oData){
            aResult[iKey]=(oData instanceof Array) ? transFormArrayToObject(oData):oData;
        });
        return aResult;
    };
    /**
     * auxiliar funtion.
     */
    var additionalDataManipulation = function (aoData,sTableName){
        switch(sTableName){
            case "dynProcessO":
                setKey(aoData,"sTableName",sTableName);
            break;
            default:
                    var oSelectArea=$("#"+sTableName).parent().find("div.workspaceFilterArea select");
                    setKey(aoData,"sTableName",sTableName);
                    if (oSelectArea.length>0){
                        setKey(aoData,"sWorkspace",oSelectArea.val());
                    }else if (typeof replicatorDefaultData!='undefined'){
                        setKey(aoData,"sWorkspace",replicatorDefaultData.sWorkspace);
                    }
                break;
        }
        // add more stuff onece all tables are created;
    };
    /**
     * Row Click event that enables rows to be clickable. 
     * 
     * @param oTableScope object: the dom element that represent the DataTable in
     * which we want to enable click behaviour
     */
    var rowClick = function (oTableScope){
        keyDetection();
        oTableScope.$('tr').click(function(event){
                removeAllSelected(oTableScope);
                shiftSelection(this,oTableScope);
            });
    };
    var removeAllSelected=function(oTableScope){
        if (!bKeyHolding){
            oTableScope.$('tr.row_selected').each(function(){
                        $(this).removeClass("row_selected");
                        var data = oTableScope.fnGetData(this);
                        selectorManipulator.removeRow(data,oTableScope.selector,1);
             });
        }
    };
    var selectRow=function(oRow,oTableScope){
       var data = oTableScope.fnGetData(oRow);
       if (!$(oRow).hasClass('row_selected')){
            $(oRow).addClass('row_selected');
            selectorManipulator.addRow(data,oTableScope.selector,1);
       }else{
           $(oRow).removeClass('row_selected');
           selectorManipulator.removeRow(data,oTableScope.selector,1);
       }
       
    };
    var shiftSelection=function(oRow,oTableScope){
       if (bShiftHold){
           if ($(oRow).index()<$(oElemntsInShift.oFirstElement).index()){
               oElemntsInShift.oLastElement=oElemntsInShift.oFirstElement;
               oElemntsInShift.oFirstElement=oRow;
           }else{
               oElemntsInShift.oLastElement=oRow;
           }
           $(oElemntsInShift.oFirstElement).
               nextUntil(oElemntsInShift.oLastElement).
               add(oRow).
               each(function(){
                selectRow(this,oTableScope);
           });
       }else{
           selectRow(oRow,oTableScope);
           oElemntsInShift.oFirstElement=oRow;
       }
    }
    /**
     * functionality that will allow the row clicked to be rememberd
     * 
     * @param oTableScope object:the dom element that represent the DataTable in
     * which we want to enable the select memory behaviour
     */
    var selectionMemory=function(oTableScope){
        oTableScope.$('tr').each(function(){
           var aData=oTableScope.fnGetData(this);
           if (selectorManipulator.existsCompleteRow(aData,oTableScope.selector,1))
               $(this).addClass('row_selected');
        });
    };
    /**
     * Bind an event thar will learn if shift, control or command is pressed
     */
    var keyDetection=function(){ 
       if (bEventAttached==false){
            $(document).bind('keydown',function(e){
                if (e.shiftKey || e.ctrlKey || e.metaKey){
                    bKeyHolding=true;
                    bShiftHold=e.shiftKey 
                }    
            }).bind('keyup',function(e){
                if (e.keyCode==16 || e.keyCode==17 || e.keyCode==224){
                    bKeyHolding=false;
                    bShiftHold=false;
                }   
            });
            bEventAttached=true;
        }
    }
    /**
     * Extends data table functionality, sending data manipulation and adding functionality
     */
    var generalControlls = {
       controlledCall : function (sSource, aoData, fnCallback,table){
            var aoAuxData=selectorManipulator.getAllSendedDataFromTable(this.selector);
            /*console.log(aoAuxData);
            selectorManipulator.testArray();*/
            if (aoAuxData!=-1 && aoAuxData.length>0)
                setKey(aoData,"aSelectedData",aoAuxData);
            var searchControl = $('input',table.aanFeatures.f);
            additionalDataManipulation(aoData,searchControl.attr("aria-controls"));
            if (getDataFromField(searchControl.attr("aria-controls")) != searchControl.val()){
                setDataFromField(searchControl.attr("aria-controls"),searchControl.val());
                delaySearch(sSource, aoData, fnCallback,searchControl.attr("aria-controls"));
            }else{
                setPostAjaxCall(sSource, aoData, fnCallback);
            }
        },
       addExtraFunctionalityToTables:function(){
           rowClick(this);
           selectionMemory(this);
       },
       addSelectFiltering:function(oData){
           var oTable=this;
           var oSelectArea=oTable.parent().find("div.workspaceFilterArea");
           var workspaceSelect=$('<select name="workspaces"></select>').ComboBoxLoad({
               sUrl:"ajaxDynLoadCombo.php",
               sLoad:"workspace"
           }).bind("change",function(){
               oTable.fnDraw();
           });
           oSelectArea.append(workspaceSelect);
       }
    }
    return generalControlls;   
}();