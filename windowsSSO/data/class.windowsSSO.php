<?php
/**
 * The Windows SSO class for the RBAC
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

// Load dependences
if (!class_exists('PlexcelUtils')) {
  require_once PATH_PLUGINS . 'windowsSSO' . PATH_SEP . 'classes' . PATH_SEP . 'class.plexcelUtils.php';
}

if (!class_exists('PlexcelUtils')) {
  return;
}

G::LoadClass('groups');

class windowsSSO extends PlexcelUtils {

  /**
   * The organizational unit where the removed users are put into
   * @var String
   */
  var $terminatedOu = '';

  /**
   * The constructor method, stores the connection to the Active Directory
   * using the plexcel configuration, also stores the base DN
   *
   * @return void
   */
  public function __construct() {
    try {
      if (!function_exists('plexcel_new')) {
        $message = 'Plexcel PHP extension has not been loaded.';
        if (isset($_SERVER['HTTP_REFERER'])) {
            if ((strpos($_SERVER['HTTP_REFERER'], '/login/login')) !== false) {
              G::SendMessageText($message . ' ' . G::LoadTranslation('ID_CONTACT_ADMIN'), 'error');
              G::header('Location: ../login/login');
              die();
            }
            else {
              throw new Exception($message);
            }
        }
        else {
          throw new Exception($message);
        }
      }
      $this->plexcelConnection = plexcel_new(null, null);
      if (!$this->plexcelConnection) {
        throw new Exception('Error connecting to the server.');
      }
      $domainInfo = plexcel_get_domain($this->plexcelConnection, null);
      $dcArray    = explode('.', $domainInfo['dnsRoot']);
      foreach ($dcArray as $value) {
        $this->baseDN .= ($this->baseDN != '' ? ',' : '') . 'dc=' . $value;
      }
      $authorities = plexcel_find_authorities_by_domain(null, 0, 0);
      $servers     = '';
      foreach ($authorities as $server) {
        $servers .= $server . ' ';
      }
      $text = 'Bind to [' . $domainInfo['nETBIOSName'] . ',' . $domainInfo['dnsRoot'] . ',' . trim($servers) . ']';
      self::log($text);
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  /**
   * Add a line in the windowsSSO log
   *
   * @author Fernando Ontiveros Lira <fernando at colosa dot com>
   * @param Object $this->plexcelConnection The plexcel connection
   * @param String $text The text to save in the log
   * @return void
   */
  public function log($text) {
    if (!class_exists('windowsSSOClass')) {
      require_once PATH_PLUGINS . 'windowsSSO/class.windowsSSO.php';
    }
  	windowsSSOClass::log($this->plexcelConnection, $text);
  }

  /**
   * Register automatically the user in ProcessMaker
   *
   * @author Fernando Ontiveros Lira <fernando at colosa dot com>
   * @param Object $this->plexcelConnection The plexcel connection
   * @param String $text The text to save in the log
   * @return Integer If the user was created correctly
   */
  public function automaticRegister($authSource, $user, $password) {
    try {
      $RBAC = RBAC::getSingleton();
      if (is_null($RBAC->userObj)) {
        $RBAC->userObj = new RbacUsers();
      }
      if (is_null($RBAC->rolesObj)) {
        $RBAC->rolesObj = new Roles();
      }
      $user = $this->searchUserByUid($user);
      $result  = 0;
      if (is_array($user)) {
        if ( $RBAC->singleSignOn ) {
    		  $result = 1;
        }
        else {
          if ($this->VerifyLogin($user['sUsername'], $password) === true) {
            $result = 1;
          }
        }
      }
      if ($result == 1) {
        $data = array();
        $data['USR_USERNAME']     = $user['sUsername'];
        $data['USR_PASSWORD']     = md5($user['sUsername']);
        $data['USR_FIRSTNAME']    = $user['sFirstname'];
        $data['USR_LASTNAME']     = $user['sLastname'];
        $data['USR_EMAIL']        = $user['sEmail'];
        $data['USR_DUE_DATE']     = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 2));
        $data['USR_CREATE_DATE']  = date('Y-m-d H:i:s');
        $data['USR_UPDATE_DATE']  = date('Y-m-d H:i:s');
        $data['USR_BIRTHDAY']     = date('Y-m-d');
        $data['USR_STATUS']       = 1;
        $data['USR_AUTH_TYPE']    = strtolower($authSource['AUTH_SOURCE_PROVIDER']);
        $data['UID_AUTH_SOURCE']  = $authSource['AUTH_SOURCE_UID'];
        $data['USR_AUTH_USER_DN'] = $user['sDN'];
        $userUID                  = $RBAC->createUser($data, 'PROCESSMAKER_OPERATOR');
        $data['USR_STATUS']       = 'ACTIVE';
        $data['USR_UID']          = $userUID;
        $data['USR_PASSWORD']     = md5($userUID);
        $data['USR_ROLE']         = 'PROCESSMAKER_OPERATOR';
        require_once 'classes/model/Users.php';
        $userInstance = new Users();
        $userInstance->create($data);
        self::log('Automatic Register for user "' . $user['sUsername'] . '".');
      }
      return $result;
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  /**
   * This method search a user in the active directory by username
   *
   * @param String $sKeyword The keyword in order to match the record with the identifier attribute
   * @param String $identifier id identifier, this parameter is optional
   * @return mixed if the user has been found or not
   */
  public function searchUserByUid($username) {
    try {
  	  // Sometimes the username is an array, but we are using only the first item
      if (is_array($username)) {
        $username = trim($username[0]);
      }
      else {
        $username = trim($username);
      }
      // Search UPN for current user, because plexcel connects only with userPrincipalName
      $results = $this->searchObjects("(&(objectClass=user)(sAMAccountName=$username))", array('sAMAccountName',
                                                                                               'cn',
                                                                                               'givenName',
                                                                                               'sn',
                                                                                               'userPrincipalName',
                                                                                               'distinguishedName'));
      if (is_array($results) == false || count($results) == 0) {
        $this->log("Search failed with filter: (&(objectClass=user)(sAMAccountName=$username))");
        return null;
      }
      if (count($results) > 1) {
        $this->log("Many results (expecting only one row) for search with filter: (&(objectClass=user)(sAMAccountName=$username))");
        return null;
      }
      $user = array();
      $user['sUsername']  = $results[0]['sAMAccountName'];
      $user['sFullname']  = $results[0]['cn'];
      $user['sFirstname'] = (string) $results[0]['givenName'];
      $user['sLastname']  = (string) $results[0]['sn'];
      $user['sEmail']     = (string) $results[0]['userPrincipalName'];
      $user['sDN']        = $results[0]['distinguishedName'];
      return $user;
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  /**
   * This method authentifies if a user has the RBAC_user privileges
   * also verifies if the user has the rights to start an application
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $username UserId  (user login)
   * @param  string $password Password
   * @return
   *  -1: user doesn't exists / no existe usuario
   *  -2: wrong password / password errado
   *  -3: inactive user / usuario inactivo
   *  -4: user due date / usuario vencido
   *  -5: connection error
   *  n : user uid / uid de usuario
   */
  public function VerifyLogin($username, $password) {
    try {
      // Sometimes the username is an array, but we are using only the first item
      if (is_array($username)) {
        $username = trim($username[0]);
      }
      else {
        $username = trim($username);
      }
      // If password or user is empty we return with error
      if (strlen($username) == 0) {
        throw new Exception('-1');
      }
      if (strlen($password) == 0) {
        throw new Exception('-2');
      }
      // Search current user for distinguished name, because plexcel connects only with userPrincipalName
      $objs = $this->searchObjects("(&(objectClass=user)(distinguishedName=$username))", array('sAMAccountName',
                                                                                               'cn',
                                                                                               'givenName',
                                                                                               'sn',
                                                                                               'userPrincipalName',
                                                                                               'distinguishedName'));
      if (!is_array($objs)) {
      	throw new Exception('-1');
      }
      if (count($objs) != 1) {
        throw new Exception('-6');
      }
      $UPN = $objs[0]['userPrincipalName'];

      $validUserPass = plexcel_logon($this->plexcelConnection, session_id(), $UPN, $password);

      if (!$validUserPass) {
      	throw new Exception('-1 ' . $UPN);
      }
      $this->log('Login user ' . $UPN);

      return $validUserPass;
    }
    catch (Exception $error) {
      return 0;
    }
  }

  /**
   * Search a user with the keyword given
   *
   * @param String $keyword The keyword to search
   * @return Array The data of the object that was found
   */
  public function searchUsers($keyword) {
    try {
      $keyword = trim($keyword);
      $filter  = "(&(|(objectClass=*))(|(samaccountname=$keyword)(userprincipalname=$keyword))(objectCategory=person))";
      $objects = $this->searchObjects($filter, array('distinguishedName',
                                                     'sAMAccountName',
                                                     'cn',
                                                     'givenName',
                                                     'sn',
                                                     'mail',
                                                     'userPrincipalName',
                                                     'objectCategory',
                                                     'manager'));
      $this->log($this->plexcelConnection, 'Search users with filter: ' . $filter);
      $results = array();
      if (count($objects) > 0) {
        foreach ($objects as $object) {
          $username = isset($object['sAMAccountName']) ? $object['sAMAccountName'] : '';
          if ($username != '') {
            $results[] = array('sUsername'  => $username,
                               'sFullname'  => $object['cn'],
                               'sFirstname' => isset($object['givenName']) ? $object['givenName'] : '',
                               'sLastname'  => isset($object['sn']) ? $object['sn'] : '',
                               'sEmail'     => isset($object['mail']) ? ($object['mail'] != '' ? $object['mail'] : (isset($object['userPrincipalName']) ? $object['userPrincipalName'] : '')) : (isset($object['userPrincipalName']) ? $object['userPrincipalName'] : ''),
                               'sCategory'  => isset($object['objectCategory']) ? $object['objectCategory'] : ''  ,
                               'sDN'        => $object['distinguishedName'],
                               'sManagerDN' => isset($object['manager']) ? is_array($object['manager']) ? $object['manager'][0] : $object['manager'] : '');
          }
        }
      }
      return $results;
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in searchUsers: ' . $error->getMessage());
      return array();
    }
    return array();
  }

  /**
   * Search all departments in the Active Directory
   *
   * @return Array The departments in the Active Directory
   */
  public function searchDepartments() {
    try {
      $departments = array();
      $unitsBase = $this->custom_ldap_explode_dn($this->baseDN);

      // Get the departments
      $objects = $this->getObjects(array('organizationalUnit'));

      if (count($objects) == 0) {
        return $departments;
      }

      // First node is root
      $department           = array();
      $department['dn']     = $this->baseDN;
      $department['parent'] = '';
      $department['ou']     = 'ROOT';
      $department['users']  = '0';
      $departments[]        = $department;

      foreach ($objects as $dn => $object) {
        $unitsEqual = $this->custom_ldap_explode_dn($dn);
        if (count($unitsEqual) == 1 && $unitsEqual[0] == '') {
          continue;
        }

        if (count($unitsEqual) > count($unitsBase)) {
          unset($unitsEqual[0]);
        }

        if (isset($object['name'] ) && !is_array($object['name'])) {
          $department = array();
          $department['dn']     = $dn;
          $department['parent'] = isset ($unitsEqual[1]) ? implode(',', $unitsEqual) : '';
          $department['ou']     = trim($object['name']);
          $department['users']  = '0';
          $departments[]        = $department;
        }
      }
      return $departments;
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in searchDepartments: ' . $error->getMessage());
      return array();
    }
    return array();
  }

  /**
   * Get the terminated OU stored in the authentication source registry
   *
   * @param String $authUid The unique identifier of the authentication source
   * @return String The DN of the terminated OU
   */
  public function getTerminatedOu($authUid) {
    $RBAC = RBAC::getSingleton();
    $authSource = $RBAC->authSourcesObj->load($authUid);
    $attributes = $authSource['AUTH_SOURCE_DATA'];
    $this->terminatedOu = isset($attributes['AUTH_SOURCE_RETIRED_OU']) ? $attributes['AUTH_SOURCE_RETIRED_OU'] : '';
    return $this->terminatedOu;
  }

  /**
   * Check if the department exists in PM
   *
   * @param String $currentDN The DN of the department
   * @return String The unique identifier of the department
   */
  public function getDepUidIfExistsDN($currentDN) {
    try {
      $criteria = new Criteria('workflow');
      $criteria->add(DepartmentPeer::DEP_STATUS, 'ACTIVE');
      $criteria->add(DepartmentPeer::DEP_LDAP_DN, $currentDN);
      $dataset = DepartmentPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      if ($row = $dataset->getRow()) {
        return $row['DEP_UID'];
      }
      return false;
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in getDepUidIfExistsDN: ' . $error->getMessage());
      return false;
    }
  }

  /**
   * Get the users from ProcessMaker tables, and returns the depUid and an array with the
   * users in that department
   *
   * @param String $currentDN The DN of the department
   * @return Array The PM users from the department
   */
  public function getUsersFromPMDepartment($currentDN) {
    $pmUsers = array();
    try {
      $depUid = $this->getDepUidIfExistsDN($currentDN);
      if ($depUid === false) {
        return array('depUid' => '', 'pmUsers' => array());
      }
      $criteria = new Criteria('workflow');
      $criteria->add(UsersPeer::DEP_UID,  $depUid);
      $criteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
      $dataset = UsersPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      while ($row = $dataset->getRow()) {
        $pmUsers[] = $row;

        $dataset->next();
      }
      return array('depUid' => $depUid, 'pmUsers' => $pmUsers);
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in getUsersFromPMDepartment: ' . $error->getMessage());
      return array('depUid' => '', 'pmUsers' => array());
    }
  }

  /**
   * Search all groups in the Active Directory
   *
   * @return Array The groups in the Active Directory
   */
  public function searchGroups() {
    try {
      $groups = array();

      // Get the groups
      $objects = $this->getObjects(array('group'));
      foreach ($objects as $dn => $object) {
        if (isset($object['name']) && !is_array($object['name'])) {
          $group = array();
          $group['dn']     = $dn;
          $group['cn']     = trim($object['name']);
          $group['users']  = '0';
          $groups[]        = $group;
        }
      }
      return $groups;
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in searchGroups: ' . $error->getMessage());
      return array();
    }
    return array();
  }

  /**
   * Check if the group exists in PM
   *
   * @param String $currentDN The DN of the group
   * @return String The unique identifier of the group
   */
  public function getGrpUidIfExistsDN($currentDN) {
    try {
      $criteria = new Criteria('workflow');
      $criteria->add(GroupwfPeer::GRP_STATUS , 'ACTIVE');
      $criteria->add(GroupwfPeer::GRP_LDAP_DN, $currentDN);
      $dataset = GroupwfPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      if ($row = $dataset->getRow()) {
        return $row['GRP_UID'];
      }
      return false;
    }
    catch (Exception $e) {
      return false;
    }
  }

  /**
   * Get the users from ProcessMaker tables, and returns the grpUid and an array with the
   * users in that group
   *
   * @param String $currentDN The DN of the group
   * @return Array The PM users from the group
   */
  public function getUsersFromPMGroup($currentDN) {
    $pmUsers = array();
    try {
      $grpUid = $this->getGrpUidIfExistsDN($currentDN);
      if ( $grpUid === false ) {
        return array('grpUid' => '', 'pmUsers' => array());
      }
      $criteria = new Criteria('workflow');
      $criteria->addSelectColumn('*');
      $criteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
      $criteria->add(GroupUserPeer::GRP_UID, $grpUid);
      $criteria->add(UsersPeer::USR_STATUS,  'CLOSED' , Criteria::NOT_EQUAL);
      $dataset = GroupUserPeer::doSelectRS($criteria);
      $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $dataset->next();
      while ($row = $dataset->getRow()) {
        $pmUsers[] = $row;
        $dataset->next();
      }
      return array('grpUid' => $grpUid, 'pmUsers' => $pmUsers);
    }
    catch (Exception $error) {
      $this->log($this->plexcelConnection, 'Error in getUsersFromPMGroup: ' . $error->getMessage());
      return array('grpUid' => '', 'pmUsers' => array());
    }
  }

  /********************* Methods used exclusively by cron *********************/

  /**
   * Get all authsource for this plugin (windowsSSO plugin, because other authsources
   * are not needed)
   *
   * @return array The authentication sources of type "windowsSSO"
   */
  public function getAuthSources() {
    require_once PATH_RBAC . 'model/AuthenticationSource.php';
    $authSource = new AuthenticationSource();
    $dataset = AuthenticationSourcePeer::doSelectRS($authSource->getAllAuthSources());
    $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $dataset->next();
    $authSources = array();
    while ($row = $dataset->getRow()) {
      if ($row['AUTH_SOURCE_PROVIDER'] == 'windowsSSO') {
        $authSources[] = $row;
      }
      $dataset->next();
    }
    return $authSources;
  }

  /**
   * Select departments but it is not recursive, only returns departments in this level
   *
   * @param string $parent the DEP_UID for parent department
   * @return Array $result The departments with the same parent
   */
  public function getDepartments($parent)  {
    try {
      $result   = array();
      $criteria = new Criteria('workflow');
      $criteria->add(DepartmentPeer::DEP_PARENT, $parent, Criteria::EQUAL);
      $con = Propel::getConnection(DepartmentPeer::DATABASE_NAME);
      $objects = DepartmentPeer::doSelect($criteria, $con);

      foreach($objects as $department) {
        $node                 = array();
        $node['DEP_UID']      = $department->getDepUid();
        $node['DEP_PARENT']   = $department->getDepParent();
        $node['DEP_TITLE']    = $department->getDepTitle();
        $node['DEP_STATUS']   = $department->getDepStatus();
        $node['DEP_MANAGER']  = $department->getDepManager();
        $node['DEP_LDAP_DN']  = $department->getDepLdapDn();
        $node['DEP_LAST']     = 0;

        $criteriaCount = new Criteria('workflow');
        $criteriaCount->clearSelectColumns();
        $criteriaCount->addSelectColumn('COUNT(*)');
        $criteriaCount->add(DepartmentPeer::DEP_PARENT, $department->getDepUid(), Criteria::EQUAL);
        $rs = DepartmentPeer::doSelectRS($criteriaCount);
        $rs->next();
        $row = $rs->getRow();
        $node['HAS_CHILDREN'] = $row[0];
        $result[] = $node;
      }
      if (count($result) >= 1) {
        $result[count($result) -1 ]['DEP_LAST'] = 1;
      }
      return $result;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Select groups but it is not recursive, only returns groups in this level
   *
   * @return Array $result The data of the groups
   */
  public function getGroups() {
    try {
      $result = array();
      $criteria = new Criteria('workflow');
      $con = Propel::getConnection(GroupwfPeer::DATABASE_NAME);
      $objects = GroupwfPeer::doSelect($criteria, $con);
      foreach($objects as $oGroup) {
        $node                = array();
        $node['GRP_UID']     = $oGroup->getGrpUid();
        $node['GRP_TITLE']   = $oGroup->getGrpTitle();
        $node['GRP_STATUS']  = $oGroup->getGrpStatus();
        $node['GRP_LDAP_DN'] = $oGroup->getGrpLdapDn();
        $result[] = $node;
      }
      return $result;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Function to get departments from the array previously obtained from LDAP
   * we are calling registered departments
   * it is a recursive function, in the first call with an array with first top level departments from PM
   * then go thru all departments and obtain a list of departments already created in PM and pass that array
   * to next function to synchronize All users for each department
   * this function is used in cron only
   *
   * @param Array $aLdapDepts Departments obtained from LDAP/Active Directory
   * @param Array $aDepartments Departments obtained from PM tables
   * @return Array $aResult Departments registered
   */
  public function getRegisteredDepartments($aLdapDepts, $aDepartments){
    $aLdapDepts[0]['ou'] = $aLdapDepts[0]['ou'] . ' ' . $aLdapDepts[0]['dn'];
    $aResult = array();
    foreach ($aLdapDepts as $ldapDept) {
      foreach ($aDepartments as $department){
        if ($department['DEP_TITLE'] == $ldapDept['ou'] && $department['DEP_LDAP_DN'] != ''){
          $department['DN'] = $ldapDept['dn'];
          $aResult[] = $department;
          if ($department['HAS_CHILDREN']!=0){
            $aTempDepartments = $this->getDepartments($department['DEP_UID']);
            $aTempRegistered  = $this->getRegisteredDepartments($aLdapDepts,$aTempDepartments);
            $aResult = array_merge($aResult, $aTempRegistered);
          }
        }
      }
    }
    return $aResult;
  }

  /**
   * Function to get groups from the array previously obtained from LDAP
   * we are calling registered groups
   * it is a recursive function, in the first call with an array with first top level groups from PM
   * then go thru all groups and obtain a list of groups already created in PM and pass that array
   * to next function to synchronize All users for each group
   * this function is used in cron only
   *
   * @param Array $aLdapGroups Groups obtained from LDAP/Active Directory
   * @param Array $aGroups Groups obtained from PM tables
   * @return Array $aResult Groups registered
   */
  public function getRegisteredGroups($aLdapGroups, $aGroups){
    $aLdapGroups[0]['cn'] = $aLdapGroups[0]['cn'] . ' ' . $aLdapGroups[0]['dn'];
    $aResult = array();
    foreach ($aLdapGroups as $ldapGroup) {
      foreach ($aGroups as $group){
        if ($group['GRP_TITLE'] == $ldapGroup['cn'] && $group['GRP_LDAP_DN'] != ''){
          $group['DN'] = $ldapGroup['dn'];
          $aResult[] = $group;
        }
      }
    }
    return $aResult;
  }

  /**
   * Get all users (UID, USERNAME) moved to Removed OU
   *
   * @param Array Authentication Source row, in this fuction we are validating if Removed OU is defined or not
   * @return Array Users in the removed OU
   */
  public function getUsersFromRemovedOu($aAuthSource) {
    $aUsers = array(); //empty array is the default result
    $attributes = unserialize($aAuthSource['AUTH_SOURCE_DATA']);
    $this->terminatedOu = isset($attributes['AUTH_SOURCE_RETIRED_OU'])? trim($attributes['AUTH_SOURCE_RETIRED_OU']) : '';
    if ($this->terminatedOu == '') {
      return $aUsers;
    }
    return $this->getUsersFromDepartmentByName($this->terminatedOu);
  }

  /**
   * Get the Users list from a department based on the name
   *
   * @param string $departmenName
   * @return array Users list from the department
   */
  public function getUsersFromDepartmentByName($departmenName) {
    $dFilter  = '(&(|(objectClass=organizationalUnit))';
    $dFilter .= "(|(ou=".$departmenName."))";
    $dFilter .= ')';
    $aUsers   = array();
    $objects  = $this->searchObjects($dFilter, array('distinguishedName'));
    foreach ($objects as $object) {
      $aUsers = $this->getUsersFromDepartment($object['distinguishedName']);
    }
    return $aUsers;
  }

  /**
   * Function to obtain users from a specific ldap Department
   *
   * @param The DN of the department
   * @return The users from the department
   */
  public function getUsersFromDepartment($departmenDN) {
    $dFilter = '(&(|(objectClass=user)))';
    $aUsers  = array();
    $params  = array('base' => $departmenDN, 'scope' => 'sub', 'filter' => $dFilter, 'attrs' => array('distinguishedName',
                                                                                                      'sAMAccountName',
                                                                                                      'cn',
                                                                                                      'givenName',
                                                                                                      'sn',
                                                                                                      'mail',
                                                                                                      'userPrincipalName',
                                                                                                      'objectCategory',
                                                                                                      'manager'));
    $objects = plexcel_search_objects($this->plexcelConnection, $params);
    foreach ($objects as $object) {
      $aUsers[] = array('sUsername'  => $object['sAMAccountName'],
                        'sFullname'  => $object['cn'],
                        'sPassword'  => '',
                        'sFirstname' => isset($object['givenName']) ? $object['givenName'] : '',
                        'sLastname'  => isset($object['sn']) ? $object['sn'] : '',
                        'sEmail'     => isset($object['mail']) ? $object['mail'] : (isset($object['userPrincipalName'])?$object['userPrincipalName'] : ''),
                        'sDN'        => $object['distinguishedName'],
                        'sManagerDN' => isset($object['manager']) ? is_array($object['manager']) ? $object['manager'][0] : $object['manager'] : '');
    }
    return $aUsers;
  }

  /**
   * Set STATUS=0 for all users in the array $aUsers
   * this functin is used to deactivate an array of users ( usually used for Removed OU )
   * this function is used in cron only
   *
   * @param array authSource row, in this fuction we are validating if Removed OU is defined or not
   * @return array of users
   */
  public function deactiveArrayOfUsers($aUsers) {
    if (!class_exists('RbacUsers')) {
      require_once(PATH_RBAC.'model/RbacUsers.php');
    }
    if (!class_exists('Users')) {
      require_once('classes/model/Users.php');
    }

    $aUsrUid = array();
    foreach ( $aUsers as $key=>$val ) {
        $aUsrUid[] = $val['sUsername'];
    }
    $con = Propel::getConnection('rbac');
    // select set
    $c1 = new Criteria('rbac');
    $c1->add(RbacUsersPeer::USR_USERNAME, $aUsrUid, Criteria::IN );
    $c1->add(RbacUsersPeer::USR_STATUS, 1 );
    // update set
    $c2 = new Criteria('rbac');
    $c2->add(RbacUsersPeer::USR_STATUS, '0');
    BasePeer::doUpdate($c1, $c2, $con);

    $con = Propel::getConnection('workflow');
    // select set
    $c1 = new Criteria('workflow');
    $c1->add(UsersPeer::USR_USERNAME, $aUsrUid, Criteria::IN );
    // update set
    $c2 = new Criteria('workflow');
    $c2->add(UsersPeer::USR_STATUS, 'INACTIVE');
    $c2->add(UsersPeer::DEP_UID, '');

    BasePeer::doUpdate($c1, $c2, $con);
    return true;
  }

  /**
   * Get all user (UID, USERNAME) registered in RBAC with this authSource
   * this function is used in cron only
   *
   * @param string authSource UID ( AUT_UID value )
   * @return array of users
   */
  public function getUsersFromAuthSource($autUid) {
    try {
      $aUsers = array();
      $oCriteria = new Criteria('rbac');
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_UID);
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_AUTH_USER_DN);
      //$oCriteria->add(RbacUsersPeer::USR_STATUS, '1', Criteria::EQUAL);
      $oCriteria->add(RbacUsersPeer::UID_AUTH_SOURCE, $autUid, Criteria::EQUAL);
      $oCriteria->add(RbacUsersPeer::USR_AUTH_TYPE, 'windowssso', Criteria::EQUAL);
      $rs = RbacUsersPeer::doSelectRS($oCriteria);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      while( is_array($row) ) {
        $aUsers[ $row['USR_UID'] ] = $row;
        $rs->next();
        $row = $rs->getRow();
      }
      asort($aUsers);
      return $aUsers;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Function to get users from USERS table in wf_workflow and filter by department
   * this function is used in cron only
   *
   * @param string department UID ( DEP_UID value )
   * @return array of users
   */
  public function getUsersFromDepartmentTable($depUid) {
    try {
      $aUsers = array();

      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(UsersPeer::USR_UID);
      $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
      $oCriteria->addSelectColumn(UsersPeer::USR_REPORTS_TO);
      $oCriteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
      $oCriteria->add(UsersPeer::DEP_UID, $depUid);

      $rs = UsersPeer::doSelectRS($oCriteria);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      while( is_array($row) ) {
        $aUsers[] = $row;
        $rs->next();
        $row = $rs->getRow();
      }
      return $aUsers;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Function to get users from USERS table in wf_workflow and filter by department
   * this function is used in cron only
   *
   * @param string department UID ( DEP_UID value )
   * @return array of users
   */
  public function getUserFromPM( $userName ) {
    try {
      $aUsers = NULL;

      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(UsersPeer::USR_UID);
      $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
      $oCriteria->addSelectColumn(UsersPeer::DEP_UID);
      $oCriteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
      $oCriteria->add(UsersPeer::USR_USERNAME, $userName);

      $rs = UsersPeer::doSelectRS($oCriteria);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      if ( is_array($row) ) {
        $aUsers = $row;
      }
      return $aUsers;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Activate an user previously deactivated
   * if user is now in another department, we need the second parameter, the depUid
   *
   * @param string $userUid
   * @param string optional department DN
   * @param string optional DepUid
   */
  public function activateUser ($userUid, $userDn = NULL, $depUid = NULL ) {
    if (!class_exists('RbacUsers')) {
      require_once(PATH_RBAC.'model/RbacUsers.php');
    }
    $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
    // select set
    $c1 = new Criteria('rbac');
    $c1->add(RbacUsersPeer::USR_UID, $userUid);
    // update set
    $c2 = new Criteria('rbac');
    $c2->add(RbacUsersPeer::USR_STATUS, '1');
    if ( $userDn != NULL ) {
      $c2->add(RbacUsersPeer::USR_AUTH_USER_DN, $userDn);
      $c2->add(RbacUsersPeer::USR_AUTH_SUPERVISOR_DN, '');
    }

    BasePeer::doUpdate($c1, $c2, $con);

    if (!class_exists('Users')) {
      require_once('classes/model/Users.php');
    }
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    // select set
    $c1 = new Criteria('workflow');
    $c1->add(UsersPeer::USR_UID, $userUid);
    // update set
    $c2 = new Criteria('workflow');
    $c2->add(UsersPeer::USR_STATUS, 'ACTIVE');
    if ( $depUid != NULL ) {
      $c2->add(UsersPeer::DEP_UID, $depUid);
    }

    BasePeer::doUpdate($c1, $c2, $con);
  }

  /**
   * Creates an users using the data send in the array $aUsers
   * and then add the user to specific department
   * this function is used in cron only
   *
   * @param array $aUser info taken from ldap
   * @param string $depUid the department UID
   * @return boolean
   */
  public function createUserAndActivate( $aUser, $depUid) {
    $RBAC = RBAC::getSingleton();
    if ($RBAC->userObj ==NULL){
      $RBAC->userObj = new RbacUsers();
    }
    if ($RBAC->rolesObj==NULL){
      $RBAC->rolesObj = new Roles();
    }
    if ($RBAC->usersRolesObj==NULL){
      $RBAC->usersRolesObj = new UsersRoles();
    }

    $sUsername  = $aUser['sUsername'];
    $sFullname  = $aUser['sFullname'];
    $sFirstname = $aUser['sFirstname'];
    $sLastname  = $aUser['sLastname'];
    $sEmail     = $aUser['sEmail'];
    $sDn        = $aUser['sDN'];

    $con = Propel::getConnection('rbac');
    $aData['USR_USERNAME']     = $sUsername;
    $aData['USR_PASSWORD']     = md5($sUsername);
    $aData['USR_FIRSTNAME']    = $sFirstname;
    $aData['USR_LASTNAME']     = $sLastname;
    $aData['USR_EMAIL']        = $sEmail;
    $aData['USR_DUE_DATE']     = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 2));
    $aData['USR_CREATE_DATE']  = date('Y-m-d H:i:s');
    $aData['USR_UPDATE_DATE']  = date('Y-m-d H:i:s');
    $aData['USR_BIRTHDAY']     = date('Y-m-d');
    $aData['USR_STATUS']       = 1;
    $aData['USR_AUTH_TYPE']    = 'windowsSSO';
    $aData['UID_AUTH_SOURCE']  = $this->sAuthSource;
    $aData['USR_AUTH_USER_DN'] = $sDn;

    $sUserUID = $RBAC->createUser($aData, 'PROCESSMAKER_OPERATOR');

    $aData['USR_STATUS']       = 'ACTIVE';
    $aData['USR_UID']          = $sUserUID;
    $aData['DEP_UID']          = $depUid;
    $aData['USR_PASSWORD']     = md5($sUserUID);//fake :p
    $aData['USR_ROLE']         = 'PROCESSMAKER_OPERATOR';

    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $oUser->create($aData);
    return $sUserUID;

  }

  /**
   * Function to get users from USERS table in wf_workflow and filter by group
   * this function is used in cron only
   *
   * @param string group UID ( GRP_UID value )
   * @return array of users
   */
  public function getUsersFromGroupTable( $grpUid ) {
    try {
      $aUsers = array();

      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(GroupUserPeer::GRP_UID);
      $oCriteria->addSelectColumn(GroupUserPeer::USR_UID);
      $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
      $oCriteria->addSelectColumn(UsersPeer::USR_REPORTS_TO);
      $oCriteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(GroupUserPeer::GRP_UID, $grpUid);
      $oCriteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);

      $rs = GroupUserPeer::doSelectRS($oCriteria);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      while( is_array($row) ) {
        $aUsers[] = $row;
        $rs->next();
        $row = $rs->getRow();
      }
      return $aUsers;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
   * Function to obtain users from a specific ldap Group
   *
   * @param String The DN of the group
   * @return Array The users of the group
   */
  function getUsersFromGroup($groupDN) {
    $dFilter = '(&(memberOf=' . $groupDN . '))';
    $aUsers  = array();
    $params  = array('base' => $this->baseDN, 'scope' => 'sub', 'filter' => $dFilter, 'attrs' => array('distinguishedName',
                                                                                                       'sAMAccountName',
                                                                                                       'cn',
                                                                                                       'givenName',
                                                                                                       'sn',
                                                                                                       'mail',
                                                                                                       'userPrincipalName',
                                                                                                       'objectCategory',
                                                                                                       'manager'));
    $objects = plexcel_search_objects($this->plexcelConnection, $params);
    foreach ($objects as $object) {
      $aUsers[] = array('sUsername'  => $object['sAMAccountName'],
                        'sFullname'  => $object['cn'],
                        'sPassword'  => '',
                        'sFirstname' => isset($object['givenName']) ? $object['givenName'] : '',
                        'sLastname'  => isset($object['sn']) ? $object['sn'] : '',
                        'sEmail'     => isset($object['mail']) ? $object['mail'] : (isset($object['userPrincipalName'])?$object['userPrincipalName'] : ''),
                        'sDN'        => $object['distinguishedName'],
                        'sManagerDN' => isset($object['manager']) ? is_array($object['manager']) ? $object['manager'][0] : $object['manager'] : '');
    }
    return $aUsers;
  }

  /**
   * Clear the managers assignments from the users specified in PM
   *
   * @param Array $usersUIDs The unique identifiers of the users
   * @return void
   */
  public function clearManager($usersUIDs) {
    try {
      $criteriaSet = new Criteria('workflow');
      $criteriaSet->add(UsersPeer::USR_REPORTS_TO, '');
      $criteriaWhere = new Criteria('workflow');
      $criteriaWhere->add(UsersPeer::USR_UID, $usersUIDs, Criteria::IN);
      BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
    }
    catch (Exception $error) {
      $this->log($this->oLink, $error->getMessage());
    }
  }

  /**
   * Synchronize the managers assignments in PM
   *
   * @param Array $managersHierarchy The managers hiearchy to update in PM
   * @return void
   */
  public function synchronizeManagers($managersHierarchy) {
    require_once 'classes/model/RbacUsers.php';
    try {
      foreach ($managersHierarchy as $managerDN => $subordinates) {
        $criteria = new Criteria('rbac');
        $criteria->addSelectColumn('*');
        $criteria->add(RbacUsersPeer::USR_AUTH_USER_DN, $managerDN);
        $dataset = RbacUsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        if ($dataset->next()) {
          $row = $dataset->getRow();
          $criteriaSet = new Criteria('workflow');
          $criteriaSet->add(UsersPeer::USR_REPORTS_TO, $row['USR_UID']);
          $criteriaWhere = new Criteria('workflow');
          $criteriaWhere->add(UsersPeer::USR_UID, $subordinates, Criteria::IN);
          BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
        }
      }
    }
    catch (Exception $error) {
      $this->log($this->oLink, $error->getMessage());
    }
  }

  public function custom_ldap_explode_dn($dn) {
    $result = ldap_explode_dn($dn, 0);
    if (is_array($result)) {
      unset($result['count']);
      foreach($result as $key => $value){
        $result[$key] = addcslashes(preg_replace("/\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\1')).''", $value), '<>,"');
      }
    }
    return $result;
  }

}