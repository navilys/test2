<?php
/**
 * class.fieldcontrol.php
 *  
 */

  class fieldcontrolClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'fieldcontrol' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }

    function getFieldsForPageSetup()
    {
    }

    function updateFieldsForPageSetup()
    {
    }
    function XMLParsing()
    {
    	$oForm = new Form ( '9364394824f722ce0dc7d15044477079/72079202250ae4bb4a12c30070933733', PATH_DYNAFORM );
      
    }

  }
?>