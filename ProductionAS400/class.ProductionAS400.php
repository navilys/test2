<?php
/**
 * class.ProductionAS400.php
 *  
 */

  class ProductionAS400Class extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'ProductionAS400' . PATH_SEPARATOR .
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