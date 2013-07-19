/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var formDataController={
/**
 * this function will retrive data for a given data table
 * 
 * @param sDataTableName string: data table naem
 * 
 * @return a data table formated array
 */
  getDataFromDataTable:function(sDataTableName){
      var oDataTable=$(sDataTableName).DataTable();
      var aRows=[];
      if (oDataTable.length>0){
        var aDomRows=oDataTable.fnGetNodes();
        $.each(aDomRows,function(iKey,oRow){
            var aoInputs=($(oRow).find('input').length > 0) ? $(oRow).find('input') : $(oRow).find('select');
            if (aoInputs.length==0){
                aRows.push(oDataTable.fnGetData(oRow));
            }else{
                var oCheckBoxes=formDataController.getCheckOptions(aoInputs.serializeArray());
                var oRowToPush=formDataController.cutArrayAndAddElement(oDataTable.fnGetData(oRow),2,oCheckBoxes);
                aRows.push(oRowToPush);
            }
        });
      }
      return aRows;
  },
  /**
   * Will give data table rows, a format in order to be parsed as json
   * 
   * @param sDataTableName string: Name of tha data table.
   * 
   * @return the formated object
   */
  getDataFormated:function(sDataTableName,bGetFromSelection){
      bGetFromSelection = bGetFromSelection || false;
      var aData= (!bGetFromSelection) ? 
          formDataController.getDataFromDataTable(sDataTableName):
          selectorManipulator.getAllDataInSelectBuffer(sDataTableName);
      var oResData={};
      $.each(aData,function(iKey,aValue){
          if (aValue[0] in oResData)
            oResData[aValue[0]].push(aValue.slice(1));
          else
            oResData[aValue[0]]=[aValue.slice(1)];
      });
      return oResData;
  },
  /**
   * this function gater data from table in a single array
   *
   * @param sDataTableName string: Name of tha data table.
   * 
   * @return a single array with all the key values. 
   */
  getDataFormatedSingleArray:function(sDataTableName){
      var aData=formDataController.getDataFromDataTable(sDataTableName);
      var arrayResult=[];
      $.each(aData,function(iKey,aValue){
         arrayResult[iKey]=aValue[0]; 
      });
      return arrayResult;
  },
  /**
   * will transform aditional inputs into a object containing the 
   * actual value.
   * 
   * @aoValue array of objects: the aditional inputs that need to be 
   * 
   * @return the formated object
   */
  getCheckOptions:function(aoValue){
      var oElemnts={}
      $.each(aoValue,function(iKey,oValue){
          oElemnts[oValue.name]=oValue.value;      
      });
      return oElemnts;
  },
  /**
   * This function will add elements inside an array
   *
   * @param aArrayToCut array: the array we need to append somthing in
   * @param iPosition integer: the position in which you need to append the elemnts
   * @param oElementToAdd object: the elements you want to append
   * 
   * @return the complete array;
   */
  cutArrayAndAddElement:function(aArrayToCut,iPosition,oElementToAdd){
      var spliceArray=aArrayToCut.slice(0,iPosition);
      spliceArray.push(oElementToAdd);
      return spliceArray;
  },
};
