<?php
//eAccelerator
//Catch: Warning "allowed_admin_path" setting
function errorHandler($errno, $errstr, $errfile, $errline) {
  throw new Exception("Error: $errno: $errstr at $errfile line $errline");
}

function scriptList($option, $r, $i)
{  $list = array();
   
   switch ($option) {
     case "CACHEDSCRIPTLST":  $list = eaccelerator_cached_scripts();  break;
     case "REMOVEDSCRIPTLST": $list = eaccelerator_removed_scripts(); break;
   }
   
   $data = array();
   
   foreach($list as $script) {
     if (function_exists("eaccelerator_dasm_file")) {
       //"<a href=\"dasm.php?file=" . $script["file"] . "\">" . $script["file"] . "</a>"
       $data["file"] = $script["file"];
     }
     else {
       $data["file"] = $script["file"];
     }
     
     $data["mtime"] = date("Y-m-d H:i", $script["mtime"]);
     $data["size"]  = number_format($script["size"] / 1024, 2) . "KB";
     $data["reloads"] = $script["reloads"] . " (" . $script["usecount"] . ")";
     $data["hits"]    = $script["hits"];
   }
   
   return (array(count($data), array_slice($data, $i, $r)));
}





set_error_handler("errorHandler", E_WARNING);





$option = (isset($_POST["option"]))? $_POST["option"] : null;

$response = array();

switch ($option) {
  case "INFO":
  case "CACHEED":
  case "OPTIMIZERED":
  case "CACHECLEAR":
  case "CACHECLEAN":
  case "CACHEPURGE":
    $status = 1;

    try {
      $info = eaccelerator_info();
      
      switch ($option) {
        case "CACHEED":
          $swED = ($info["cache"])? false : true;
          eaccelerator_caching($swED);
          break;

        case "OPTIMIZERED":
          if (function_exists("eaccelerator_optimizer")) {
            $swED = ($info["optimizer"])? false : true;
            eaccelerator_optimizer($swED);
          }
          break;
        
        case "CACHECLEAR": eaccelerator_clear(); break;
        case "CACHECLEAN": eaccelerator_clean(); break;
        case "CACHEPURGE": eaccelerator_purge(); break;
      }
      
      ///////
      $info = eaccelerator_info();
      
      $muA = number_format(100 * $info["memoryAllocated"] / $info["memorySize"], 2);
      $muB = number_format($info["memoryAllocated"] / (1024 * 1024), 2);
      $muC = number_format($info["memorySize"] / (1024 * 1024), 2);
      
      $response["version"] = $info["version"];
      $response["cache"]   = ($info["cache"])? "yes" : "no";
      $response["optimizer"]   = ($info["optimizer"])? "yes" : "no";
      $response["memoryUsage"] = $muA . "% (" . $muB . "MB/" . $muC . "MB)";
      $response["memoryFree"]  = number_format($info["memoryAvailable"] / (1024 * 1024), 2) . "MB";
      $response["cachedScripts"]  = $info["cachedScripts"];
      $response["removedScripts"] = $info["removedScripts"];
      $response["cachedKeys"]     = $info["cachedKeys"];
      
      $response["status"] = "OK";
    }
    catch (Exception $e) {
      $response["message"] = $e->getMessage();
      $status = 0;
    }

    if ($status == 0) {
      $response["status"] = "ERROR";
    }
    break;
  
  case "CACHEDSCRIPTLST":
  case "REMOVEDSCRIPTLST":
    $pageSize = $_POST["pageSize"];
    $limit = $_POST["limit"];
    $start = $_POST["start"];
    
    $status = 1;

    try {
      //$list = array();
      //for ($i = 0; $i <= 100 - 1; $i++) {
      //  $list[] = array("file" => "x$i", "mtime" => "x", "size" => "x", "reloads" => "x", "hits" => "x");
      //}
      //$listNum = count($list);
      //$list = array_slice($list, $start, $limit);
      list($listNum, $list) = scriptList($option, $limit, $start);
      
      $response["success"] = true;
      $response["total"] = $listNum;
      $response["root"] = $list;
      
      $response["status"] = "OK";
    }
    catch (Exception $e) {
      $response["message"] = $e->getMessage();
      $status = 0;
    }

    if ($status == 0) {
      $response["status"] = "ERROR";
    }
    break;
  
  case "INSTALLED":
    $status = 1;
    
    try {
      $array = eaccelerator_info();
      $array = eaccelerator_cached_scripts();
      $array = eaccelerator_removed_scripts();
      
      $response["status"] = "OK";
    }
    catch (Exception $e) {
      $response["message"] = $e->getMessage();
      $status = 0;
    }

    if ($status == 0) {
      $response["status"] = "ERROR";
    }
    break;
}

echo json_encode($response);
?>