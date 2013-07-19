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
                dataToBeSend.process=formDataController.getDataFormated("#processTargetData");
                dataToBeSend.tables=formDataController.getDataFormated("#tablesTargetData");
                dataToBeSend.workspaces=formDataController.getDataFormated("#wrkspaceD");
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
    TableLoader.loadTablesOrigin();
    TableLoader.loadProcessOrigin();
    TableLoader.loadProcessTarget();
    TableLoader.loadTablesTarget();
    TableLoader.loadWorkspacesO();
    TableLoader.loadWorkspacesD();
    $(".tclButton").bind("click",function(){
       var sOTable=$(this).attr("org_table");
       var sDTable=$(this).attr("dest_table");
       
       var oOriginDataTable=$(sOTable).dataTable();
       var oDestDataTable=$(sDTable).dataTable();
       if (oOriginDataTable.fnSettings().oFeatures.bServerSide){
           var aOData=selectorManipulator.getAllDataFromTable(sOTable);
           if (aOData.length>0){
               oOriginDataTable.fnDraw();
               aOData = (sDTable=="#tablesTargetData") ? addCheckBoxToData(aOData):aOData;
               oDestDataTable.fnAddData(aOData);
           } 
       }else{
          var aToDelete=selectorManipulator.deleteFromTableARow(oOriginDataTable,sOTable);
          if (aToDelete.length>0){
              $.each(aToDelete,function(iKey,aRow){
                  selectorManipulator.removeRow(aRow,sOTable,1);
                  selectorManipulator.removeRow(aRow,sDTable,1);
              });
              oDestDataTable.fnDraw();
          }
       }
    });
});
function addCheckBoxToData(aOData){
  var sStrucCheck="<input type='checkbox' name='structure' value='yes' checked />";
  var sDataCheck="<input type='checkbox' name='data' value='yes' checked />";
  $.each(aOData,function(iKey,aRow){
      aRow.push(sStrucCheck);
      aRow.push(sDataCheck);
     aOData[iKey]=aRow; 
  });
  return aOData;
}