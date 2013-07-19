<?php
        include_once(PATH_PLUGINS.'elock'.PATH_SEP.'class.elock.php');
        include_once(PATH_PLUGINS.'elock'.PATH_SEP.'classes/class.pmFunctions.php');
        //global $fields;
        require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
        $elockObj = new elockClass ();
$elockObj->ElockOperatorUserName;


$formData = $_FILES["form"];
$tempFilePath=$formData["tmp_name"]["SIGNATURE_FILENAME"];
var_dump($tempFilePath);
$sourcePath = $tempFilePath;
$fileName = $formData["name"]["SIGNATURE_FILENAME"];
$destPath ='pm/' .$fileName;




/*  Start of File transfer from Local machine to Elock Server */

//$server = $fields['ElockSERVER_IP'];
$server = $elockObj->ElockSERVER_IP;
$connection = ftp_connect($server);
//echo("hi");
var_dump($connection);



//$ftp_user_name =  $fields['ElockFTP_USER'];
$ftp_user_name = $elockObj->ElockFTP_USER;

//$ftp_user_pass =  $fields['ElockFTP_PASSWORD'];
$ftp_user_pass = $elockObj->ElockFTP_PASSWORD;

$login = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
var_dump($login);

if (!$connection || !$login) { die('Connection attempt failed!'); }

//$dest = "/test/upload.html";
//$source = "/opt/lampp/htdocs/upload.html";
$mode = FTP_BINARY;
ftp_pasv($connection, false);
$upload = ftp_put($connection, $destPath, $sourcePath, $mode);

var_dump($upload);
if (!$upload) { echo 'FTP upload failed!'; }

ftp_close($connection);



//$usrName =  $fields['ElockOperatorUserName'];
$usrName = $elockObj->ElockOperatorUserName;
//$userId = $_SESSION['USR_USERNAME'];
$userId = 'pmsigner';
//$strPassword =  $fields['ElockOperatorUserPassword'];
$strPassword = $elockObj->ElockOperatorUserPassword;
$operatorAuthToken = elockLogin($usrName,$strPassword);
$signatureImageFilePath = "d:/mobiSignerData/".$fileName;




$signResult = SetSigningParameters($userId,$signatureImageFilePath,$operatorAuthToken);
var_dump($signResult);



$backUrlObj=explode("sys".SYS_SYS,$_SERVER['HTTP_REFERER']);
var_dump($backUrlObj);
//die;
G::header("location: "."/sys".SYS_SYS.$backUrlObj[1]);

die('<script type="text/javascript">parent.window.location = parent.window.location.href;</script>');

?>
