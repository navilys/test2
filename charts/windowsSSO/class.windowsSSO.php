<?php
/**
 * The Windows SSO class
 *
 * @author Julio Cesar Laura <juliocesar at colosa dot com> <contact at julio-laura dot com>
 * @package plugins.windowsSSO
 * @copyright Copyright (C) 2004 - 2011 Colosa Inc.
 */

G::LoadClass('plugin');

class windowsSSOClass extends PMPlugin {

  // The constants of the class
  const PLEXCEL_AUTH_SSO       = 0x1;
  const PLEXCEL_AUTH_LOGON     = 0x2;
  const PLEXCEL_AUTH_SSO_LOGON = 0x3;

  /**
   * Contructor of the class
   * @return void
   */
  public function __construct() {
    set_include_path(PATH_PLUGINS . 'windowsSSO' . PATH_SEPARATOR . get_include_path());
  }

  /**
   * The generic setup function
   *
   * @return void
   */
  public function setup() {
  }

  /**
   * Add a line in the windowsSSO log
   *
   * @author Fernando Ontiveros Lira <fernando at colosa dot com>
   * @param Object $px Plexcel connection
   * @param String $text The text to save in the log
   */
  public static function log($px, $text) {
    $fpt = fopen(PATH_DATA . 'log/windowsSSO.log', 'a');
    $plexcelErrorNr = '';
    if (!is_null($px)) {
    	$plexcelErrorNr = @plexcel_status($px);
    }
    fwrite($fpt, sprintf("%s %s %s %s %s\n", date('Y-m-d H:i:s'), getenv('REMOTE_ADDR'), SYS_SYS, $plexcelErrorNr, $text));
    fclose($fpt);
  }

  /**
   * Get the value of the parameters sent to the server
   *
   * @param string $name The parameter name
   * @param string $default The default value for the requested parameter
   * @return String The value of the parameter requested
   */
  public function plexcel_get_param($name, $default = null) {
    if (!isset($_REQUEST[$name])) {
      $_REQUEST[$name] = '';
    }
    $str = trim($_REQUEST[$name]);
    return strlen($str) > 0 ? $str : $default;
  }

  /**
   * Get a token
   *
   * @param string $name The session variable name
   * @return String The token value
   */
  public function plexcel_token($name) {
    $token = $_SESSION[$name] = rand(10000, 99999);
    return $token;
  }

  /**
   * Match the token sent with the token in session
   *-
   * @param string $name The session variable name with the token
   * @return Boolean If the token exists
   */
  public function plexcel_token_matches($name) {
    if (isset($_SESSION[$name])) {
      if ($_SESSION[$name] == $this->plexcel_get_param($name, null)) {
        unset($_SESSION[$name]);
        return true;
      }
      else {
        return false;
      }
    }
    return false;
  }

  /**
   * Negotiate the authentication with the server
   *
   * @param Object $px Plexcel connection
   * @return Mixed A boolean or the 401 http header
   */
  private function plexcel_sso($px) {
    if ($this->plexcel_token_matches('p_authenticate_repost')) {
      plexcel_status($px, PLEXCEL_NO_CREDS);
      return false;
    }
    $token = '';
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
      $token = $headers['Authorization'];
      if (strncmp($token, 'Negotiate ', 10) != 0) {
        plexcel_status($px, 'Token does not begin with "Negotiate "');
        return false;
      }
      $token = plexcel_accept_token($px, $token);
      if (plexcel_status($px) != PLEXCEL_CONTINUE_NEEDED) {
        if (plexcel_status($px) == PLEXCEL_SUCCESS) {
          // Authentication success
          if ($token) {
            header('WWW-Authenticate: Negotiate ' . $token, true, 200);
          }
          return true;
        }
        // Authentication failed or something unexpected happend
        return false;
      }
      $token = ' ' . $token;
    }
    header('WWW-Authenticate: Negotiate' . $token);
    header('HTTP/1.1 401 Unauthorized');
  }

  /**
   * Try to Single Sign On in the server
   *
   * @return Boolean If the SSO is possible
   */
  public function singleSignOn() {
    if (function_exists('plexcel_new')) {
  	  global $RBAC;
      $px = plexcel_new(null, null);
      $domainInfo  = plexcel_get_domain($px, null);
      $dcArray = explode('.', $domainInfo['dnsRoot']);
      $dcRoot = '';
      foreach ($dcArray as $k => $v) {
        $dcRoot .= (($dcRoot!='') ? ',' : '' ) . 'dc=' . $v;
      }
      $text = 'SSO bind to [' . $domainInfo['nETBIOSName'] . ',' . $domainInfo['dnsRoot']. ']';
      self::log($px, $text);
      // TODO: Currently Single Sign On is working based in the http/user, nevermind if the authsource is enabled or not.
      if (!$px) {
        self::log($px, 'Error: ' . plexcel_status(null));
      }
      else {
        // Try Kerberos Single Sign-On only
        if (!$this->plexcel_sso($px)) {
          self::log($px, 'Negociating SSO.');
        }
        else {
          $acct = plexcel_get_account($px, null, PLEXCEL_SUPPLEMENTAL);
          if (!is_array($acct)) {
            self::log($px, 'Account is empty.');
          }
        }
      }

      // If program reach this line, means the single sign on conversation was ended.
      // Now if the $acct array is defined means we sucessfully got an account

      if (@plexcel_status($px) == PLEXCEL_SUCCESS && isset($acct) && is_array($acct)) {
      	$RBAC =& RBAC::getSingleton();
        $RBAC->initRBAC();
        // If the user exists, the VerifyUser function will return the user properties
        $resVerifyUser = $RBAC->verifyUser($acct['sAMAccountName']);
        $RBAC->singleSignOn = true;
        if ($resVerifyUser == 0) {
          // Here we are checking if the automatic user Register is enabled, ioc return -1
          $res = $RBAC->checkAutomaticRegister($acct['sAMAccountName'], 'fakepassword');
          if ($res === -1) {
            return false; // No sucessful auto register, skipping the auto register and back to normal login form
          }
          $RBAC->verifyUser($acct['sAMAccountName']);
        }
        if ($RBAC->userObj->fields['USR_STATUS'] == 0) {
          self::log($px, 'Single Sign On failed, user ' . $acct['userPrincipalName'] . ' is INACTIVE');
          return false;
        }
        self::log($px, 'Single Sign On for user ' . $acct['userPrincipalName']);
        return true;
      }
    }
    return false;
  }
}