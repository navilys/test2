<?php
/**
 * @section Filename
 * dashboardView.php
 * @subsection Description
 * Display the default/dashboard that a user have access and is assigned.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

require_once 'class.pentahoreports.php';
require_once 'classes/model/PhRoleReport.php';
require_once 'classes/model/PhUserRole.php';
require_once 'classes/model/PhRole.php';
require_once 'classes/model/PhReport.php';

/**
 * The object of the main pentaho reports class
 */
$objPentaho = new pentahoreportsClass();
$objPentaho->readConfig();

/**
 * The user role relation object
 */
$userRole = new PhUserRole();

/**
 * The current workspace 
 */
$solution = SYS_SYS;

/**
 * The default dashboard
 */
$defaultName = 'report_efficiency.xcdf';

/**
 * The path of the dashboard file
 */
$filePath = '';

/**
 * The current logged user
 */
$userUid = $_SESSION['USER_LOGGED'];

/**
 * The Roles that a user is assigned to
 */
$usersAndRoles = $userRole->getRolesByUser($userUid);

/**
 * This variable checks if the dashboard is set for the current user
 */
$isSet = false;
// according the display order the first is the user
foreach ($usersAndRoles as $role){
  if ($role['OBJ_TYPE']=='USER' && $role['OBJ_DASHBOARD']!='0'){
    $fileName = $role['OBJ_DASHBOARD'];
    $isSet = true;
    break;
  }
}
// if a user default dashborad has been not found, search by groups
if (!$isSet){
  foreach ($usersAndRoles as $role){
    if ($role['OBJ_TYPE']=='GROUP' && $role['OBJ_DASHBOARD']!='0'){
      $fileName = $role['OBJ_DASHBOARD'];
      $isSet = true;
      break;
    }
  }
}
// if a group default dashborad has been not found, search by departments
if (!$isSet){
  foreach ($usersAndRoles as $role){
    if ($role['OBJ_TYPE']=='DEPARTMENT' && $role['OBJ_DASHBOARD']!='0'){
      $fileName = $role['OBJ_DASHBOARD'];
      $isSet = true;
      break;
    }
  }
}
// if any has been found display the default dashboard
if (!$isSet){
  $fileName = $defaultName;
}
$browser = G::browser_detection('full_assoc');
if ($browser['browser_name'] == 'msie'){
   $content =  '<div id="homePentaho" style="overflow: hidden; position: absolute; left: 0; width: 100%; height: 420px;">';  
}else{
   $content =  '<div id="homePentaho" style="position: absolute; height: 100%; width: 100%;">';
}
$content .= $objPentaho->viewAction($solution, $filePath, $fileName, $userUid);
$content .= '</div>';  
echo($content);
?>