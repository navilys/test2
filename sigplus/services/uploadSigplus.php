<?php

  $appid = $_POST['appid'];
  $tasid = $_POST['tasid'];
  $stpid = $_POST['stpid'];
  $sigid = $_POST['sigid'];
		
  $pathSign = PATH_DB . SYS_SYS . PATH_SEP . 'sigplus' . PATH_SEP;
  if (isset($_FILES) && $_FILES['jpgfile']['error'] == 0) {
		$sPathName = $pathSign . $appid . PATH_SEP . $tasid . PATH_SEP . $stpid . PATH_SEP ;
		$sFileName = $sigid . '.jpg';
		G::uploadFile($_FILES['jpgfile']['tmp_name'], $sPathName, $sFileName );		
  }

  if (isset($_FILES) && $_FILES['sigfile']['error'] == 0) {
		$sPathName = $pathSign . $appid . PATH_SEP . $tasid . PATH_SEP . $stpid . PATH_SEP ;
		$sFileName = $sigid . '.sig';
		G::uploadFile($_FILES['sigfile']['tmp_name'], $sPathName, $sFileName );		
  }
	