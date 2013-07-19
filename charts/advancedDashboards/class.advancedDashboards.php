<?php

class advancedDashboardsClass extends PMPlugin {

  public function __construct() {
    set_include_path(PATH_PLUGINS . 'advancedDashboards' . PATH_SEPARATOR . get_include_path());
  }

  public function setup() {
  }

  public function getFieldsForPageSetup() {
    return array();
  }

  public function updateFieldsForPageSetup() {
  }

}