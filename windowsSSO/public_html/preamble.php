<?php
session_start();

require_once 'plexcel.php';

function htmlesc($data) {
	return htmlentities($data, ENT_COMPAT, 'UTF-8');
}

$preamble = plexcel_preamble();
list($authority, $bindstr, $px, $err, $action, $is_authenticated) = $preamble;

if ($is_authenticated) {

	//
	// WORK OF SCRIPT GOES HERE
	//

	//
	// An account name of null means the current user.
	//
	plexcel_set_conv_attrdefs($px);
	$acct = plexcel_get_account($px, null, null);
	if (is_array($acct) == false)
		$err .= '<p/><pre>' . plexcel_status($px) . '</pre>';
}

$username = plexcel_get_param('p_username');

// END WORK / START OUTPUT

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>preamble</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="../style.css?v=1">
<script type="text/javascript">
function act(a) {
	document.f.p_action.value = a;
	document.f.submit();
}
function try_sso() {
	document.f.p_authority.value = '';
	document.f.p_username.value = '';
	act('default');
}
</script>
</head>
<body>
<form name="f" method="POST" style="margin: 0px; padding: 0px;">
<table border="0">

<input type='hidden' name='p_action' value='<?php echo htmlesc($action); ?>'/>
<tr><td colspan='2'><h1>preamble</h1></td>

<?php
if ($authority == null) {
	//
	// Allow operator to select an alternative domain controller.
	//
?>
<input type="hidden" name="p_username" value="<?php echo $username;?>"/>
<td colspan='2' style='vertical-align: top; text-align: right;'><a href="javascript:try_sso();"><small>Try Single Sign-On</small></a></td></tr>
<tr><td colspan='4' style='background-color: #e0e0e0'></td>
<?php
	if ($err)
		echo "<tr><td colspan='4'>$err</td></tr>\n";
	$authority = plexcel_get_param('p_authority');
?>
<tr><td class="formlabel">DC:</td><td colspan="3"><input type="text" name="p_authority" size="20" value="<?php echo htmlesc($authority); ?>"/></td></tr>
<tr><td></td><td><input type='submit' class='button' value='Find DC'/></td><td></td><td></td></tr>
<?php
} else if ($is_authenticated == false) {
	//
	// Username and password form for plexcel_logon
	//
?>
<input type="hidden" name="p_authority" value=""/>
<td colspan='2' style='vertical-align: top; text-align: right;'><a href="javascript:try_sso();"><small>Try Single Sign-On</small></a></td></tr>
<tr><td colspan='4' style='background-color: #e0e0e0'></td>
<?php
	if ($err)
		echo "<tr><td colspan='4'>$err</td></tr>\n";

	if ($username == null)
		$username = 'user@example.com';
?>
<tr><td class="formlabel">Username:</td><td colspan="3"><input type="text" name="p_username" size="20" value="<?php echo htmlesc($username); ?>"/></td></tr>
<tr><td class="formlabel">Password:</td><td colspan="3"><input type="password" name="p_password" size="20" value=""/></td></tr>
<tr><td></td><td><input type='submit' class='button' value='Logon'/></td><td></td><td></td></tr>
<tr><td colspan='4'><table width='500'><tr><td><small>If transparent authentication was expected this page indicates that Kerberos 5 single sign-on was not successful. Please consider the following possible causes: <?php echo plexcel_get_sso_helpmsg(); ?>If the above remedies do not resolve the issue, please contact your network administrator.</small></td></tr></table></td><tr>
<?php
} else {
	//
	// Authentication completed successfully.
	//
?>
<input type="hidden" name="p_authority" value="<?php echo $authority; ?>"/>
<input type="hidden" name="p_username" value="<?php echo $username; ?>"/>
<td colspan='2' style='vertical-align: top; text-align: right;'><small><a href="javascript:act('change_authority')"><?php echo $authority; ?></a><br/>
<a href="javascript:act('refresh');">Refresh</a>
<?php
	if ($is_authenticated)
		echo "| <a href=\"javascript:act('logoff');\">Logoff</a>";
?>
</small></td></tr>
<tr><td colspan='4' style='background-color: #e0e0e0'></td>
<?php
	if ($err)
		echo "<tr><td colspan='4'>$err</td></tr>\n";

	//
	//
	// OUTPUT OF SCRIPT GOES HERE
	//
	//

	if ($acct) {
		//
		// Just print the current user's information.
		//
		echo "<tr><td colspan='4'>Your account information:<pre>";
		print_r($acct);
		echo '</pre></td></tr>';
	}
}

//
// Show what's in the $preamle array
//
echo "<tr><td colspan='4'>Preamble: <pre>";
print_r($preamble);
echo "</pre>";

echo "<tr><td colspan='4'>POST parameters: <pre>";
print_r($_POST);
echo "</pre>";
/*
*/

?>

</form>
</table>
</body></html>

