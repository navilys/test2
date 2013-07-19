<?php

include_once("class.elock.php");
$elock=new elockClass();
G::pr($elock);


define ( 'WS_WSDL_URL' ,  'http://192.168.1.40:8010/mobiSignerServer?wsdl');
define ( 'WS_USER_ID'  ,  'ankit');
define ( 'WS_USER_PASS',  'ankit');

global $sessionId;
global $client;

$session = $elock->ws_elock_open();

 $params = array (
    'userId' => 'ankit',
    'displayName' => 'Ankit Mishra',
    'userType' => 2,
    'userDN'=> 'CN=Ankit Mishra;C=IN;', 
    'pwd'=> 'test',
    'email'=> 'ankit.mishra@bistasolutions.com',
    'appPassword'=> 'PetVbgtnbntwbu'
  );

$elock->AddUser($params);



/* this method is used to create a work space */
$params = array (
    'workspaceId' => '',   // not required
    'workspaceName' => 'New Work Space',
    'workspaceDescription' => 2,
    'signerId'=> 'ankit',   // username
    'sessionId'=> $sessionId);
$workSpaceId = $elock->CreateWorkspace($params);



/* this method is used to create a work flow */
$params = array (
    'workspaceIds' => '$workSpaceId',
    'sessionId'=> $sessionId);

$boolean = $elock->CreateWorkflow($params);



/*this method is used for adding file on the workspace */
$params = array (
    'workspaceIds' => '$workSpaceId',
    'sessionId'=> $sessionId,
    'serverPath' => $client );

$boolean = $elock->AddFiles($params);




/*this method is used to check whether StartProcess can be called on the workspace  */
$params = array (
    'workspaceIds' => '$workSpaceId',
    'sessionId'=> $sessionId);

/*this method is used for checking whether the workspace is ready to start the process or not*/
$boolean = $elock->IsWorkspaceReadyToStartProcess($params);



/* this method will start the process */
$boolean = $elock->StartProcess($params);


/* this method will logout the current user */
$boolean = $elock->LogOut($params);


/* this method will login the user signer  */
$session = $elock->ws_elocksigner_open();


/* this method will retrieve workspace paths which needs to be signed by current logged in signer */
$workSpaceID = $elock->GetSigningWorkspacePaths($session);


/*this method is used to check whether StartProcess can be called on the workspace  */
$params = array (
    'sigVisibilityPos' => '3', // bottom-left corner  
    'sigPagePos' =>'0', //   Last page 
    'reason'=>'test',
    'location'=>'mumbai',
    'hashAlgo'=>'MD5', // MD5, SHA-1, SHA-256
    'sessionId'=> $sessionId);

$boolean = $elock->SetSigAppearanceParam($params);



/* This method can be called by Signer OR Operator. If Signer Id is blank the signature image will be uploaded for current user by checking the currently logged in user is signer or not */
$params = array (
    'signerId' => 'girish',
    'signatureImageFilePath' =>'', //path of image
    'sessionId'=> $sessionId);

$boolean = $elock->SetSigningParameters($params);



/* Starts the signing process. Signer of the workspace calls this method, to initialize the signing process  */
$params = array (
    'workspaceId' => '$workSpaceId',
    'signingReason' =>'test',
    'signingLocation'=>'mumbai',
    'sessionId'=> $sessionId);

$boolean = $elock->SignWorkspaceFiles($params);


/* this method will logout the current user */
$boolean = $elock->LogOut($params);






