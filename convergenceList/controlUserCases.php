<?php
G::LoadClass('pmFunctions');
$APP_UID = $_POST['appUid'];
$delete = executeQuery("DELETE FROM PMT_USER_CONTROL_CASES WHERE APP_UID = '$APP_UID' AND USR_UID = '".$_SESSION['USER_LOGGED']."' ");