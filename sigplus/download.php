<?php

  Header("Content-type: image/jpeg");
  function showDefault() {
    $w  = 364;
    $h  =  93;
    $im = ImageCreate($w, $h);
    $width  = $w;
    $height = $h;
    $center_x = intval($width / 2);
    $center_y = intval($height / 2);
   
    $bgcolor = ImageColorAllocate($im, 220, 220, 255);
    $plomo   = ImageColorAllocate($im, 220, 220, 220);
    $orange  = ImageColorAllocate($im, 252, 252, 128);
    $gris    = ImageColorAllocate($im, 150, 150, 155);
    $white   = ImageColorAllocate($im, 255, 255, 255);
    $red     = ImageColorAllocate($im, 255, 0, 0);
    $brown   = ImageColorAllocate($im, 160, 80, 0);
    $black   = ImageColorAllocate($im,   0,0,0);
    $green   = imagecolorallocate ($im, 0, 200,  0);
  
    ImageFilledRectangle($im, 0, 0, $width-1, $height-1, $bgcolor);
    ImageRectangle      ($im, 0, 0, $width-1, $height-1, $black);
    
    ImageJpeg($im);  
  }
  
  $stpid = isset( $_GET ['stpid'] ) ? trim($_GET ['stpid']) : '';
  $sigid = isset( $_GET ['sigid'] ) ? trim($_GET ['sigid']) : '';
  $appid = $_SESSION ['APPLICATION'];
  $tasid = $_SESSION ['TASK'];

  $pathSign = PATH_DB . SYS_SYS . PATH_SEP . 'sigplus' . PATH_SEP . $appid . PATH_SEP . $tasid . PATH_SEP . $stpid . PATH_SEP;
  
  if ( file_exists ( $pathSign . $sigid  . '.jpg' ) )  {
  	print file_get_contents (  $pathSign . $sigid . '.jpg' );
  }
  else {
  	showDefault();
  }
  
  
  