/**
 * pentahoRolesManager.js
 * Library of Javascript functions related to the roles management almost all the function uses ajax requests.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * @package plugins.pentahoreports.javascript
 */

PROCESS_REQUEST_FILE = '../pentahoreports/pentahoRolesAjax';

/**
 * popup a window that renders the new role interface
 */
function newRol() {
  var uri = 'request=newRole';
  currentPopupWindow.remove();
  popupWindow('', '../pentahoreports/pentahoRolesAjax?'+uri, 350, 225);

}

/**
 * generate a popup window with roles list interface for the pentaho roles manager interface
 */
function saveNewRole()
{
  code = $('form[ROL_CODE]').value;
  if(code == '') {
    new leimnud.module.app.alert().make({label: G_STRINGS.ID_ROLES_MSG1});
    return false;
  }

  var uri = 'request=verifyNewRole&code='+code;
  var ajax = AJAX();
  ajax.open("POST", PROCESS_REQUEST_FILE, true);
  ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
  ajax.onreadystatechange = function() {
    if(ajax.readyState == 4) {

      if(eval(ajax.responseText.trim())){

        code = $('form[ROL_CODE]').value;
        var uri = 'request=saveNewRole&code='+code;

          var oRPC = new leimnud.module.rpc.xmlhttp({
            url : PROCESS_REQUEST_FILE,
            args: uri
          });

          oRPC.callback = function(rpc){
           currentPopupWindow.remove();
           popupWindow("Roles Manager","../pentahoreports/pentahoRolesList", 550, 420);

        }.extend(this);
        oRPC.make();
        }
        else{
          new leimnud.module.app.alert().make({label: G_STRINGS.ID_ROLES_MSG2});
          return false;
        }
    }
  }
  ajax.send(uri);

}

/**
 * generate a popup window with the edit role interface for the pentaho roles manager interface
 */

function editRole(ROL_UID)
{
  if(ROL_UID!='00000000000000000000000000000001')
   {
      var uri = 'request=editRole&ROL_UID='+ROL_UID;
      currentPopupWindow.remove();
      popupWindow('', '../pentahoreports/pentahoRolesAjax?'+uri, 350, 225);
   }
   else
   {
      new leimnud.module.app.alert().make({label: G_STRINGS.ID_ROLES_MSG});
   }
}

/**
 * updates a pentaho role and also
 * generate a popup window with the edit role interface for the pentaho roles manager interface
 */
function updateRole(ROL_UID) {
  code = $('form[ROL_CODE]').value;
  if(code == '') {
    new leimnud.module.app.alert().make({label: G_STRINGS.ID_ROLES_MSG1});
    return false;
  }

    var uri = 'request=updateRole&code='+code+'&rol_uid='+ROL_UID;
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url : PROCESS_REQUEST_FILE,
      method: 'POST',
      args: uri
    });

    oRPC.callback = function(rpc){
    currentPopupWindow.remove();
    popupWindow("Roles Manager","../pentahoreports/pentahoRolesList", 550, 420);
  }.extend(this);
  oRPC.make();
};

/**
 * delete a pentaho role and also
 * refresh the user interface with the current roles list
 */
function deleteRole(ROL_UID) {
  currentPopupWindow.remove();
  popupWindow("Roles Manager","../pentahoreports/pentahoRolesList", 550, 420);
  new leimnud.module.app.confirm().make({
    label:G_STRINGS.ID_REMOVE_ROLE,
    action:function() {
      var uri = 'request=deleteRole&ROL_UID='+ROL_UID;

      var oRPC = new leimnud.module.rpc.xmlhttp({
        url : PROCESS_REQUEST_FILE,
        args: uri
      });

      oRPC.callback = function(rpc){
        var oRPC = new leimnud.module.rpc.xmlhttp({
          url   : PROCESS_REQUEST_FILE,
          async : false,
          method: 'POST',
          args  : 'request=show'
        });
      oRPC.make();
      $('publisherContent[0]').innerHTML = oRPC.xmlhttp.responseText;
    }.extend(this);
    oRPC.make();

    }.extend(this)
  });
};

/**
 * gets a list of the users assigned to a role using an Ajax call
 * and generates a leinmud popup window
 */
function usersIntoRole(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=usersIntoRole&ROL_UID='+ROL_UID
    });
  oRPC.make();
  if(currentPopupWindow!=undefined){
    currentPopupWindow.clearContent();
    currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
  } else {
    $('publisherContent[0]').innerHTML = oRPC.xmlhttp.responseText;
  }
}

/**
 * delete a user from a role
 * and generates a leinmud popup window with the data refreshed
 */
function deleteUserRole(ROL_UID, ROL_OBJ_UID){

  new leimnud.module.app.confirm().make({
    label:G_STRINGS.ID_MSG_CONFIRM,
    action:function(){
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url   : PROCESS_REQUEST_FILE,
        async : false,
        method: 'POST',
        args  : 'request=deleteUserRole&ROL_UID=' + ROL_UID + '&ROL_OBJ_UID=' + ROL_OBJ_UID
      });
      oRPC.make();
      currentPopupWindow.clearContent();
      currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
    }.extend(this)
  });
};

/**
 * refresh the user interface with the current users list
 */
function showUsers(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=showUsers&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * refresh the user interface with the current groups list
 */
function showGroups(ROL_UID){
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=showGroups&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * refresh the user interface with the current departments list
 */
function showDepartments(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=showDepartments&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * asign via an Ajax call a user to a role
 */
function assignUserToRole(ROL_UID, USR_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=assignUserToRole&ROL_UID=' + ROL_UID + '&USR_UID=' + USR_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * asign via Ajax a group to a role
 */
function assignGroupToRole(ROL_UID, GRP_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=assignGroupToRole&ROL_UID=' + ROL_UID + '&GRP_UID=' + GRP_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * asign via Ajax a department to a role
 */
function assignDepartmentToRole(ROL_UID, DEP_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=assignDepartmentToRole&ROL_UID=' + ROL_UID + '&DEP_UID=' + DEP_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * show the reports List
 */
function viewReports(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=viewReports&ROL_UID='+ROL_UID
  });
  oRPC.make();
  currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * show the dashboards List
 */
function showDashboardList(ROL_UID, USR_UID, ROL_OBJ_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=showDashboardList&ROL_UID=' + ROL_UID + '&ROL_OBJ_UID=' + ROL_OBJ_UID
  });
  oRPC.make();
  currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
};

/**
 * Assign a Dashboard to an object
 */
function assignDashboard(ROL_UID, DSH_UID, ROL_OBJ_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=assignDashboard&ROL_OBJ_UID=' + ROL_OBJ_UID + '&DSH_UID=' + DSH_UID + '&ROL_UID=' + ROL_UID
  });
  oRPC.make();
  currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
};

/**
 * Show the reports list
 */
function showReports(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=showReports&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
    currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * Assign a Report to a Role
 */
function assignReportToRole(ROL_UID, REP_UID, IS_FOLDER)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=assignReportToRole&ROL_UID=' + ROL_UID + '&REP_UID=' + REP_UID + '&IS_FOLDER=' + IS_FOLDER
  });
  oRPC.make();
  currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}
/**
 * Delete a Report from a Role
 */
function deleteReportRole(ROL_UID, ROL_REP_UID, IS_FOLDER){
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=deleteReportRole&ROL_REP_UID=' + ROL_REP_UID + '&ROL_UID=' + ROL_UID + '&IS_FOLDER=' + IS_FOLDER
  });
  oRPC.make();
  currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
};

/**
 * Toogle the visualization of the report list
 */
function toggleDisplayFolder(elementId){
  elementId = 'child_'+elementId;
  if (document.getElementById(elementId).style.display=="none"){
    document.getElementById(elementId).style.display="";
  } else {
    document.getElementById(elementId).style.display="none";
  }
}

/**
 * Toogle the visualization of the report list
 */
function toggleRoleAssignment(rolUid, rolRepUid, repUid, action, isFolder){
  if (action == 'Remove') {
    deleteReportRole(rolUid, rolRepUid, isFolder);
  } else {
    assignReportToRole(rolUid, repUid, isFolder);
  }
}

/**
 * go back to the reports list in the roles manager interface
 */
function backPermissions(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=viewReports&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * go back to the roles list
 */
function backRoles()
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=viewRoles'
    });
    oRPC.make();
    currentPopupWindow.clearContent();
    currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}

/**
 * go back to the users list
 */
function backUsers(ROL_UID)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : PROCESS_REQUEST_FILE,
    async : false,
    method: 'POST',
    args  : 'request=usersIntoRole&ROL_UID=' + ROL_UID
    });
    oRPC.make();
    currentPopupWindow.clearContent();
  currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
}


/**
 * Ajax request function
 */
function AJAX()
{
  try	{
    xmlhttp = new XMLHttpRequest();
  }
  catch(generic_error) {
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (microsoft_old_error) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (microsoft_error) {
        xmlhttp = false;
      }
    }
  }
  return xmlhttp;
}

/**
 * trim prototyped function
 */
String.prototype.trim = function()
{
  return this.replace(/^\s+|\s+get/g,"");
}

/**
 * getElementById wrapper
 */
function $(id){
  return document.getElementById(id);
}
