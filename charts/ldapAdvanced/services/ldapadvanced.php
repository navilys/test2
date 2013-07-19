<?php
/**
 * processImportSyncAjax.php
 * @file
 * The following script is the cron service that the plugin attaches to the
 * main cron in order to synchronize automatically.
 * @author
 * Colosa Development Team 2010
 * @copyright
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * @package bin.plugins.ldapAdvanced
 */

ini_set('memory_limit', '128M');
//error_reporting(0);
if (!defined('SYS_LANG')) {
	define('SYS_LANG', 'en');
}

if (!defined('PATH_HOME')) {
  if ( !defined('PATH_SEP') ) {
    define('PATH_SEP', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? '\\' : '/');
  }
  $docuroot = explode(PATH_SEP, str_replace('engine' . PATH_SEP . 'methods' . PATH_SEP . 'services', '', dirname(__FILE__)));
  array_pop($docuroot);
  array_pop($docuroot);
  $pathhome = implode(PATH_SEP, $docuroot) . PATH_SEP;
  //try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
  //in a normal installation you don't need to change it.
  array_pop($docuroot);
  $pathTrunk = implode(PATH_SEP, $docuroot) . PATH_SEP ;
  array_pop($docuroot);
  $pathOutTrunk = implode( PATH_SEP, $docuroot) . PATH_SEP ;
  // to do: check previous algorith for Windows  $pathTrunk = "c:/home/";

  define('PATH_HOME',     $pathhome);
  define('PATH_TRUNK',    $pathTrunk);
  define('PATH_OUTTRUNK', $pathOutTrunk);

  //***************** In this file we cant to get the PM paths , RBAC Paths and Gulliver Paths  ************************
  require_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
  //***************** In this file we cant to get the PM definitions  **************************************************
  require_once (PATH_HOME . PATH_SEP . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php');
  //require_once (PATH_THIRDPARTY . 'krumo' . PATH_SEP . 'class.krumo.php');
  //***************** Call Gulliver Classes **************************
  //G::LoadThirdParty('pear/json','class.json');
  //G::LoadThirdParty('smarty/libs','Smarty.class');

  G::LoadThirdParty('pear/json','class.json');
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem('error');
  G::LoadSystem('dbconnection');
  G::LoadSystem('dbsession');
  G::LoadSystem('dbrecordset');
  G::LoadSystem('dbtable');
  G::LoadSystem('rbac' );
  G::LoadSystem('publisher');
  G::LoadSystem('templatePower');
  G::LoadSystem('xmlDocument');
  G::LoadSystem('xmlform');
  G::LoadSystem('xmlformExtension');
  G::LoadSystem('form');
  G::LoadSystem('menu');
  G::LoadSystem("xmlMenu");
  G::LoadSystem('dvEditor');
  G::LoadSystem('table');
  G::LoadSystem('pagedTable');
  require_once ( "propel/Propel.php" );
  require_once ( "creole/Creole.php" );
}

//******* main program ********************************************************************************************************

require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Event.php';
require_once 'classes/model/AppEvent.php';
require_once 'classes/model/CaseScheduler.php';
//G::loadClass('pmScript');

//default values
$bCronIsRunning = false;
$sLastExecution = '';
if ( file_exists(PATH_DATA . 'cron') ) {
  $aAux = unserialize( trim( @file_get_contents(PATH_DATA . 'cron')) );
  $bCronIsRunning = (boolean)$aAux['bCronIsRunning'];
  $sLastExecution = $aAux['sLastExecution'];
}
else {
  //if not exists the file, just create a new one with current date
  @file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' => '1', 'sLastExecution' => date('Y-m-d H:i:s'))));
}

if (!defined('SYS_SYS')) {
  $sObject = $argv[1];
  $sNow    = $argv[2];
  $sFilter = '';

  for($i=3; $i<count($argv); $i++){
      $sFilter .= ' '.$argv[$i];
  }

  $oDirectory = dir(PATH_DB);

  if (is_dir(PATH_DB . $sObject)) {
    //saveLog ( 'main', 'action', "checking folder " . PATH_DB . $sObject );
    if (file_exists(PATH_DB . $sObject . PATH_SEP . 'db.php')) {

      define('SYS_SYS', $sObject);

      include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths_installed.php');
      include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths.php');

      //***************** PM Paths DATA **************************
      define( 'PATH_DATA_SITE',                 PATH_DATA      . 'sites/' . SYS_SYS . '/');
      define( 'PATH_DOCUMENT',                  PATH_DATA_SITE . 'files/' );
      define( 'PATH_DATA_MAILTEMPLATES',        PATH_DATA_SITE . 'mailTemplates/' );
      define( 'PATH_DATA_PUBLIC',               PATH_DATA_SITE . 'public/' );
      define( 'PATH_DATA_REPORTS',              PATH_DATA_SITE . 'reports/' );
      define( 'PATH_DYNAFORM',                  PATH_DATA_SITE . 'xmlForms/' );
      define( 'PATH_IMAGES_ENVIRONMENT_FILES',  PATH_DATA_SITE . 'usersFiles'.PATH_SEP);
      define( 'PATH_IMAGES_ENVIRONMENT_USERS',  PATH_DATA_SITE . 'usersPhotographies'.PATH_SEP);

      if(is_file(PATH_DATA_SITE.PATH_SEP.'.server_info')){
        $SERVER_INFO = file_get_contents(PATH_DATA_SITE.PATH_SEP.'.server_info');
        $SERVER_INFO = unserialize($SERVER_INFO);
        define( 'SERVER_NAME',  $SERVER_INFO ['SERVER_NAME']);
        define( 'SERVER_PORT',  $SERVER_INFO ['SERVER_PORT']);
      } else {
        eprintln("WARNING! No server info found!", 'red');
      }

      $sContent = file_get_contents(PATH_DB . $sObject . PATH_SEP . 'db.php');

      $sContent = str_replace('<?php', '', $sContent);
      $sContent = str_replace('<?', '', $sContent);
      $sContent = str_replace('?>', '', $sContent);
      $sContent = str_replace('define', '', $sContent);
      $sContent = str_replace("('", "$", $sContent);
      $sContent = str_replace("',", '=', $sContent);
      $sContent = str_replace(");", ';', $sContent);

      eval($sContent);
      $dsn = $DB_ADAPTER . '://' . $DB_USER . ':' . $DB_PASS . '@' . $DB_HOST . '/' . $DB_NAME;
      $dsnRbac = $DB_ADAPTER . '://' . $DB_RBAC_USER . ':' . $DB_RBAC_PASS . '@' . $DB_RBAC_HOST . '/' . $DB_RBAC_NAME;
      $dsnRp = $DB_ADAPTER . '://' . $DB_REPORT_USER . ':' . $DB_REPORT_PASS . '@' . $DB_REPORT_HOST . '/' . $DB_REPORT_NAME;
      switch ($DB_ADAPTER) {
        case 'mysql':
          $dsn     .= '?encoding=utf8';
          $dsnRbac .= '?encoding=utf8';
        break;
        case 'mssql':
        break;
        default:
        break;
      }
      $pro['datasources']['workflow']['connection'] = $dsn;
      $pro['datasources']['workflow']['adapter'] = $DB_ADAPTER;
      $pro['datasources']['rbac']['connection'] = $dsnRbac;
      $pro['datasources']['rbac']['adapter'] = $DB_ADAPTER;
      $pro['datasources']['rp']['connection'] = $dsnRp;
      $pro['datasources']['rp']['adapter'] = $DB_ADAPTER;
      $oFile = fopen(PATH_CORE . 'config/_databases_.php', 'w');
      fwrite($oFile, '<?php global $pro;return $pro; ?>');
      fclose($oFile);
      Propel::init(PATH_CORE . 'config/_databases_.php');


      eprintln("Processing workspace: " . $sObject, 'green');
      try{
      }catch(Exception $e){
        echo  $e->getMessage();
        eprintln("Probelm in workspace: " . $sObject.' it was ommited.', 'red');
      }
      eprintln();
    }
  }
  unlink(PATH_CORE . 'config/_databases_.php');
}

/////////////
  require_once 'classes/model/Department.php';

  require_once(PATH_RBAC.'model/AuthenticationSource.php');
  require_once(PATH_RBAC.'model/Permissions.php');
  require_once(PATH_RBAC.'model/Systems.php');
  require_once(PATH_RBAC.'model/RolesPermissions.php');

  require_once(PATH_RBAC.'model/RbacUsersPeer.php');
  require_once(PATH_RBAC.'model/om/BaseRbacUsers.php');
  require_once(PATH_RBAC.'model/UsersRolesPeer.php');
  require_once(PATH_RBAC.'model/om/BaseUsersRoles.php');
  require_once(PATH_RBAC.'model/RolesPeer.php');
  require_once(PATH_RBAC.'model/om/BaseRoles.php');
  require_once(PATH_RBAC.'model/UsersRoles.php');
  require_once(PATH_RBAC.'model/RbacUsers.php');
  require_once(PATH_RBAC.'model/Permissions.php');

  require_once(PATH_RBAC.'model/Systems.php');
  require_once(PATH_RBAC.'model/RolesPermissions.php');
  require_once(PATH_RBAC.'model/om/BaseRoles.php');
  require_once(PATH_RBAC.'model/om/BaseRbacUsers.php');
  require_once(PATH_RBAC.'model/om/BaseUsersRoles.php');
  require_once(PATH_RBAC.'model/Roles.php');

  require_once 'classes/model/Content.php';

  if (!class_exists('ldapAdvanced')){
    //require_once PATH_PLUGINS.'ldapAdvanced/class.ldapAdvanced.php';
    require_once PATH_RBAC.'plugins/class.ldapAdvanced.php';
  }


class ldapadvancedClassCron {
  var $already              = 0; //count for already existing users
  var $removed              = 0; //users in the removed OU
  var $created              = 0; //users created
  var $moved                = 0; //users moved from a Department to another Department
  var $impossible           = 0; //users already created using another Authentication source
  var $managersHierarchy    = array();
  var $oldManagersHierarchy = array();
  var $managersToClear      = array();
  var $deletedManager       = 0;

  function __construct (  ) {
  }

  /**
    function executed by the cron
    this function will synchronize users from ldap/active directory to PM users tables
    @return void
  */
  public function executeCron(){
    $RBAC   =& RBAC::getSingleton();
    if (is_null($RBAC->authSourcesObj)) {
      $RBAC->authSourcesObj = new AuthenticationSource();
    }
    $plugin =  new ldapAdvanced();
    $plugin->sSystem     = $RBAC->sSystem;

    // Get all authsource for this plugin ( ldapAdvanced plugin, because other authsources are not needed )
    $aAuthSources = $plugin->getAuthSources();

    $aDepartments = $plugin->getDepartments('');
    $aGroups = $plugin->getGroups('');

    $arrayDepartmentUserAd = array();
    $arrayGroupUserAd = array();

    foreach ($aAuthSources as $authSource) {
      eprintln();
      $plugin->log(null, "Executing cron for Authentication Source: " . $authSource["AUTH_SOURCE_NAME"]);

      $plugin->sAuthSource = $authSource["AUTH_SOURCE_UID"];
      $plugin->oLink = null;

      //Get all departments from Ldap/ActiveDirectory and build a hierarchy using dn (ou->ou parent)
      $aLdapDepts = $plugin->searchDepartments();

      //Obtain all departments from PM with a valid department in LDAP/ActiveDirectory
      $aRegisteredDepts = $plugin->getRegisteredDepartments($aLdapDepts,$aDepartments);

      //Get all group from Ldap/ActiveDirectory
      $aLdapGroups = $plugin->searchGroups();

      //Obtain all groups from PM with a valid group in LDAP/ActiveDirectory
      $aRegisteredGroups = $plugin->getRegisteredGroups($aLdapGroups,$aGroups);

      //Get all users from Removed OU
      $this->usersRemovedOu = $plugin->getUsersFromRemovedOu( $authSource);
      $plugin->deactiveArrayOfUsers($this->usersRemovedOu);

      //Get all user (UID, USERNAME) registered in RBAC with this authSource
      $this->aUsersAuthSource = $plugin->getUsersFromAuthSource($authSource['AUTH_SOURCE_UID']);
      $this->already = 0; //already exist
      $this->removed = count($this->usersRemovedOu);
      $this->created = 0;
      $this->moved = 0;
      $this->impossible = 0;

      //Department
      foreach ($aRegisteredDepts as $registeredDept) {
          if (!isset($arrayDepartmentUserAd[$registeredDept["DEP_UID"]])) {
              $arrayDepartmentUserAd[$registeredDept["DEP_UID"]] = array(); //Current users in department based in Active Directory
          }

          $arrayAux = $this->synchronizeAllDepartmentUsers($registeredDept, $plugin, $authSource);
          $arrayAux = array_merge($arrayDepartmentUserAd[$registeredDept["DEP_UID"]], $arrayAux);

          $arrayDepartmentUserAd[$registeredDept["DEP_UID"]] = array_unique($arrayAux);
      }

      //Group
      foreach ($aRegisteredGroups as $registeredGroup) {
          if (!isset($arrayGroupUserAd[$registeredGroup["GRP_UID"]])) {
              $arrayGroupUserAd[$registeredGroup["GRP_UID"]] = array(); //Current users in group based in Active Directory
          }

          $arrayAux = $this->synchronizeAllGroupUsers($registeredGroup, $plugin, $authSource);
          $arrayAux = array_merge($arrayGroupUserAd[$registeredGroup["GRP_UID"]], $arrayAux);

          $arrayGroupUserAd[$registeredGroup["GRP_UID"]] = array_unique($arrayAux);
      }

      $plugin->clearManager($this->managersToClear);

      if (!is_array($authSource['AUTH_SOURCE_DATA'])) {
        $authSource['AUTH_SOURCE_DATA'] = unserialize($authSource['AUTH_SOURCE_DATA']);
      }
      if (isset($authSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'])) {
        if (is_array($authSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'])) {
          foreach ($authSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'] as $departmentUID) {
            // Delete manager assignments
            $criteriaSet = new Criteria('workflow');
            $criteriaSet->add(UsersPeer::USR_REPORTS_TO, '');
            $criteriaWhere = new Criteria('workflow');
            $criteriaWhere->add(UsersPeer::DEP_UID, $departmentUID);
            $criteriaWhere->add(UsersPeer::USR_REPORTS_TO, '', Criteria::NOT_EQUAL);
            $this->deletedManager = BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
            // Delete department assignments
            $criteriaSet = new Criteria('workflow');
            $criteriaSet->add(UsersPeer::DEP_UID, '');
            $criteriaWhere = new Criteria('workflow');
            $criteriaWhere->add(UsersPeer::DEP_UID, $departmentUID);
            $this->moved += UsersPeer::doCount($criteriaWhere);
            BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
          }
        }
        unset($authSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN']);
        $RBAC =& RBAC::getSingleton();
        $RBAC->authSourcesObj->update($authSource);
      }
      if (isset($authSource['AUTH_SOURCE_DATA']['GROUPS_TO_UNASSIGN'])) {
        if (is_array($authSource['AUTH_SOURCE_DATA']['GROUPS_TO_UNASSIGN'])) {

          foreach ($authSource['AUTH_SOURCE_DATA']['GROUPS_TO_UNASSIGN'] as $groupUID) {
            // Delete manager assignments
            G::LoadClass('groups');
            $groupsInstance = new Groups();
            $criteria = $groupsInstance->getUsersGroupCriteria($groupUID);
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $users = array();
            while ($row = $dataset->getRow()) {
              $users[] = $row['USR_UID'];
              $dataset->next();
            }
            $criteriaSet = new Criteria('workflow');
            $criteriaSet->add(UsersPeer::USR_REPORTS_TO, '');
            $criteriaWhere = new Criteria('workflow');
            $criteriaWhere->add(UsersPeer::USR_UID, $users, Criteria::IN);
            $criteriaWhere->add(UsersPeer::USR_REPORTS_TO, '', Criteria::NOT_EQUAL);
            $this->deletedManager = BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
            // Delete group assignments
            $criteria = new Criteria('workflow');
            $criteria->add(GroupUserPeer::GRP_UID, $groupUID);
            $this->moved += GroupUserPeer::doCount($criteria);
            BasePeer::doDelete($criteria, Propel::getConnection('workflow'));
          }
        }
        unset($authSource['AUTH_SOURCE_DATA']['GROUPS_TO_UNASSIGN']);
        $RBAC =& RBAC::getSingleton();
        $RBAC->authSourcesObj->update($authSource);
      }

      // Delete the managers that not exists in PM
      $criteria = new Criteria('rbac');
      $criteria->addSelectColumn(RbacUsersPeer::USR_AUTH_USER_DN);
      $criteria->add(RbacUsersPeer::USR_AUTH_USER_DN, '', Criteria::NOT_EQUAL);
      $dataset = RbacUsersPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      $existingUsers = array();
      while ($row = $dataset->getRow()) {
        $existingUsers[] = $row['USR_AUTH_USER_DN'];
        $dataset->next();
      }
      foreach ($this->managersHierarchy as $managerDN => $subordinates) {
        if (!in_array($managerDN, $existingUsers)) {
          unset($this->managersHierarchy[$managerDN]);
        }
      }

      // Get the managers assigments counters
      $plugin->synchronizeManagers($this->managersHierarchy);

      $deletedManagersAssignments = self::array_diff_assoc_recursive($this->oldManagersHierarchy, $this->managersHierarchy);
      $newManagersAssignments = self::array_diff_assoc_recursive($this->managersHierarchy, $this->oldManagersHierarchy);
      $deletedManagers = array();
      $newManagers = array();
      $movedManagers = array();
      if (is_array($deletedManagersAssignments)) {
        foreach ($deletedManagersAssignments as $dn1 => $subordinates1) {
          foreach ($subordinates1 as $subordinate) {
            if (!in_array($subordinate, $deletedManagers)) {
              $deletedManagers[] = $subordinate;
            }
            foreach ($newManagersAssignments as $dn2 => $subordinates2) {
              if (isset($subordinates2[$subordinate])) {
                $movedManagers[] = $subordinate;
              }
            }
          }
        }
      }
      if (is_array($newManagersAssignments)) {
        foreach ($newManagersAssignments as $dn1 => $subordinates1) {
          foreach ($subordinates1 as $subordinate) {
            if (!in_array($subordinate, $newManagers)) {
              $newManagers[] = $subordinate;
            }
            foreach ($deletedManagersAssignments as $dn2 => $subordinates2) {
              if (isset($subordinates2[$subordinate])) {
                if (!in_array($subordinate, $movedManagers)) {
                  $movedManagers[] = $subordinate;
                }
              }
            }
          }
        }
      }

      // Print and log the users's information
      $logResults = sprintf('|- Existing users: %d, created %d, moved %d, removed %d, impossible: %d ',
    	$this->already,$this->created,$this->moved, $this->removed,$this->impossible);
    	$plugin->log(null, $logResults);
    	eprintln($logResults);

    	// Print and log the managers assignments's information
    	$logResults = sprintf('|- Managers assignments: created %d, moved %d, removed %d', count($newManagers) - count($movedManagers),
                                                                                         count($movedManagers),
                                                                                         count($deletedManagers) - count($movedManagers) + $this->deletedManager);
    	$plugin->log(null, $logResults);
    	eprintln($logResults);
    }

    //Department //Upgrade users in departments
    foreach ($arrayDepartmentUserAd as $departmentUid => $arrayUserAd) {
        $arrayDepartmentUserDb = $plugin->getUsersFromDepartmentTable($departmentUid);
        $arrayUserDb = array();

        foreach ($arrayDepartmentUserDb as $index => $arrayUser) {
          $arrayUserDb[] = $arrayUser["USR_UID"];
        }

        $arrayAux = array_diff($arrayUserDb, $arrayUserAd);

        //Upgrade data
        if (count($arrayAux) > 0) {
            $department = new Department();
            $department->Load($departmentUid);

            $departmentManagerUid = $department->getDepManager();

            foreach ($arrayAux as $index => $userUid) {
                $department->removeUserFromDepartment($departmentUid, $userUid);

                if ($userUid == $departmentManagerUid) {
                    $departmentUpdate = array();

                    $departmentUpdate["DEP_UID"] = $departmentUid;
                    $departmentUpdate["DEP_MANAGER"] = "";

                    $department->update($departmentUpdate);
                    $department->updateDepartmentManager($departmentUid);
                }
            }
        }
    }

    //Group //Upgrade users in groups
    G::LoadClass("groups");

    foreach ($arrayGroupUserAd as $groupUid => $arrayUserAd) {
        $arrayGroupUserDb = $plugin->getUsersFromGroupTable($groupUid);
        $arrayUserDb = array();

        foreach ($arrayGroupUserDb as $index => $arrayUser) {
          $arrayUserDb[] = $arrayUser["USR_UID"];
        }

        $arrayAux = array_diff($arrayUserDb, $arrayUserAd);

        //Upgrade data
        if (count($arrayAux) > 0) {
            $group = new Groups();

            foreach ($arrayAux as $index => $userUid) {
                $group->removeUserOfGroup($groupUid, $userUid);
            }
        }
    }
  }

  function array_diff_assoc_recursive($array1, $array2) {
    foreach ($array1 as $key => $value) {
      if (is_array($value)) {
        if (!isset($array2[$key])) {
          $difference[$key] = $value;
        }
        else {
          if (!is_array($array2[$key])) {
            $difference[$key] = $value;
          }
          else {
            $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
            if ($new_diff != false) {
              $difference[$key] = $new_diff;
            }
          }
        }
      }
      else {
        if (!isset($array2[$key]) || $array2[$key] != $value) {
          $difference[$key] = $value;
        }
      }
    }
    return !isset($difference) ? array() : $difference;
  }

  function synchronizeAllDepartmentUsers ($department, $oPlugin, $authSource) {
    //get users from ProcessMaker tables ( for this Department)
    $aUsers = $oPlugin->getUsersFromDepartmentTable($department['DEP_UID']);

    //Clear the manager assignments
    $usersUIDs = array();
    foreach ($aUsers as $key => $user) {
      $usersUIDs[] = $user['USR_UID'];
      if (isset($user['USR_REPORTS_TO'])) {
	      if ($user['USR_REPORTS_TO'] != '') {
	        $dn = isset($this->aUsersAuthSource[$user['USR_REPORTS_TO']]['USR_AUTH_USER_DN']) ? $this->aUsersAuthSource[$user['USR_REPORTS_TO']]['USR_AUTH_USER_DN'] : '';
	        if ($dn != '') {
	          if (!isset($this->oldManagersHierarchy[$dn])) {
	            $this->oldManagersHierarchy[$dn] = array();
	          }
	          $this->oldManagersHierarchy[$dn][$user['USR_UID']] = $user['USR_UID'];
	        }
	      }
	    }
    }
    $this->managersToClear = $usersUIDs;

    //get users from ldap (for this department)
    $aLdapUsers = $oPlugin->getUsersFromDepartment( $department['DN'] );

    //now we need to go over ldapusers and check if the user exists in ldap but not in PM, then we need to create it.
    $arrayUser = array();

    foreach($aLdapUsers as $ldapUser) {
      $found = false;
      $userUID = '';
      foreach ($aUsers as $key=> $user) {
      	//if user exists in this department.. do:
        if ($ldapUser['sUsername']==$user['USR_USERNAME']) {
          $inArray = false;
          foreach ($this->aUsersAuthSource as $aux) {
            if ($ldapUser['sUsername'] == $aux['USR_USERNAME']) {
              $inArray = true;
              break;
            }
          }
          if ($inArray)
            $this->already ++ ; //user already exists in this department and there is nothing to do.
          else {
            $this->impossible ++;  //users exists in another authSource
          }
          $found = true;
          $userUID = $aUsers[$key]['USR_UID'];
          unset($aUsers[$key]);
          break;
        }
      } //loop end, searching  an specific user in ldap with all users in PM

      if (!$found) {
       	//if user DO NOT exists in this department.. do:
      	//if exists with another AuthSource -> impossible
      	//if exists in another group, but in PM and for this authsource, we need to move it
      	$aUser = $oPlugin->searchUserByUid($ldapUser['sUsername'] );
      	$newGroupCNArray = $oPlugin->custom_ldap_explode_dn($aUser['sDN']);
      	array_shift($newGroupCNArray);
      	$newGroupCN =  implode(',', $newGroupCNArray );
      	$depUid = $oPlugin->getDepUidIfExistsDN ( $newGroupCN );

        $inArray = false;
        foreach ($this->aUsersAuthSource as $aux) {
          if ($ldapUser['sUsername'] == $aux['USR_USERNAME']) {
            $inArray = true;
            break;
          }
        }
        if ($inArray) {
        	//users exists in this department, now we need to update it or create it.
        	$aUserPM = $oPlugin->getUserFromPM($aUser['sUsername']);
      	  if ($aUserPM != NULL) {
      	    $userUID = $aUserPM['USR_UID'];
      	  	$oPlugin->activateUser($aUserPM['USR_UID'], $aUser['sDN'], $depUid);
      	  	$oPlugin->log(NULL, $aUserPM['USR_UID'] . " , " . $aUser['sDN']. " , " .  $depUid);
        	  $this->moved ++ ; //move user
        	}
        	else {
      	  	$userUID = $oPlugin->createUserAndActivate($aUser, $depUid);
        	  $this->created ++ ; //move user
        	}
        }
        else {
        	$aUserPM = $oPlugin->getUserFromPM($aUser['sUsername']);
      	  if ($aUserPM != NULL) {
      	    $userUID = $aUserPM['USR_UID'];
            $this->impossible ++;  //users exists in another authSource and another department.
        	}
        	else {
      	  	$userUID = $oPlugin->createUserAndActivate($aUser, $depUid);
        	  $this->created ++; //move user
        	}
        }
      }

      $arrayUser[] = $userUID;

      if (isset($ldapUser['sManagerDN'])) {
	      if ($ldapUser['sManagerDN'] != '') {
	        if (!isset($this->managersHierarchy[$ldapUser['sManagerDN']])) {
	          $this->managersHierarchy[$ldapUser['sManagerDN']] = array();
	        }
	        $this->managersHierarchy[$ldapUser['sManagerDN']][$userUID] = $userUID;
	      }
	    }
    }

    return $arrayUser;
  }

  function synchronizeAllGroupUsers($group, $oPlugin, $authSource) {
    //get users from ProcessMaker tables ( for this Group)
    $aUsers = $oPlugin->getUsersFromGroupTable($group['GRP_UID']);

    //Clear the manager assignments
    $usersUIDs = array();
    foreach ($aUsers as $key => $user) {
      $usersUIDs[] = $user['USR_UID'];
      if (isset($user['USR_REPORTS_TO'])) {
	      if ($user['USR_REPORTS_TO'] != '') {
	        $dn = isset($this->aUsersAuthSource[$user['USR_REPORTS_TO']]['USR_AUTH_USER_DN']) ? $this->aUsersAuthSource[$user['USR_REPORTS_TO']]['USR_AUTH_USER_DN'] : '';
	        if ($dn != '') {
	          if (!isset($this->oldManagersHierarchy[$dn])) {
	            $this->oldManagersHierarchy[$dn] = array();
	          }
	          $this->oldManagersHierarchy[$dn][$user['USR_UID']] = $user['USR_UID'];
	        }
	      }
	    }
    }

    $this->managersToClear = array_merge($this->managersToClear, $usersUIDs);

    //get users from ldap (for this group)
    $aLdapUsers = $oPlugin->getUsersFromGroup($group['DN']);

    //now we need to go over ldapusers and check if the user exists in ldap but not in PM, then we need to create it.
    G::LoadClass('groups');
    $groupsInstance = new Groups();

    $arrayUser = array();

    foreach($aLdapUsers as $ldapUser) {
      $found = false;
      $userUID = '';
      foreach ($aUsers as $key=> $user) {
      	//if user exists in this group.. do:
        if ($ldapUser['sUsername']==$user['USR_USERNAME']) {
          $inArray = false;
          foreach ($this->aUsersAuthSource as $aux) {
            if ($ldapUser['sUsername'] == $aux['USR_USERNAME']) {
              $inArray = true;
              break;
            }
          }
          if ($inArray) {
            $this->already ++ ; //user already exists in this group and there is nothing to do.
          }
          $found = true;
          $userUID = $aUsers[$key]['USR_UID'];
          unset($aUsers[$key]);
          break;
        }
      } //loop end, searching  an specific user in ldap with all users in PM

      if (!$found) {
       	//if user DO NOT exists in this group.. do:
      	//if exists with another AuthSource -> impossible
      	//if exists in another group, but in PM and for this authsource, we need to move it
      	$aUser = $oPlugin->searchUserByUid($ldapUser['sUsername']);
        $inArray = false;
        foreach ($this->aUsersAuthSource as $aux) {
          if ($ldapUser['sUsername'] == $aux['USR_USERNAME']) {
            $inArray = true;
            break;
          }
        }
        if ($inArray) {
        	//users exists in this group, now we need to update it or create it.
        	$aUserPM = $oPlugin->getUserFromPM($aUser['sUsername']);
      	  if ($aUserPM != NULL) {
      	    $userUID = $aUserPM['USR_UID'];
      	  	$oPlugin->activateUser($aUserPM['USR_UID'], $aUser['sDN']);
        	  $this->moved ++ ; //move user
        	}
        	else {
      	  	$userUID = $oPlugin->createUserAndActivate($aUser, '');
        	  $this->created ++ ; //move user
        	}
        }
        else {
        	$aUserPM = $oPlugin->getUserFromPM($aUser['sUsername']);
      	  if ($aUserPM != NULL) {
      	    $userUID = $aUserPM['USR_UID'];
            $this->impossible ++;  //users exists in another authSource and another group.
        	}
        	else {
      	  	$userUID = $oPlugin->createUserAndActivate($aUser, '');
        	  $this->created ++; //move user
        	}
        }

        $groupsInstance->addUserToGroup($group['GRP_UID'], $userUID);
      }

      $arrayUser[] = $userUID;

      if (isset($ldapUser['sManagerDN'])) {
	      if ($ldapUser['sManagerDN'] != '') {
	        if (!isset($this->managersHierarchy[$ldapUser['sManagerDN']])) {
	          $this->managersHierarchy[$ldapUser['sManagerDN']] = array();
	        }
	        $this->managersHierarchy[$ldapUser['sManagerDN']][$userUID] = $userUID;
	      }
	    }
    }

    return $arrayUser;
  }
}
