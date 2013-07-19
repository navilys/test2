<?php
/*
 * Class XML Service.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

require_once 'class.objectToXML.php';
require_once 'class.response.php';

class Service {

  protected $response = null;

  public function __construct() {
    self::checkRequiredModules();
  }

  public function setResponse($response) {
    $this->response = $response;
  }

  public function healthCheck() {
    $this->response = new Response();
    $this->response->status = 'OK';
    $this->write();
  }

  public function write() {
    header('Content-Type: text/xml;');
    $objectToXML = new ObjectToXML($this->response, 'response');
    die($objectToXML->__toString());
  }

  public static function checkRequiredModules() {
    if (!class_exists('DOMDocument')) {
      header('Content-Type: text/xml;');
      die('<?xml version="1.0" encoding="UTF-8"?><response><status>ERROR</status><description>The PHP module "domxml" not available in the server.</description></response>');
    }
  }

}

?>