/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*var structure={
    [{
      sTable:"",
      aoData:[],
      bPased:false
    }]
};*/
var selectorManipulator = function (){
    var aoSelecAlm=[];
    var createNewTable= function(aData,sTable){
        var oNewTable={
          sTable:sTable,
          aoData:[{
                  aData:aData,
                  bIsSend:false
           }]
        };
        return oNewTable;
    };
    var searchIfValueExists=function(oElement,sKeyValue,iKey){
        var iIndex=-1;
        $.each(oElement.aoData,function(index,aValue){
            if (aValue.aData[iKey]==sKeyValue){
                iIndex=index;
                return false;
            }
        });
        return iIndex;
    };
    var searchForTable = function(sTableToSearch){
       var iIndex=-1;
       $.each(aoSelecAlm,function(index,oValue){
           if (oValue.sTable==sTableToSearch){
               iIndex=index;
                return false;
           }
       });
       return iIndex;
    };
    var addElement = function(aArrayToAdd,sTable,sKey){
        var iIndex=searchForTable(sTable);
        if (iIndex==-1)
           aoSelecAlm[aoSelecAlm.length]=createNewTable(aArrayToAdd,sTable);
        else{
            var iIndexElemnt = searchIfValueExists(aoSelecAlm[iIndex],aArrayToAdd[sKey],sKey);
            if (iIndexElemnt==-1)
                aoSelecAlm[iIndex].aoData[aoSelecAlm[iIndex].aoData.length]={aData:aArrayToAdd,
                                                                             bIsSend:false};
            else 
                return false;
        }
        return true;
    };
    var deleteElement=function(aArrayToDelete,sTable,sKey){
        var iTableIndex=searchForTable(sTable);
        if (iTableIndex!=-1){
            aoSelecAlm[iTableIndex].aoData=$.grep(aoSelecAlm[iTableIndex].aoData,function(oElement){
                return oElement.aData[sKey]!=aArrayToDelete[sKey];
            });
            aoSelecAlm=deleteTables();
            return true;
        }
        return false;
    };
    var deleteTables=function(sTable){
        if (sTable!=undefined){
            return $.grep(aoSelecAlm,function(value){
               return  value.table!=sTable;
            });
        }else{
            return $.grep(aoSelecAlm,function(value){
                return value.aoData.length!=0;
            });
        }
    }
    var findIfCompleteRowExists=function(aArrayToSearch,sTable){
       var iTableIndex=searchForTable(sTable);
       var aoData = [];
       if (iTableIndex!=-1) {
          aoData =  $.grep(aoSelecAlm[iTableIndex].aoData,function(oValue){
              return $(oValue.aData).not(aArrayToSearch).length==0 && $(aArrayToSearch).not(oValue.aData).length==0;
          });
       }
       return (aoData.length==1);      
    };
    var manipulator ={
        addRow:function(aArrayToAdd,sTable,sKey){  
          return addElement(aArrayToAdd,sTable,sKey);
        },
        removeRow:function(aArrayToRemove,sTable,sKey){
          return deleteElement(aArrayToRemove,sTable,sKey);
        },
        existRow:function(aArrayToSearch,sTable,sKey){
           var iTableIndex=searchForTable(sTable);
           if (iTableIndex==-1)
               return iTableIndex;
           return searchIfValueExists(aoSelecAlm[iTableIndex],sKey,aArrayToSearch[sKey]);
        },
        existsCompleteRow:function(aArrayToSearch,sTable){
            return findIfCompleteRowExists(aArrayToSearch,sTable);
        },
        getAllDataFromTable:function(sTable){
            var iTableIndex=searchForTable(sTable);
            if (iTableIndex==-1)
               return iTableIndex; 
            var aResult=[]
            $.each(aoSelecAlm[iTableIndex].aoData,function(sKey,oData){
             if (!oData.bIsSend){
                 aResult[aResult.length]=oData.aData;
                 oData.bIsSend=true;
             }  
            });
            return aResult;
        },
        getAllSendedDataFromTable:function(sTable){
           var iTableIndex=searchForTable(sTable);
            if (iTableIndex==-1)
               return iTableIndex; 
            var aResult=[];
            $.each(aoSelecAlm[iTableIndex].aoData,function(sKey,oData){
             if (oData.bIsSend)
                 aResult[aResult.length]=oData.aData;
            });
            return aResult;
        },
        getAllDataInSelectBuffer:function(sTable){
          var iTableIndex=searchForTable(sTable);
            if (iTableIndex==-1)
               return iTableIndex; 
            var aResult=[];
            $.each(aoSelecAlm[iTableIndex].aoData,function(sKey,oData){
                 aResult[aResult.length]=oData.aData;
            });
            return aResult;
        },
        deleteFromTableARow:function(oTable,sTable){
            var aResultantArray=new Array();
            var aArrayToDelete=oTable.fnGetNodes();
            $.each(aArrayToDelete,function(iKey,oRow){
               if ($(oRow).hasClass('row_selected')){
                   aResultantArray.push(oTable.fnGetData(oRow));
                   oTable.fnDeleteRow(oRow);
               }
                   
            });
            return aResultantArray;
        },
        testArray:function(){
            console.log(aoSelecAlm)
        }
    };
    return manipulator
}();
