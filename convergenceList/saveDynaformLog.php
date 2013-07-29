<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', True);
G::LoadClass("case");
G::LoadClass("pmFunctions");
require_once ("classes/model/AppHistory.php");

if(isset($_REQUEST['APP_UID']) && $_REQUEST['APP_UID']!='' )
{  
  //unsetSessionVars("FLAG|FLAG_ACTION");
# Set Variables
	$version = 1;
	$APP_UID= $_REQUEST['APP_UID'];
	$CURRENTDATETIME=$_REQUEST['CURRENTDATETIME'];
  
  	$DYN_UID = $_REQUEST['DYN_UID'];
  # End Set Variables

  ///////////////////////// End Regenerate Tables ////////////////////////////////////////

  	$auxUsrUID = $_SESSION['USER_LOGGED'];
  	$auxUsruname = $_SESSION['USR_USERNAME'];
  
  ///////////////////////// Route Again the Case /////////////////////////////////////////
	
  	# Get ProcessID, Initial Task and Index
  	$PRO_UID = '';
  	$TAS_UID = '';
  	$USR_UID = '';
  	$dataDelegation="SELECT MIN(DEL_INDEX) AS MIN_INDEX, PRO_UID, TAS_UID, USR_UID FROM APP_DELEGATION  WHERE APP_UID = '$APP_UID' ";
   	$resultDelegation=executeQuery($dataDelegation);
  	if(sizeof($resultDelegation)){
    	$PRO_UID = $resultDelegation[1]['PRO_UID'];
    	$TAS_UID = $resultDelegation[1]['TAS_UID'];
    	$USR_UID = $resultDelegation[1]['USR_UID'];
    	$MIN_INDEX = $resultDelegation[1]['MIN_INDEX'];
  	}
  	# End Get ProcessID, Initial Task and Index
  	if($PRO_UID == '')
  		$PRO_UID = $_GET['PROCESS'];
 	if($PRO_UID !='' && $DYN_UID != '' )
 	{
  		$oForm = new Form( $PRO_UID . "/" . $DYN_UID, PATH_DYNAFORM ); 
    	$oForm->validatePost(); 
 	}
 	
    $DYNAFORMDATA = $_POST['form'];
   	# Get the the data from the Current Case
  	$oCase = new Cases ();  
  	$Fields2 = $oCase->loadCase ($APP_UID);    
  	
  	# End get the the data from the Current Case
  	$Fields2['APP_DATA'] = unsetCasesFlag("FLAG|COLOSA_FLAG", $Fields2['APP_DATA']); ### ronald
  	# Get APP_NUMBER initial case 
 	if(isset($Fields2['APP_DATA']['NUM_DOSSIER']))
 	{
    	$APP_NUMBER_DOSSIER = $Fields2['APP_DATA']['NUM_DOSSIER'];
 	}
 	else
 	{
    	$dataAplication="SELECT APP_NUMBER FROM APPLICATION WHERE APP_UID = '$APP_UID' ";
    	$resultAplication=executeQuery($dataAplication);
    	if(sizeof($resultAplication))
    	{
        	$APP_NUMBER_DOSSIER = $resultAplication[1]['APP_NUMBER'];
    	}
 	} 
	 #End get APP_NUMBER initial case
 
  	# Get the last version of the Demandes Table    
  	$data="SELECT * FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID = '$APP_UID' ";
  	$resultData=executeQuery($data);
  	$dataSize = count($resultData);
  
  # If the APP_UID is not saved we will create with the version 1

		if ((sizeof($resultData) == 0))
			$version = 1;
		else
			$version = $version + $resultData[$dataSize]['HLOG_VERSION'];    
		
		#Create the New Case and Derivate automalically      
		if(isset($_SESSION['APPLICATION_EDIT']) && $_SESSION['APPLICATION_EDIT'] != '')
		{
			$newAPP_UID = $_SESSION['APPLICATION_EDIT']; 
		}
		else
		{
			$newAPP_UID = PMFNewCase($PRO_UID, $USR_UID, $TAS_UID, $Fields2['APP_DATA']); 
		}   
		$newFields = $oCase->loadCase ($newAPP_UID);
		$newFields["APP_DATA"] = array_merge( $newFields["APP_DATA"], G::getSystemConstants() );
		//G::pr($DYNAFORMDATA);
	  	$newFields['APP_DATA']['FLAG_ACTION'] = 'editForms'; # this is for the typo3 trigger
		$newFields['APP_DATA']['DYN_UID'] = $DYN_UID; # this is for the typo3 trigger
		$newFields['APP_DATA']['CURRENTDATETIME'] = $CURRENTDATETIME; # this is for the typo3 trigger
		$newFields['APP_DATA']['NUM_DOSSIER'] = $APP_NUMBER_DOSSIER;
  		$newFields['APP_DATA']['FLG_INITUSERUID'] = $auxUsrUID;
  		$newFields['APP_DATA']['FLG_INITUSERNAME'] = $auxUsruname;
		//$newFields['APP_DATA']['VALIDATION'] = isset($newFields['APP_DATA']['VALIDATION']) ? $newFields['APP_DATA']['VALIDATION'] :'0'; 
	
		$newFields = str_replace("'","'",$newFields ['APP_DATA']);  
	 
		//$newFields['APP_DATA'] = array_replace_recursive($newFields , ( array ) $DYNAFORMDATA);
		//$newFields['APP_DATA'] = G::array_merges($newFields , ( array ) $DYNAFORMDATA);
		$newFields["APP_DATA"] = array_merge( $newFields, $_POST['form'] );
	  	$newFields['APP_DATA']['APPLICATION'] = $newAPP_UID;
	  	$newFields['APP_DATA']['FLAG_EDIT'] = 1;

	   // 	If the user is different
	  	if($_SESSION['USER_LOGGED'] != $newFields['APP_DATA']['USER_LOGGED'])
	  	{
    		$arrayUser = userInfo($newFields['APP_DATA']['USER_LOGGED']);      
    		$_SESSION['USER_LOGGED'] = $newFields['APP_DATA']['USER_LOGGED'];
    		$_SESSION['USR_USERNAME'] = $arrayUser['username'];
  		}
  		// End If the user is different
      
  		PMFSendVariables($newAPP_UID, $newFields['APP_DATA']);		    
		$oCase->updateCase($newAPP_UID, $newFields);
		//G::pr($newFields['APP_DATA']['FLAG_EDIT ']);
   		//G::pr($_SESSION['APPLICATION_EDIT']);
  		if(isset($_SESSION['APPLICATION_EDIT']) && $_SESSION['APPLICATION_EDIT'] != '')
  		{
			$_SESSION['APPLICATION'] = $newAPP_UID;
      		$_SESSION['APPLICATION_EDIT'] = $newAPP_UID;   
  			$_SESSION['USER_LOGGED'] = $auxUsrUID ;
    		$_SESSION['USR_USERNAME'] = $auxUsruname;
    		//executeTriggers($PRO_UID, $newAPP_UID ,$auxUsrUID);
			
     	}
  		else	
  		{  			
  			$end_date =  Date("m-d-Y H:i:s");
       		$update = executeQuery("UPDATE PMT_USER_CONTROL_CASES SET USR_CTR_CAS_END_DATE = '$end_date' 
       							    WHERE APP_UID = '$APP_UID' AND USR_UID = '$auxUsrUID' "); 
       		
			$_SESSION['APPLICATION'] = $newAPP_UID;
     		$_SESSION['APPLICATION_EDIT'] = $newAPP_UID;
  			$_SESSION['USER_LOGGED_INI'] = $auxUsrUID ;
  			insertHistoryLogPlugin($APP_UID,$USR_UID,$CURRENTDATETIME,$version,$newAPP_UID,'Modification'); // PM function in aquitineProject Plugin		    
		  	DuplicateMySQLRecord('APP_HISTORY','APP_UID',$APP_UID,$newAPP_UID);
		  	
		  	$query = "SELECT TAS_UID FROM TASK WHERE TAS_START = 'TRUE' AND PRO_UID = '".$PRO_UID."'";	//query for select all start tasks
	        $startTasks = executeQuery($query);
	        foreach($startTasks as $rowTask){
		        $taskId = $rowTask['TAS_UID'];
		        $stepsByTask = getStepsByTask($taskId);
	            foreach ($stepsByTask as $caseStep){
				    $caseStepRes[] = 	 $caseStep->getStepUidObj();
			    }
			    break;
	        }
	        //G::pr($caseStepRes);die;
			$totStep = 0;
			foreach($caseStepRes as $index)
			{
				$stepUid = $index;
				executeTriggersMon($PRO_UID, $newAPP_UID, $stepUid, 'BEFORE', $taskId);	//execute trigger before form
				executeTriggersMon($PRO_UID, $newAPP_UID, $stepUid, 'AFTER', $taskId);	//execute trigger after form	
				$totStep++;
			} 
			
		  	$resInfo = PMFDerivateCase($newAPP_UID, 1,true, $USR_UID); 
			$_SESSION['USER_LOGGED'] = $auxUsrUID ;
    		$_SESSION['USR_USERNAME'] = $auxUsruname;
			
  		}
		#End Create the New Case and Derivate automalically
        
  # End If the APP_UID is not saved we will create with the version 1
    
  # End Get the last version of the Demandes Table     
	$url = '../convergenceList/casesHistoryDynaformPage_Ajax.php?ACTIONTYPE=edit&actionAjax=historyDynaformGridPreview&DYN_UID='.$DYN_UID.'&APP_UID='.$APP_UID.'&PRO_UID='.$PRO_UID.'&CURRENTDATETIME='.$CURRENTDATETIME;
	echo "<script language='javascript'> location.href = '".$url."'; </script>";
	die();
}   
?>
