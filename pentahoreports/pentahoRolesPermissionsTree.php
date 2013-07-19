<?php
/**
 * @section Filename
 * pentahoRolesPermissionsTree.php
 * @subsection Description
 * This is the View of all reports that a user can access.
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @date   19/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  require_once 'classes/model/PhRoleReport.php';
  require_once 'classes/model/PhRole.php';
  require_once 'class.pentahoreports.php';

  /**
   * The role uid parameter passed via the GET method
   */
  $rolUid = $_POST['ROL_UID'];

  /**
   * The role report object
   */
  $roleReports = new PhRoleReport();

  /**
   * The role object
   */
  $role = new PhRole();

  /**
   * The report object
   */
  $report = new PhReport();

  /**
   * The main pentahoreports object
   */
  $pentahoObject = new pentahoreportsClass();

  /**
   * reading the plugin configuration
   */
  $pentahoObject->readConfig();

  /**
   * getting the reports assignments
   */
  $oReportsDataset = $roleReports->getAllRoleReports();

  /**
   * getting all the reports
   */
  $oReportsDataset = $report->getAllReports();

  /**
   * Setting the role code based in the role uid
   */
  $roleCode = $role->getRolCode($rolUid);
  G::LoadClass('tree');

  /**
   * Creating the tree object
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
  $tree->width = "350px";

  /**
   * The tree value attribute
   */
  $tree->value = '
  <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  <div class="boxContentBlue">
    <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
      <tr>
        <td class="userGroupTitle">Reports Assigned to the Role: '.$roleCode.'
        - <a href="javascript:backRoles();">Back</a>
        </td>
      </tr>
    </table>
  </div>
  <div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  ';

  /**
   * The tree show sign attribute
   */
  $tree->showSign = false;

  /**
   * The current root is the solution with the same name as the current workspace
   */
  $root = SYS_SYS;

  /** 
   * rendering the tree content
   */
  print ($tree->render());
  print (showReportsDirTree( $root, $rolUid));

/**
 * This function recursively assembles the Reports Directories tree
 * @todo encapsulate this function inside one of the core classes maybe the class.pentahoreports.php
 * @param String the root path from which the reports tree will be rendered
 * @param String the role uid
 * @return String with the html code that renders the tree object
 */
function showReportsDirTree( $root, $rolUid){
  
  $tree = new Tree();
  $tree->name = 'Users';
  $tree->nodeType = "base";
  $tree->contentWidth = "";
  // initializing the variables
  $tree->showSign = false;
  $roleReports = new PhRoleReport();
  $report = new PhReport();
  $oReportsDataset = $report->getAllReports();
  // iterating of the current folder level
  foreach ($oReportsDataset as $aRow) {
    // if is assigned then show the remove link of not the assign
    if ($roleReports->isReportInRole($rolUid,$aRow['REP_UID'])){
      $idAssign = "Remove";
    } else {
      $idAssign = "Assign";
    }
    $code      = $aRow['REP_NAME'];
    $repUid    = $aRow['REP_UID'];

    $uid       = $roleReports->getRolRepUidFromData($rolUid, $repUid); // 
    $reportDir = getCurrentDirectory($aRow['REP_PATH']);
    $parentDir = getParentDirectory($aRow['REP_PATH']);
    $childName = $aRow['REP_TITLE'];


    if ($reportDir==$root){
      // if is a directory check the child items and call this function recursively
      if (isDirectory($aRow['REP_TITLE'])){
        if($rolUid != "00000000000000000000000000000001") { // because the removal of the admin permitions is not posible
          $refer = "<a href=\"javascript:toggleRoleAssignment('{$rolUid}','{$uid}','{$repUid}','{$idAssign}','true');\">{$idAssign}</a>";
        } else {
          $refer = "<font color='#CFCFCF'>{$idAssign}</font>";
        }
	
        $html = "
          <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
            <tr>
              <td width='280px' class='treeNode' style='border:0px;background-color:transparent;cursor:pointer;cursor:hand;' onClick=\"toggleDisplayFolder('{$childName}');\"><img valign=\"top\" src=\"/images/folderV2.gif\"/> {$code}</td>
              <td class='treeNode' style='border:0px;background-color:transparent;'>[$refer]</td>
            </tr>
          </table>
          <div id='child_{$childName}' style='display:none;'>
            "
            . showReportsDirTree($childName,$rolUid)
           . "
          </div>
          ";
        // adding the child item into the tree
        $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
        $ch->point = '';
      } else {
        // if is a report file
          if($rolUid != "00000000000000000000000000000001") { //because the removal of the admin permitions is not posible
            $refer = "<a href=\"javascript:toggleRoleAssignment('{$rolUid}','{$uid}','{$repUid}','{$idAssign}','false');\">{$idAssign}</a>";
          } else {
            $refer = "<font color='#CFCFCF'>{$idAssign}</font>";
          }
          $html = "
            <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
              <tr>
                <td width='280px' class='treeNode' style='border:0px;background-color:transparent;'><img valign=\"middle\" src=\"/images/doc.gif\"/> {$code}</td>
                <td class='treeNode' style='border:0px;background-color:transparent;'>[$refer]</td>
              </tr>
            </table>";
          // adding the child item into the tree
          $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
          $ch->point = '';
      }
    }
  }
  return $tree->render();
}

/**
 * This function gets the current directory of a given path
 * @todo encapsulate this function inside one of the core classes maybe the class.pentahoreports.php
 * @param String the path to be evaluated
 * @return String with the current directory
 */
  function getCurrentDirectory($path){
    $currentDir = "/";
    $directory = explode("/", $path);
    for ($j=1;$j<count($directory);$j++){
      if ($j==count($directory)-1){
        $currentDir = $directory[$j];
      }
    }
    return $currentDir;
  }

/**
 * This function gets the parent directory of a given path
 * @todo encapsulate this function inside one of the core classes maybe the class.pentahoreports.php
 * @param String the path to be evaluated
 * @return String with the parent directory
 */
  function getParentDirectory($path){
    $parentDir = "/";
    $directory = explode("/", $path);
    for ($j=1;$j<count($directory);$j++){
      if ($j==count($directory)-2){
        $parentDir = $directory[$j];
      }
    }
    return $parentDir;
  }

/**
 * This function evaluates a String in order to verify if it's a directory
 * @todo encapsulate this function inside one of the core classes maybe the class.pentahoreports.php
 * @param String the title to be evaluated
 * @return Boolean true if is a directory or false if not
 */
  function isDirectory($title){
    $directory = preg_split("/\./", $title);
    if (count($directory)>=2){
      return false;
    } else {
      return true;
    }
  }

  
