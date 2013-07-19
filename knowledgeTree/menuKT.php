<?php
  global $G_TMP_MENU;
  //if(!method_exists($this,'registerDashboardPage')){
      
      require_once ( "class.knowledgeTree.php" );
      $KnowledgeTreeClass = new KnowledgeTreeClass( );
      if($KnowledgeTreeClass->connected){
          $G_TMP_MENU->AddIdRawOption('KT', 'knowledgeTree/ktDashboard', "KT Documents" );
      }
  //}
  

?>