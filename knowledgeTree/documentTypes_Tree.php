<?php


//krumo ($_SESSION);
	    require_once ( "class.knowledgeTree.php" );
        $KnowledgeTreeClass = new KnowledgeTreeClass( );
        //krumo($KnowledgeTreeClass);

        $documentTypes=$KnowledgeTreeClass->kt_get_documentTypes();
       // krumo($documentTypes);
      if (PEAR::isError($documentTypes)) {
    	  print $documentTypes->getMessage();
    	  exit;
      }

  $WIDTH_PANEL = 250;


  $xVar = 1;
  $html = '';
  $htmlGroup = "<table width=\"100%\" class=\"pagedTable\" cellspacing='0' cellpadding='0' border='0' style='border:0px;'>";
  foreach($documentTypes->document_types as $typeName) {

  	$RowClass = ($xVar%2==0)? 'Row1': 'Row2';
  	$xVar++;
    $ID_EDIT     = G::LoadTranslation('ID_EDIT');
    $ID_MEMBERS  = G::LoadTranslation('ID_MEMBERS');
    $ID_DELETE   = G::LoadTranslation('ID_DELETE');
    $ID_VIEW_ASSIGNED_DOCUMENTS = "Assigned Documents";
    $UID         = $typeName;
    //$GROUP_TITLE = htmlentities($group->getGrpTitle());
    $GROUP_TITLE = strip_tags($typeName);
    $htmlGroup   .="
        <tr id=\"{$xVar}\" onclick=\"focusRow(this, 'Selected')\" onmouseout=\"setRowClass(this, '{$RowClass}')\" onmouseover=\"setRowClass(this, 'RowPointer' )\" class=\"{$RowClass}\">
          <td><img src=\"/images/users.png\" border=\"0\" width=\"20\" height=\"20\"/></td>
          <td>{$GROUP_TITLE}</td>
          <td>[<a class=\"normal\" href=\"#\" onclick=\"assignedDocuments('{$UID}');return false;\">{$ID_VIEW_ASSIGNED_DOCUMENTS}</a>]</td>
        </tr>";
  }
  $htmlGroup .= "</table>";

  echo '<div class="treeBase" style="width:'.($WIDTH_PANEL).'px">
			<div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
			<div class="content">
			  <table class="treeNode">
		        <tr>
		          <td valign="top">
		            <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
					<div class="boxContentBlue">
					  <table width="95%" style="margin:0px;" cellspacing="0" cellpadding="0">
					    <tr>
						  <td class="userGroupTitle">Document Types
						    <br>
						    <small style="font-weight:normal">Document Types are defined in Knowledgetree using the Knowledgetree interface</small></td>
						</tr>
					  </table>
					</div>
					<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>



				  	<div id="groupsListDiv" style="height: expression( this.scrollHeight > 319 ? \'320px\' : \'auto\' ); /* sets max-height for IE */  max-height: 320px; /* sets max-height value for all standards-compliant browsers */  overflow:auto; width:'.($WIDTH_PANEL-20).'px;">
				  	  <table class="pagedTableDefault"><tr><td>'
  					  .$htmlGroup.
				  	 '</td></tr></table>
				  	</div>
		          </td>
		        </tr>
		      </table>
			</div>
			<div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
		</div>';
  ?>
  <script>
  var screenX = WindowSize();
	wW = screenX[0];
	wH = screenX[1];

	document.getElementById('groupsListDiv').style.height = (wH/100)*70;
  </script>
