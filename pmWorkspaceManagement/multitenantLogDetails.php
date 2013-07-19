<?php

// requiers the logger class
require_once(PATH_PLUGINS.SYS_COLLECTION.PATH_SEP.'classes'.PATH_SEP.'class.logger.php');

// retrieves the complete details for a given entry log, for displaying additional details in the log viewer
if (array_key_exists("logID", $_POST)) {
    
    $logID = intval($_POST["logID"]);
    $logger = new logger();
    $details = $logger->getDetailsFromLogID($logID);
    $logLines = split("\r\n", $details);
    echo json_encode($logLines);
}

?>