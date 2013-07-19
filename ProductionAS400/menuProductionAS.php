<?php
  G::LoadClass('pmFunctions');
  global $G_TMP_MENU;
  require_once ("classes/model/Users.php");
  # Variables
  $users=$_SESSION['USER_LOGGED'];
  $Us = new Users();
  $Roles=$Us->load($users);
  $rolesAdmin=$Roles['USR_ROLE'];
  $LegalGroups = array('','','');
  # End Variables  
  
  # Permissions     
   $G_TMP_MENU->AddIdRawOption('ID_CONFIGURATION', 'ProductionAS400/configurationOptions', "Configuration" );
   
  # End Permissions  
?>