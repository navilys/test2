<?php

G::LoadClass('configuration');
G::loadClass('pmFunctions');
G::LoadClass('case');


$G_PUBLISH = new Publisher;
 
	$users=$_SESSION['USER_LOGGED'];
	$aDatosUser = userInfo($_SESSION['USER_LOGGED']);
    $Us = new Users();
	$Rol=$Us->load($users);
	$usr_rol=$Rol['USR_ROLE'];	
	include('welcome.html');

?>
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type">
<link href="/plugin/convergenceList/welcomeCss.css" type="text/css" rel="stylesheet">




