<?php
/**
 * class.pmReplicatorClass.php
 *  
 */

  class pmReplicatorClass extends PMPlugin  {

    function __construct (  ) {
      set_include_path(
        PATH_PLUGINS . 'pmReplicator' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }



  }