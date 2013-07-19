<?php
ini_set("max_execution_time", 0);
ini_set("display_errors", 0);
error_reporting(E_ALL & ~E_NOTICE);





//processmaker/workflow/engine/bin/tasks/cliUpgrade.php
function rm_dir($filename, $filesOnly = false)
{  if (is_file($filename)) {
     @unlink($filename); // or CLI::logging(CLI::error("Could not remove file $filename")."\n");
   }
   else {
     foreach (glob("$filename/*") as $f) {
       rm_dir($f);
     }
     if (!$filesOnly) {
       @rmdir($filename); // or CLI::logging(CLI::error("Could not remove directory $filename")."\n");
     }
   }
}

//processmaker/workflow/engine/bin/tasks/cliUpgrade.php
//function run_upgrade($command, $args)
function upgrade()
{  require (PATH_CORE . "bin" . PATH_SEP . "tasks" . PATH_SEP . "cliCommon.php");

   G::LoadClass("system");
   G::LoadClass("wsTools");

   $result = array();

   /*
   $checksum = System::verifyChecksum();

   if ($checksum === false) {
   }
   else {
    if (!empty($checksum["missing"])) {
    }
    if (!empty($checksum["diff"])) {
    }
    if (!(empty($checksum["missing"]) || empty($checksum["diff"]))) {
    }
  }
  */

  if (defined("PATH_C")) {
    rm_dir(PATH_C, true);
  }

  $workspaces = get_workspaces_from_args(array()); //returns all available workspaces
  $count = count($workspaces);
  $first = true;
  $errors = false;

  ///////
  $strOutput = null;

  ob_start();

  foreach ($workspaces as $index => $workspace) {
    try {
      //CLI::logging("Upgrading workspaces ($index/$count): " . CLI::info($workspace->name) . "\n");
      $workspace->upgrade($first);
      $workspace->close();
      $first = false;

      $strOutput = $strOutput . ob_get_contents();
    }
    catch (Exception $e) {
      //CLI::logging("Errors upgrading workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n");
      $errors = true;
    }
  }

  ob_end_clean();

  //$strOutput = nl2br($strOutput);
  $strOutput = null;

  ///////
  if ($errors) {
    //CLI::logging("Upgrade finished but there were errors upgrading workspaces.\n");
    //CLI::logging(CLI::error("Please check the log above to correct any issues.")."\n");
    //$strOutput = $strOutput . "<br />";
    $strOutput = $strOutput . "Upgrade finished but there were errors upgrading workspaces.<br />";
    $strOutput = $strOutput . "Please check the log above to correct any issues.<br />";
  }
  else {
    //CLI::logging("Upgrade successful\n");
    //$strOutput = $strOutput . "<br />";
    $strOutput = $strOutput . "Upgrade successful.<br />";
  }

  $result["status"] = "OK";
  $result["message"] = $strOutput;

  return ($result);
}





echo G::json_encode(upgrade());
?>