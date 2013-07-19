<?php
/**
 * class.obladyConvergence.php
 *  
 */

  class obladyConvergenceClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'obladyConvergence' . PATH_SEPARATOR .
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