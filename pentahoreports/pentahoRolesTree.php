<?php
/**
 * @section Filename
 * pentahoRolesTree.php
 * @subsection Description
 * This is the list of all users, groups or departments assigned to a role
 *
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @Date 17/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhUserRole.php';
  require_once 'classes/model/PhRole.php';
  require_once 'classes/model/PhReport.php';

  /**
   * The role uid parameter passed via the GET method
   */
  $rolUid = $_GET['ROL_UID'];

  /**
   * The role report object
   */
  $roleUsers = new PhUserRole();

  /**
   * The role object
   */
  $role = new PhRole();

  /**
   * The users list assigned to a role
   */
  $oDataset = $roleUsers->getRoleUsers($rolUid);

  /**
   * The groups list assigned to a role
   */
  $gDataset = $roleUsers->getRoleGroups($rolUid);

  /**
   * The departments list assigned to a role
   */
  $dDataset = $roleUsers->getRoleDepartments($rolUid);

  /**
   * The role code based on a role uid.
   */
  $roleCode = $role->getRolCode($rolUid);
  
  G::LoadClass('tree');

  /**
   * initializing the tree object
   */
  $tree = new Tree();

  /**
   * The tree name attribute
   */
  $tree->name = 'Users';

  /**
   * The tree node type attribute
   */
  $tree->nodeType = "base";

  /**
   * The tree width attribute
   */
  $tree->width = "400px";

  /**
   * The tree content width attribute
   */
  $tree->contentWidth = "450px";

  /**
   * The tree value attribute
   */
  $tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">' . G::LoadTranslation('ID_USER_WITH_ROLE') . ': '.$roleCode.'
          - <a href="javascript:backRoles();">Back</a>
          </td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="userGroupLink">
    <a href="#" onclick="showUsers(\''.$_GET['ROL_UID'].'\');return false;">'.G::LoadTranslation('ID_ASSIGN_ROLE').'</a> -
    <a href="#" onclick="showGroups(\''.$_GET['ROL_UID'].'\');return false;">Assign Groups</a> -
    <a href="#" onclick="showDepartments(\''.$_GET['ROL_UID'].'\');return false;">Assign Departments</a>
  </div>';

  /**
   * The tree show sign attribute
   */
  $tree->showSign = false;
  
  // assembling the group list
  $gDataset->next();

  while ($aRow = $gDataset->getRow()) {
    // assigning some group variables 
    $idDelete  = G::LoadTranslation('ID_REMOVE');
    $idAssigned = 'Dashboard';
    $user       = '['.$aRow['CON_VALUE'].'] [Group]';
    $relUid     = $aRow['ROL_OBJ_UID'];
    $grpUid     = $aRow['OBJ_UID'];
    $dashboard  = $aRow['OBJ_DASHBOARD'];
    $refer      = "<a href=\"javascript:deleteUserRole('{$rolUid}','{$relUid}');\">{$idDelete}</a>";
    $assignLink = "<a href=\"javascript:showDashboardList('{$rolUid}','{$grpUid}','{$relUid}');\">Dashboard</a>";
    $html = "
			<table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			<tr>
				<td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
				<td width='150px' class='treeNode' style='border:0px;background-color:transparent;'>[$assignLink] [$refer]</td>
			</tr>
			</table>
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$idAssigned} {$dashboard}</td>
          <td width='150px' class='treeNode' style='border:0px;background-color:transparent;'></td>
        </tr>
			</table>
        ";
    // adding the node to the tree element
    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';
    $gDataset->next();
  }

  // assembling the user list
  $oDataset->next();
  while ($aRow = $oDataset->getRow()) {
    // assigning some user variables 
    $idDelete = G::LoadTranslation('ID_REMOVE');
    $idAssigned = 'Dashboard';
    $un = ($aRow['USR_USERNAME'] != '')?$aRow['USR_USERNAME']:'none';
    $user = '['.$un.'] '.$aRow['USR_FIRSTNAME'].' '.$aRow['USR_LASTNAME'];
    $usrUid = $aRow['USR_UID'];
    $relUid = $aRow['ROL_OBJ_UID'];
    $dashboard  = $aRow['OBJ_DASHBOARD'];
    $assignLink = "<a href=\"javascript:showDashboardList('{$rolUid}','{$usrUid}','{$relUid}');\">Dashboard</a>";
    if($usrUid != "00000000000000000000000000000001") { //because the removal of the admin rol is not posible
	$refer = "<a href=\"javascript:deleteUserRole('{$rolUid}','{$relUid}');\">{$idDelete}</a>";
    } else {
	$refer = "<font color='#CFCFCF'>{$idDelete}</font>";
    }
    $html = "
			<table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			<tr>
				<td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
				<td width='150px' class='treeNode' style='border:0px;background-color:transparent;'>[$assignLink] [$refer]</td>
			</tr>
			</table>
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$idAssigned} {$dashboard}</td>
          <td width='150px' class='treeNode' style='border:0px;background-color:transparent;'></td>
        </tr>
			</table>
      ";
    // adding the node to the tree element
    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';

    $oDataset->next();
  }
  // assembling the department list
  $dDataset->next();
  while ($aRow = $dDataset->getRow()) {
    // assigning some department variables 
    $idDelete   = G::LoadTranslation('ID_REMOVE');
    $idAssigned = 'Dashboard';
    // assembling the node
    $user = '['.$aRow['CON_VALUE'].'] [Department]';
    $depUid = $aRow['OBJ_UID'];
    $relUid = $aRow['ROL_OBJ_UID'];
    $dashboard  = $aRow['OBJ_DASHBOARD'];
    $refer = "<a href=\"javascript:deleteUserRole('{$rolUid}','{$relUid}');\">{$idDelete}</a>";
    $assignLink = "<a href=\"javascript:showDashboardList('{$rolUid}','{$depUid}','{$relUid}');\">Dashboard</a>";

    $html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
          <td width='150px' class='treeNode' style='border:0px;background-color:transparent;'>[$assignLink] [$refer]</td>
        </tr>
      </table>
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>{$idAssigned} {$dashboard}</td>
          <td width='150px' class='treeNode' style='border:0px;background-color:transparent;'></td>
        </tr>
      </table>
      ";
    // adding the node to the tree element
    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';

    $dDataset->next();
  }
  /**
   * rendering the tree object
   */
  print ($tree->render());
