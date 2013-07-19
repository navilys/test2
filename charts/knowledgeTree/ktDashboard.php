<?php

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'knowledgeTree/menuKTConfiguration';
$G_ID_MENU_SELECTED = 'KT';
$G_ID_SUB_MENU_SELECTED = 'KT_PREFERENCES, KT_CONFIG';

$G_PUBLISH = new Publisher;

$G_PUBLISH->AddContent('view', 'knowledgeTree/ktFolder_Tree' );
$G_PUBLISH->AddContent('smarty', 'knowledgeTree/kt_fileList', '', '', array());

if (substr(SYS_SKIN, 0, 2) == 'ux') {
    G::RenderPage( "publish-treeview","blank" );
} else {
    G::LoadClass( 'configuration' );
    $conf = new Configurations();
    try {
        $preferencesKt = $conf->getConfiguration( 'KT_PREFERENCES', '' );
    } catch (Exception $e) {
        $preferencesKt = array ();
    }
    if (isset($preferencesKt['KT_WIN']) && $preferencesKt['KT_WIN'] != false && SYS_SKIN == 'classic') {
        G::RenderPage( "publish-treeview","blank");
    } else {
        G::RenderPage( "publish-treeview");
    }
}

?>


<script>
    kt_toggleFolder(0);
</script>
