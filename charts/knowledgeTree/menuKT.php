<?php
global $G_TMP_MENU;
//if(!method_exists($this,'registerDashboardPage')){

require_once ( "class.knowledgeTree.php" );
$KnowledgeTreeClass = new KnowledgeTreeClass( );
if($KnowledgeTreeClass->connected){
    G::LoadClass( 'configuration' );
    $conf = new Configurations();
    try {
        $preferencesKt = $conf->getConfiguration( 'KT_PREFERENCES', '' );
    } catch (Exception $e) {
        $preferencesKt = array ();
    }
    if (isset($preferencesKt['KT_WIN']) && $preferencesKt['KT_WIN'] != false && SYS_SKIN == 'classic') {
        $G_TMP_MENU->AddIdOption('KT', 'KT Documents', 'javascript:showDnsPopUp();', 'absolute');
    } else {
        $G_TMP_MENU->AddIdRawOption('KT', 'knowledgeTree/ktDashboard', "KT Documents" );
    }
}
  //}


?>