<?php

G::LoadClass("webResource");
G::LoadClass("pmFunctions");

ini_set('display_errors', 0);

class ajax_brd extends WebResource
{
 function verify_user($username){
  	$res = '';
  	$query = "SELECT USR_FIRSTNAME, USR_LASTNAME FROM USERS where USR_USERNAME = '$username' ";
	$result = executeQuery($query);
	if(isset($result))
		$res = $result[1]['USR_FIRSTNAME']." ". $result[1]['USR_LASTNAME'];

	return $res;
	}

	function  getRules(){
	G::LoadClass('case');
	$status = true;
	$oCase = new Cases();
	$Fields = $oCase->loadCase($_SESSION['APPLICATION']);
	
	if($Fields['APP_DATA']['username'] > 40)
		$status = false;

	return $status;
	}

 }

$o = new ajax_brd( $_SERVER['REQUEST_URI'], $_POST );
?>