<?php

  $G_MAIN_MENU = 'processmaker';
  $G_SUB_MENU = 'knowledgeTree/menuKTConfiguration';
  $G_ID_MENU_SELECTED = 'KT';
  $G_ID_SUB_MENU_SELECTED = '';


    			
  $G_PUBLISH = new Publisher;
  
  
  $G_PUBLISH->AddContent('view', 'knowledgeTree/ktFolder_Tree' );
  $G_PUBLISH->AddContent('smarty', 'knowledgeTree/kt_fileList', '', '', array());
  G::RenderPage( "publish-treeview" );

  ?>
<script>
  
  
  kt_toggleFolder(0);
 </script>