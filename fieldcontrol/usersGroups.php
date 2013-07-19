<?php


if (($RBAC_Response = $RBAC->userCanAccess("PM_LOGIN")) != 1) {
    return $RBAC_Response;
}
global $RBAC;

$access = $RBAC->userCanAccess('PM_USERS');
if ($access != 1) {
    switch ($access) {
        case -1:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
            break;
        case -2:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
            G::header('location: ../login/login');
            die;
            break;
        default:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
            break;
    }
}
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'users';
$G_ID_MENU_SELECTED = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'USERS';

$G_PUBLISH = new Publisher;

G::LoadClass('configuration');
$c = new Configurations();
$configEnv = $c->getConfiguration('ENVIRONMENT_SETTINGS', '');
$Config['fullNameFormat'] = isset($configEnv['format']) ? $configEnv['format'] : '@firstName @lastName (@userName)';

require_once 'classes/model/Users.php';
$oCriteria = new Criteria();
$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
$oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
$oCriteria->add(UsersPeer::USR_UID, $_GET['uUID']);
$oDataset = UsersPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aRow = $oDataset->getRow();

switch ($_REQUEST['type']) {
    case 'summary':
        $ctab = 0;
        break;
    case 'group':
        $ctab = 2;
        break;
    case 'auth':
        $ctab = 1;
        break;
}

$users = Array();
$users['USR_UID'] = $_GET['uUID'];
$users['USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
$users['USR_LASTNAME'] = $aRow['USR_LASTNAME'];
$users['USR_USERNAME'] = $aRow['USR_USERNAME'];
$users['fullNameFormat'] = $Config['fullNameFormat'];
$users['CURRENT_TAB'] = $ctab;

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript('fieldcontrol/usersGroups', false);    //adding a javascript file .js
// $oHeadPublisher->addContent('users/usersGroups'); //adding a html file  .html.
$oHeadPublisher->assign('USERS', $users);

$oHeadPublisher->assign('hasAuthPerm', ($RBAC->userCanAccess('PM_SETUP_ADVANCE') == 1));

G::RenderPage('publish', 'extJs');
 