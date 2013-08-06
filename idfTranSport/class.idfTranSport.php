<?php
/**
 * class.idfTranSport.php
 *  
 */

  class idfTranSportClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'idfTranSport' . PATH_SEPARATOR .
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