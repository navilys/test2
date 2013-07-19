<?php
/*
 * Class Object to XML.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

class ObjectToXML {

  private $dom;

  public function __construct($obj, $rootName = '') {
    $this->dom = new DOMDocument('1.0', 'UTF-8');
    $root = $this->dom->createElement($rootName != '' ? $rootName : get_class($obj));
    foreach ($obj as $key => $value) {
      $node = $this->createNode($key, $value);
      if (!is_null($node)) {
        $root->appendChild($node);
      }
    }
    $this->dom->appendChild($root);
  }

  private function createNode($key, $value) {
  	$node = null;
  	if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
  	  if (is_null($value)) {
  	    $node = $this->dom->createElement($key);
  	  }
	    else {
	      $node = $this->dom->createElement($key, (string)$value);
	    }
  	}
  	else {
	    $node = $this->dom->createElement($key);
	    if (!is_null($value)) {
		    foreach ($value as $key => $value) {
		      if (is_numeric($key)) {
		        $key = get_class($value);
		      }
		      $sub = $this->createNode($key, $value);
	        if (!is_null($sub)) {
	          $node->appendChild($sub);
	        }
		    }
	    }
  	}
	  return $node;
  }

  public function __toString() {
    return $this->dom->saveXML();
  }

}
?>