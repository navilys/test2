<?php 
G::LoadClass("plugin");
G::LoadThirdParty("pear/json", "class.json");

$appUid  = $_SESSION ["APPLICATION"];
$tasUid  = $_SESSION ["TASK"];

$numSigner = intval($_GET["numsgr"]);
$stepUid   = $_GET["stpid"];

$pathSign = PATH_DB . SYS_SYS . PATH_SEP . "sigplus" . PATH_SEP . $appUid . PATH_SEP . $tasUid . PATH_SEP . $stepUid . PATH_SEP;
  
$res = null;
$signArray = array();

for ($i = 0; $i <= $numSigner - 1; $i++) {
  $filename = $pathSign . $i . ".jpg";
  
  if (file_exists($filename)) {
    $signArray[] = filemtime($filename);
  }
  else {
    $signArray[] = 0;
  }
}

$oJSON = new Services_JSON();

echo $oJSON->encode($signArray);
?>