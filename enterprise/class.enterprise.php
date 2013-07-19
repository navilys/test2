<?php
require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "class.enterpriseUtils.php");





if (!defined("PM_VERSION")) {
    if (file_exists(PATH_METHODS . "login/version-pmos.php")) {
        include (PATH_METHODS . "login/version-pmos.php");
    } else {
        define("PM_VERSION", "2.0.0");
    }
}





class enterpriseClass extends PMPlugin
{
    public function __construct()
    {
        set_include_path(PATH_PLUGINS . 'enterprise' . PATH_SEPARATOR . get_include_path());
    }

    public function getFieldsForPageSetup()
    {
        return array();
    }

    //update fields
    public function updateFieldsForPageSetup($oData)
    {
        return array();
    }

    public function setup()
    {
    }

    public function enterpriseSystemUpdate($data) //$data = $oData
    {
        require_once ("classes/model/Users.php");

        $user = $data;

        $criteria = new Criteria("workflow");

        //SELECT
        $criteria->addSelectColumn(UsersPeer::USR_UID);
        //FROM
        //WHERE
        $criteria->add(UsersPeer::USR_USERNAME, $user->lName); //$user->lPassword
        $criteria->add(UsersPeer::USR_ROLE, "PROCESSMAKER_ADMIN");

        //query
        $rsSQLUSR = UsersPeer::doSelectRS($criteria);
        $rsSQLUSR->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $sw = 0;

        if (UsersPeer::doCount($criteria) > 0) {
        //if ($rsSQLUSR->getRecordCount() > 0) {
            $sw = 1;
        }

        /*
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $sql = "SELECT USR.USR_UID
                FROM   USERS AS USR
                WHERE  USR.USR_USERNAME = '" . $user->lName . "' AND USR.USR_ROLE = 'PROCESSMAKER_ADMIN'";
        $rsSQLUSR = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $sw = 0;

        if ($rsSQLUSR->getRecordCount() > 0) {
            $sw = 1;
        }
        */

        if ($sw == 1) {
            //Upgrade available
            $swUpgrade = 0;

            $addonList = AddonsStore::addonList();
            $addon = $addonList["addons"];

            if (count($addon) > 0) {
                $status = array("ready", "upgrade", "available");
                $pmVersion = EnterpriseUtils::pmVersion(PM_VERSION);

                foreach($addon as $index => $value) {
                    if ($addon[$index]["id"] == "processmaker") {
                        if (version_compare($pmVersion . "", (EnterpriseUtils::pmVersion($addon[$index]["version"])) . "", "<")) {
                          $swUpgrade = 1;
                          break;
                        }
                    } else {
                        if (in_array($addon[$index]["status"], $status)) {
                          $swUpgrade = 1;
                          break;
                        }
                    }
                }
            }

            if ($swUpgrade == 1) {
                $_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"] = 1;
            }
        }
    }
}

if (!class_exists("pmLicenseManager")) {
    require_once (PATH_PLUGINS . 'enterprise/class.pmLicenseManager.php');
}

