<?php
/**
 * users_List.php
*/
if (($RBAC_Response=$RBAC->userCanAccess("PM_LOGIN"))!=1) return $RBAC_Response;
global $RBAC;

$access = $RBAC->userCanAccess('PM_USERS');
if( $access != 1 ){
  switch ($access)
  {
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

$G_MAIN_MENU            = 'processmaker';
$G_SUB_MENU             = 'users';
$G_ID_MENU_SELECTED     = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'USERS';

$G_PUBLISH = new Publisher;

G::LoadClass('configuration');
$c = new Configurations();
$configPage = $c->getConfiguration('usersList', 'pageSize','',$_SESSION['USER_LOGGED']);
$Config['pageSize'] = isset($configPage['pageSize']) ? $configPage['pageSize'] : 20;

### fields list users
G::loadClass('pmFunctions');
$array = Array ();
$query = "SELECT CFG_LIST_USR_FIELD_NAME AS FIELD_NAME, CFG_LIST_USR_DESCRIPTION AS DESCRIPTION, CFG_LIST_USR_HIDDEN AS HIDDEN_FIELD, CFG_LIST_USR_INCLUDE 
		  FROM PMT_CONFIG_LIST_USERS 
		  ORDER BY CFG_LIST_USR_POSITION ";
$dataQuery= executeQuery ( $query );
if(sizeof($dataQuery)){
	foreach ( $dataQuery as $row ) 
	{
		if ($row ['CFG_LIST_USR_INCLUDE'] == '1') 
		{
			$array[] = $row;
		}
	}
}	

$oHeadPublisher =& headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript('fieldcontrol/usersList', false);    //adding a javascript file .js
$oHeadPublisher->addContent('fieldcontrol/usersList'); //adding a html file  .html.
$oHeadPublisher->assign('CONFIG', $Config);
$oHeadPublisher->assign('FORMATS',$c->getFormats());
$oHeadPublisher->assign('tableDef', $array);

G::RenderPage('publish', 'extJs');