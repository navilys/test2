<?php
 /**
  * @section Filename
  * pentahoReportsListAjax.php
  * @subsection Description
  * This Script serves 3 possible requests based in the POST action parameter. \n
  * 'openReportFolder' opens and renders the report list obtained from the pentaho server. \n
  * 'openReport' gets the server link in order to render the report inside pm. \n
  * 'loadLibs' gets the header libraries in order to solve some render issues for IE.
  * @author Gustavo Cruz gustavo@colosa.com  
  * @subsection copyright 
  * Copyright (C) 2004 - 2010 Colosa Inc.23
  * <hr>
  * @package plugins.pentahoreports.scripts
  */

  G::LoadInclude ( 'ajax' );
  G::LoadClass ( 'tree' );
  require_once ( "class.pentahoreports.php" );

  /**
   * setting the post action parameter
   */
  $_POST ['action'] = get_ajax_value ( 'action' );

  /**
   * The main class pentaho object
   */  
  $objPentaho = new pentahoreportsClass();
  $objPentaho->readConfig();
  
  /**
   * Setting up the root Folder
   */
  $rootFolder = "/";


  /**
   * This function recursively assembles the Report list obtained from the pentaho server.
   * @author Gustavo Cruz <gustavo@colosa.com>
   * @param Array The folder validated array 
   * @param Boolean Check if the folder evaluated is the root folder
   * @param String The current path of the folder
   * @todo  Ecapsulate this function inside a class possibly the class.pentahoreports.php
   * @return String of the code resulting of the rendered tree
   */
  function getCurrentFolderList ($foldersArray, $rootFlag, $currentPath){    
    $tree = new Tree ();
    $tree->name = 'DMS';
    $tree->nodeType = "blank";
    
    $tree->value = '';
    $tree->showSign = false;
    
    $i = 0;
      
    $files = $foldersArray;
    $currentPath = trim($currentPath);
    // file by file, dir by dir, the foreach block assemble the current directory
      foreach( $files as $key => $file ) {
        $i++;
        $RowClass = ($i % 2 == 0) ? 'Row1' : 'Row2';
        // if the the record is a folder then recursively the function call itself to assemble the inner directory  
        if ( is_array($file['files']) && $file['type'] == 'folder') {
            $styleShow = 'style=';
            if ($rootFlag == 1)
              $styleShow .= '\'display:none;\'';
            else if($rootFlag == 0)
              $styleShow .= '\'\'';
            // setting the arrows for each folder
            if (count($file['files'])>0){
                $divSpan = '<span id="arrow_'.$file['name'].'">&#9658;</span>';
            } else {
                $divSpan = '';
            }
                      
            $separator = '/';
            $reportPath = $currentPath . $file['name'] . $separator;
            // if the folder has child items the function calls itself
            $filesList = getCurrentFolderList($file['files'],'1',$reportPath);
            $fileNameDecoded = urldecode($file['name']);
            $htmlGroup = <<<GHTML
            <table cellspacing='0' cellpadding='0' border='1' style='border:0px;' width="100%" class="pagedTable">
            <tr id="{$i}" onclick="toggleShowFolderContent('{$fileNameDecoded}','0');" onmouseout="this.style.backgroundColor='#FFFFFF'" onmouseover="this.style.backgroundColor='#EEEEEE'" class="{$RowClass}" style="cursor:hand">
            <td width='' class='treeNode' style='border:0px;background-color:transparent;'><a href="#" onclick=""><img src="/images/folderV2.gif" border = "0" valign="middle" />&nbsp;{$file['label']}</a>{$divSpan}</td>
            </tr>
            </table>
            <div id="child_{$file['name']}" {$styleShow}>{$filesList}</div>
GHTML;
            $ch = & $tree->addChild ( $key, $htmlGroup, array ('nodeType' => 'child' ) );
            $ch->point = ' ';
            
        } 
        else {
            // if is a normal report
            if (substr($currentPath,-1)=="/")
              $currentPath = substr($currentPath,0,-1);
                      
            $iconFilename = 'default';
            // assembling the link for each report
            if ( substr($file['name'],-5 ) == '.xcdf' ) $iconFilename = 'dashboard';
            if ( substr($file['name'],-20 ) == 'analysisview.xaction' ) $iconFilename = 'analysis';
            $fileNameDecoded = urldecode($file['name']);
            $htmlGroup = <<<GHTML
            <table cellspacing='0' cellpadding='0' border='1' style='border:0px;' width="100%" class="pagedTable">
            <tr id="{$i}" onclick="openReport('{$currentPath}','{$fileNameDecoded}');" onmouseout="this.style.backgroundColor='#FFFFFF'" onmouseover="this.style.backgroundColor='#EEEEEE'" class="{$RowClass}" style="cursor:hand">
            <td width='' class='treeNode' style='border:0px;background-color:transparent;'><a href="#" onclick="">
            <img src="/plugin/pentahoreports/$iconFilename.png" border = "0" valign="middle" />&nbsp;{$file['label']}</a></td>
            </tr>
            </table>
            <div id="child_{$file['name']}">
            </div>
GHTML;
            // adding the assembled link to the tree
            $ch = & $tree->addChild ( $key, $htmlGroup, array ( 'nodeType' => 'child' ) );
            $ch->point = ' ';
        }
      }
      return $tree->render();
  }
  
  /**
   * getting the logged user
   */
  if( isset($_SESSION['USER_LOGGED'])){
    $user_logged = trim($_SESSION['USER_LOGGED']);
  } 
  else {
    $user_logged = '';
  }
  
  try {
  switch ($_POST ['action']) {
    // opening and render the list of the report folder content
    case 'openReportFolder' :
      $fields = $objPentaho->readLocalConfig ( SYS_SYS );
      if ( isset( $fields['PentahoUsername'] ) && $fields['PentahoUsername'] != '' )  
      	$workspace = substr($fields['PentahoUsername'],3);
      else
        $workspace = SYS_SYS;
      	
      $folderContent = $objPentaho->getSolutionWorkspace( $workspace );
      $folderContent = $objPentaho->checkPermissions ( $folderContent );
      if (isset($_POST['folderID']) && $_POST['folderID'] == 0)
        $rootFlag = 1;
      else   
        $rootFlag = 0;
      // printing the report list
      print ( getCurrentFolderList ($folderContent,$rootFlag,'') );
      
    break;
    // opening the report, in this case the pentaho server link is printed
    case 'openReport' :
      $fields = $objPentaho->readLocalConfig ( SYS_SYS );
      if ( isset( $fields['PentahoUsername'] ) && $fields['PentahoUsername'] != '' )  
      	$workspace = substr($fields['PentahoUsername'],3);
      else
        $workspace = SYS_SYS;

      $fileName = trim($_POST['fileName']);
      
      $filePath = $_POST['filePath'];
      // getting the report link  
      $reportContent = $objPentaho->viewAction( $workspace, $filePath, $fileName, $user_logged);
      $pentahoServer = $objPentaho->readConfig();
      $pentahoServer = $pentahoServer['PentahoServer'];
  
      print ($reportContent);
      
    break;
    // currently the best approach is to load the server header in that way
    case 'loadLibs' :
      $pentahoServer = $objPentaho->readConfig();
      $pentahoServer = $pentahoServer['PentahoServer']; 
      print ($pentahoServer);
    break;
    }

  }
  catch ( Exception $e ) {
          //throw ( $e  );
    echo $e->getMessage();
  }

