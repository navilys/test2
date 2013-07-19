<?php
/**
 * @section Filename 
 * pentahoAssignDepartmentRole.php
 * @subsection Description
 * This is the list of all departments that can be assigned to a determined role.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @date 24/04/2010
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.scripts
 */
  
  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhUserRole.php';
  require_once 'classes/model/PhRole.php';
  require_once 'classes/model/PhReport.php';
  require_once PATH_CORE.'classes/model/Department.php';
  /**
   * the role uid that is passed via the Get method.
   */
  $rolUid = $_GET['ROL_UID'];

  G::LoadClass('tree');

  /**
   * The Role object required in order to get some information from a role.
   */
  $role        = new PhRole();

  /**
   * The role code obtained from the role uid.
   */
  $roleCode    = $role->getRolCode($rolUid);

  /**
   * Initializing the tree object.
   */
  $tree = new Tree();

  /**
   * The name attribute of the tree.
   */
  $tree->name  = 'Users';

  /**
   * The type of the nodes within the tree.
   */
  $tree->nodeType = "base";

  /**
   * The width attribute of the tree.
   */
  $tree->width = "350px";

  /**
   * The tree content that is rendered.
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
  print ($tree->render());
  // printing the assembled tree 
  print (getDepartmentsTree('', $rolUid));
  
  /**
   * This function assembles recursively the tree components and nodes with the department/role information 
   * @param String The parent node or department
   * @param String The role uid in order to filter the departments that are showed
   * @return String of the html code rendered
   */
  function getDepartmentsTree($parentDepartment, $rolUid){
    // preparing the tree object
    $treeDep = new Tree();
    $oDepartment = new Department();
    $departments = $oDepartment->getDepartments($parentDepartment);
    $userRole    = new PhUserRole();
    $treeDep->showSign = false;
    // recusively iterating over the reports structure 
    foreach ($departments as $aRow) {
        $ID_ASSIGN = G::LoadTranslation('ID_ASSIGN');
        $user = '['.$aRow['DEP_TITLE'].']';
        $depUid = $aRow['DEP_UID'];
        $html = "
              <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
                <tr>
                  <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
                  <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href=\"javascript:assignDepartmentToRole('{$rolUid}','{$depUid}');\">{$ID_ASSIGN}</a>]</td>
                </tr>
              </table>";

      // if the department is not assigned to the role then display the link
      if (!$userRole->existsInRole($depUid,$rolUid)){
        $ch = &$treeDep->addChild('', $html, array('nodeType' => 'child'));
        $ch->point = '<img height="2" width="2px" src="'.PATH_CORE.'images/ftv2vertline.gif"/>';
      }
      // if the department has children call this function recursively
      if ($aRow['HAS_CHILDREN']!=0){
        $innerDepartments = getDepartmentsTree($aRow['DEP_UID'],$rolUid);
        $ch = &$treeDep->addChild('', $innerDepartments, array('nodeType' => 'child'));
      }

    }
      return ($treeDep->render());

  }


