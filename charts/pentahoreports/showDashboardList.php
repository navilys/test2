<?php
/**
 * @section Filename
 * showDashboardList.php
 * @subsection Description
 * This is the View of all reports that a user can access
 *
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @date 19/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhRole.php';

  /**
   * The role uid passed as parameter.
   */
  $rolUid = $_GET['ROL_UID'];

  /**
   * The role object uid passed as parameter.
   */
  $rolObjUid = $_GET['ROL_OBJ_UID'];
  /**
   * The role report object.
   */
	$roleReports = new PhRoleReport();

  /**
   * The role object.
   */
  $role = new PhRole();

  /**
   * The dataset with the dashboards assigned to a role.
   */
	$oDataset = $roleReports->getRoleDashboards($rolUid);

  /**
   * The role code of a role.
   */
	$roleCode = $role->getRolCode($rolUid);

	G::LoadClass('tree');

  /**
   * The tree object.
   */
	$tree = new Tree(); 

  /**
   * The tree name attribute.
   */
	$tree->name = 'Users';

  /**
   * The tree node type attribute.
   */
	$tree->nodeType = "base";

  /**
   * The tree width attribute.
   */
	$tree->width = "350px";

  /**
   * The tree value attribute, this is the container of the tree code.
   */
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">Reports Assigned to the Role: '.$roleCode.'</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="userGroupLink"><a href="#" onclick="showReports(\''.$_GET['ROL_UID'].'\');return false;">'.G::LoadTranslation('ID_ASSIGN').'</a></div>';

	$tree->showSign = false;

	$oDataset->next();
	while ($aRow = $oDataset->getRow()) {
    $idRemove = G::LoadTranslation('ID_REMOVE');

    $code = $aRow['REP_NAME'];

    $uid = $aRow['REP_TITLE'];

	  if($rolUid != "") { #because the admin remove permitions it doesn't posible
			$refer = "<a href=\"javascript:assignDashboard('{$rolUid}','{$uid}','{$rolObjUid}');\">Assign</a>";
		} else {
			$refer = "<font color='#BFBFBF'>Assigned</font>";
		}

    $html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$code}</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[$refer]</td>
        </tr>
      </table>";

    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';

    $oDataset->next();
	}

  /**
   * Rendering the tree code.
   */
	print ($tree->render());
