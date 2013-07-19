<?php

/* Authenticate the Admin user to get the Session ID */
include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');

$elockObj = new elockClass ();
$operatorAuthToken=$elockObj->elockLogin($elockObj->ElockOperatorUserName, $elockObj->ElockOperatorUserPassword);


/* Get the list of all Signers*/
$signerList= $elockObj->GetAllSignerList($operatorAuthToken);
$counter = count($signerList);

$ROW[] = array(    'USR_USERNAME'=> 'char'
      	          );



for($i=0;$i<$counter;$i++)
			{
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



?>
