<?php
/**
 * class.limousinProject.php
 *  
 */

  class limousinProjectClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'limousinProject' . PATH_SEPARATOR .
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

  }
 
?>