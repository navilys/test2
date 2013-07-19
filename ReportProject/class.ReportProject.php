<?php
/**
 * class.ReportProject.php
 *  
 */

  class ReportProjectClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'ReportProject' . PATH_SEPARATOR .
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