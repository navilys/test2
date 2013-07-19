<?php
class Library
{
  public static function getUrlServerName()
  {
    $s = (empty($_SERVER["HTTPS"]))? null : (($_SERVER["HTTPS"] == "on")? "s" : null);
    $p = strtolower($_SERVER["SERVER_PROTOCOL"]);
  
    $protocol = substr($p, 0, strpos($p, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80")? null : ":" . $_SERVER["SERVER_PORT"];
  
    return ($protocol . "://" . $_SERVER["SERVER_NAME"] . $port);
  }
  
  public static function getUrl()
  {
    return (self::getUrlServerName() . $_SERVER["REQUEST_URI"]);
  }
}
?>