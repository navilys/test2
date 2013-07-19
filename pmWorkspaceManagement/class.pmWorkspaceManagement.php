<?php
/**
 * class.pmWorkspaceManagementClass.php
 *  
 */

  class pmWorkspaceManagementClass extends PMPlugin  {

    function __construct (  ) {
      set_include_path(
        PATH_PLUGINS . 'pmWorkspaceManagement' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }



  }