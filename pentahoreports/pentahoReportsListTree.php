<?php
 /**
  * @section Filename
  * pentahoReportsListTree.php
  * @subsection Description
  * Report list tree base layout
  * @author Gustavo Cruz <gustavo@colosa.com>
  * @subsection copyright
  * Copyright (C) 2004 - 2010 Colosa Inc.23
  * <hr>
  * @package plugins.pentahoreports.scripts
  */

$rootFolder = 0;
$folderPath['PATH']="/";

$html = '<div>
   <div class="boxTopBlue">
   <div class="a"></div>
   <div class="b"></div>
   <div class="c"></div>
   </div>
   <div class="boxContentBlue">
    <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
    <tr>
      <td class="userGroupTitle"><div class="userGroupLink">Pentaho Solution Repository: <a href="javascript:openReportFolder(\''.$rootFolder.'\',\''.$rootFolder.'\')"> - Refresh -</a></div></td>
    </tr>
  </table>
  </div>
  <div class="boxBottomBlue">
    <div class="a"> </div>
    <div class="b"></div>
    <div class="c"></div>
  </div>';

$html.='<div class="treeBase" style="width:360px;height: expression( this.scrollHeight > 319 ? \'320px\' : \'auto\' ); /* sets max-height for IE */  max-height: 500px; /* sets max-height value for all standards-compliant browsers */  overflow:auto;">
  <div class="boxTop">
    <div class="a"></div>
    <div class="b"></div>
    <div class="c"></div>
  </div>
  <div class="content">';
  $html.="<div id='child_$rootFolder'></div>
  </div>";
  $html.='
  <div class="boxBottom">
    <div class="a"></div>
    <div class="b"></div>
    <div class="c"></div>
  </div>
  </div>';

  echo $html;
?>
  
