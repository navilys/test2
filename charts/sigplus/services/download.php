<?php
function imageDefault() {
  $width  = 364;
  $height = 93;
  
		$img = imagecreate($width, $height);
   
  $colorBackground = imagecolorallocate($img, 225, 230, 237);
  $colorBlack      = imagecolorallocate($img, 0, 0, 0);
  
  imagefilledrectangle($img, 0, 0, $width-1, $height-1, $colorBackground);
  //imagerectangle($img, 0, 0, $width-1, $height-1, $colorBlack);
    
  imagejpeg($img);  
}
  
$stpid = isset($_GET["stpid"])? trim($_GET["stpid"]) : null;
$sigid = isset($_GET["sigid"])? trim($_GET["sigid"]) : null;
$appid = isset($_GET["appid"])? trim($_GET["appid"]) : null;
$tasid = isset($_GET["tasid"])? trim($_GET["tasid"]) : null;

//$appid = $_SESSION ["APPLICATION"];
//$tasid = $_SESSION ["TASK"];

$pathSign = PATH_DB . SYS_SYS . PATH_SEP . "sigplus" . PATH_SEP . $appid . PATH_SEP . $tasid . PATH_SEP . $stpid . PATH_SEP;

header("Content-type: image/jpeg");

if (file_exists($pathSign . $sigid . ".jpg")) {
	 echo file_get_contents($pathSign . $sigid . ".jpg");
}
else {
	 imageDefault();
}
?>