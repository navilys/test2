<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );

if (($RBAC_Response=$RBAC->userCanAccess("PM_USERS"))!=1) return $RBAC_Response;
G::LoadInclude('ajax');
$_POST['action'] = get_ajax_value('action');


function obtainRoleInfo($name){
	G::LoadClass('pmFunctions');
  	require_once 'classes/model/Groupwf.php';
  	require_once PATH_RBAC."model/Roles.php";
  	$roles     = new Roles();
  	$rolesData = $roles->getAllRoles();
 	$res = '';
  	if(isset($rolesData) && $rolesData!='')
   	foreach($rolesData as $rol)
   	{
   		if($rol['ROL_CODE'] == $name)
   		{
   			$res = $rol['ROL_UID'];
   		}
   	}
  
  	return $res;
}

switch ($_POST['action'])
{
  
  case 'saveNewGroup':
    $code=G::generateUniqueID();
    $newid = md5($code.date("d-M-Y_H:i:s"));
    $aData['ROL_UID'] = $newid;    
    $aData['ROL_SYSTEM'] = '00000000000000000000000000000002';
    $aData['ROL_CODE'] = trim($_POST['name']);
    $aData['ROL_NAME'] = $_POST['name'];
    $aData['ROL_CREATE_DATE'] = date("Y-M-d H:i:s");
    $aData['ROL_UPDATE_DATE'] = date("Y-M-d H:i:s");
    $aData['ROL_STATUS'] = '1';
    $oCriteria = $RBAC->createRole($aData);
    echo '{success: true}';
  case 'saveEditGroup':
  	if(isset($_POST['nameId']))
    	$aData['ROL_UID'] = obtainRoleInfo($_POST['nameId']);   	 
    $aData['ROL_CODE'] = trim($_POST['name']);
    $aData['ROL_NAME'] = $_POST['name'];
    $aData['ROL_UPDATE_DATE'] = date("Y-M-d H:i:s");
    $rolstatus = 0;
    if(isset($_POST['status']) && $_POST['status'] == 'ACTIVE')
      $rolstatus = 1;
    $aData['ROL_STATUS'] = $rolstatus;    
    $oCriteria = $RBAC->updateRole($aData);
    echo '{success: true}';
    break;
  case 'deleteGroup':
    $rId = obtainRoleInfo($_POST['name']);
   	$totUser = $RBAC->numUsersWithRole($rId);
   //	die($_POST['GRP_UID'].'  '.$totUser. '  users');
	if($totUser == 0)
	{
      	$oCriteria = $RBAC->removeRole($rId);
      	/* G::LoadClass( 'groups' );
        $group = new Groupwf();
        if (! isset( $_POST['GRP_UID'] )) {
            return;
        }
        $group->remove( urldecode( $_POST['GRP_UID'] ) );
        require_once 'classes/model/TaskUser.php';
        $oProcess = new TaskUser();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( TaskUserPeer::USR_UID, $_POST['GRP_UID'] );
        TaskUserPeer::doDelete( $oCriteria );
        
        //Delete permissions
        require_once 'classes/model/ObjectPermission.php';
        $criteria = new Criteria( 'workflow' );
        $criteria->add(ObjectPermissionPeer::USR_UID, $_POST['GRP_UID']);
        ObjectPermissionPeer::doDelete( $criteria );
        
        //Delete supervisors assignments
        require_once 'classes/model/ProcessUser.php';
        $criteria = new Criteria( 'workflow' );
        $criteria->add(ProcessUserPeer::USR_UID, $_POST['GRP_UID']);
        $criteria->add(ProcessUserPeer::PU_TYPE, 'GROUP_SUPERVISOR');
        ProcessUserPeer::doDelete( $criteria );*/
      	$deleteRol = array ('success' => true);
		echo json_encode ($deleteRol);
    } else {
    	$deleteRol = array ('success' => false);
     	echo json_encode ($deleteRol);
    }
    //$oCriteria = $RBAC->removeRole($rId);
   // echo '{success: true}';
    break;
  case 'groupsList':
    require_once 'classes/model/Groupwf.php';
    require_once 'classes/model/TaskUser.php';
    require_once 'classes/model/GroupUser.php';
    require_once PATH_RBAC."model/Roles.php";
    G::LoadClass('configuration');
    $co = new Configurations();
    $config = $co->getConfiguration('groupList', 'pageSize','',$_SESSION['USER_LOGGED']);
    $env = $co->getConfiguration('ENVIRONMENT_SETTINGS', '');
    $limit_size = isset($config['pageSize']) ? $config['pageSize'] : 20;
    $start   = isset($_REQUEST['start'])  ? $_REQUEST['start'] : 0;
    $limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit'] : $limit_size;
    $filter = isset($_REQUEST['textFilter']) ? $_REQUEST['textFilter'] : '';
	
    global $RBAC;
    if ($limit == $start) $limit = $limit +$limit ;
    $tasks = new TaskUser();
    $aTask = $tasks->getCountAllTaksByGroups();

    $members = new GroupUser();
    $aMembers = $members->getCountAllUsersByGroup();

    require_once PATH_CONTROLLERS . 'adminProxy.php';
    $uxList = adminProxy::getUxTypesList();

    $groups = new Groupwf();
    $data = $groups->getAllGroup($start,$limit,$filter);
    $result  = $data['rows'];
    
    $roles     = new Roles();
 	$rolesData = $roles->getAllRoles();
 	if($filter == '')
 	{
   		foreach ( $rolesData as $rowid => $row )  
    	{
 			$sw = 0;
    		$rolCode =  $row['ROL_CODE'];
    		foreach( $result as $rowG ) 
    		{
    			$group = $rowG['GRP_TITLE'];
       			if($rolCode == $group && $sw == 0)
       			{
       				$sw = 1;
        		}
        	
    		}
    	
    		/*if($sw == 0)
    		{
    			G::LoadClass('groups');
    			$_POST['status'] = 'ACTIVE';
    			$newGroup['GRP_UID'] = '';
    			$newGroup['GRP_STATUS'] = G::toUpper($_POST['status']);
    			$newGroup['GRP_TITLE'] = trim($rolCode);
    			unset($newGroup['GRP_UID']);
    			$group = new Groupwf();
    			$group->create($newGroup);
    		}*/
    	}
 	}
    $data = $groups->getAllGroup($start,$limit,$filter);
    $result  = $data['rows'];
    $totalRows =  0;
    $arrData   =  array();
    foreach ($result as $results) {
        /*$sw = 0;
        $group = $results['GRP_TITLE'];
        foreach ( $rolesData as $rowid => $row )  
    	{
    		$rolCode =  $row['ROL_CODE'];
    		if($rolCode == $group && $sw == 0)
    		{   $sw = 1;
                $totalRows ++;
                $results['CON_VALUE'] = str_replace(array("<", ">"), array("&lt;", "&gt;"), $results['GRP_TITLE']);
                $results['GRP_TASKS'] = isset($aTask[$results['GRP_UID']]) ? $aTask[$results['GRP_UID']] : 0;
                $results['GRP_USERS'] = isset($aMembers[$results['GRP_UID']]) ? $aMembers[$results['GRP_UID']] : 0;
                $arrData[] = $results;
            }
        }*/
        $totalRows ++;
                $results['CON_VALUE'] = str_replace(array("<", ">"), array("&lt;", "&gt;"), $results['GRP_TITLE']);
                $results['GRP_TASKS'] = isset($aTask[$results['GRP_UID']]) ? $aTask[$results['GRP_UID']] : 0;
                $results['GRP_USERS'] = isset($aMembers[$results['GRP_UID']]) ? $aMembers[$results['GRP_UID']] : 0;
                $arrData[] = $results;
      
    }
    $result = new StdClass();
    $result->success = true;
    $result->groups = $arrData;
    $result->total_groups = $data['totalCount'];

    echo G::json_encode($result);
    break;   
}
