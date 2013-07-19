<?php
/**
 * @section Filename
 * pentahoAssignRole.php
 * @subsection Description 
 * This is the list of all groups from a determinated user.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @date 19/05/2010
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr> 
 * @package plugins.pentahoreports.scripts
 */

  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhUserRole.php';
  require_once 'classes/model/PhRole.php';
  require_once 'classes/model/PhReport.php';

 /**
  * The role uid passed via the GET method
  */
  $ROL_UID = $_GET['ROL_UID'];

 /**
  * The Rbac global object
  */
  global $RBAC;

 /**
  * This dataset stores a list of all the users in a role
  */
  $oDataset = $RBAC->getAllUsers( $ROL_UID );

 /**
  * Setting up the role object 
  */
  $role     = new PhRole();

 /**
  * getting the role code
  */
  $roleCode = $role->getRolCode ( $ROL_UID );

 /**
  * Setting up the user role object 
  */
  $userRole = new PhUserRole();

	G::LoadClass('tree');
 /**
  * Creating the tree object
  */  
	$tree = new Tree();
 /**
  * The tree object name attribute
  */
	$tree->name = 'Users';
 /**
  * The tree object node type attribute
  */
	$tree->nodeType = "base";
 /**
  * The tree object width
  */
	$tree->width = "350px";
 /**
  * The tree object value attribute, that stores the content of a tree.
  */
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">' . G::LoadTranslation('ID_ASSIGN_THE_ROLE') . ': '.$roleCode.'</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="userGroupLink"><a href="#" onclick="backUsers(\''.$_GET['ROL_UID'].'\');return false;">' . G::LoadTranslation('ID_BACK_TO_USERS_LIST') . '</a></div>';

 /**
  * The tree object showSign attribute seto to false.
  */
	$tree->showSign = false;

	$oDataset->next();
        // iterating over the users result array
	while ($aRow = $oDataset->getRow()) {
	    $ID_ASSIGN = G::LoadTranslation('ID_ASSIGN');
            // assembling the user name
	    $user = '['.$aRow['USR_USERNAME'].'] '.$aRow['USR_FIRSTNAME'].' '.$aRow['USR_LASTNAME'];
	    $USR_UID = $aRow['USR_UID'];
            // assembling the link list for users
	    $html = "
	      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
	        <tr>
	          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
	          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href=\"javascript:assignUserToRole('{$ROL_UID}','{$USR_UID}');\">{$ID_ASSIGN}</a>]</td>
	        </tr>
	      </table>";
            // if the user has been assigned skip the render of the link
            if (!$userRole->existsInRole($USR_UID,$ROL_UID)){
              $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
              $ch->point = '<img src="/images/users.png" />';
            }

	    $oDataset->next();
	}

        // render the users tree 
	print ($tree->render());
