<?php
/**
 * class.pmBusinessRules.php
 *  
 */

  class pmBusinessRulesClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'pmBusinessRules' . PATH_SEPARATOR .
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