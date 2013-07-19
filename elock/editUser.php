<?php


/* Authenticate the Admin user to get the Session ID */
/*include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
include_once(PATH_PLUGINS.'elock'.PATH_SEP.'classes/class.pmFunctions.php');
global $fields;


$userId =  ($fields['ElockOperatorUserName']);
$strPassword =  ($fields['ElockOperatorUserPassword']);
$operatorAuthToken = elockLogin($userId,$strPassword);*/


/* Get the list of all Signers*/
/*$signerList= GetAllSignerList($operatorAuthToken);
$counter = count($signerList);

$ROW[] = array(    'USR_USERNAME'=> 'char'
      	          );



for($i=0;$i<$counter;$i++)
			{
				$ROW[] = array('USR_USERNAME' => $signerList[$i]);

                        }



  global $_DBArray;
  $_DBArray['list']  = $ROW;
  $_SESSION['_DBArray'] = $_DBArray;
  G::LoadClass('ArrayPeer');
  $oCriteria = new Criteria('dbarray');
  $oCriteria->setDBArrayTable('list');

  $G_MAIN_MENU = 'processmaker';
  $G_ID_MENU_SELECTED     = 'ID_ELOCK';
  $G_SUB_MENU             = 'elock/elockOnTransitList';
  $G_ID_SUB_MENU_SELECTED = 'ID_ELOCK_ONTRANSIT';



  $G_PUBLISH = new Publisher;
  //$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'elock/userInfo','','','someNewServerPAges' );
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/userInfo', '', '', 'users_Save?USR_UID=' . $_SESSION['USR_USERNAME']);

  G::RenderPage( "publish" );*/





$REQUEST = (isset($_GET['request']))?$_GET['request']:$_POST['request'];
var_dump($REQUEST);
die;
switch ($REQUEST) {

    case 'newRole':
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'roles/roles_New', '', '');
        G::RenderPage('publish', 'raw');
        break;

    case 'saveNewRole':

    	$newid = md5($_POST['code'].date("d-M-Y_H:i:s"));
    	g::pr($_POST);
    	$aData['ROL_UID'] = $newid;
    	//$aData['ROL_PARENT'] = $_POST['parent'];
    	$aData['ROL_SYSTEM'] = '00000000000000000000000000000002';
    	$aData['ROL_CODE'] = $_POST['code'];
    	$aData['ROL_NAME'] = $_POST['name'];
    	$aData['ROL_CREATE_DATE'] = date("Y-M-d H:i:s");
    	$aData['ROL_UPDATE_DATE'] = date("Y-M-d H:i:s");
    	$aData['ROL_STATUS'] = $_POST['status'];
    	$oCriteria = $RBAC->createRole($aData);
        break;

    case 'editRole':

    	$ROL_UID = $_GET['ROL_UID'];
    	$aFields = $RBAC->loadById($ROL_UID);

    	$G_PUBLISH = new Publisher();
        //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/userInfo', '', $aFields);
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'elock/userInfo','','','someNewServerPAges' );
        G::RenderPage('publish', 'raw');
        break;

}




?>
