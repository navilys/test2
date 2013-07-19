<?php
G::LoadClass('case');
$case = new Cases();
if ($case->canStartCase($_SESSION['USER_LOGGED'])) {
  $initialTasks = $case->getStartCasesPerType($_SESSION['USER_LOGGED'], '');
  array_shift($initialTasks);
}
else {
  //Todo: Poner mensaje
}

$smarty = new Smarty();
$smarty->compile_dir  = PATH_SMARTY_C;
$smarty->cache_dir    = PATH_SMARTY_CACHE;
$smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';
$smarty->caching      = false;
$smarty->templateFile = PATH_PLUGINS . 'N_InOutlook/templates/newCase.html';
$smarty->assign('initialTasks', $initialTasks);
die($smarty->fetch($smarty->templateFile));
?>