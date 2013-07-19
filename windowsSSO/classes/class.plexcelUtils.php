<?php
/**
 * Class with plexcel functions
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO.classes
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

class PlexcelUtils {

  // Class attributes
  protected $session = '';
  protected $baseDN = '';
  protected $user = '';
  protected $password = '';
  protected $plexcelConnection;
  protected $characters = array('<', '>', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

  /**
   * Constructor class
   *
   * @param String The PHP session
   * @param String The base DN
   * @param String A user in the Active Directory
   * @param String the pasword of the user
   * @return void
   */
  public function __construct($session, $baseDN, $user, $password = '') {
    $this->session = $session;
    $this->baseDN = $baseDN;
    $this->user = $user;
    $this->password = $password;
    $this->plexcelConnection = plexcel_new(NULL, NULL);
    $this->logon();
  }

  /**
   * Destructor class
   *
   * @return void
   */
  public function __destruct() {
    unset($this->plexcelConnection);
  }

  /**
   * Get the current plexcel connection object
   *
   * @return Object plexcelConnection The plexcel connection object
   */
  public function getPlexcelConnection() {
    return $this->plexcelConnection;
  }

  /**
   * Logon in the server
   *
   * @return void
   */
  public function logon() {
    try {
      if (plexcel_logon($this->plexcelConnection, $this->session, $this->user, $this->password) === false) {
        throw new Exception(plexcel_status($this->plexcelConnection));
      }
    }
    catch (Exception $error) {
      die(sprintf('Error in logon: ', $error->getMessage()));
    }
  }

  /**
   * Get a object by the DN in the Active Directory
   *
   * @param String The DN of the object
   * @param Array The additional attributes to return
   * @return Array The object associated with the DN requested
   */
  public function getObject($dn, $additionalAttributes = array()) {
    try {
      return plexcel_get_account($this->plexcelConnection, $dn, $additionalAttributes);
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
      return false;
    }
  }

  /**
   * Get the objects by class in the Active Directory
   *
   * @param Array The object classes to search
   * @return Array The objects with the class types requested
   */
  public function getObjects($classes) {
    $objects = array();
    try {
      foreach ($classes as $objectClass) {
        switch ($objectClass) {
          case 'organizationalUnit':
            $filter = '(&(objectClass=organizationalUnit)(ou=%s*))';
          break;
          case 'group':
            $filter = '(&(objectCategory=group)(name=%s*))';
          break;
        }
        foreach ($this->characters as $character) {
          $params = array('base' => $this->baseDN, 'scope' => 'sub', 'filter' => sprintf($filter, $character));
          $results = plexcel_search_objects($this->plexcelConnection, $params);
          if (is_array($results)) {
            foreach ($results as $result) {
              $objects[$result['distinguishedName']] = array('objectClass' => $objectClass,
                                                             'name'        => $result['name']);
            }
          }
        }
      }
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
    }
    return $objects;
  }

  /**
   * Search objects using a filter in the Active Directory
   *
   * @param String The filter to use
   * @param Array The additional attributes to return
   * @return Array The objects found
   */
  public function searchObjects($filter, $additionalAttributes = array()) {
    $objects = array();
    try {
      $params  = array('base' => $this->baseDN, 'scope' => 'sub', 'filter' => $filter, 'attrs' => array_merge(array('objectClass', 'name'), $additionalAttributes));
      $results = plexcel_search_objects($this->plexcelConnection, $params);
      foreach ($results as $result) {
        $object = array('objectClass' => (isset($result['objectClass'][0]) ? $result['objectClass'][0] : ''), 'name' => $result['name']);
        foreach ($additionalAttributes as $additionalAttribute) {
          $object[$additionalAttribute] = isset($result[$additionalAttribute]) ? $result[$additionalAttribute] : '';
        }
        $objects[] = $object;
      }
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
    }
    return $objects;
  }

  /**
   * Create a object in the Active Directory
   *
   * @param String $objectClass The class of the object to create
   * @param String $name The name of the object to create (CN)
   * @param String $parent The DN of the parent object
   * @param Array $additionalAttributes The additional attributes to add to the object
   * @return String The DN of the new object
   */
  public function createObject($objectClass, $name, $parent = '', $additionalAttributes = array()) {
    try {
      $account = array();
      $account['objectClass'] = array($objectClass);
      if ($parent != '') {
        $parent = ',' . str_ireplace(',' . $this->baseDN, '', $parent);
      }
      switch ($objectClass) {
        case 'organizationalUnit':
          $dn = sprintf('OU=%s%s,%s', $name, $parent, $this->baseDN);
        break;
        case 'group':
          $dn = sprintf('CN=%s%s,%s', $name, $parent, $this->baseDN);
        break;
        case 'user':
          $dn = sprintf('CN=%s%s,%s', $name, $parent, $this->baseDN);
        break;
        default:
          $dn = '';
        break;
      }
      if ($dn != '') {
        $account['distinguishedName'] = $dn;
        foreach ($additionalAttributes as $name => $value) {
          $account[$name] = $value;
        }
        if (!$this->objectExists($dn)) {
          if (plexcel_add_object($this->plexcelConnection, $account, null) === false) {
            return false;
          }
          return $dn;
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
      return false;
    }
  }

  /**
   * If a object exists in the Active Directory
   *
   * @param String The DN of the object
   * @return Boolean If existe the object
   */
  public function objectExists($dn) {
    try {
      return is_array(plexcel_get_account($this->plexcelConnection, $dn, null));
    }
    catch (Exception $error) {
      throw $error;
    }
  }

  /**
   * Change the password of a user in the Active Directory
   *
   * @param String The user principal name
   * @param String The new pasword od the user
   * @return void
   */
   public function changePassword($userPrincipalName, $password) {
    try {
      if (!plexcel_change_password($this->plexcelConnection, $userPrincipalName, '', $password)) {
        throw new Exception(sprintf('Error changing the password for %s', $userPrincipalName));
      }
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
    }
  }

  /**
   * If a object is member of a department or group
   *
   * @param String The DN of the object
   * @return Boolean If the object is member
   */
  public function isMemberOf($dn) {
    return plexcel_is_member_of($this->plexcelConnection, $dn);
  }

  /**
   * Modify the data of a object in the Active Directory
   *
   * @param Array The object data to update
   * @return void
   */
  public function modifyObject($object) {
    try {
      if (!plexcel_modify_object($this->plexcelConnection, $object, array('member' => PLEXCEL_MOD_ADD))) {
        throw new Exception(plexcel_status($this->plexcelConnection));
      }
    }
    catch (Exception $error) {
      // ToDo: Register error in the log
    }
  }

}