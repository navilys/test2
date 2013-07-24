<?php   

    G::LoadClass('case');
    G::LoadClass('configuration');
    G::loadClass('pmFunctions');

    
	$USER_UID = $_SESSION['USER_LOGGED'];
	$where="";
	$fieldName = isset($_GET['fieldName']) ? $_GET['fieldName']: '';
	$array = Array();
	if($fieldName == '')
	{
		$start = isset($_POST['start']) ? $_POST['start'] : 0;
    	$limit = isset($_POST['limit']) ? $_POST['limit'] : 20;
		$sQuery = "SELECT CFG_USR_ID AS CONFIG_USERS_ID, CFG_USR_FIELD_NAME AS FIELD_NAME, CFG_USR_DESCRIPTION AS DESCRIPTION, CFG_USR_TYPE AS TYPE, 
				   CFG_USR_TYPE_ACTION AS TYPE_ACTION, CFG_USR_PARAMETERS AS PARAMETERS, CFG_USR_STATUS AS STATUS
                   FROM PMT_CONFIG_USERS  
                   ORDER BY CFG_USR_POSITION ASC ";
		$aData = executeQuery ($sQuery);
		foreach($aData as $index)
		{       	
			$array[] = $index;
		}
	
		$total = count($aData); 

	
	}
	else
	{
		$start = isset($_POST['start']) ? $_POST['start'] : 0;
    	$limit = isset($_POST['limit']) ? $_POST['limit'] : 1000;
    	
		$query = "SELECT CFG_USR_ID AS CONFIG_USERS_ID, CFG_USR_FIELD_NAME AS FIELD_NAME, CFG_USR_DESCRIPTION AS DESCRIPTION, CFG_USR_TYPE AS TYPE, 
				  CFG_USR_TYPE_ACTION AS TYPE_ACTION, CFG_USR_PARAMETERS AS PARAMETERS, CFG_USR_STATUS AS STATUS
                  FROM PMT_CONFIG_USERS  
                  WHERE CFG_USR_FIELD_NAME = '$fieldName' ";
		$aDataConfig = executeQuery ($query);
		$typeOption = $aDataConfig[1]['TYPE_ACTION'];
		if($typeOption == 'SELECT OPTIONS')
		{
			$sQuery = "SELECT CFG_USR_OPT_ID_OPTION AS ID, CFG_USR_OPT_DESCRIPTION AS NAME
               		   FROM PMT_CONFIG_USERS_OPTIONS  
                       WHERE CFG_USR_FIELD_NAME = '$fieldName' 
                       ORDER BY ID";
			$aData = executeQuery ($sQuery);
			foreach($aData as $index)
			{
				$array[] = $index;
			}
			
			$total = count($aData); 
		}
		else 
		{
			$sQuery = "SELECT CFG_USR_PARAMETERS AS PARAMETERS
               		   FROM PMT_CONFIG_USERS  
               		   WHERE CFG_USR_FIELD_NAME = '$fieldName' ";
			$sConfig = executeQuery ($sQuery);
			$queryConfig = $sConfig[1]['PARAMETERS'];
			$aData =  executeQuery ($queryConfig);
			foreach($aData as $index)
			{
				$array[] = $index;
			}
			
			$total = count($aData); 
		}
	}
	
		header("Content-Type: text/plain");


		$paging = array(
	   	 	'success'=> true,
	   	 	'total'=> $total,
	    	'data'=> array_splice($array,$start,$limit)
		);
		echo json_encode($paging);    
?>

