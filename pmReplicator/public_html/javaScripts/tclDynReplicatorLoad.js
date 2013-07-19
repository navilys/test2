/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    var dataToBeSend={}
    var options = {
        transitionEffect:"slideleft",
        enableFinishButton:false,
        enableAllSteps:true,
        labelFinish:'Transfer',
        onFinish:function(){
           $("#transferSummary").hide();
           $(".tclLoader").show();
           $.post("processReplicatorTransfer.php",dataToBeSend,function(obj){
               $(".tclLoader").hide();
               $("#transferSummary").html(obj);
               $("#transferSummary").show("slow");
           }).fail(function(){
               $(".tclLoader").hide();
               $("#transferSummary").show("slow");
           });
        },
        onShowStep:function(oWiz){
            var sTabName= oWiz.attr('rel');
            if(sTabName==3){
                dataToBeSend.dynaforms=formDataController.getDataFormated("#dynaformsO",true);
                dataToBeSend.workspaces=formDataController.getDataFormated("#dynProcessD");
                $(".tclLoader").show();
                $.post("processReplicatorSummary.php",dataToBeSend,function(result){
                   $(".tclLoader").hide();
                   $("#transferSummary").html(result);
                }).fail(function(){
                   $(".tclLoader").hide();
                });
            }  
        }
    };
    $("#wizard").smartWizard(options);
    TableLoader.loadDynReplicatorO();
    TableLoader.loadDynReplicatorD();
    TableLoader.loadDynProcessO();
    elementChange();
    $(".tclButton").bind("click",function(){
       var sOTable=$(this).attr("org_table");
       var sDTable=$(this).attr("dest_table");
       
       var oOriginDataTable=$(sOTable).dataTable();
       var oDestDataTable=$(sDTable).dataTable();
       if (oOriginDataTable.fnSettings().oFeatures.bServerSide){
           var aOData=selectorManipulator.getAllDataFromTable(sOTable);
           if (aOData.length>0){
               oOriginDataTable.fnDraw();
               if (sDTable=="#dynaformsD")
                   addInput(aOData,oDestDataTable);
               else
                    oDestDataTable.fnAddData(aOData);
           } 
       }else{
          $aToDelete=selectorManipulator.deleteFromTableARow(oOriginDataTable,sOTable);
          if ($aToDelete.length>0){
              $.each($aToDelete,function(iKey,aRow){
                  selectorManipulator.removeRow(aRow,sDTable,1);
                  selectorManipulator.removeRow(aRow,sOTable,1);
              });
              oDestDataTable.fnDraw();
          }
       }
    });
});
/**
* Creates additional select inputs in order to ad them in the row
* 
*  @aOData array: a DataTable formated array with the information of a row
*  @oDestDataTable dom: a dom object of a table in which you want to insert data
*/
function addInput(aOData,oDestDataTable){
  var iRowIndex=oDestDataTable.fnGetNodes().length;
  $('<select></select>').ComboBoxLoad({
      sUrl:"ajaxDynLoadCombo.php",
      sLoad:"workspace"
  },function(){
        var combo=this;
        $.each(aOData,function(iKey,aRow){
          aRow.push("<select name='workspace' row_index="+iRowIndex+">"+combo.html()+"</select>");
          aOData[iKey]=aRow; 
        });
        addProcessSelector(aOData,iRowIndex,combo.val(),oDestDataTable );
  });
}
function addProcessSelector(aOData,iRowIndex,sWorkspace,oDestDataTable){
    $('<select></select>').ComboBoxLoad({
       sUrl:"ajaxDynLoadCombo.php",
       sLoad:"process",
       sWorkspace:sWorkspace
    },function(){
        var combo=this;
        $.each(aOData,function(iKey,aRow){
           aRow.push("<select name='process' row_index="+iRowIndex+">"+combo.html()+"</select>");
           aOData[iKey]=aRow;  
        });
        oDestDataTable.fnAddData(aOData);
    });
}
function elementChange(){
    $("body").on("change","select[name=workspace]",function(){
       var sWorkspace=$(this).val();
       var iRowIndex=$(this).attr("row_index");
       $("<select></select>").ComboBoxLoad({
          sUrl:"ajaxDynLoadCombo.php",
          sLoad:"process",
          sWorkspace:sWorkspace
       },function(){
           var combo=this;
           var selectToChange=$("select[name=process]").filter(function(){
              return $(this).attr("row_index")==iRowIndex;
           });
           selectToChange.html(combo.html());
       });
    });
}