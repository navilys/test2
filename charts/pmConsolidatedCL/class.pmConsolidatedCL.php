<?php
/**
 * class.pmConsolidatedCL.php
 *
 */

class pmConsolidatedCLClass extends PMPlugin {
  function __construct () {
    set_include_path(PATH_PLUGINS . "pmConsolidatedCL" . PATH_SEPARATOR . get_include_path());
  }

  function setup() {
  }

  function getFieldsForPageSetup() {
  }
}
?>