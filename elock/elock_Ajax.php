<?php

$REQUEST = (isset($_GET['request']))?$_GET['request']:$_POST['request'];
//var_dump($REQUEST);
//echo "Hello how are you?";

switch ($REQUEST) {

 case 'editUser':




        $USR_USERNAME = $_GET['USR_USERNAME'];
        //var_dump($USR_USERNAME);
        $aFields['USER_NAME'] = $USR_USERNAME;
        //var_dump($aFields);

    	$G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'elock/elockUser_Edit', '', $aFields);
        G::RenderPage('publish', 'raw');
        break;

    case 'updateUser':


        include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
        include_once(PATH_PLUGINS.'elock'.PATH_SEP.'classes/class.pmFunctions.php');
        global $fields;

        
        $appPassword = ($fields['ElockAPP_PASSWORD']);

    	$aData['PASSWORD'] = $_POST['password']; //get changed password from popup Window, entered by User
    	//$aData['ROL_PARENT'] = $_POST['parent'];
    	$aData['USER_NAME'] = $_POST['username'];
        //
        $userId = $aData['USER_NAME'];
        $pwd = $aData['PASSWORD'];
        $displayName = $aData['USER_NAME'];
        $userType= '2';
        $cn = "Elock";   //CN is Common Name
        $ou="Processmaker"; //OU is organizational unit
        $userDN ='CN='.$cn.';OU='.$ou.';'; //userDN is user distinguished name
        $email = "ankit.mishar@bistasolutions.com";
        $profileChange = ChangeUserProfile($userId,$displayName,$userType,$userDN,$pwd,$email,$appPassword);
        var_dump($profileChange);
        //die;
        //G::header('location: ../login/login');
        //G::header('location:http://processmaker.elock/sysos/en/green/setup/main');
       
        break;


    case 'show':



    /* Authenticate the Admin user to get the Session ID */
include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
include_once(PATH_PLUGINS.'elock'.PATH_SEP.'classes/class.pmFunctions.php');
global $fields;


$userId =  ($fields['ElockOperatorUserName']);
$strPassword =  ($fields['ElockOperatorUserPassword']);
$operatorAuthToken = elockLogin($userId,$strPassword);


/* Get the list of all Signers*/
$signerList= GetAllSignerList($operatorAuthToken);
$counter = count($signerList);

$ROW[] = array(    'USR_USERNAME'=> 'char'
      	          );



for($i=0;$i<$counter;$i++)
			{
                                //var_dump($signerList[$i]);
                                $pos = strrpos($signerList[$i], "=");
                                //$userName[$i] = stristr($signerList[$i], '=');
                                //var_dump($userName[$i]);
                                $displayName[$i] = substr($signerList[$i],'0', $pos);
				$ROW[] = array('USR_USERNAME' => $displayName[$i]);

                        }



  global $_DBArray;
  $_DBArray['list']  = $ROW;
  $_SESSION['_DBArray'] = $_DBArray;
  G::LoadClass('ArrayPeer');
  $oCriteria = new Criteria('dbarray');
  $oCriteria->setDBArrayTable('list');

  /*$G_MAIN_MENU = 'processmaker';
  $G_ID_MENU_SELECTED     = 'ID_ELOCK';
  $G_SUB_MENU             = 'elock/elockOnTransitList';
  $G_ID_SUB_MENU_SELECTED = 'ID_ELOCK_ONTRANSIT';*/



  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'elock/elockUser_List', $oCriteria );

  G::RenderPage( "publishBlank","blank" );

  break;


}






?>
