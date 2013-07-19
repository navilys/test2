<?php
/*
 * Class JSON Service.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

require_once 'class.json.php';
require_once 'class.response.php';

class Service {

  protected $response = null;

  public function __construct() {
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
    header('Content-Type: application/json;');
    $json = new Services_JSON();
    die($json->encode($this->response));
  }

}

?>