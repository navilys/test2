<?php
//ini_set ( 'error_reporting', E_ALL );
//ini_set ( 'display_errors', True );

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
    ## UPDATE CONTENT ROL NANE LANGUAGE
    G::LoadClass('pmFunctions');
    $query = "SELECT LAN_ID FROM LANGUAGE WHERE LAN_ENABLED = 1 ";
    $aData = executeQuery($query);
    foreach($aData as $row)
    {
    	$query = "SELECT CON_ID FROM CONTENT 
    			  WHERE CON_ID = '".$newid."' AND CON_CATEGORY = 'ROL_NAME' AND 
    			  CON_LANG = '".$row['LAN_ID']."' AND CON_VALUE = '".$_POST['name']."' ";
    	$aDataContent = executeQuery($query);
    	if(sizeof($aDataContent) == 0)
    	{
    		$insert = "INSERT INTO CONTENT (CON_CATEGORY, CON_ID, CON_LANG, CON_VALUE) 
    								VALUES ('ROL_NAME', '".$newid."', '".$row['LAN_ID']."', '".$_POST['name']."') ";
    		executeQuery($insert);
    	}
    }
    ## END UPDATE CONTENT ROL NANE LANGUAGE
    echo '{success: true}';
  case 'saveEditGroup':
  	$idRol = '';
  	if(isset($_POST['nameId']))
    	$idRol = $aData['ROL_UID'] = $_POST['nameId']; //obtainRoleInfo($_POST['nameId']);   	 
    //$aData['ROL_CODE'] = trim($_POST['name']);
    $aData['ROL_NAME'] = $_POST['name'];
    $aData['ROL_UPDATE_DATE'] = date("Y-M-d H:i:s");
    $rolstatus = 0;
    if(isset($_POST['status']) && $_POST['status'] == 'ACTIVE')
      $rolstatus = 1;
    $aData['ROL_STATUS'] = $rolstatus;
    $oCriteria = $RBAC->updateRole($aData);
    ## UPDATE CONTENT ROL NANE LANGUAGE
   /* G::LoadClass('pmFunctions');
    $query = "SELECT LAN_ID FROM LANGUAGE WHERE LAN_ENABLED = 1 ";
    $aData = executeQuery($query);
    foreach($aData as $row)
    {
    	$update = "UPDATE CONTENT SET CON_VALUE = '".$_POST['name']."' 
    			   WHERE CON_ID = '".$idRol."' AND CON_CATEGORY = 'ROL_NAME' 
    			   AND CON_LANG = '".$row['LAN_ID']."' AND CON_VALUE != '".$_POST['name']."' ";
    	executeQuery($update);
    }*/
    ## END UPDATE CONTENT ROL NANE LANGUAGE
    echo '{success: true}';
    break;
  case 'deleteGroup':
    $rId = obtainRoleInfo($_POST['name']);
   	$totUser = $RBAC->numUsersWithRole($rId);
   //	die($_POST['GRP_UID'].'  '.$totUser. '  users');
	if($totUser == 0)
	{
      	$oCriteria = $RBAC->removeRole($rId);
      	
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
 	{   $group = '';
   		foreach( $result as $rowG ) 
    	{   
    		$sw = 0;
    		$group = $rowG['GRP_TITLE'];
 			foreach ( $rolesData as $rowid => $row )  
    		{ 				
    			$rolCode =  $row['ROL_CODE'];
    			$rolName = $row['ROL_NAME'];
       			if(($rolCode == $group || $rolName == $group) && $sw == 0)
       			{
       				$sw = 1;
        		}
        		else 
        		{
        			$rolCodeAux = $rolCode;
        			$rolNameAux = $rolName;
        		}
        	} 
    		if($sw == 0)
    		{ //G::pr($group.'  ----   '.$rolCodeAux.'  ' .$rolNameAux);
    			/*$code=G::generateUniqueID();
    			$newid = md5($code.date("d-M-Y_H:i:s"));
    			$aData['ROL_UID'] = $newid;    
    			$aData['ROL_SYSTEM'] = '00000000000000000000000000000002';
    			$aData['ROL_CODE'] = trim($group);
    			$aData['ROL_NAME'] = $group;
    			$aData['ROL_CREATE_DATE'] = date("Y-M-d H:i:s");
    			$aData['ROL_UPDATE_DATE'] = date("Y-M-d H:i:s");
    			$aData['ROL_STATUS'] = '1';
    			$oCriteria = $RBAC->createRole($aData);*/
    		}
    		$group = '';
    	}
    	
 	}
 	## UPDATE CONTENT GRP_TITLE AND ROL_NAME LANGUAGE
    $query = "SELECT LAN_ID FROM LANGUAGE WHERE LAN_ENABLED = 1 ";
    $aData = executeQuery($query);
	foreach( $aData as $rowL ) 
    {
    	foreach($result as $rowG)
    	{
    		$query = "SELECT CON_ID FROM CONTENT 
    			  	  WHERE CON_ID = '".$rowG['GRP_UID']."' AND CON_CATEGORY = 'GRP_TITLE' AND 
    			      CON_LANG = '".$rowL['LAN_ID']."' ";
    		$aDataContent = executeQuery($query);
    		
       		if(sizeof($aDataContent) == 0)
       		{
       			$insert = "INSERT INTO CONTENT (CON_CATEGORY, CON_ID, CON_LANG, CON_VALUE) 
    								VALUES ('GRP_TITLE', '".$rowG['GRP_UID']."', '".$rowL['LAN_ID']."', '".$rowG['GRP_TITLE']."') ";
    			executeQuery($insert);
        	}
        	/*else 
        	{
        		$update = "UPDATE CONTENT SET CON_VALUE = '".$rowG['GRP_TITLE']."' 
    			  		   WHERE CON_ID = '".$rowG['GRP_UID']."' AND CON_CATEGORY = 'GRP_TITLE' AND 
    			           CON_LANG = '".$rowL['LAN_ID']."' AND CON_VALUE != '".$rowG['GRP_TITLE']."'  ";
    			executeQuery($update);
        	}*/
    	}
    	foreach ( $rolesData as $rowid => $rowR )  
    	{
    		$query = "SELECT CON_ID FROM CONTENT 
    			  	  WHERE CON_ID = '".$rowR['ROL_UID']."' AND CON_CATEGORY = 'ROL_NAME' AND 
    			      CON_LANG = '".$rowL['LAN_ID']."' ";
    		$aDataContent = executeQuery($query);
    		if(sizeof($aDataContent) == 0)
       		{
       			$insert = "INSERT INTO CONTENT (CON_CATEGORY, CON_ID, CON_LANG, CON_VALUE) 
    								VALUES ('ROL_NAME', '".$rowR['ROL_UID']."', '".$rowL['LAN_ID']."', '".$rowR['ROL_CODE']."') ";
    			executeQuery($insert);
        	}
    	}
 		
    }
    ## END UPDATE CONTENT GRP_TITLE AND ROL_NAME LANGUAGE
    $data = $groups->getAllGroup($start,$limit,$filter);
    $result  = $data['rows'];
    $totalRows =  0;
    $arrData   =  array();
   
    foreach ($result as $results) 
    {
    	$sw = 0;
       	$group = $results['GRP_TITLE'];
       	foreach ( $rolesData as $rowid => $row )  
    	{
    		$rolCode =  $row['ROL_CODE'];
    		$rolName = $row['ROL_NAME'];
    		// G::pr($rolCode.' '.$group.' '.$rolName);
       		if(($rolCode == $group || $rolName == $group) && $sw == 0)
       		{
        		$totalRows ++;
        		$results['CON_VALUE'] = str_replace(array("<", ">"), array("&lt;", "&gt;"), $results['GRP_TITLE']);
        		$results['GRP_TASKS'] = isset($aTask[$results['GRP_UID']]) ? $aTask[$results['GRP_UID']] : 0;
        		$results['GRP_USERS'] = isset($aMembers[$results['GRP_UID']]) ? $aMembers[$results['GRP_UID']] : 0;
        		$results['ROL_ID'] = $row['ROL_UID'];
        		$results['ROL_CODE'] = $row['ROL_CODE'];
        		$arrData[] = $results;
        		$sw = 1;
       		}
      
    	}
    }
    $result = new StdClass();
    $result->success = true;
    $result->groups = $arrData;
    $result->total_groups = $data['totalCount'];

    echo G::json_encode($result);
    break;   
}
