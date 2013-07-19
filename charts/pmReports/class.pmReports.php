<?php
/**
 * class.pmReports.php
 *
 */

class pmReportsClass extends PMPlugin  {

  function __construct () {
    set_include_path(PATH_PLUGINS . 'pmReports' . PATH_SEPARATOR . get_include_path());
  }

  function setup() {
  }

  function getFieldsForPageSetup() {
  }

}