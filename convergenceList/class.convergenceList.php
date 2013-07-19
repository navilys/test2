<?php
/**
 * class.convergenceList.php
 *  
 */

  class convergenceListClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'convergenceList' . PATH_SEPARATOR .
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