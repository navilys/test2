<?php
/*
 * Class Nightlies.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

class Nightlies {

  const key = '_n1gtl135_plu91n5_';
  
  public static function uuid() {
    return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
        mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
        mt_rand(0, 65535), // 16 bits for "time_mid"
        mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
    );
  }

  public static function encrypt($text, $key) {
    $encryptedText = '';
    $aux = 0;
    if (($text != '') && ($key != '')) {
      $textSize = strlen($text);
      $keySize = strlen($key);
      if ($textSize != $keySize) {
        if ($textSize > $keySize) {
          $compareSize = $textSize;
          $key = self::validateText($key, $compareSize);
        }
        else {
          $compareSize = $keySize;
          $text = self::validateText($text, $compareSize);
        }
      }
      else {
        $compareSize = $textSize;
      }
      for ($i = 0; $i < $compareSize; $i++) {
        $value = ord(substr($text, $i, 1));
        $value = $value + ord(substr($key, $i, 1)) + ($keySize * 2);
        if ($value > 255) {
          $value = $value - 255;
        }
        $value = $value + $aux;
        if ($value > 255) {
          $value = $value - 255;
        }
        $aux = $value;
        if (strlen((string) dechex($value)) < 2) {
          $encryptedText .= '0' . (string) dechex($value);
        }
        else {
          $encryptedText .= (string) dechex($value);
        }
      }
      if (strlen((string) dechex($textSize)) < 2) {
        $encryptedText .= '0' . (string) dechex($textSize * 2);
      }
      else {
        $encryptedText .= (string) dechex($textSize * 2);
      }
    }
    return strtoupper($encryptedText);
  }

  public static function decrypt($text, $key) {
    $decryptedText = '';
    try {
      if (($text != '') && ($key != '')) {
        $textSize = strlen($text);
        $keySize = strlen($key);
        if ($textSize != $keySize) {
          if ($textSize > $keySize) {
            $compareSize = $textSize;
            $key = self::validateText($key, $compareSize);
          }
          else {
            $compareSize = $keySize;
            $text = self::validateText($text, $compareSize);
          }
        }
        else {
          $compareSize = $textSize;
        }
        $originalSize = self::hex2Dec(substr($text, $textSize - 2, $textSize - 1)) / 2;
        if (($originalSize - (int) $originalSize) > 0) {
          throw new Exception('Error trying decrypt the string.');
        }
        $j = 0;
        $aux2 = 0;
        for ($i = 0; $i < ($textSize - 3); $i += 2) {
          $value = self::hex2Dec(substr($text, $i, 2));
          $aux1 = $value;
          $value = $value - $aux2;
          if ($value < 0) {
            $value = $value + 255;
          }
          $aux2 = $aux1;
          $j = $j + 1;
          $value = $value - ord(substr($key, $j - 1, 1)) - ($keySize * 2);
          if ($value < 0) {
            $value = $value + 255;
          }
          $decryptedText = $decryptedText . chr($value);
        }
        if (strlen(substr($decryptedText, 0, $originalSize)) != $originalSize) {
          throw new Exception('Error trying decrypt the string.');
        }
        if ($originalSize > 0) {
          if (strlen($decryptedText) % $originalSize != 0) {
            $j = (int) (strlen($decryptedText) / $originalSize) + 1;
          }
          else {
            $j = (int) (strlen($decryptedText) / $originalSize);
          }
        }
        else {
          $j = 0;
        }
        $auxString2 = substr($decryptedText, 0, $originalSize);
        $aux1 = 0;
        $errorOccured = false;
        for ($i = 0; $i < $j; $i++) {
          if ($i < $j - 1) {
            $auxString1 = substr($decryptedText, $aux1, $originalSize);
            if (strcmp($auxString2, $auxString1) != 0) {
              $errorOccured = true;
            }
          }
          else {
            $auxString1 = substr($decryptedText, $aux1, strlen($decryptedText));
            if (strpos($auxString2, $auxString1) === false) {
              $errorOccured = true;
            }
          }
          $aux1 = $aux1 + $originalSize;
        }
        if ($errorOccured) {
          throw new Exception('Error trying decrypt the string.');
        }
        else {
          $decryptedText = substr($decryptedText, 0, $originalSize);
        }
      }
      else {
        $decryptedText = '';
      }
    }
    catch (Exception $error) {
      $decryptedText = '';
      for ($i = 0; $i < 8; $i++) {
        do {
          $auxString1 = chr((126 * rand(0, 1)) + 33);
        } while ($auxString1 == ',');
        $decryptedText = $decryptedText . $auxString1;
      }
    }
    return $decryptedText;
  }

  public static function authenticate($username, $password) {
    $response = array('status' => '', 'result' => '');
    if ($username != '') {
      global $RBAC;
      if ($result = $RBAC->VerifyLogin($username, $password)) {
        switch ($result) {
          case -1:
            $response['status'] = 'ERROR';
            $response['result'] = 'User "' . $username . '" not exists.';
          break;
          case -2:
            $response['status'] = 'ERROR';
            $response['result'] = 'Password incorrect.';
          break;
          case -3:
            $response['status'] = 'ERROR';
            $response['result'] = 'User inactive, please contact con your System Administrator.';
          break;
          case -4:
            $response['status'] = 'ERROR';
            $response['result'] = 'User account expired, please contact con your System Administrator.';
          break;
          default:
            $response['status'] = 'OK';
            $response['result'] = $result;
          break;
        }
      }
      else {
        $response['status'] = 'ERROR';
        $response['result'] = 'User "' . $username . '" not exists.';
      }
    }
    else {
      $response['status'] = 'ERROR';
      $response['result'] = 'Parameter "username" is required.';
    }
    return $response;
  }

  public static function getInformationForSession($userUID) {
    $result = array('', '', '');
    if ($userUID != '') {
      global $RBAC;
      $RBAC->initRBAC();
      $userData = $RBAC->userObj->load($userUID);
      if (is_array($userData)) {
        $result[0] = $userData['USR_UID'];
        $result[1] = $userData['USR_USERNAME'];
        $result[2] = $userData['USR_FIRSTNAME'] . ' ' . $userData['USR_LASTNAME'];
      }
    }
    return $result;
  }

  public static function checkPermission($userUID, $permission) {
    $result = false;
    if ($userUID != '') {
      global $RBAC;
      $RBAC->initRBAC();
      $RBAC->loadUserRolePermission($RBAC->sSystem, $userUID);
      $result = ($RBAC->userCanAccess($permission) == 1);
    }
    return $result;
  }

  /* Private Functions */

  private static function hex2Dec($numHex) {
    switch (substr($numHex, 0, 1)) {
      case 'A':
        $hex2Dec = 10 * 16;
      break;
      case 'B':
        $hex2Dec = 11 * 16;
      break;
      case 'C':
        $hex2Dec = 12 * 16;
      break;
      case 'D':
        $hex2Dec = 13 * 16;
      break;
      case 'E':
        $hex2Dec = 14 * 16;
      break;
      case 'F':
        $hex2Dec = 15 * 16;
      break;
      default:
        $hex2Dec = ((int) substr($numHex, 0, 1)) * 16;
      break;
    }
    switch (substr($numHex, 1, 1)) {
      case 'A':
        $hex2Dec = $hex2Dec + 10;
      break;
      case 'B':
        $hex2Dec = $hex2Dec + 11;
      break;
      case 'C':
        $hex2Dec = $hex2Dec + 12;
      break;
      case 'D':
        $hex2Dec = $hex2Dec + 13;
      break;
      case 'E':
        $hex2Dec = $hex2Dec + 14;
      break;
      case 'F':
        $hex2Dec = $hex2Dec + 15;
      break;
      default:
        $hex2Dec = $hex2Dec + ((int) substr($numHex, 1, 1));
      break;
    }
    return $hex2Dec;
  }

  private static function validateText($text, $size) {
    $validateText = '';
    while (strlen($validateText) < $size) {
      $validateText .= $text;
    }
    $validateText = substr($validateText, 0, $size);
    return $validateText;
  }

}

?>