<?php
/**
 * class.elock.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// elock PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////
//define(sPluginFolder,'elock');
include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');


function elockLogin($userId,$strPassword){
$newElockObj = new elockClass();
  
  return $newElockObj->elockLogin($userId,$strPassword);
  
}


function signDocumentInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$sessionId){
$newElockObj = new elockClass();
  
  return $newElockObj->signDocumentInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$sessionId);
  
}

function signDataInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$operatorAuthToken){
$newElockObj = new elockClass();
  
  return $newElockObj->signDataInMemory($data,$filePath,$outFilePath,$bDetachedSignature,$operatorAuthToken);
}



function verifyData($base64EncodedSig,$originalData,$operatorAuthToken)
{

$newElockObj = new elockClass();
  
  return $newElockObj->verifyData($base64EncodedSig,$originalData,$operatorAuthToken);
  

}




function GetOperationStatusString($operatorAuthToken){

$newElockObj = new elockClass();
  
  return $newElockObj->GetOperationStatusString($operatorAuthToken);
  
}



function GetUserDetails($userId,$operatorAuthToken){
  $newElockObj = new elockClass();
  
return $newElockObj->GetUserDetails($userId,$operatorAuthToken);
}





function AddNewSignUser($userId,$displayName,$userType,$userDN,$pwd,$email,$appPassword){

 $newElockObj = new elockClass();
  
return $newElockObj->AddNewSignUser($userId,$displayName,$userType,$userDN,$pwd,$email,$appPassword);
}




function ChangeUserProfile($userId,$displayName,$userType,$userDN,$pwd,$email,$appPassword){


	global $fields;

	$clName = 'elockClass';
	//$wsLink = "http://elock.firesalesup.com:8010/mobiSignerServer?wsdl";

	$wsLink =  ($fields['ElockWebService_WSDL']);

	$paramArr = array();
	//$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


	$userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

	$sigObj = new $clName();
	$sigObj->setwsdlurl($wsLink);

	$paramChangeUserProfile = array("userId"=>$userId,"displayName"=>$displayName,"userType"=>$userType,"userDN"=>$userDN,"pwd"=>$pwd,"email"=>$email,"appPassword"=>$appPassword);
	//var_dump($paramChangeUserProfile);
	$ChangeUserProfileResultObject =  $sigObj->callWsMethod("ChangeUserProfile",$paramChangeUserProfile);
	echo "this is the one";
	//var_dump($ChangeUserProfileResultObject);

	//var_dump($VerifySignedDataResultObject);
	$ChangeUserProfileResult = $ChangeUserProfileResultObject->ChangeUserProfileResult;

	//echo "result Data";
	//echo "$VerifySignedDataResult[0]";
	//var_dump($VerifySignedDataResult);
	//echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
	//var_dump($ChangeUserProfileResult);
	return $ChangeUserProfileResult;



}



function GetAllSignerList($operatorAuthToken){
  
 $newElockObj = new elockClass();
  
return $newElockObj->GetAllSignerList($operatorAuthToken);
}







function SetSigningParameters($userId,$signatureImageFilePath,$operatorAuthToken)
{


    		global $fields;

		$clName = 'elockClass';
		//$wsLink = "http://elock.firesalesup.com:8010/mobiSignerServer?wsdl";

                $wsLink =  ($fields['ElockWebService_WSDL']);

		$paramArr = array();
		//$paramOperatorUser = array("userId"=>$userId, "strPassword"=>$strPassword);


		$userCreateParams = array();//UefVmgpndnlw.uuVfgdnin

		$sigObj = new $clName();
		$sigObj->setwsdlurl($wsLink);

	
	//var_dump($paramChangeUserProfile);
          $paramSetSigningParameters = array("signerId"=>$userId,"signatureImageFilePath"=>$signatureImageFilePath,"sessionId"=>$operatorAuthToken);
          var_dump($paramSetSigningParameters);
	$SetSigningParametersResultObject =  $sigObj->callWsMethod("SetSigningParameters",$paramSetSigningParameters);
        
        //var_dump($ChangeUserProfileResultObject);

	//var_dump($VerifySignedDataResultObject);
	$SetSigningParametersResult = $SetSigningParametersResultObject->SetSigningParametersResult;

	//echo "result Data";
	//echo "$VerifySignedDataResult[0]";
	//var_dump($VerifySignedDataResult);
	//echo "paramGetOperationStatusString Result: " . $GetOperationStatusStringResultObject;
	//var_dump($ChangeUserProfileResult);
        return $SetSigningParametersResult;



}