<?php
/**
 * @section Filename
 * pentahoRolesAssignReports.php
 * @subsection Description
 * This is the reports in order to assign a report to any role
 * @author gustavo cruz <gustavo@colosa.com>
 * @Date 20/05/2010
 * @subsection copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */
  
  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhRole.php';
  require_once 'classes/model/PhReport.php';

 /**
  * The Role Uid parameter obtained via the GET method
  */
  $ROL_UID = $_GET['ROL_UID'];
 /**
  * The role object
  */
  $role = new PhRole();

 /**
  * The report object
  */
  $report = new PhReport();

 /**
  * Setting up the RBAC role 
  */
	global $RBAC;

 /**
  * getting the list of unassigned reports
  */
	$oDataset = $report->getUnassignedReports($ROL_UID);

 /**
  * getting the role code based in the rolw uid
  */
	$roleCode = $role->getRolCode($ROL_UID);
	G::LoadClass('tree');

 /**
  * Initializing the tree object
  */
	$tree = new Tree();

 /**  
  * The tree name attribute
  */
	$tree->name = 'Users';

 /**  
  * The tree node type  attribute
  */
	$tree->nodeType = "base";

 /**  
  * The tree width attribute
  */
	$tree->width = "350px";

 /**  
  * The tree value attribute
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
	<div class="userGroupLink"><a href="#" onclick="backPermissions(\''.$_GET['ROL_UID'].'\');return false;">' . G::LoadTranslation('ID_BACK_PERMISSIONS_LIST').'</a></div>';
	$tree->showSign = false;
	// iterating over the unassigned reports list
	$oDataset->next();
	while ($aRow = $oDataset->getRow()) {
            $ID_ASSIGN = G::LoadTranslation('ID_ASSIGN');
            $CODE = $aRow['REP_NAME'];
	    $UID = $aRow['REP_UID'];   
	    $html = "
	      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
	        <tr>
	          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$CODE}</td>	
	          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href=\"javascript:assignReportToRole('{$ROL_UID}','{$UID}');\">{$ID_ASSIGN}</a>]</td>
	        </tr>
	      </table>";
            // adding the link data to the tree
	    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
	    $ch->point = '<img src="/images/users.png" />';
    
	    $oDataset->next();
	}
  /**
   * render the generated tree
   */
	print ($tree->render());
