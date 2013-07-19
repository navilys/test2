<?php


class variableStruct {
   public $name;
   public $value;
 }

$option=array('trace'=>1);
$client = new SoapClient('http://172.17.20.29/sysfred/en/classic/services/wsdl2',$option);

try{
    $result = $client->__SoapCall('login', array(array('userid'=>'test2', 'password'=>'azerty')));
    $sessionID = $result->message;
    
    //Get Process
    $result = $client->__SoapCall('processList', array(array('sessionId' => $sessionID)));
    $processID =  $result->processes[3]->guid;
    
    //Get Task
    $result = $client->__SoapCall('taskList', array(array('sessionId'=>$sessionID)));
    $tasks = array_shift($result->tasks);
    $taskID = $tasks->guid;
    
    $name = new variableStruct();
    $vars = array(
        'Nom'    => 'Rousseau',
        'Prenom' => 'Arnaud'
    );
    $aVars = array();
    foreach ($vars as $key => $val){ 
        $obj = new variableStruct();
        $obj->name = $key;
        $obj->value = $val;
        $aVars[] = $obj;	 
    }
    
    $params = array(array(
        'sessionId' => $sessionID, 
        'processId' => $processID, 
        'taskId'    => $taskID,
        'variables' => $aVars)
    );
  
    $result = $client->__SoapCall('newCase', $params);
    $caseID = $result->caseId;
    
    $params = array(array(
        'sessionId' => $sessionID,
        'caseId'    => $caseID,
      //  'triggerIndex' => '69634489450c606ce0000a2070881075',
        'delIndex'  => '1',
    ));
    
    $result = $client->__SoapCall('routeCase',$params);
    $params = array(array(
        'sessionId' => $sessionID,
        'caseId'    => $caseID,
        'delIndex'  => '2',
    ));
    
    $result = $client->__SoapCall('routeCase',$params);


	
    var_dump($result);
    
    
}catch(SoapFault $fault){
    var_dump($fault);
}




