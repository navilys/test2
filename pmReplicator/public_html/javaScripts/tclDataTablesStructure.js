var TableLoader = {
    /**
     * Loads Tables with origin data and structure
     */
    loadTablesOrigin: function (){
        var loadColumnsTables = function(){
            return[
                {"sTitle":"Workspace","sClass":"center","sWidth":"25%","bVisible":false},
                {"bVisible":false},
                {"sTitle":"Tables"}
              ]; 
        };
        $("#tablesOrigin").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="tables"></table>');
       return $("#tables").dataTable({
            "sDom":"<'workspaceFilterArea'f>tip",
            "bProcess" : true,
            "bServerSide" : true,
            "sAjaxSource" : "ajaxFunctionality.php",
            "sServerMethod":"POST",
            "bSort":true,
            "bLengthChange":false,
            "sPaginationType":"full_numbers",
            "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
            "fnServerData":additionalFunc.controlledCall,
            "fnInitComplete":additionalFunc.addSelectFiltering,
            "aoColumns":loadColumnsTables()
        });
    },
    /**
     * Loads Process with origin data and structure
     */
    loadProcessOrigin: function(){
        var loadColumnsProcess =function(){
            return [
                    {"sTitle":"Workspace","sClass":"center","sWidth":"25%","bVisible":false},
                    {"bVisible":false},
                    {"sTitle":"Process"}
                ];
        };
       $("#processOrigin").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="process"></table>');
       return $("#process").dataTable({
            "sDom":"<'workspaceFilterArea'f>tip",
            "bProcess" : true,
            "bServerSide" : true,
            "sAjaxSource" : "ajaxFunctionality.php",
            "sServerMethod":"POST",
            "bSort":true,
            "bLengthChange":false,
            "sPaginationType":"full_numbers",
            "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
            "fnServerData":additionalFunc.controlledCall,
            "fnInitComplete":additionalFunc.addSelectFiltering,
            "aoColumns":loadColumnsProcess()
        });
    },
    loadProcessTarget:function(){
        var loadColumnsProcess =function(){
             return [
                     {"sTitle":"Workspace","sName":"WORKSPACE","sClass":"center","sWidth":"25%","bVisible":false},
                     {"bVisible":false,"sName":"S.CON_ID","sClass":"tclkey"},
                     {"sTitle":"Process","sName":"CON_VALUE"}
                 ];
         };
        $("#processTarget").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="processTargetData"></table>');
        return $("#processTargetData").dataTable({
              "bProcess" : true,
              "bSort":true,
              "bLengthChange":false,
              "sPaginationType":"full_numbers",
              "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
              "aoColumns":loadColumnsProcess()
        });
    },
    loadTablesTarget:function(){
        var loadColumns =function(){
             return [
                     {"sTitle":"Workspace","sClass":"center","sWidth":"25%","bVisible":false},
                     {"bVisible":false},
                     {"sTitle":"Tables"},
                     {"sTitle":"S"},
                     {"sTitle":"D"}
                 ];
         };
        $("#tablesTarget").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="tablesTargetData"></table>');
        return $("#tablesTargetData").dataTable({
              "bProcess" : true,
              "bSort":true,
              "bLengthChange":false,
              "sPaginationType":"full_numbers",
              "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
              "aoColumns":loadColumns()
        });
    },
    loadWorkspacesO:function(){
        var loadColumns=function(){
          return [{"sTitle":"Workspace"},{"sTitle":"Workspace","bVisible":false}];  
        };
        $("#workspacesO").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="wrkspaceO"></table>');
        return $("#wrkspaceO").dataTable({
           "bProcess" : true,
            "bServerSide" : true,
            "sAjaxSource" : "ajaxFunctionality.php",
            "sServerMethod":"POST",
            "bSort":true,
            "bLengthChange":false,
            "sPaginationType":"full_numbers",
            "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
            "fnServerData":additionalFunc.controlledCall,
            "aoColumns":loadColumns()
        });
    },
    loadWorkspacesD:function(){
         var loadColumns=function(){
          return [{"sTitle":"Workspace"},{"sTitle":"Workspace","bVisible":false}];  
        };
         $("#workspacesD").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="wrkspaceD"></table>');
        return $("#wrkspaceD").dataTable({
              "bProcess" : true,
              "bSort":true,
              "bLengthChange":false,
              "sPaginationType":"full_numbers",
              "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
              "aoColumns":loadColumns()
        });
    },
    loadDynReplicatorO:function(){
        var loadColumns =function(){
             return [
                     {"sTitle":"Workspace","sName":"WORKSPACE","sClass":"center","sWidth":"25%","bVisible":false},
                     {"bVisible":false,"sName":"S.CON_ID","sClass":"tclkey"},
                     {"sTitle":"Process","sName":"CON_VALUE"},
                     {"sTitle":"Dynaforms"}
                 ];
        };
       $("#dynaforms").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="dynaformsO"></table>');
       return $("#dynaformsO").dataTable({
           "sDom":"<'workspaceFilterArea'f>tip",
            "bProcess" : true,
            "bServerSide" : true,
            "sAjaxSource" : "ajaxFunctionality.php",
            "sServerMethod":"POST",
            "bSort":true,
            "bLengthChange":false,
            "sPaginationType":"full_numbers",
            "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
            "fnServerData":additionalFunc.controlledCall,
            "fnInitComplete":additionalFunc.addSelectFiltering,
            "aoColumns":loadColumns()
        });
    },
    loadDynProcessO:function(){
         var loadColumns = function(){
            return[
                {"sTitle":"Workspace","sClass":"center","sWidth":"25%"},
                {"bVisible":false},
                {"sTitle":"Process"}
              ]; 
        };
        $("#dynProcess").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="dynProcessO"></table>');
       return $("#dynProcessO").dataTable({
            "bProcess" : true,
            "bServerSide" : true,
            "sAjaxSource" : "ajaxFunctionality.php",
            "sServerMethod":"POST",
            "bSort":true,
            "bLengthChange":false,
            "sPaginationType":"full_numbers",
            "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
            "fnServerData":additionalFunc.controlledCall,
            "aoColumns":loadColumns()
        });
    },
    loadDynReplicatorD:function(){
        var loadColumns =function(){
             return [
                     {"sTitle":"Workspace","sClass":"center","sWidth":"25%"},
                     {"bVisible":false},
                     {"sTitle":"Process"},
                 ];
         };
        $("#dynaformstarget").html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="dynProcessD"></table>');
        return $("#dynProcessD").dataTable({
              "bProcess" : true,
              "bSort":true,
              "bLengthChange":false,
              "sPaginationType":"full_numbers",
              "fnDrawCallback":additionalFunc.addExtraFunctionalityToTables,
              "aoColumns":loadColumns()
        }); 
         
    }
};

