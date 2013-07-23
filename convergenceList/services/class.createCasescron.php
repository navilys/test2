<?php

class archivedCasesClassCron
{
	public $workspace = SYS_SYS;
	
	function followUpActions()
	{
		G::LoadClass('configuration');
		G::LoadClass('pmFunctions');
		G::LoadClass('wsBase');
		G::LoadClass('case');
		
		
	    $this->createCasesCSV();
		/*$date_current = Date("m-d-Y");
		$cron = "SELECT COUNT(DATE_CRON) as CANT FROM wf_".$this->workspace.".PMT_CRON_ARCHIVED WHERE DATE_CRON = '".$date_current."' ";
		$dataCron = executeQuery($cron);
		$cantCron = $dataCron[1]['CANT'];
		if($cantCron == '0')
		{
			$ins="INSERT
            		INTO wf_".$this->workspace.".PMT_CRON_ARCHIVED 
              		(DATE_CRON,STATUS)
            		VALUES 
             	 ('".$date_current."', 'NEW') ";	
			executeQuery($ins);
			
			$this->archivedCases();
		}*/
		echo "* ARCHIVED CASES EXECUTED *"; 
			
	}
 


	function createCasesCSV()
	{
	    $query = "SELECT IMPCSV_IDENTIFY, IMPCSV_TYPE_ACTION, IMPCSV_CONDITION_ACTION, IMPCSV_FIRSTLINEHEADER, IMPCSV_TABLE_NAME, IMPCSV_TAS_UID
	              FROM wf_".$this->workspace.".PMT_IMPORT_CSV_DATA GROUP BY IMPCSV_IDENTIFY ";
		$data = executeQuery($query);
		if(sizeof($data))
		{
		    foreach($data as $row)
		    {
		        $query = "SELECT IMPCSV_FIELD_NAME, IMPCSV_VALUE FROM wf_".$this->workspace.".PMT_IMPORT_CSV_DATA WHERE IMPCSV_IDENTIFY = '".$row['IMPCSV_IDENTIFY']."' ";
		        $dataCsv = executeQuery($query);
		        $dataImportCSV =  array();
		        
		        foreach($dataCsv as $index)
		        {
		            $record = array (
							"FIELD_NAME" => $index['IMPCSV_FIELD_NAME'], 
							"COLUMN_CSV" => $index['IMPCSV_VALUE']
					);
					$dataImportCSV[] = $record;
		        }
		        
		        $actionType = $row['IMPCSV_TYPE_ACTION'];
		        $matchFields = $dataImportCSV;
                $uidTask     = isset($row["IMPCSV_TAS_UID"])? $row["IMPCSV_TAS_UID"]:'';
                $tableName   = isset($row["IMPCSV_TABLE_NAME"])? $row["IMPCSV_TABLE_NAME"]:'';
                $firstLineHeader   = isset($row["IMPCSV_FIRSTLINEHEADER"])? $row["IMPCSV_FIRSTLINEHEADER"]:'on';
                $fileCSV     = $tableName.'_'.$row['IMPCSV_IDENTIFY'];
                //print($fileCSV);
		        $informationCSV = $this->getDataCronCSV($firstLineHeader, $fileCSV);
		       //print($actionType);
		        switch ($actionType) 
		        {
    	        	case "ADD": 
                        $totalCases = $this->importCreateCaseCSV($matchFields,$uidTask,$tableName,$firstLineHeader,$informationCSV);
                    break;
                
                  /*  case "ADD_DELETE": 
                        $matchFields = $dataImportCSV;
                        $uidTask     = isset($index["IMPCSV_TAS_UID"])? $index["IMPCSV_TAS_UID"]:'';
                        $tableName   = isset($index["IMPCSV_TABLE_NAME"])? $index["IMPCSV_TABLE_NAME"]:'';
                        $firstLineHeader   = isset($index["IMPCSV_FIRSTLINEHEADER"])? $index["IMPCSV_FIRSTLINEHEADER"]:'on';
                        $dataDelete  = isset($index["IMPCSV_CONDITION_ACTION"])?$index["IMPCSV_CONDITION_ACTION"]:'';
                        $totalCases = importCreateCaseDelete($matchFields,$uidTask,$tableName,$firstLineHeader, $dataDelete);
                        break;
                   case "ADD_UPDATE": 
                        $matchFields = $dataImportCSV;
                        $uidTask     = isset($index["IMPCSV_TAS_UID"])? $index["IMPCSV_TAS_UID"]:'';
                        $tableName   = isset($index["IMPCSV_TABLE_NAME"])? $index["IMPCSV_TABLE_NAME"]:'';
                        $firstLineHeader   = isset($index["IMPCSV_FIRSTLINEHEADER"])? $index["IMPCSV_FIRSTLINEHEADER"]:'on';
                        $dataEdit  = isset($index["IMPCSV_CONDITION_ACTION"])?$index["IMPCSV_CONDITION_ACTION"]:'';
                        $totalCases = importCreateCaseEdit($matchFields,$uidTask,$tableName,$firstLineHeader,$dataEdit);
                        break;*/
                }  
  		    }
		}
		
		
	}
	
	function getDataCronCSV($firstLineCsvAs = 'on', $fileCSV)
	{
	    ($fileCSV);
        //$pathFile = fopen("/opt/processmaker/workflow/engine/plugins/convergenceList/csvTmp/".$fileCSV.".csv", "r");
        set_include_path(PATH_PLUGINS . 'convergenceList' . PATH_SEPARATOR . get_include_path());
        if (!$handle = fopen("/opt/processmaker/workflow/engine/plugins/convergenceList/csvTmp/".$fileCSV.".csv", "r")) {  
            echo "Cannot open file";  
            exit;  
        } 
        $csvData = array(); 
        $csvDataIni = array();
        while ($data = fgetcsv($handle, 4096, ";")) {
            $num = count ($data);
            $i = 0; 
            foreach($data as $row) {

               $csvDataIni[]= $row;

            }
             $csvData[] = $csvDataIni;
            $csvDataIni = '';
        }
        
        return $csvData;
    }
	
    function importCreateCaseCSV($jsonMatchFields,$uidTask, $tableName,$firstLineHeader,$informationCSV)
    {
       
        G::LoadClass('case');
        $items   =$jsonMatchFields; 
        $dataCSV = isset($informationCSV) ?$informationCSV: array();
        $USR_UID = '00000000000000000000000000000001';
        $_SESSION['USER_LOGGED_INI'] = $USR_UID;
        $proUid  = '87479663751a5c3a664a656077060757';
        $totalCases = 0;
    
        // load Dynaforms of process
    	 $select = "SELECT DYN_UID, PRO_UID, DYN_TYPE, DYN_FILENAME FROM wf_".$this->workspace.".DYNAFORM WHERE PRO_UID = '".$proUid ."'";
    		$resultDynaform = executeQuery($select);
    		
    		$_dataForms =  array();
    		foreach($resultDynaform As $rowDynaform)
    		{
    			$dynaform = new Form($proUid . PATH_SEP . $rowDynaform['DYN_UID'], PATH_DYNAFORM , SYS_LANG , false);
    			
    			foreach ($dynaform->fields as $fieldName => $field) {
    				if( $field->type == 'dropdown')
    				{
    					$aData = array();
    					$dataSQL = array();
    					$data = array();
    					if(strlen($field->sql))
    					{
    						$query = $field->sql;
    						$valueData = explode(",",$query); 
    						$valueId = explode(" ",$valueData[0]);
    						$position = count($valueId)-1 ;
    						$valueId = $valueId[$position];
    						$valueDataCount = count($valueData);
    						$valueName = explode(" ",$valueData[$valueDataCount-1]);
    						for($i = 0; $i <count($valueName) ; $i++)
    						{
    							if($valueName[$i]=="from" || $valueName[$i]=="FROM")
    							{
    								$dataName = $valueName[$i-1];
    								break;		
    							}
    						}
    						
    						$aData = executeQuery($field->sql);
    						
    					}	
    					if(sizeof($aData))
    					{
    						foreach($aData As $key => $row)
    						{
    							$rowData = array ( 'id'=>$row[$valueId],'descrip'=>$row[$dataName]);
    							$dataSQL[] = $rowData;
    						}
    					}
    					
    					if(sizeof($field->option))
    					{
    						foreach($field->option As $key => $row)
    						{
    							$rowData = array ( 'id'=>$key,'descrip'=>$row);
    							$data[] = $rowData;
    						}
    					}
    					
    					$record = array (
    							"FIELD_NAME" => $field->name, 
    							"FIELD_LABEL" => $field->label,
    							"FIELD_TYPE" => $field->type,
    							"FIELD_DEFAULT_VALUE" => $field->defaultValue,
    							"FIELD_DEPENDENT_FIELD" => $field->dependentFields,
    							"FIELD_OPTION" => $data,
    							"FIELD_READONLY" => $field->readonly,
    							"FIELD_SQL_CONNECTION" => $field->sqlConnection,
    							"FIELD_SQL" => $field->sql,
    							"FIELD_SQL_OPTION" => $dataSQL,
    							"FIELD_SELECTED_VALUE" => $field->selectedValue,
    							"FIELD_SAVE_LABEL" => $field->saveLabel
    					);
    					$_dataForms[] = $record;
    				}
    			}
    		}
    		
    	$select = executeQuery("SELECT MAX(IMPCSV_IDENTIFY) AS IDENTIFY FROM wf_".$this->workspace.".PMT_IMPORT_CSV_DATA WHERE IMPCSV_TABLE_NAME = '$tableName'");
        $identify = isset($select[1]['IDENTIFY'])? $select[1]['IDENTIFY']:0;
        $identify = $identify + 1;
        $csv_file = $tableName."_".$identify.".csv";  
    	$csv_sep = ";";  
    	$csv="";  
        $csv_end = "\n";
        $swInsert = 0;
       
        foreach ($dataCSV as $row) 
        {
            $appData =  array();
                foreach ($items as $field) { 
                    if($firstLineHeader == 'on'){
                	
                        if(isset($row[$field['COLUMN_CSV']]))
                        {
                    	    if($row[$field['COLUMN_CSV']])
                    		    $appData[$field['FIELD_NAME']] = utf8_encode($row[$field['COLUMN_CSV']]);
                    	    else
                    		    $appData[$field['FIELD_NAME']] = ' ';
                        }
                        else
                        {
                    	    if($field['COLUMN_CSV'])
                        	    $appData[$field['FIELD_NAME']] = utf8_encode($field['COLUMN_CSV']);
                            else
                        	    $appData[$field['FIELD_NAME']] = ' ';
                        } 
                    }
                    else
                    {
                        $aCol = explode(' ', trim($field['COLUMN_CSV']));
                        if( (isset($aCol[0]) && trim($aCol[0]) == 'Column' ) &&  ( isset($aCol[1]) && isset($row[$aCol[1]]) ) )
                            $appData[$field['FIELD_NAME']] = utf8_encode($row[$aCol[1]]);
                        else if ( ( isset($aCol[0])  &&  trim($aCol[0]) != 'Column' )  ){
                            $appData[$field['FIELD_NAME']] =  utf8_encode($field['COLUMN_CSV']);
                        }        
                    }
                }  
                $appData['FLAG_ACTION'] = 'multipleDerivation';
            
                $appData['EXEC_AUTO_DERIVATE'] = 'NO';
                $appData['eligible'] = 0; // only process beneficiary
                $appData['FLAG_EDIT'] = 1;
                $appData['CurrentUserAutoDerivate'] = $USR_UID;       
            
      		    $appData['SIRET'] = isset($appData['SIRET'])? $appData['SIRET']:'';
                $query = "SELECT APP_UID FROM wf_".$this->workspace.".PMT_PRESTATAIRE WHERE STATUT = 1 AND SIRET ='".$appData['SIRET']."'";
                $result= executeQuery($query);
                
                $caseUID = '0';
           
                 // labels //
            
    	        foreach($appData As $key => $fields)
    	        {
    		        foreach ($_dataForms As $row)
    				{
    					if($row['FIELD_DEFAULT_VALUE'] == '')
    						$row['FIELD_DEFAULT_VALUE'] = 0;
    					
    					if($key == $row['FIELD_NAME'])
    					{
    						$i = isset($fields)?$fields:$row['FIELD_DEFAULT_VALUE'];
    						
    						if(count($row['FIELD_SQL_OPTION']))
    						{
    							$options = $row['FIELD_SQL_OPTION'];
    							$id = "";
    							$label = "";
    							foreach($options As $row2)
    							{
    								if($row2['id'] == $i)
    								{
    									$id = $row2['id'];
    									$label = $row2['descrip'];
    									break;
    								}
    							}
    							
    							if($id=="" && $label=="")
    							{
    								$id = $row['FIELD_SQL_OPTION'][0]['id'];
    								$label = $row['FIELD_SQL_OPTION'][0]['descrip'];
    							}
    							
    							$record[$row['FIELD_NAME']] = $id;
    							$appData = array_merge($record,$appData);
    							$record[$row['FIELD_NAME']."_label"] = $label;
    							$appData = array_merge($record,$appData);
    						}
    						else
    						{
    							if(count($row['FIELD_OPTION']))
    							{
    								$options = $row['FIELD_OPTION'];
    								$id = "";
    								$label = "";
    								foreach($options As $row2)
    								{
    									if($row2['id'] == $i)
    									{
    										$id = $row2['id'];
    										$label = $row2['descrip'];
    										break;
    									}
    								}
    								
    								$record = Array();
    								
    								$record[$row['FIELD_NAME']] = $id;
    								$appData = array_merge($record,$appData);
    								$record[$row['FIELD_NAME']."_label"] = $label;
    								$appData = array_merge($record,$appData);
    								
    							}
    						}
    					}
    				}	
    	        }
    	       
    	        foreach($appData As $key => $fields)
    	        {
    	        	foreach ($_dataForms As $row)
    				{
    					if($row['FIELD_DEFAULT_VALUE'] == '')
    						$row['FIELD_DEFAULT_VALUE'] = 0;
    						
    					$appData[$row['FIELD_NAME']."_label"] = isset($appData[$row['FIELD_NAME']."_label"])? $appData[$row['FIELD_NAME']."_label"]:'';
    					
    					if($appData[$row['FIELD_NAME']."_label"] =="")
    					{
    						$i = $row['FIELD_DEFAULT_VALUE'];	
    						if(count($row['FIELD_SQL_OPTION']))
    						{
    							
    							$options = $row['FIELD_SQL_OPTION'];
    							$id = "";
    							$label = "";
    							foreach($options As $row2)
    							{
    								if($row2['id'] == $i)
    								{
    									$id = $row2['id'];
    									$label = $row2['descrip'];
    									break;
    								}
    							}
    							
    							if($id=="" && $label=="")
    							{
    								$id = $row['FIELD_SQL_OPTION'][0]['id'];
    								$label = $row['FIELD_SQL_OPTION'][0]['descrip'];
    							}
    							
    							$record[$row['FIELD_NAME']] = $id;
    							$appData = array_merge($record,$appData);
    							$record[$row['FIELD_NAME']."_label"] = $label;
    							$appData = array_merge($record,$appData);
    							
    						}
    						else
    						{
    							if(count($row['FIELD_OPTION']))
    							{
    								$options = $row['FIELD_OPTION'];
    								$id = "";
    								$label = "";
    								foreach($options As $row2)
    								{
    									if($row2['id'] == $i)
    									{
    										$id = $row2['id'];
    										$label = $row2['descrip'];
    										break;
    									}
    								}
    								
    								if($id=="" && $label=="")
    								{
    									$id = $row['FIELD_OPTION'][0]['id'];
    									$label = $row['FIELD_OPTION'][0]['descrip'];
    								}
    								
    								$record[$row['FIELD_NAME']] = $id;
    								$appData = array_merge($record,$appData);
    								$record[$row['FIELD_NAME']."_label"] = $label;
    								$appData = array_merge($record,$appData);
    								
    							}
    						}
    					}
    				}
    	        }
          
            // end labels //
       
                if(is_array($result) && count($result)>0){ 
                    $caseUID =$result[1]['APP_UID'];                    
                    $oCase = new Cases ();    
                    $FieldsCase = $oCase->loadCase ( $caseUID );  
                    $FieldsCase['APP_DATA'] = array_merge($FieldsCase['APP_DATA'],$appData);
                    $FieldsCase['APP_DATA']['NUM_DOSSIER'] = $FieldsCase['APP_NUMBER'];
                    $FieldsCase['APP_DATA']['STATUT'] = 1;
                    $oCase->updateCase($caseUID,$FieldsCase);
                
                }else{
                	 G::LoadClass('case');
                	//include (PATH_PLUGINS.'convergenceList/classes/class.pmFunctions.php');
                	//include (PATH_PLUGINS.'obladyConvergence/classes/class.pmFunctions.php');
    	            $caseUID = PMFNewCase($proUid, $USR_UID, $uidTask, $appData);    print_r($caseUID);
                    if($caseUID >0) {
    		    		
                    	$_SESSION['APPLICATION'] = $caseUID;
    		           
                    	$this -> autoDerivate($proUid,$caseUID,$USR_UID);
    	                $oCase = new Cases ();
    	                $FieldsCase = $oCase->loadCase ( $caseUID );
    	                $FieldsCase['APP_DATA']['NUM_DOSSIER'] = $FieldsCase['APP_NUMBER'];
    	                $FieldsCase['APP_DATA']['STATUT'] = 1;
    	                
    	                $oCase->updateCase($caseUID,$FieldsCase);
                    }
                }    
            
            $totalCases++;
        }
       
        
        unset($informationCSV);
        return $totalCases;
    }
    
    function autoDerivate($processId,$caseUID,$userId){	

	$query = "SELECT TAS_UID FROM TASK WHERE TAS_START = 'TRUE' AND PRO_UID = '".$processId."'";	//query for select all start tasks
	$startTasks = executeQuery($query);	
	$taskId = $startTasks[1]['TAS_UID'];
	$queryNextTask = "SELECT ROU_NEXT_TASK FROM ROUTE WHERE PRO_UID = '".$processId."' AND TAS_UID = '".$taskId."'";
	$taskNumber = 1;
	$NextTask = executeQuery($queryNextTask);
	if($NextTask[1]['ROU_NEXT_TASK'] == '-1')
		$taskNumber = 0;
	$userLoggedIni = $_SESSION['USER_LOGGED'];
	if(isset($_SESSION['USER_LOGGED_INI']) && $_SESSION['USER_LOGGED_INI'] != '')
		$userLoggedIni = $_SESSION['USER_LOGGED_INI'];
        
	foreach($startTasks as $rowTask){
		$this -> updateDateAPPDATA($caseUID);
		$taskId = $rowTask['TAS_UID'];
		$currentTask = $taskId;
		$process = $processId;
		$appUid = $caseUID;    
		$task = $taskId;	
		$this ->frderivateCase($processId, $currentTask , $caseUID,$userId,$taskNumber, $userLoggedIni);		//Function for derivate case
	}
	if($userLoggedIni !='')
		$_SESSION['USER_LOGGED'] = $userLoggedIni ; 
 
        
}

function frderivateCase($processId, $currentTask , $fcaseUID,$userId,$taskNumber, $userLoggedIni)
{
	try 
	{
		$sw = 0;
		while($sw == 0)
		{
	    	$_SESSION['APPLICATION'] = $fcaseUID;
			$queryDelIndex = "SELECT MAX(DEL_INDEX) AS DEL_INDEX FROM APP_DELEGATION WHERE APP_UID = '".$fcaseUID."'";
			$DelIndex = executeQuery($queryDelIndex);      
			$queryNextTask = "SELECT ROU_NEXT_TASK FROM ROUTE WHERE PRO_UID = '".$processId."' AND TAS_UID = '".$currentTask."'";
			$NextTask = executeQuery($queryNextTask);			
			$stepsByTask = $this ->getStepsByTask($currentTask);//FORM IDS in THE TASK			
			$caseStepRes = array();
			if(isset($DelIndex[1]['DEL_INDEX']) && $DelIndex[1]['DEL_INDEX'] != ''){
	        	$queryDel = "SELECT * FROM APP_DELEGATION WHERE APP_UID = '".$fcaseUID."' AND DEL_INDEX = '".$DelIndex[1]['DEL_INDEX']."' ";
	        	$resDel = executeQuery($queryDel);
	        	if(sizeof($resDel)){
	        		if($resDel[1]['USR_UID'] == ""){
	            		$queryuPDel = "UPDATE APP_DELEGATION SET USR_UID = '".$userLoggedIni."' 
	            		WHERE APP_UID = '".$fcaseUID."' AND DEL_INDEX = '".$DelIndex[1]['DEL_INDEX']."' ";
	            		$queryuPDel = executeQuery($queryuPDel);
	            		$userId = $userLoggedIni;
	          		}
	        		elseif(isset($_SESSION['USER_LOGGED_INI']) && $_SESSION['USER_LOGGED_INI'] != '' && $DelIndex[1]['DEL_INDEX']!= 1)
	        		{
	        			/*$queryuPDel = "UPDATE APP_DELEGATION SET USR_UID = '".$userLoggedIni."' 
	            		WHERE APP_UID = '".$fcaseUID."' AND DEL_INDEX = '".$DelIndex[1]['DEL_INDEX']."' ";
	            		$queryuPDel = executeQuery($queryuPDel);*/
	            		$userId = $resDel[1]['USR_UID'];
	        		}
	        	}
			}
			
			foreach ($stepsByTask as $caseStep){
				$caseStepRes[] = 	 $caseStep->getStepUidObj();
			}
			//G::pr($caseStepRes);
			$totStep = 0;
			foreach($caseStepRes as $index)
			{
				$stepUid = $index;
				$this ->executeTriggersMon($processId, $fcaseUID, $stepUid, 'BEFORE', $currentTask);	//execute trigger before form
				$this ->executeTriggersMon($processId, $fcaseUID, $stepUid, 'AFTER', $currentTask);	//execute trigger after form	
				$totStep++;
			} 
			G::LoadClass( 'wsBase' );
	    	$ws = new wsBase();
			if($NextTask[1]['ROU_NEXT_TASK'] == '-1')
			{
				$stepUid = -1;							
				$beforeA = true;
				if($taskNumber == 0){
					$beforeA = false;
				}
				else 
				{
					$this ->executeTriggersMon($processId, $fcaseUID, $stepUid, 'BEFORE', $currentTask);	//execute trigger before form
					$this ->executeTriggersMon($processId, $fcaseUID, $stepUid, 'AFTER', $currentTask);	//execute trigger after form	
				}							
				//$control = PMFDerivateCase($fcaseUID, $DelIndex[1]['DEL_INDEX'], $beforeA, $userId);
				
	    		$result = $ws->derivateCase( $userId, $fcaseUID, $DelIndex[1]['DEL_INDEX'], $beforeA );   
	    		$rpta = $result['status_code'];
				$sw = 1;
			}
			else
			{
				if($totStep == 0)
					$this ->executeTriggersMon( $processId, $fcaseUID, -1, 'BEFORE', $currentTask );
				//$control = PMFDerivateCase($fcaseUID, $DelIndex[1]['DEL_INDEX'], false, $userId);
				$result = $ws->derivateCase( $userId, $fcaseUID, $DelIndex[1]['DEL_INDEX'], false );  //G::pr($result);
	    		//$rpta = $result['status_code'];	
				$queryDelIndex = "SELECT MAX(DEL_INDEX) AS DEL_INDEX FROM APP_DELEGATION WHERE APP_UID = '".$fcaseUID."'";
				$DelIndex = executeQuery($queryDelIndex); 
				$queryDel = "SELECT TAS_UID FROM APP_DELEGATION WHERE APP_UID = '".$fcaseUID."' AND DEL_INDEX = '".$DelIndex[1]['DEL_INDEX']."' ";
	        	$resDel = executeQuery($queryDel);
				$currentTask = $resDel[1]['TAS_UID']; 
			}
				
		}
	} 
	catch (Exception $e) 
	{
		$err = $e->getMessage();
		$err = preg_replace("[\n|\r|\n\r]", ' ', $err);
		die($err);
	}			

	
}


function updateDateAPPDATA($application){
 
 $caseInstance = new Cases();
 $caseFields = $caseInstance->loadCase( $application ); 
 $caseInstance->updateCase($application, $caseFields);
}


function getStepsByTask($task){
		require_once 'classes/model/Step.php';
	  $c = new Criteria();
    $c->addSelectColumn('*');    	
		    $c->setDistinct();
		    $c->add(StepPeer::TAS_UID, $task);
		    $c->addAscendingOrderByColumn (StepPeer::STEP_POSITION);		    
		    $caseSteps =  StepPeer::doSelect($c);  
				return $caseSteps;
		}
		
	
function executeTriggersMon($process, $appUid, $stepUid, $time='BEFORE', $task){
  
  $type = $this ->getStepType($stepUid);
  //$type = '';
  $oCase = new Cases();
  $Fields = $oCase->loadCase($appUid);  
  if($stepUid == -1){
  	$obj = 'ASSIGN_TASK';
  }else{
  	$obj = 'DYNAFORM';  	
  }
  $triggers = $oCase->loadTriggers ( $task, $obj, $stepUid, $time );  
 /* G::pr($triggers);	
  print($task.'  '. $obj .'  '. $stepUid.'  '. $time);*/
  $Fields['APP_DATA'] = $oCase->ExecuteTriggers($task, $type , $stepUid, $time, $Fields['APP_DATA'] ); 
  $oCase->updateCase($appUid, $Fields);
  return true;
}

function getStepType($step){
		$task = executeQuery("SELECT * FROM STEP WHERE STEP_UID_OBJ = '".$step."'");
	  return $task[1]['STEP_TYPE_OBJ'];
}




}
