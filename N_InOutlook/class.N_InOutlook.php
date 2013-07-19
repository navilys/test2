<?php
/*
 * Class N_InOutlookClass by Nightlies.
 * @author Julio Cesar Laura Avendaño <juliocesar@nightlies.com> <contact@julio-laura.com>
 * @version 1.0 (2011-04-01)
 * @link http://plugins.nightlies.com
 */

class N_InOutlookClass extends PMPlugin  {

  public function __construct() {
    set_include_path(PATH_PLUGINS . 'N_InOutlook' . PATH_SEPARATOR . get_include_path());
  }

  public function getFieldsForPageSetup() {
  }

}