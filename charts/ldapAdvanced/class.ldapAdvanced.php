<?php
/**
 * class.ldapAdvanced.php
 * LDAP plugin for the RBAC class. This class encapsulates all the methods required in order to bind
 * ProcessMaker and a Ldap Directory server.
 *
 * @author
 * Fernando Ontiveros
 * Colosa
 * @copyright
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * @package plugins.ldapAdvanced.classes
 */

// evaluating the requiring of some classes
if (!class_exists('Department')) {
  require_once 'classes/model/Department.php';
}

if (!class_exists('Groupwf')) {
  require_once 'classes/model/Groupwf.php';
}

if (!class_exists('GroupUser')) {
  require_once 'classes/model/GroupUser.php';
}

if (!class_exists('RbacUsers')){
  require_once PATH_RBAC.'model/RbacUsers.php';
}

if (!class_exists('RBAC')){
    require_once PATH_TRUNK . 'gulliver/system/class.rbac.php';
}

require_once PATH_RBAC.'model/AuthenticationSource.php';

//require_once PATH_RBAC.'model/Roles.php';

class ldapAdvancedClass {

  function __construct() {
    set_include_path(PATH_PLUGINS . 'ldapAdvanced' . PATH_SEPARATOR . get_include_path());
  }

  function getFieldsForPageSetup () {
    return array();
  }

  function updateFieldsForPageSetup($data) {
    return array();
  }

  function setup() {
  }

}

if (!class_exists('ldapAdvancedPlugin')) require_once PATH_PLUGINS . 'ldapAdvanced.php';

if (class_exists('ldapAdvanced')) return;

class ldapAdvanced
{

  /**
   * The authsource id
   * @var String
   */
  var $sAuthSource = '';

  /**
   * The organizational unit where the removed users are put into
   * @var String
   */
  var $sTerminatedOu = '';

  /**
   * a local variable to store connection with LDAP, and avoid multiple bindings
   * @var String
   */
  var $oLink = NULL;

  /**
   * The users information array
   * @var Array
   */
  var $aUserInfo = array();

  /**
   * System information
   * @var String
   */
  var $sSystem = '';

  /**
   * Object where an rbac instance is set
   * @var Object
   */
  static private $instance = NULL;

  /**
   * default constructor method
   */
  function __construct() {
  }

  /**
   * This method gets the singleton Rbac instance.
   * @return Object instance of the rbac class
   */
  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new RBAC();
    }
    return self::$instance;
  }

  function getFieldsForPageSetup() {
    return array();
  }

  /**
   * add a line in the ldap log
   *
   * before the log was generated in shared/sites/<site> folder, but it was deprecated
   * and now we are saving the log in  shared/log the entry in the log file.
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @param Object $_link ldap connection
   * @param String $text
   */
  function log ( $_link , $text ) {
    //$serverAddr = $_SERVER['SERVER_ADDR'];
    $logFile = PATH_DATA . 'log/ldapAdvanced.log';
    if ( !file_exists($logFile) || is_writable( $logFile ) ) {
      $fpt= fopen ( $logFile, 'a' );
      $ldapErrorMsg = '';
      $ldapErrorNr = 0;
      if ( $_link != NULL ) {
        $ldapErrorNr = @ldap_errno($_link);
       if ( $ldapErrorNr != 0 ){
          $ldapErrorMsg = @ldap_error($_link);
          $text = $ldapErrorMsg  . " : " . $text;
        }
      }
      //log format:   date hour ipaddress workspace ldapErrorNr
      fwrite( $fpt, sprintf ( "%s %s %s %s %s \n", date('Y-m-d H:i:s'), getenv('REMOTE_ADDR'), SYS_SYS, $ldapErrorNr, $text ));
      fclose( $fpt);
    }
    else
      error_log ("file $logFile is not writable ");
  }

  /**
   * This method obtains the attributes of a ldap Connection passed as parameter
   * @param Object $oLink ldap connection
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @param Object $oEntry Entry object
   * @return Array attributes
   */
  function getLdapAttributes ( $oLink, $oEntry ) {
    $aAttrib['dn'] = @ldap_get_dn($oLink, $oEntry);
    $aAttr = @ldap_get_attributes($oLink, $oEntry);
    for ( $iAtt = 0 ; $iAtt < $aAttr['count']; $iAtt++ ) {
      switch ( $aAttr[ $aAttr[$iAtt] ]['count'] ) {
        case 0: $aAttrib[ strtolower($aAttr[$iAtt]) ]= '';
                break;
        case 1: $aAttrib[ strtolower($aAttr[$iAtt]) ]= $aAttr[ $aAttr[$iAtt] ][0];
                break;
        default:
                $aAttrib[ strtolower($aAttr[$iAtt]) ]= $aAttr[ $aAttr[$iAtt] ];
                unset( $aAttrib[ $aAttr[$iAtt] ]['count'] );
                break;
      }
    }
    if (!isset($aAttrib['mail']) && isset($aAttrib['userprincipalname'])) {
      $aAttrib['mail'] = $aAttrib['userprincipalname'];
    }
    return $aAttrib;
  }

  /**
   * This method generates the ldap connection bind and returns the link object
   * for a determined authsource
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @param Array $aAuthSource the authsource data
   * @return Object A object with the resulting ldap bind
   */
  function ldapConnection ($aAuthSource) {
    $pass =explode("_",$aAuthSource['AUTH_SOURCE_PASSWORD']);
    foreach($pass as $index => $value) {
      if($value == '2NnV3ujj3w'){
        $aAuthSource['AUTH_SOURCE_PASSWORD'] = G::decrypt($pass[0],$aAuthSource['AUTH_SOURCE_SERVER_NAME']);
      }
    }
    $oLink = @ldap_connect($aAuthSource['AUTH_SOURCE_SERVER_NAME'], $aAuthSource['AUTH_SOURCE_PORT']);

    $ldapServer = $aAuthSource['AUTH_SOURCE_SERVER_NAME'] . ":" . $aAuthSource['AUTH_SOURCE_PORT'] ;

    @ldap_set_option($oLink, LDAP_OPT_PROTOCOL_VERSION, $aAuthSource['AUTH_SOURCE_VERSION']);
    //$this->log ( $oLink, "ldap set Protocol Version " . $aAuthSource['AUTH_SOURCE_VERSION'] );

    @ldap_set_option($oLink, LDAP_OPT_REFERRALS, 0);
    //$this->log ( $oLink, "ldap set option Referrals " );

    if (isset($aAuthSource['AUTH_SOURCE_ENABLED_TLS']) && $aAuthSource['AUTH_SOURCE_ENABLED_TLS']) {
      @ldap_start_tls($oLink);
      $ldapServer = "TLS " . $ldapServer;
      //$this->log ( $oLink, "start tls " );
    }
    if ($aAuthSource['AUTH_ANONYMOUS'] == '1') {
      $bBind = @ldap_bind($oLink);
      $this->log ( $oLink, "bind $ldapServer like anonymous user" );
    }
    else {
      $bBind = @ldap_bind($oLink, $aAuthSource['AUTH_SOURCE_SEARCH_USER'], $aAuthSource['AUTH_SOURCE_PASSWORD']);
      $this->log ( $oLink, "bind $ldapServer with user " . $aAuthSource['AUTH_SOURCE_SEARCH_USER'] );
    }

    if ( !$bBind ) {
      throw ( new Exception ( "Unable to bind to server: $ldapServer . " .
                 "LDAP-Errno: " . ldap_errno($oLink) . " : " . ldap_error($oLink) . " \n"  ) );
    }
    return $oLink;
  }

  /**
   * This method authentifies if a user has the RBAC_user privileges
   * also verifies if the user has the rights to start an application
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $strUser    UserId  (user login)
   * @param  string $strPass    Password
   * @return
   *  -1: user doesn't exists / no existe usuario
   *  -2: wrong password / password errado
   *  -3: inactive user / usuario inactivo
   *  -4: user due date / usuario vencido
   *  -5: connection error
   *  n : user uid / uid de usuario
   */
  function VerifyLogin( $strUser, $strPass) {
    if (is_array($strUser)){
      $strUser     = $strUser[0];
    }
    else {
      $strUser     = trim($strUser);
    }
    if ( $strUser == '' ) return -1;
    if ( strlen( $strPass ) == 0) return -2;

    $validUserPass = 1;

    try {
      $RBAC = RBAC::getSingleton();

      if ($RBAC->authSourcesObj == null) {
          $RBAC->authSourcesObj = new AuthenticationSource();
      }

      if ($RBAC->userObj == null) {
          $RBAC->userObj = new RbacUsers();
      }

      $arrayAuthSource = $RBAC->authSourcesObj->load($this->sAuthSource);

      $setAttributes = 0;
      $attributeUserSet = array();
      if (isset($arrayAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_SHOWGRID']) &&
          $arrayAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_SHOWGRID'] == 'on') {

          $setAttributes = 1;
          foreach ($arrayAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'] as $value) {
            $attributeUserSet[$value['attributeUser']] = $value['attributeLdap'];
          }
      }

      //Get UserName
      $criteria = new Criteria("rbac");

      $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
      $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
      $criteria->add(RbacUsersPeer::USR_STATUS, 1);
      $criteria->add(RbacUsersPeer::UID_AUTH_SOURCE, $arrayAuthSource["AUTH_SOURCE_UID"]);
      $criteria->add(RbacUsersPeer::USR_AUTH_USER_DN, $strUser);

      $rsCriteria = RbacUsersPeer::doSelectRs($criteria);
      $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

      $rsCriteria->next();
      $row = $rsCriteria->getRow();

      $usrName = $row["USR_USERNAME"];
      $usrUid = $row["USR_UID"];

      //Get the AuthSource properties
      //Check if the dn in the database record matches with the dn for the ldap account
      $verifiedUser = $this->searchUserByUid(
          $usrName,
          $arrayAuthSource["AUTH_SOURCE_DATA"]["AUTH_SOURCE_IDENTIFIER_FOR_USER"]
      );

      if (count($verifiedUser) == 0 || trim($verifiedUser["sDN"]) == null) {
          return -1;
      }

      $userDn = $strUser;

      if ($verifiedUser["sDN"] != $strUser || $setAttributes==1) {
        // if not Equals for that user uid
        if (!class_exists('RbacUsers')) {
          require_once(PATH_RBAC.'model/RbacUsers.php');
        }

        $columnsWf = array();
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        // select set
        $c1 = new Criteria('rbac');
        $c1->add(RbacUsersPeer::USR_STATUS, 1);
        $c1->add(RbacUsersPeer::UID_AUTH_SOURCE, $arrayAuthSource["AUTH_SOURCE_UID"]);
        $c1->add(RbacUsersPeer::USR_AUTH_USER_DN, $strUser);
        // update set
        $c2 = new Criteria('rbac');
        $c2->add(RbacUsersPeer::USR_AUTH_USER_DN, $verifiedUser['sDN']);

        foreach ($attributeUserSet as $key => $value) {
            eval('$flagExist = (defined("RbacUsersPeer::' . $key . '")) ? 1: 0;');
            if ($flagExist == 1){
                if ($key == 'USR_STATUS') {
                    $evalValue = $verifiedUser[$key];
                    
                    $statusValue = '0';
                    if (is_string($evalValue) && G::toUpper($evalValue) == 'ACTIVE') {
                        $statusValue = '1';
                    }
                    if (is_bool($evalValue) && $evalValue == true) {
                        $statusValue = '1';
                    }
                    if ( (is_float($evalValue) || is_int($evalValue) ||
                                    is_integer($evalValue) || is_numeric($evalValue)) && (int)$evalValue != 0 && (int)$evalValue != 66050) {
                        $statusValue = '1';
                    }
                    $verifiedUser[$key] = $statusValue;
                }
                eval('$c2->add(RbacUsersPeer::' . $key . ', $verifiedUser["' . $key . '"]);');
            }
        }
        BasePeer::doUpdate($c1, $c2, $con);

        $columnsWf = array();
        foreach ($attributeUserSet as $key => $value) {
            if (isset($verifiedUser[$key])) {
                if ($key == 'USR_STATUS') {
                    $evalValue = $verifiedUser[$key];
                    
                    $statusValue = 'INACTIVE';
                    if (is_string($evalValue) && G::toUpper($evalValue) == 'ACTIVE') {
                        $statusValue = 'ACTIVE';
                    }
                    if (is_bool($evalValue) && $evalValue == true) {
                        $statusValue = 'ACTIVE';
                    }
                    if ( (is_float($evalValue) || is_int($evalValue) ||
                                    is_integer($evalValue) || is_numeric($evalValue)) && (int)$evalValue != 0 && (int)$evalValue > 66000) {
                        $statusValue = 'ACTIVE';
                    }
                    $verifiedUser[$key] = $statusValue;
                }
                $columnsWf[$key] = $verifiedUser[$key];
            }
        }
        $columnsWf['USR_UID'] = $usrUid;

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $oUser->update($columnsWf);
        $userDn = $verifiedUser["sDN"];
      }


      //Check ldap connection for user
      $arrayAuthSource["AUTH_ANONYMOUS"]          = "0";
      $arrayAuthSource["AUTH_SOURCE_SEARCH_USER"] = $userDn;
      $arrayAuthSource["AUTH_SOURCE_PASSWORD"]    = $strPass;

      $oLink = $this->ldapConnection($arrayAuthSource);

      $attributes = $arrayAuthSource["AUTH_SOURCE_DATA"];

      if (!isset($attributes['AUTH_SOURCE_RETIRED_OU'])) {
        $attributes ['AUTH_SOURCE_RETIRED_OU'] = '';
      }

      //Check if the user is in the terminated organizational unit
      if ($this->userIsTerminated($usrName, $attributes["AUTH_SOURCE_RETIRED_OU"])) {
          $this->deactivateUser($usrName);
          $this->log($oLink, "user $strUser is member of Remove OU, deactivating this user.");

          return -3;
      }

      $validUserPass = ldap_errno($oLink) == 0;
    }
    catch ( Exception $e ) {
      $validUserPass = -5;
    }

    if ( $validUserPass == 1 )
      $this->log ( $oLink, "sucessful login user " . $verifiedUser['sDN']  );
    else
      $this->log ( $oLink, "failure authentication for user $strUser "  );

    return $validUserPass ;
  }

  /**
   * This method searches for the users that has some attribute
   * that matches the keyword.
   * @param String $sKeyword search criteria
   * @return array Users that match the search criteria
   */
  function searchUsers($sKeyword) {
    $sKeyword     = trim($sKeyword);
    $RBAC         = RBAC::getSingleton();
    if ($RBAC->authSourcesObj==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }

    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);
    $attributeUserSet = array();
    $attributeSetAdd = array();
    if (    isset($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'])
        &&  count($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE']) ) {
        foreach ($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'] as $value) {
            $attributeSetAdd[] = $value['attributeLdap'];
            $attributeUserSet[$value['attributeUser']] = $value['attributeLdap'];
        }
    }

    if ( $this->oLink == NULL ) {
      $oLink = $this->ldapConnection($aAuthSource);
      $this->oLink = $oLink;
    }
    else
      $oLink = $this->oLink;

    //prefix
    $sKeyword = ( (substr($sKeyword, 0,1) != '*') ? '*' : '' ) . $sKeyword;
    //sufix
    $sKeyword = $sKeyword . ( (substr($sKeyword, -1) != '*') ? '*' : '' );

    $sFilter  = '(&(|(objectClass=*))';
/*    if (count($aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']) > 0) {
      $sFilter .= '(|';
      $aObjects = explode("\n", $aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']);
      foreach ($aObjects as $sObject) {
        $sFilter .= '(objectClass=' . trim($sObject) . ')';
      }
      $sFilter .= ')';
    }
*/
/*
    if (count($aAuthSource['AUTH_SOURCE_ATTRIBUTES']) > 0) {
      $sFilter .= '(|';
      $aAttributes = explode("\n", $aAuthSource['AUTH_SOURCE_ATTRIBUTES']);
      foreach ($aAttributes as $sObject) {
        $sObject = trim($sObject);
        if ($sObject != '') {
          $sFilter .= '(' . trim($sObject) . '=' . $sKeyword . ')';
        }
      }
      $sFilter .= ')';
    }
    */
    if ( isset( $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE']) && $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' ) {
      $sFilter = "(&(|(objectClass=*))(|(samaccountname=$sKeyword)(userprincipalname=$sKeyword)))";
    }
    else
      $sFilter = "(&(|(objectClass=*))(|(uid=$sKeyword)(cn=$sKeyword)))";

    $this->log ( $oLink, "search users with filter: $sFilter"  );
    $aUsers  = array();
    $attributeSet = array('dn','uid','samaccountname', 'cn','givenname','sn','mail','userprincipalname','objectcategory', 'manager');
    $attributeSet = array_merge($attributeSet, $attributeSetAdd);
    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, $attributeSet);
    if ($oError = @ldap_errno($oLink)) {
      $this->log ( $oLink, "Error in Search users"  );
      return $aUsers;
    }
    else {
      if ($oSearch) {
        $entries = @ldap_count_entries($oLink, $oSearch);
        if ( $entries > 0) {

          $sUsername = '';
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';
          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );

            $sUsername = isset($aAttr[ $uidUser ]) ? $aAttr[ $uidUser ] : '';
            if ($sUsername != '') {
              $aUserAttributes = array();
              foreach ($attributeUserSet as $key => $value) {
                if (isset($aAttr[$value])) {
                  $aUserAttributes[$key] = $aAttr[$value];
                }
              }
              $aUsers[] = array_merge(array('sUsername'  => $sUsername,
                                'sFullname'  => $aAttr['cn'],
                                'sFirstname' => isset($aAttr['givenname']) ? $aAttr['givenname'] : '',
                                'sLastname'  => isset($aAttr['sn']) ? $aAttr['sn'] : '',
                                'sEmail'     => isset($aAttr['mail']) ? $aAttr['mail'] : ( isset($aAttr['userprincipalname'])?$aAttr['userprincipalname'] : '')  ,
                                'sCategory'  => isset($aAttr['objectcategory']) ? $aAttr['objectcategory'] : ''  ,
                                'sDN'        => $aAttr['dn'],
                                'sManagerDN' => isset($aAttr['manager']) ? is_array($aAttr['manager']) ? $aAttr['manager'][0] : $aAttr['manager'] : ''),$aUserAttributes);
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      $sUsers = "found $entries users: ";
      foreach ( $aUsers as $key => $val ) {
        $sUsers .= $val['sUsername'] . ' ';
      }
      $this->log ( $oLink, $sUsers  );
      return $aUsers;
    }
  }

  /**
   * This method search in the ldap/active directory source for an user using the UID, (samaccountname or uid )
   * the value should be in $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER']
   * @param String $sKeyword The keyword in order to match the record with the identifier attribute
   * @param String $identifier id identifier, this parameter is optional
   * @return mixed if the user has been found or not
   */
  function searchUserByUid($sKeyword, $identifier='') {

    if (is_array($sKeyword)){
      $sKeyword     = $sKeyword[0];
    } else {
      $sKeyword     = trim($sKeyword);
    }
    $RBAC         = RBAC::getSingleton();
//    $RBAC->userObj = new RbacUsers();
    if ($RBAC->authSourcesObj==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }

    $aAuthSource = $RBAC->authSourcesObj->load($this->sAuthSource);

    $attributeUserSet = array();
    $attributeSetAdd = array();
    if (    isset($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'])
        &&  count($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE']) ) {
        foreach ($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'] as $value) {
            $attributeSetAdd[] = $value['attributeLdap'];
            $attributeUserSet[$value['attributeUser']] = $value['attributeLdap'];
        }
    }

    if ( $this->oLink == NULL ) {
      $oLink = $this->ldapConnection($aAuthSource);
      $this->oLink = $oLink;
    }
    else
      $oLink = $this->oLink;

    $sKeyword = ( (substr($sKeyword, 0,1) != '') ? '' : '' ) . $sKeyword;

/*    $sFilter  = '(&';
    if (count($aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']) > 0) {
      $sFilter .= '(|';
      $aObjects = explode("\n", $aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']);
      foreach ($aObjects as $sObject) {
        $sFilter .= '(objectClass=' . trim($sObject) . ')';
      }
      $sFilter .= ')';
    }
*/
    $sFilter  = '(&(|(objectClass=*))';

    $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';

    $altFilter = '';

    if ( $identifier!='' && $identifier!=$uidUser ){
      $altFilter = "($identifier=$sKeyword)";
    }

    $sFilter .= "(|($uidUser=$sKeyword)(samaccountname=$sKeyword)(userprincipalname=$sKeyword)$altFilter))";
    $aUser  = null;

    $attributeSet = array('dn','uid','samaccountname','cn','givenname','sn','mail','userprincipalname');
    $attributeSet = array_merge($attributeSet, $attributeSetAdd);

    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, $attributeSet);

    if ($oError = @ldap_errno($oLink)) {
      return $aUser;
    }
    else {
      if ($oSearch) {
        if (@ldap_count_entries($oLink, $oSearch) > 0) {
          $sUsername = '';
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';
          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            $sUsername = isset($aAttr[ $uidUser ]) ? $aAttr[ $uidUser ] : '';
            if ($sUsername != '') {
              $aUserAttributes = array();
              foreach ($attributeUserSet as $key => $value) {
                if (isset($aAttr[$value])) {
                  $aUserAttributes[$key] = $aAttr[$value];
                }
              }
              $aUser = array_merge(array(
                                'sUsername'  => $sUsername,
                                'sFullname'  => isset($aAttr['cn']) ? $aAttr['cn'] : $sUsername ,
                                'sFirstname' => isset($aAttr['givenname']) ? $aAttr['givenname'] : $sUsername ,
                                'sLastname'  => isset($aAttr['sn']) ? $aAttr['sn'] : '',
                                'sEmail'     => isset($aAttr['mail']) ? $aAttr['mail'] : ( isset($aAttr['userprincipalname'])?$aAttr['userprincipalname'] : '')  ,
                                'sDN'        => $aAttr['dn']),$aUserAttributes);
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      return $aUser;
    }
  }

  function automaticRegister($aAuthSource, $strUser, $strPass) {

    $RBAC = RBAC::getSingleton();
    if ($RBAC->userObj==NULL){
      $RBAC->userObj = new RbacUsers();
    }
    if ($RBAC->rolesObj==NULL){
      $RBAC->rolesObj = new Roles();
    }
    $user = $this->searchUserByUid($strUser);

    $res = 0;

    if ( is_array( $user) ) {
      if ( $this->VerifyLogin( $user['sUsername'], $strPass) === TRUE ) {
        $res = 1;
      }
      if ( $res == 0 &&  $this->VerifyLogin( $user['sDN'], $strPass) === TRUE ) {
        $res = 1;
      }
    }
    if ( $res == 0 ) {
      $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);
      $aAttributes = array();
      if (isset($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'])) {
          $aAttributes = $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'];
      }
      $aData['USR_USERNAME']     = $user['sUsername'];
      $aData['USR_PASSWORD']     = md5($user['sUsername']);
      $aData['USR_FIRSTNAME']    = $user['sFirstname'];
      $aData['USR_LASTNAME']     = $user['sLastname'];
      $aData['USR_EMAIL']        = $user['sEmail'];
      $aData['USR_DUE_DATE']     = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 2));
      $aData['USR_CREATE_DATE']  = date('Y-m-d H:i:s');
      $aData['USR_UPDATE_DATE']  = date('Y-m-d H:i:s');
      $aData['USR_BIRTHDAY']     = date('Y-m-d');
      $aData['USR_STATUS']       = 1;
      $aData['USR_AUTH_TYPE']    = strtolower($aAuthSource['AUTH_SOURCE_PROVIDER']);
      $aData['UID_AUTH_SOURCE']  = $aAuthSource['AUTH_SOURCE_UID'];
      $aData['USR_AUTH_USER_DN'] = $user['sDN'];
      $aData['USR_STATUS']       = 'ACTIVE';
      $aData['USR_PASSWORD']     = md5($sUserUID);//fake :p
      $aData['USR_ROLE']         = 'PROCESSMAKER_OPERATOR';

      if (count($aAttributes)) {
          foreach ($aAttributes as $value) {
              if (isset( $user[$value['attributeUser']] )) {
                  $aData[$value['attributeUser']] = str_replace( "*", "'", $user[$value['attributeUser']] );
                  if ($value['attributeUser'] == 'USR_STATUS') {
                      $evalValue = $aData[$value['attributeUser']];
                      $statusValue = 'INACTIVE';
                      if (is_string($evalValue) && G::toUpper($evalValue) == 'ACTIVE') {
                          $statusValue = 'ACTIVE';
                      }
                      if (is_bool($evalValue) && $evalValue == true) {
                          $statusValue = 'ACTIVE';
                      }
                      if ( (is_float($evalValue) || is_int($evalValue) ||
                                      is_integer($evalValue) || is_numeric($evalValue)) && (int)$evalValue != 0 && (int)$evalValue != 66050) {
                          $statusValue = 'ACTIVE';
                      }
                      $aData[$value['attributeUser']] = $statusValue;
                  }
              }
          }
      }

      $sUserUID                  = $RBAC->createUser($aData, 'PROCESSMAKER_OPERATOR');
      $aData['USR_UID']          = $sUserUID;
      require_once 'classes/model/Users.php';
      $oUser = new Users();
      $oUser->create($aData);
      $this->log( null, "Automatic Register for user $strUser " );
      $res = 1;
    }
    return $res;
  }



  /**
    function to obtain users from a specific ldap Department

  */
  function getUsersFromDepartment($sNewBaseDn) {
    $sNewBaseDn   = trim($sNewBaseDn);
    $RBAC         = RBAC::getSingleton();

    if ($RBAC->authSourcesObj==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);
    if (!isset($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER'])) {
      $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER'] = '';
    }

    //use previously connection if it exists
    if ( $this->oLink == NULL ) {
      $oLink = $this->ldapConnection($aAuthSource);
      $this->oLink = $oLink;
    }
    else
      $oLink = $this->oLink;

    //now the filter to obtain user for this organizational units
    $sFilter = $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER'] != '' ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER'] : '(&(!(objectClass=organizationalUnit)))';
    $aUsers  = array();
    $oSearch = @ldap_list($oLink, $sNewBaseDn , $sFilter, array('dn','cn','samaccountname','uid','givenname','sn','mail','userprincipalname', 'manager'));

    if ($error = @ldap_errno($oLink) ) {
      // Added by JC - Start
      if ($error == 4) {
        return $this->searchByInitialLetters($oLink, $sNewBaseDn, ($aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' ? 'samaccountname' : 'uid'), $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER']);
      }
      else {
        $this->log($oLink, "Error in Search users from Department $sNewBaseDn");
      }
      // Added by JC - End
      return $aUsers;
    }
    else {
      if ($oSearch) {
        if ( $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' )
          $uidUser = 'samaccountname';
        else
          $uidUser = 'uid';
        $this->log ( $oLink, "Search $sNewBaseDn accounts with identifier = $uidUser "  );
        $entryUsers = @ldap_count_entries($oLink, $oSearch);
        if ( $entryUsers > 0) {
          $sUsername = '';
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';

          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            $sUsername = isset($aAttr[ $uidUser ]) ? $aAttr[ $uidUser ] : '';
            //seems this fix was for weird active directories, because they were returning an array... anyway here it is the bug.
            if ( is_array($sUsername ) && isset($sUsername['0']) ) $sUsername = $sUsername['0'];

            if ($sUsername != '') {
              $aUsers[] = array('sUsername'  => $sUsername,
                                'sFullname'  => $aAttr['cn'],
                                'sPassword'  => isset($aAttr['userpassword']) ? $aAttr['userpassword'] : '',
                                'sFirstname' => isset($aAttr['givenname']) ? $aAttr['givenname'] : '',
                                'sLastname'  => isset($aAttr['sn']) ? $aAttr['sn'] : '',
                                'sEmail'     => isset($aAttr['mail']) ? $aAttr['mail'] : ( isset($aAttr['userprincipalname'])?$aAttr['userprincipalname'] : '')  ,
                                'sDN'        => $aAttr['dn'],
                                'sManagerDN' => isset($aAttr['manager']) ? is_array($aAttr['manager']) ? $aAttr['manager'][0] : $aAttr['manager'] : '');
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      $this->log ( $oLink, "Found " . count($aUsers) . " users in department $sNewBaseDn"  );
      return $aUsers;
    }
  }

  /**
   * Get a deparment list
   * @return <type>
   */
  function searchDepartments()
  {
    if (!class_exists('RBAC')){
      G::LoadSystem('rbac' );
    }

    $RBAC         = RBAC::getSingleton();
    if ($RBAC->authSourcesObj ==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);

    //El this $this->oLink debe ser NULL cuando se ejecuta el cron

    if ($this->oLink == null) {
        $oLink = $this->ldapConnection($aAuthSource);
        $this->oLink = $oLink;
    } else {
        $oLink = $this->oLink;
    }

    $sFilter  = '(&';
    if (count($aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']) > 0) {
      $sFilter .= '(|';
      $aObjects = explode("\n", $aAuthSource['AUTH_SOURCE_OBJECT_CLASSES']);
      foreach ($aObjects as $sObject) {
        $sFilter .= '(objectClass=' . trim($sObject) . ')';
      }
      $sFilter .= ')';
    }
    $sFilter = '(&(|(objectClass=organizationalUnit))';
    $sFilter .= "(|(ou=*))";
    $sFilter .= ')';

    $this->log ( $oLink, "search Departments with Filter: $sFilter"  );

    $aDepts = array();
    $unitsBase = $this->custom_ldap_explode_dn($aAuthSource['AUTH_SOURCE_BASE_DN']);
    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, array('dn','ou'));

    if ($oError = @ldap_errno($oLink)) {
      $this->log ( $oLink, "Error in Search"  );
      return $aDepts;
    }
    else {
      if ($oSearch) {
        //the first node is root
        $node = array();
        $node['dn']          = $aAuthSource['AUTH_SOURCE_BASE_DN'];
        $node['parent']      = '';
        $node['ou']          = 'ROOT';
        $node['users']       = '0';
        $aDepts[] = $node;

        //get departments from the ldap entries
        if (@ldap_count_entries($oLink, $oSearch) > 0) {
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            $unitsEqual = $this->custom_ldap_explode_dn($aAttr['dn']);
            if ( count($unitsEqual ) == 1 && $unitsEqual[0] == '' ) continue;

            if (count($unitsEqual) > count($unitsBase)) {
              unset($unitsEqual[0]);
            }

            if ( isset( $aAttr['ou'] ) && !is_array($aAttr['ou']) ) {
              $node = array();
              $node['dn']          = $aAttr['dn'];
              $node['parent']      = isset ($unitsEqual[1]) ? implode(',', $unitsEqual) : '';
              $node['ou']          = trim($aAttr['ou']);
              $node['users']       = '0';
              $aDepts[] = $node;
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
          //$this->createDepartments ($aDepts);
        }
      }
      $sDeptos = '';
      foreach ($aDepts as $dep ) $sDeptos .= ' ' . $dep['ou'];
      $this->log ( $oLink, "found ". count($aDepts) . " departments:$sDeptos"  );
      return $aDepts;
    }
  }

  /**
   * Get the Userlist from a department based on the name
   * @param string $departmenName
   * @return array
   */
  function getUsersFromDepartmentByName($departmenName) {

    $dFilter  = '(&(|(objectClass=organizationalUnit))';
    $dFilter .= "(|(ou=".$departmenName."))";
    $dFilter .= ')';
    $aUsers  = array();
    $RBAC    = RBAC::getSingleton();
//    $RBAC->userObj = new RbacUsers();
    $RBAC->authSourcesObj = new AuthenticationSource();
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);

    if ( $this->oLink == NULL )
      $oLink = $this->ldapConnection($aAuthSource);
    else
      $oLink = $this->oLink;

    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $dFilter,  array('dn','cn','uid','samaccountname','givenname','sn','mail','userprincipalname') );
    if ($oError = @ldap_errno($oLink)) {
      return $aUsers;
    }
    else {
      if ($oSearch) {
        //get the departments from the ldap entries
        if (@ldap_count_entries($oLink, $oSearch) > 0) {
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          do {
            $aAttr  = $this->getLdapAttributes ( $oLink, $oEntry );
            $aUsers = $this->getUsersFromDepartment($aAttr['dn']);
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
          //$this->createDepartments ($aDepts);
        }
      }
    return $aUsers;
   }

  }

/**
 * Check if the department exists and returns the PM UID
 * @param <type> $currentDN
 * @return <type>
 */

  function getDepUidIfExistsDN ( $currentDN ) {
    try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(DepartmentPeer::DEP_STATUS ,  'ACTIVE' );
      $oCriteria->add(DepartmentPeer::DEP_LDAP_DN,  $currentDN );
      $oDataset = DepartmentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();

      if ($aRow = $oDataset->getRow()) {
        return ( $aRow['DEP_UID'] );
      }
      return false;
    }
    catch ( Exception $e ) {
      return false;
    }
  }

  /**
   * get the users from ProcessMaker tables, and returns the depUid and an array with the users in that department
   * @param <type> $currentDN
   * @return <type>
   */

  function getUsersFromPMDepartment( $currentDN ) {
    $pmUsers = array();
    try {
      $depUid = $this->getDepUidIfExistsDN( $currentDN );
      if ( $depUid === false ) {
        return array( 'depUid' => '', 'pmUsers' => array() );
        //throw ( new Exception ("invalid department $currentDN") );
      }

      $oCriteria = new Criteria('workflow');
      $oCriteria->add(UsersPeer::DEP_UID,  $depUid );
      $oCriteria->add(UsersPeer::USR_STATUS,  'CLOSED' , Criteria::NOT_EQUAL );
      $oDataset = UsersPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();

      while ($aRow = $oDataset->getRow()) {
        $pmUsers[] = $aRow;

        $oDataset->next();
      }

      return array( 'depUid' => $depUid, 'pmUsers' => $pmUsers );
    }
    catch ( Exception $e ) {
      throw ( new Exception ( $e->getMessage() ));
    }
  }

  function userIsTerminated ($userUid,$sOuTerminated) {
    $terminated = false;
    $aLdapUsers = $this->getUsersFromDepartmentByName($sOuTerminated);
    foreach ($aLdapUsers as $aLdapUser){
      if ($aLdapUser['sUsername'] == $userUid){
        $terminated = true;
        break;
      }
    }

    return $terminated;
  }

  /* activate an user previously deactivated
    if user is now in another department, we need the second parameter, the depUid

    @param string $userUid
    @param string optional department DN
    @param string optional DepUid
  */
  function activateUser ($userUid, $userDn = NULL, $depUid = NULL ) {
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

  function deactivateUser ($userUid) {
    if (!class_exists('RbacUsers')) {
          require_once(PATH_RBAC.'model/RbacUsers.php');
        }
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        // select set
        $c1 = new Criteria('rbac');
        $c1->add(RbacUsersPeer::USR_USERNAME, $userUid);
        // update set
        $c2 = new Criteria('rbac');
        $c2->add(RbacUsersPeer::USR_STATUS, '0');

        BasePeer::doUpdate($c1, $c2, $con);

        if (!class_exists('Users')) {
          require_once('classes/model/Users.php');
        }
        $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
        // select set
        $c1 = new Criteria('workflow');
        $c1->add(UsersPeer::USR_USERNAME, $userUid);
        // update set
        $c2 = new Criteria('workflow');
        $c2->add(UsersPeer::USR_STATUS, 'INACTIVE');
        $c2->add(UsersPeer::DEP_UID, '');

        BasePeer::doUpdate($c1, $c2, $con);
  }

  public function getTerminatedOu() {
    if (trim($this->sAuthSource)!=''){
      $RBAC = RBAC::getSingleton();
      $aAuthSource = $RBAC->authSourcesObj->load($this->sAuthSource );
      $attributes = $aAuthSource['AUTH_SOURCE_DATA'];
      $this->sTerminatedOu = isset($attributes['AUTH_SOURCE_RETIRED_OU'])?$attributes['AUTH_SOURCE_RETIRED_OU']:'';
    }
    return $this->sTerminatedOu;
  }

  /**
    get all authsource for this plugin ( ldapAdvanced plugin, because other authsources are not needed )
    this function is used only by cron
    returns only AUTH_SOURCE_PROVIDER = ldapAdvanced

    @return array with authsources with type = ldap
  */
  function getAuthSources(){
    $oCriteria = new Criteria('rbac');
    $aAuthSources = array();
    require_once(PATH_RBAC.'model/AuthenticationSource.php');
    $oAuthSource = new AuthenticationSource();
    $oCriteria = $oAuthSource->getAllAuthSources();
    $oDataset = AuthenticationSourcePeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
      if (  $aRow['AUTH_SOURCE_PROVIDER'] == 'ldapAdvanced' )
        $aAuthSources[] = $aRow;
      $oDataset->next();
    }
    return $aAuthSources;
  }


  /**
    function to get departments from the array previously obtained from LDAP
    we are calling registered departments
    it is a recursive function, in the first call with an array with first top level departments from PM
    then go thru all departments and obtain a list of departments already created in PM and pass that array
    to next function to synchronize All users for each department
    this function is used in cron only

    @param array departments obtained from LDAP/Active Directory
    @param array of departments, first call have only top level departments
  */
  function getRegisteredDepartments($aLdapDepts,$aDepartments){
    $aResult = array();
    if (!empty($aLdapDepts)) {
      $aLdapDepts[0]['ou'] = $aLdapDepts[0]['ou'] . ' ' . $aLdapDepts[0]['dn'];
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
    }
    return $aResult;
  }

  /**
   select departments but it is not recursive, only returns departments in this level
   @param string $DepParent the DEP_UID for parent department
  */
  function getDepartments( $DepParent )  {
    try {
      $result = array();
      $criteria = new Criteria('workflow');
      $criteria->add(DepartmentPeer::DEP_PARENT, $DepParent, Criteria::EQUAL);
      $con = Propel::getConnection(DepartmentPeer::DATABASE_NAME);
      $objects = DepartmentPeer::doSelect($criteria, $con);

      foreach( $objects as $oDepartment ) {
        $node = array();
        $node['DEP_UID']      = $oDepartment->getDepUid();
        $node['DEP_PARENT']   = $oDepartment->getDepParent();
        $node['DEP_TITLE']    = stripslashes($oDepartment->getDepTitle());
        $node['DEP_STATUS']   = $oDepartment->getDepStatus();
        $node['DEP_MANAGER']  = $oDepartment->getDepManager();
        $node['DEP_LDAP_DN']  = $oDepartment->getDepLdapDn();
        $node['DEP_LAST']     = 0;

        $criteriaCount = new Criteria('workflow');
        $criteriaCount->clearSelectColumns();
        $criteriaCount->addSelectColumn( 'COUNT(*)' );
        $criteriaCount->add(DepartmentPeer::DEP_PARENT, $oDepartment->getDepUid(), Criteria::EQUAL);
        $rs = DepartmentPeer::doSelectRS($criteriaCount);
        $rs->next();
        $row = $rs->getRow();
        $node['HAS_CHILDREN'] = $row[0];
        $result[] = $node;
      }
      if ( count($result) >= 1 )
        $result[ count($result) -1 ]['DEP_LAST'] = 1;
      return $result;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
    function to get users from USERS table in wf_workflow and filter by department
    this function is used in cron only

    @param string department UID ( DEP_UID value )
    @return array of users
  */
  function getUsersFromDepartmentTable( $depUid ) {
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
    function to get users from USERS table in wf_workflow and filter by department
    this function is used in cron only

    @param string department UID ( DEP_UID value )
    @return array of users
  */
  function getUserFromPM( $userName ) {
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
    get all user (UID, USERNAME) registered in RBAC with this authSource
    this function is used in cron only

    @param string authSource UID ( AUT_UID value )
    @return array of users
  */
  function getUsersFromAuthSource( $autUid ) {
    try {
      $aUsers = array();

      $oCriteria = new Criteria('rbac');
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_UID);
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
      $oCriteria->addSelectColumn(RbacUsersPeer::USR_AUTH_USER_DN);
      //$oCriteria->add(RbacUsersPeer::USR_STATUS, '1', Criteria::EQUAL);
      $oCriteria->add(RbacUsersPeer::UID_AUTH_SOURCE, $autUid, Criteria::EQUAL);
      $oCriteria->add(RbacUsersPeer::USR_AUTH_TYPE, 'ldapadvanced', Criteria::EQUAL);
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
    get all user (UID, USERNAME) moved to Removed OU
    this function is used in cron only

    @param array authSource row, in this fuction we are validating if Removed OU is defined or not
    @return array of users
  */
  public function getUsersFromRemovedOu( $aAuthSource) {
    $aUsers = array(); //empty array is the default result
    $attributes = unserialize($aAuthSource['AUTH_SOURCE_DATA']);
    $this->sTerminatedOu = isset($attributes['AUTH_SOURCE_RETIRED_OU'])? trim($attributes['AUTH_SOURCE_RETIRED_OU']) : '';
    if ($this->sTerminatedOu == '' ) {
      return $aUsers;
    }
    return $this->getUsersFromDepartmentByName( $this->sTerminatedOu );
  }

  /**
    set STATUS=0 for all users in the array $aUsers
    this functin is used to deactivate an array of users ( usually used for Removed OU )
    this function is used in cron only

    @param array authSource row, in this fuction we are validating if Removed OU is defined or not
    @return array of users
  */
  public function deactiveArrayOfUsers( $aUsers) {
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
    creates an users using the data send in the array $aUsers
    and then add the user to specific department
    this function is used in cron only

    @param array $aUser info taken from ldap
    @param string $depUid the department UID
    @return boolean
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
    $aData['USR_AUTH_TYPE']    = 'ldapadvanced';
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

  public function searchByInitialLetters($link, $baseDN, $uidUser, $filter = '') {
    $this->log($link, "Search $baseDN accounts with identifier = $uidUser");
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    if ($filter == '') {
      $filter = '(&(|(objectClass=*))(|(samaccountname=%s)(userprincipalname=%s))(objectCategory=person))';
    }
    else {
      $filter = substr($filter, 0, -1) . '(|(samaccountname=%s)(userprincipalname=%s)))';
    }
    $users = array();
    foreach ($characters as $character) {
      $keyword = $character . '*';
      $resource = @ldap_search($link, $baseDN, sprintf($filter, $keyword, $keyword), array('dn', 'cn', 'samaccountname', 'givenname', 'sn', 'mail', 'userprincipalname', 'manager'));
      if (!($error = @ldap_errno($link))) {
        $entriesQuantity = @ldap_count_entries($link, $resource);
        if ($entriesQuantity > 0) {
          $username = '';
          $entry = @ldap_first_entry($link, $resource);
          do {
            $attributes = $this->getLdapAttributes($link, $entry);
            $username = isset($attributes[$uidUser]) ? $attributes[$uidUser] : '';
            if (is_array($username) && isset($username['0'])) $username = $username['0'];
            if ($username != '') {
              $users[] = array('sUsername'  => $username,
                               'sFullname'  => $attributes['cn'],
                               'sPassword'  => isset($attributes['userpassword']) ? $attributes['userpassword'] : '',
                               'sFirstname' => isset($attributes['givenname']) ? $attributes['givenname'] : '',
                               'sLastname'  => isset($attributes['sn']) ? $attributes['sn'] : '',
                               'sEmail'     => isset($attributes['mail']) ? $attributes['mail'] : (isset($attributes['userprincipalname']) ? $attributes['userprincipalname'] : ''),
                               'sDN'        => $attributes['dn'],
                               'sManagerDN' => isset($aAttr['manager']) ? is_array($aAttr['manager']) ? $aAttr['manager'][0] : $aAttr['manager'] : '');
            }
          } while ($entry = @ldap_next_entry($link, $entry));
        }
      }
      else {
        $this->log($link, "Error in Search users from Department $baseDN");
        return array();
      }
    }
    $this->log($link, "Found " . count($users) . " users in department $baseDN");
    return $users;
  }

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
   * Get a group list
   * @return <type>
   */
  function searchGroups()
  {
    if (!class_exists('RBAC')){
      G::LoadSystem('rbac' );
    }

    $RBAC = RBAC::getSingleton();
    if ($RBAC->authSourcesObj ==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);

    if ($this->oLink == null) {
        $oLink = $this->ldapConnection($aAuthSource);
        $this->oLink = $oLink;
    } else {
        $oLink = $this->oLink;
    }

    $sFilter = "(&(objectCategory=group)(name=*))";

    $this->log ( $oLink, "search groups with Filter: $sFilter"  );

    $aGroups = array();
    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, array('dn','cn'));

    if ($oError = @ldap_errno($oLink)) {
      $this->log ( $oLink, "Error in Search"  );
      return $aGroups;
    }
    else {
      if ($oSearch) {
        //the first node is root
        $node = array();
        /*$node['dn']          = $aAuthSource['AUTH_SOURCE_BASE_DN'];
        $node['parent']      = '';
        $node['cn']          = 'ROOT';
        $node['users']       = '0';
        $aGroups[] = $node;*/

        //get groups from the ldap entries
        if (@ldap_count_entries($oLink, $oSearch) > 0) {
          $oEntry = @ldap_first_entry($oLink, $oSearch);
          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            if ( isset( $aAttr['cn'] ) && !is_array($aAttr['cn']) ) {
              $node = array();
              $node['dn']          = $aAttr['dn'];
              $node['cn']          = trim($aAttr['cn']);
              $node['users']       = '0';
              $aGroups[] = $node;
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      $sGroups = '';
      foreach ($aGroups as $group ) $sGroups .= ' ' . $group['cn'];
      $this->log ( $oLink, "found ". count($aGroups) . " groups:$sGroups"  );
      return $aGroups;
    }
  }

/**
 * Check if the group exists and returns the PM UID
 * @param <type> $currentDN
 * @return <type>
 */

  function getGrpUidIfExistsDN($currentDN) {
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
   * get the users from ProcessMaker tables, and returns the grpUid and an array with the users in that group
   * @param <type> $currentDN
   * @return <type>
   */

  function getUsersFromPMGroup($currentDN) {
    $pmUsers = array();
    try {
      $grpUid = $this->getGrpUidIfExistsDN($currentDN);
      if ( $grpUid === false ) {
        return array( 'grpUid' => '', 'pmUsers' => array() );
      }

      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn('*');
      $oCriteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(GroupUserPeer::GRP_UID,  $grpUid);
      $oCriteria->add(UsersPeer::USR_STATUS,  'CLOSED' , Criteria::NOT_EQUAL);
      $oDataset = GroupUserPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();

      while ($aRow = $oDataset->getRow()) {
        $pmUsers[] = $aRow;
        $oDataset->next();
      }

      return array( 'grpUid' => $grpUid, 'pmUsers' => $pmUsers );
    }
    catch ( Exception $e ) {
      throw ( new Exception ( $e->getMessage() ));
    }
  }

  /**
   select groups but it is not recursive, only returns groups in this level
  */
  function getGroups()  {
    try {
      $result = array();
      $criteria = new Criteria('workflow');
      $con = Propel::getConnection(GroupwfPeer::DATABASE_NAME);
      $objects = GroupwfPeer::doSelect($criteria, $con);
      foreach($objects as $oGroup) {
        $node = array();
        $node['GRP_UID']      = $oGroup->getGrpUid();
        $node['GRP_TITLE']    = stripslashes($oGroup->getGrpTitle());
        $node['GRP_STATUS']   = $oGroup->getGrpStatus();
        $node['GRP_LDAP_DN']  = $oGroup->getGrpLdapDn();
        $result[] = $node;
      }
      return $result;
    }
    catch (exception $e) {
      throw $e;
    }
  }

  /**
    function to get groups from the array previously obtained from LDAP
    we are calling registered groups
    it is a recursive function, in the first call with an array with first top level groups from PM
    then go thru all groups and obtain a list of groups already created in PM and pass that array
    to next function to synchronize All users for each group
    this function is used in cron only

    @param array groups obtained from LDAP/Active Directory
    @param array of groups, first call have only top level groups
  */
  function getRegisteredGroups($aLdapGroups,$aGroups){
    $aResult = array();
    if (!empty($aLdapGroups)) {
      $aLdapGroups[0]['cn'] = $aLdapGroups[0]['cn'] . ' ' . $aLdapGroups[0]['dn'];
      foreach ($aLdapGroups as $ldapGroup) {
        foreach ($aGroups as $group){
          if ($group['GRP_TITLE'] == $ldapGroup['cn'] && $group['GRP_LDAP_DN'] != ''){
            $group['DN'] = $ldapGroup['dn'];
            $aResult[] = $group;
          }
        }
      }
    }
    return $aResult;
  }

  /**
    function to get users from USERS table in wf_workflow and filter by group
    this function is used in cron only

    @param string group UID ( GRP_UID value )
    @return array of users
  */
  function getUsersFromGroupTable( $grpUid ) {
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
    function to obtain users from a specific ldap Group

  */
  function getUsersFromGroup($sNewBaseDn) {
    $sNewBaseDn   = trim($sNewBaseDn);
    $RBAC         = RBAC::getSingleton();

    if ($RBAC->authSourcesObj==NULL){
      $RBAC->authSourcesObj = new AuthenticationSource();
    }
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);

    //use previously connection if it exists
    if ( $this->oLink == NULL ) {
      $oLink = $this->ldapConnection($aAuthSource);
      $this->oLink = $oLink;
    }
    else
      $oLink = $this->oLink;

    //now the filter to obtain user for this organizational units
    $sFilter  = '(&(memberOf=' . $sNewBaseDn . '))';
    $aUsers  = array();
    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, array('dn','cn','samaccountname','givenname','sn','mail','userprincipalname', 'manager'));

    if ($error = @ldap_errno($oLink) ) {
      // Added by JC - Start
      if ($error == 4) {
        return $this->searchByInitialLetters($oLink, $sNewBaseDn, ($aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' ? 'samaccountname' : 'uid'), $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_USERS_FILTER']);
      }
      else {
        $this->log($oLink, "Error in Search users from Group $sNewBaseDn");
      }
      // Added by JC - End
      return $aUsers;
    }
    else {
      if ($oSearch) {
        if ( $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' )
          $uidUser = 'samaccountname';
        else
          $uidUser = 'uid';
        $this->log ( $oLink, "Search $sNewBaseDn accounts with identifier = $uidUser "  );
        $entryUsers = @ldap_count_entries($oLink, $oSearch);
        if ( $entryUsers > 0) {
          $sUsername = '';
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';

          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            $sUsername = isset($aAttr[ $uidUser ]) ? $aAttr[ $uidUser ] : '';
            //seems this fix was for weird active directories, because they were returning an array... anyway here it is the bug.
            if ( is_array($sUsername ) && isset($sUsername['0']) ) $sUsername = $sUsername['0'];

            if ($sUsername != '') {
              $aUsers[] = array('sUsername'  => $sUsername,
                                'sFullname'  => $aAttr['cn'],
                                'sPassword'  => isset($aAttr['userpassword']) ? $aAttr['userpassword'] : '',
                                'sFirstname' => isset($aAttr['givenname']) ? $aAttr['givenname'] : '',
                                'sLastname'  => isset($aAttr['sn']) ? $aAttr['sn'] : '',
                                'sEmail'     => isset($aAttr['mail']) ? $aAttr['mail'] : ( isset($aAttr['userprincipalname'])?$aAttr['userprincipalname'] : '')  ,
                                'sDN'        => $aAttr['dn'],
                                'sManagerDN' => isset($aAttr['manager']) ? is_array($aAttr['manager']) ? $aAttr['manager'][0] : $aAttr['manager'] : '');
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      $this->log ( $oLink, "Found " . count($aUsers) . " users in group $sNewBaseDn"  );
      return $aUsers;
    }
  }

  function custom_ldap_explode_dn($dn) {
    $dn = trim($dn, ',');
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