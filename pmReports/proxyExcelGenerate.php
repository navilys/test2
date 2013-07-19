<?php
$sPmrUid = $_POST["pmrUid"];

$aDatosJson  = G::json_decode(str_replace(",]", "]", $_POST["data"]));
$headersFields = array();

foreach ($_GET as $key => $val) {
 if (substr($key, 0, 7) == "VARS___") {
   $headersFields[str_replace("VARS___", "", $key)] = $val;
 }
};

if (isset($aDatosJson[0])) {

  $vars = get_object_vars($aDatosJson[0]);
  $headers =(array_keys($vars));

  $data = null;

  $data = "<table>
             <tr>";

  //headers
  foreach ($vars as $key => $val) {
    if ($key != "APP_UID"and $key != "DEL_INDEX") {
      $data = $data . "<td align=\"center\">" . $headersFields[$key] . "</td>";
    }
  }

  $data = $data . "</tr>";

  foreach ($aDatosJson as $aRow) {
    $data = $data . "<tr>";
    $vars = get_object_vars($aRow);

    foreach ($vars as $key => $val) {
      if ($key != "APP_UID" && $key != "DEL_INDEX") {
        $data = $data . "<td> ".$val."</td>";
      }
    }

    $data = $data . "</tr>";
  }

  $data = $data . "</table>";
}
else {
  $data = '';
}

$xlsDir  = PATH_PLUGINS . "pmReports" . PATH_SEP . "public_html" . PATH_SEP . "generatedReports" . PATH_SEP;
$xlsName = $sPmrUid . ".xls";

///////
$date = date("Y-m-d");
$aDate = explode("-", $date);
$mktDatePrevious = mktime(23, 59, 59, $aDate[1], $aDate[2], $aDate[0]) - (1 * (24 / 1) * (60 / 1) * (60 / 1));

if (is_dir($xlsDir)) {
  if ($dir = opendir($xlsDir)) {
    while (($file = readdir($dir)) !== false) {
      if ($file != "." && $file != ".." && preg_match("/^.*\.xls$/", $file)) {
        $mktFile = filemtime($xlsDir . $file);

        if ($mktFile <= $mktDatePrevious) {
          unlink($xlsDir . $file);
        }
      }
    }

    closedir($dir);
  }
}
///////

G::verifyPath($xlsDir);
file_put_contents($xlsDir . $xlsName, $data);
?>