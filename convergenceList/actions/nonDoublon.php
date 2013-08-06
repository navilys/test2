<?php
G::LoadClass("case");
G::LoadClass("pmFunctions");
require_once ("classes/model/AppHistory.php");
header ( "Content-Type: text/plain" );
$array=array();
$array = isset($_REQUEST['AppUid'])?$_REQUEST['AppUid']:'';
$items = json_decode($array,true);
$appUid =  isset($_REQUEST['AppUid'])?$_REQUEST['AppUid']:'';
//G::pr($items);die;
$array=array();
$auxUsrUID = $_SESSION['USER_LOGGED'];
$auxUsruname = $_SESSION['USR_USERNAME'];

$i=1;

  	if($appUid != ''){
	  # Set Variables
		$APP_UID = $appUid;
	  # End Set Variables
	  
	  //insertHistoryLogPlugin($APP_UID,$_SESSION['USER_LOGGED'],date('Y-m-d H:i:s'),'0','','Non Doublon',2); // PM function in aquitineProject Plugin		    
	  convergence_changeStatut($APP_UID, '0', 'Non doublon');

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
	 
	   # Get the the data from the Current Case
	  $oCase = new Cases ();  
	  $Fields = $oCase->loadCase ($APP_UID);    
	
	  # Set Flag
	  $Fields['APP_DATA']['FLAG_NON_DOUBLON'] = 1;   
	  $Fields['APP_DATA']['FLAG_ACTION'] = "actionAjax";
      $Fields['APP_DATA']['FLG_INITUSERUID_DOUBLON'] = $auxUsrUID;
  	  $Fields['APP_DATA']['FLG_INITUSERNAME_DOUBLON'] = $auxUsruname; 
  	  $Fields['APP_DATA']['FLAG_EDIT'] = 1;
     
        #Create the New Case and Derivate automalically      
	  $newAPP_UID = PMFNewCase($PRO_UID, $USR_UID, $TAS_UID, $Fields['APP_DATA']);   
		
		if($newAPP_UID >0) {
		    # execute Triggers task Ini
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
	        
			$totStep = 0;
			foreach($caseStepRes as $index)
			{
				$stepUid = $index;
				executeTriggersMon($PRO_UID, $newAPP_UID, $stepUid, 'BEFORE', $taskId);	//execute trigger before form
				executeTriggersMon($PRO_UID, $newAPP_UID, $stepUid, 'AFTER', $taskId);	//execute trigger after form	
				$totStep++;
			} 
			# end execute Triggers task Ini
			
			$resp = PMFDerivateCase($newAPP_UID, 1,true, $USR_UID);	
		}
	  
		#End Create the New Case and Derivate automalically
	  
	  }
  
   	$messageInfo = "NON DOUBLON OK ";
	$paging = array ('success' => true, 'messageinfo' => $messageInfo);
	echo G::json_encode ( $paging );
  
  # End Get the last version of the Demandes Table     

?>