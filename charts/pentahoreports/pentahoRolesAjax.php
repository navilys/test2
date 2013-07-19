<?php
/**
 * @section Filename
 * pentahoRolesAjax.php
 * @subsection Description
 * this script handles all the Ajax requests made by the roles javascript
 * functions, the request parameter indicates the case condition.
 * 
 * @author gustavo cruz <gustavo@colosa.com>
 * @param $_GET/$_POST['request'] the request made to this script
 * @param $_GET/$_POST['ROL_UID'] role uid optional
 * @param $_GET/$_POST['USR_UID'] usr uid optional
 * @param $_GET/$_POST['USR_UID'] usr uid optional
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

require_once 'classes/model/PhRoleReport.php';
require_once 'classes/model/PhRole.php';
require_once 'classes/model/PhReport.php';
require_once 'classes/model/PhUserRole.php';
 
$REQUEST = (isset($_GET['request']))?$_GET['request']:$_POST['request'];
 
switch ($REQUEST) {
    // new pentaho plugin role form
    case 'newRole':
      $G_PUBLISH = new Publisher();
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pentahoreports/pentahoRolesNew', '', '');
      G::RenderPage('publish', 'raw');
    break;
    // save the new pentaho role data
    case 'saveNewRole':
      $newid = md5($_POST['code'].date("d-M-Y_H:i:s"));
      g::pr($_POST);
      $aData['ROL_UID'] = $newid;
      $aData['ROL_CODE'] = $_POST['code'];
      $role = new PhRole();
      $role->create($aData);
    break;
    // edit form for the pentaho role data        
    case 'editRole':
      $ROL_UID = $_GET['ROL_UID'];
      $role = new PhRole();
      $aFields = $role->load($ROL_UID);
      $G_PUBLISH = new Publisher();
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'pentahoreports/pentahoRolesEdit', '', $aFields);
      G::RenderPage('publish', 'raw');
    break;
    // update the pentaho role data        		    
    case 'updateRole':
      $aData['ROL_UID'] = $_POST['rol_uid'];
      $aData['ROL_CODE'] = $_POST['code'];
      $_GET['ROL_UID']=$_POST['rol_uid'];
      $role = new PhRole();
      $role->updateRole($aData);
    break;
    // Show the roles list
    case 'show':
      G::LoadClass('ArrayPeer');
      $role = new PhRole();
      $aRoles = $role->getAllRoles();
      $fields = Array(
              'ROL_UID'=>'char',
              'ROL_CODE'=>'char',
      );
      $rows = array_merge(Array($fields), $aRoles);
      global $_DBArray;
      $_DBArray['virtual_roles'] = $rows;
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('virtual_roles');
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'pentahoreports/pentahoRolesList', $oCriteria);
      G::RenderPage('publish', 'raw');
    break;
    // delete a role
    case 'deleteRole':
      $role = new PhRole();
      $role->load($_POST['ROL_UID']);
      $role->delete();
    break;
    // can Delete a Role feature pending
    case 'canDeleteRole':
      echo 'true';
    break;
    case 'verifyNewRole':
      $response = ($RBAC->verifyNewRole($_POST['code']))?'true':'false';
      print($response);
    break;
    // list of users assigned to a role
    case 'usersIntoRole':
      $_GET['ROL_UID'] = (isset($_GET['ROL_UID']))?$_GET['ROL_UID']:$_POST['ROL_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // unassign a user from a role
    case 'deleteUserRole':
      $userRole = PhUserRolePeer::retrieveByPK($_POST['ROL_OBJ_UID']);
      $userRole->delete();
      $_GET['ROL_UID'] = $_POST['ROL_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // list of users for the assignment interface
    case 'showUsers':
      $ROL_UID = $_POST['ROL_UID'];
      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoAssignRole' );
      G::RenderPage('publish', 'raw');
    break;
    // list of groups for the assignment interface
    case 'showGroups':
      $ROL_UID = $_POST['ROL_UID'];
      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoAssignGroupRole' );
      G::RenderPage('publish', 'raw');
    break;
    // list of departments for the assignment interface
    case 'showDepartments':
      $ROL_UID = $_POST['ROL_UID'];
      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent( 'view', 'pentahoreports/pentahoAssignDepartmentRole' );
      G::RenderPage('publish', 'raw');
    break;
    // list of Assigned Reports
    case 'showReports':
      $ROL_UID = $_POST['ROL_UID'];
      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesAssignReports' );
      G::RenderPage('publish', 'raw');
    break;
    // Assign a user to a role
    case 'assignUserToRole':
      $USR_UID = $_POST['USR_UID'];
      $ROL_UID = $_POST['ROL_UID'];
      $sData['OBJ_UID'] = $USR_UID;
      $sData['ROL_UID'] = $ROL_UID;
      $sData['OBJ_TYPE'] = 'USER';
      $userRole = new PhUserRole();
      $userRole->create($sData);

      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // Assign a group to a role
    case 'assignGroupToRole':
      $GRP_UID = $_POST['GRP_UID'];
      $ROL_UID = $_POST['ROL_UID'];
      $sData['OBJ_UID'] = $GRP_UID;
      $sData['ROL_UID'] = $ROL_UID;
      $sData['OBJ_TYPE'] = 'GROUP';
      $userRole = new PhUserRole();
      $userRole->create($sData);

      $_GET['ROL_UID'] = $ROL_UID;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent( 'view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // Assign a department to a role
    case 'assignDepartmentToRole':
      $depUid = $_POST['DEP_UID'];
      $rolUid = $_POST['ROL_UID'];
      $sData['OBJ_UID'] = $depUid;
      $sData['ROL_UID'] = $rolUid;
      $sData['OBJ_TYPE'] = 'DEPARTMENT';
      $userRole = new PhUserRole();
      $userRole->create($sData);

      $_GET['ROL_UID'] = $rolUid;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent( 'view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // Show the dashoboard list
    case 'showDashboardList':
      $_GET['ROL_UID'] = $_POST['ROL_UID'];
      $_GET['ROL_OBJ_UID'] = $_POST['ROL_OBJ_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent( 'view', 'pentahoreports/showDashboardList' );
      G::RenderPage( 'publish', 'raw' );
    break;
    // Assign a Dashboard to a user or object
    case 'assignDashboard':
      $aData['ROL_UID'] = $_POST['ROL_UID'];
      $aData['ROL_OBJ_UID'] = $_POST['ROL_OBJ_UID'];
      $sDashboard = $_POST['DSH_UID'];
      $userRole = PhUserRolePeer::retrieveByPK($_POST['ROL_OBJ_UID']);
      $userRole->setObjDashboard($sDashboard);
      $userRole->save();

      $_GET['ROL_UID'] = $aData['ROL_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesTree' );
      G::RenderPage('publish', 'raw');
    break;
    // Assign a report top a role
    case 'assignReportToRole':
      $repUid = $_POST['REP_UID'];
      $rolUid = $_POST['ROL_UID'];
      $sData['REP_UID'] = $repUid;
      $sData['ROL_UID'] = $rolUid;
      $roleReport = new PhRoleReport();

      if ($_POST['IS_FOLDER'] == 'true'){
        $roleReport->create($sData);
        $rolRepUid = $roleReport->getRolRepUidFromData($rolUid, $repUid);
        $roleReport->recursiveAssign($_POST['REP_UID'],$_POST['ROL_UID']);
      } else {
        $roleReport->create($sData);
      }
        $_GET['ROL_UID'] = $rolUid;
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesPermissionsTree' );
      G::RenderPage('publish', 'raw');
    break;
    // Assign a report from a role
    case 'viewReports':
      $_GET['ROL_UID'] = (isset($_GET['ROL_UID']))?$_GET['ROL_UID']:$_POST['ROL_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent( 'view', 'pentahoreports/pentahoRolesPermissionsTree' );
      G::RenderPage('publish', 'raw');
    break;
    // delete a report from a role
    case 'deleteReportRole':
      $roleReport = PhRoleReportPeer::retrieveByPK($_POST['ROL_REP_UID']);
      if ($_POST['IS_FOLDER'] == 'true'){
        $roleReport->recursiveDelete($_POST['ROL_REP_UID'],$_POST['ROL_UID']);
        $roleReport->delete();
      } else {
        $roleReport->delete();
      }
      $_GET['ROL_UID'] = $_POST['ROL_UID'];
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'pentahoreports/pentahoRolesPermissionsTree');
      G::RenderPage('publish', 'raw');
    break;
    // view all the pentaho plugin roles
    case 'viewRoles':
      G::LoadClass('ArrayPeer');
      $role = new PhRole();
      $aRoles = $role->getAllRoles();
      $fields = Array(
          'ROL_UID'=>'char',
          'ROL_CODE'=>'char',
      );

      $rows = array_merge(Array($fields), $aRoles);

      global $_DBArray;
      $_DBArray['pentaho_roles'] = $rows;
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('pentaho_roles');
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'pentahoreports/pentahoRolesList', $oCriteria);
      G::RenderPage('publishBlank','blank');
    break;
    default: echo 'default';
}
