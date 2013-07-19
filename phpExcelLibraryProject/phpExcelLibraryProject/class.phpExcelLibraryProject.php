<?php
/**
 * class.phpExcelLibraryProject.php
 *  
 */

  class phpExcelLibraryProjectClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'phpExcelLibraryProject' . PATH_SEPARATOR .
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