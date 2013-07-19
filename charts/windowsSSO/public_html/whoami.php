<?php
session_start();
require_once("plexcel.php");

$err = NULL;
$acct = NULL;

$px = plexcel_new(NULL, NULL);
if ($px == FALSE) {
	$err = 'Error: <pre>' . plexcel_status(NULL) . '</pre>';
} else {
	//
	// Try Kerberos Single Sign-On only
	//
	if (plexcel_sso($px) == FALSE) {
		$err = 'Error: <pre>' . plexcel_status($px) . '</pre>';
	} else {
		//
		// Use PLEXCEL_SUPPLEMENTAL to only get info supplied in
		// client's Kerberos ticket thereby eliminating any
		// communication with the domain controller.
		//
		// An account name of NULL means the current user.
		//
		$acct = plexcel_get_account($px, NULL, PLEXCEL_SUPPLEMENTAL);
		if (is_array($acct) == FALSE)
			$err = 'Error: <pre>' . plexcel_status($px) . '</pre>';
	}
}

// END WORK / START OUTPUT
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>whoami</title>
</head>
<body>

<h2>whoami</h2>

<?php
if ($err) {
	echo $err;
} else {
	echo "Single Sign-On was successful!<p/>";
	echo '<pre>';
	print_r($acct);
	echo '</pre>';
}
?>

</body></html>
