<?
G::LoadClass('pmFunctions');
$processId = '718104157515d487b01e384008299230';
$userId = '00000000000000000000000000000001';
$query = "SELECT TAS_UID FROM TASK WHERE TAS_START = 'TRUE' AND PRO_UID = '".$processId."'";	//query for select all start tasks
$startTasks = executeQuery($query);
$taskId = $startTasks[1]['TAS_UID'];
$data['NAME'] = '777';
$data['FIRSTNAME'] = '1';
$data['EIE'] = '1';

$data['ID_USER'] = $userId;
$caseUID = PMFNewCase($processId, $userId, $taskId, $data);
$a = autoDerivate($processId,$caseUID,$userId);
print_r($a);
?>