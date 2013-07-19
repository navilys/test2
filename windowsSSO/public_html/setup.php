<?php
//
// There are no license restrictions on this file. Feel free to
// copy or modify this code for your own purposes.
//

// Characters not permitted in sAMAccountName
$invalid_sam_chars = '"/\[]:|<>+=;?*';

session_start();
require_once('common.php');

if (!plexcel_get_param('p_authority'))
	$_GET['p_action'] = 'change_authority';

$msg = '';
$preamble = plexcel_preamble(array('auth_type' => PLEXCEL_AUTH_LOGON));
list($authority, $bindstr, $px, $err, $action, $is_authenticated) = $preamble;

//plexcel_log(1, print_r($preamble, TRUE));

if ($is_authenticated == FALSE) {
	$_arr = plexcel_find_authorities_by_domain($authority,
				0,
				PLEXCEL_AUTHORITY_KERBEROS | PLEXCEL_AUTHORITY_CHKHOST);
	if (is_array($_arr) == FALSE || count($_arr) == 0) {
		if (plexcel_get_param('p_authority')) {
			$msg = '<p/>The specified DNS domain or server could not be located using DNS SRV queries. Perhaps you need to set the plexcel.dns.servers INI property? Please review the DNS requirements section in the Plexcel Operator\'s Manual for details.';
		}
		$authority = NULL;
	}
}

if (stristr(apache_get_version(), 'Apache/1.'))
	$err .= '<p/>Error: Plexcel requires Apache 2.0 or later but you appear to be running Apache 1.x.';
if (ini_get('magic_quotes_gpc'))
	$msg = '<p/>Notice: magic_quotes_gpc appears to be on.';

$locale = setlocale(LC_CTYPE,NULL);

//
// Need a non-authenticated binding to test validity of
// current keytab.
//
$pxn = plexcel_new($bindstr, NULL);

$plexcel_home = ini_get("plexcel.home");
if ($plexcel_home == NULL)
	$plexcel_home = "/var/lib/plexcel";
$hostname = strtolower($_SERVER['SERVER_NAME']);
$distinguishedName = plexcel_get_param('p_distinguishedName');
$username = plexcel_get_param('p_username');
$operator = $username;
$spn = "HTTP/$hostname";
$default_uac = PLEXCEL_PASSWD_NOTREQD | PLEXCEL_NORMAL_ACCOUNT | PLEXCEL_DONT_EXPIRE_PASSWORD | PLEXCEL_TRUSTED_FOR_DELEGATION;
$saccts = NULL;

if (ereg('^\[?[0-9\.]+\]?$', $hostname)) {
	$err = 'The hostname "' . $hostname . '" appears to be an IP address. The hostname within the URL used to address this script must be a fully qualified domain name (e.g. name.example.com). Please refer to the DNS section in the Plexcel Operator\'s Manual.';
} else if (strpos($hostname, '.') == FALSE) {
	$err = 'The hostname "' . $hostname . '" does not appear to be a fully qualified domain name (FQDN). The hostname within the URL used to address this script must be an FQDN (e.g. "wiki-5.example.com" and <i>not</i> simply "wiki-5"). Please refer to the DNS section in the Plexcel Operator\'s Manual.';
}

function randpass($n) {
	$p = "";
	while (strlen($p) < $n) {
		$p .= dechex(mt_rand()) . '$';
	}
	return substr($p, 0, $n);
}

function modify_uac_param($uac, $pname) {
	$pconst = constant('PLEXCEL' . substr($pname, 1));
	$tmp = plexcel_get_param($pname);
	if ($tmp && strcasecmp($tmp, 'on') == 0) {
		$uac |= $pconst;
	} else {
		$uac &= ~$pconst;
	}
	return $uac;
}

function _is_alnum($ch) {
	return strchr('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $ch) != FALSE;
}
function sAMAccountName_flatten($name) {
	global $invalid_sam_chars;

	$ret = '';
	$nlen = strlen($name);
	$prev = '';
	for ($ni = 0; $ni < $nlen; $ni++) {
		$ch = $name[$ni];
		if (strchr($invalid_sam_chars, $ch) || _is_alnum($ch) == FALSE) {
			$ch = '_';
		}
		if ($ch != '_' || $prev != '_') {
			$ret .= $ch;
			$prev = $ch;
		}
	}
	return $ret ? strtolower($ret) : FALSE;
}
function gen_names_from_dn($dn) {
	// determine diplayName
	$p1 = strpos($dn, '=') + 1;
	$p2 = strpos($dn, '=', $p1);
	$displayName = substr($dn, $p1, $p2 - $p1);
	$p2 = strrpos($displayName, ',');
	$displayName = str_replace('\\', '', substr($displayName, 0, $p2));

	// determine sn and givenName (also, fixup dn)
	$p1 = strpos($displayName, ',');
	if ($p1) {
		$sn = substr($displayName, 0, $p1);
		$givenName = trim(substr($displayName, $p1 + 1));
		$p1 = strpos($dn, ',');
		if ($dn[$p1 - 1] != '\\')
			$dn = substr($dn, 0, $p1) . '\\' . substr($dn, $p1);
	} else {
		$givenName = $sn = $displayName;
	}

	// create sAMAccountName
	$sAMAccountName = sAMAccountName_flatten($displayName);

	return array($dn, $sAMAccountName, $displayName, $givenName, $sn);
}

if ($is_authenticated) {

	plexcel_set_conv_attrdefs($px);

	if ($operator == NULL) {
		$acct = plexcel_get_account($px, NULL, PLEXCEL_SUPPLEMENTAL);
		$operator = $acct['userPrincipalName'];
	}

	/* Deactivate reposted form data (e.g. reload)
	 */
	if (plexcel_token_matches('p_tok') == FALSE) {
		switch ($action) {
			case 'create_account':
				$action = 'vcreate';
				break;
			case 'edit':
				$action = 'vedit';
				break;
			case 'add_spn':
			case 'delete_spn':
			case 'set_uac':
				$action = 'vedit';
				break;
			default:
				$action = 'default';
		}
	}

	/* If the default account can be retrieved successfully
	 * using the non-authenticated binding, then the current
	 * keytab must be valid. That is a the focal account to
	 * work with.
	 */
	$ktexists = file_exists("$plexcel_home/plexcel.keytab");
	$ktacct = $ktexists ? plexcel_get_account($pxn, NULL, NULL) : NULL;
	$tmpktexists = file_exists("$plexcel_home/tmp/plexcel.keytab");

	if ($action == 'create_account') {
		$servicePrincipalName = plexcel_get_param('p_spn');

		if ($distinguishedName == NULL || $servicePrincipalName == NULL) {
			$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
		} else {
			$names = gen_names_from_dn($distinguishedName);

			if (strlen($names[1]) > 20) {
				$msg = 'The account name (which is derived from the first CN of the DN below) is limited to 20 characters. Please use a shorter name.';
				$action = 'vcreate';
			} else {
				$distinguishedName = $names[0];

				$uac = $default_uac;
				$uac = modify_uac_param($uac, 'p_TRUSTED_FOR_DELEGATION');
				$uac = modify_uac_param($uac, 'p_USE_DES_KEY_ONLY');
				$realm = strtolower(substr(strchr(plexcel_get_authority($px, FALSE), '.'), 1));
				$userPrincipalName = $names[1] . "@$realm";

				$_acct = array('objectClass' => array('user'),
						'distinguishedName' => $distinguishedName,
						'sAMAccountName' => $names[1],
						'givenName' => $names[3],
						'sn' => $names[4],
						'servicePrincipalName' => array($servicePrincipalName),
						'userPrincipalName' => $userPrincipalName,
						'userAccountControl' => $uac,
						'description' => array('Plexcel HTTP SSO service account'));
				if (plexcel_add_object($px, $_acct, NULL) == FALSE) {
					$err .= "An unexpected Plexcel error occured trying to create the account $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
				} else {
					$action = 'vedit';
					$msg = 'The account was created successfully. <b>The password must now be reset</b>.';
				}
			}
		}
	}
	if ($action == 'set_uac') {
		if ($distinguishedName == NULL) {
			$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
		} else {
			$_acct = plexcel_get_account($px, $distinguishedName, array('userAccountControl'));
			if (is_array($_acct) == FALSE) {
				$err .= "An unexpected Plexcel error occured trying to retrieve account for $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
			} else {
				$uac = 0;
				if (isset($_acct['userAccountControl']))
					$uac = $_acct['userAccountControl'];

				$uac = modify_uac_param($uac, 'p_ACCOUNTDISABLE');
				$uac = modify_uac_param($uac, 'p_LOCKOUT');
				$uac = modify_uac_param($uac, 'p_TRUSTED_FOR_DELEGATION');
				$uac = modify_uac_param($uac, 'p_USE_DES_KEY_ONLY');

				$_acct['userAccountControl'] = $uac;
				if (plexcel_modify_object($px,
								$_acct,
								array('userAccountControl')) == FALSE) {
					$err .= "<p/>Plexcel error: <pre>" . plexcel_status($px) . "</pre>";
				} else {
					$action = 'vedit';
					$msg = 'The account options were successfully modified.';
				}
			}
		}
	}
	if ($action == 'delete_account') {
		if ($distinguishedName == NULL) {
			$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
		} else {
			if (plexcel_delete_object($px, $distinguishedName) == FALSE) {
				$err .= "<p/>An unexpected Plexcel error occured trying to delete the account $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
			} else {
				if ($ktacct && $distinguishedName == $ktacct['distinguishedName'])
					$ktacct = NULL;
				$action = 'default';
			}
		}
	}
	if ($action == 'add_spn' || $action == 'delete_spn') {
		$_spn = plexcel_get_param('p_spn', NULL);
		if ($distinguishedName == NULL || $_spn == NULL) {
			$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
		} else {
			$_acct = plexcel_get_account($px,
						$distinguishedName,
						array('servicePrincipalName'));
			if (is_array($_acct) == FALSE) {
				$err .= "<p/>An unexpected Plexcel error occured trying to retrieve account for $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
			} else if (count($_acct) == 0) {
				$err .= "<p/>Plexcel failed to retrieve account for $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
			} else {
				$_acct['servicePrincipalName'] = array($_spn);
				$_attrs = array('servicePrincipalName' =>
						$action == 'delete_spn' ? PLEXCEL_MOD_DELETE : PLEXCEL_MOD_ADD);
				if (plexcel_modify_object($px, $_acct, $_attrs) == FALSE) {
					$err .= "<p/>An unexpected Plexcel error occured trying to modify the account for $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
				} else {
					$action = 'vedit';
					$msg = "<p/>The SPN $_spn has been successfully modified. <b>You must reset the password</b> after updating SPNs.";
				}
			}
		}
	}
	if ($action == 'set_password') {
		$spassword = plexcel_get_param('p_spassword');
		$_is_rand_pass = plexcel_get_param('p_is_rand_pass');
		if ($_is_rand_pass && strcasecmp($_is_rand_pass, 'on') == 0)
			$spassword = randpass(32);

		if ($distinguishedName == NULL || $spassword == NULL) {
			$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
		} else {
			$_acct = plexcel_get_account($px,
						$distinguishedName,
						array('userPrincipalName'));
			if (is_array($_acct) == FALSE) {
				$err .= "<p/>An unexpected Plexcel error occured trying to retrieve account for $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
			} else if (isset($_acct['userPrincipalName']) == FALSE) {
				$err .= "<p/>No userPrincipalName is set for account $distinguishedName.";
			} else {
				$_is_set_in_dir = plexcel_get_param('p_is_set_in_dir');
				$_is_set_in_dir = $_is_set_in_dir && strcasecmp($_is_set_in_dir, 'on') == 0;

				$userPrincipalName = $_acct['userPrincipalName'];
				if ($_is_set_in_dir && plexcel_set_password($px, $userPrincipalName, $spassword) == FALSE) {
					$err .= "<p/>An unexpected Plexcel error occured trying to set the password for the account $userPrincipalName: <pre>" . plexcel_status($px) . "</pre>";
				} else {
					$ret = plexcel_gen_service_keytab($px, $userPrincipalName, $spassword, NULL);
					if ($ret == FALSE) {
						$msg = NULL;
						$err .= "An unexpected Plexcel error occured trying to create the service keytab file for the account $userPrincipalName: <pre>" . plexcel_status($px) . "</pre>";
					} else {
						$msg = 'The password for this account has been set successfully. To begin using the account, <b>the web server must now be restarted</b>. Additionally, any clients that have recently authenticated with this server will need to logoff and back on (or purge their tickets with kerbtray.exe).';
						$action = 'vedit';
					}
				}
			}
		}
	}
	if ($action == 'default' || $action == 'search' || $action == 'vedit' || $action == 'edit') {

		// Search for an existing account(s) that has the target SPN

		// The base was specified as an option to plexcel_preamble
		$_params = array('scope' => 'sub',
				'filter' => "(servicePrincipalName=$spn)");
		$spnmatch = plexcel_search_objects($px, $_params);
		if (is_array($spnmatch) == FALSE) {
			$err .= "<p/>An unexpected Plexcel error occured searching for accounts with the target SPN of $spn: <pre>" . plexcel_status($px) . '</pre>';
		}

		if (count($spnmatch) > 1) {
			$err .= "<p/>There are multiple service accounts with the target SPN of <i>$spn</i>. Active Directory will not issue tickets for an SPN that is set on multiple accounts.";
		}

		if ($action == 'vedit' || $action == 'edit') {

			if ($distinguishedName == NULL) {
				$err .= '<p/>Plexcel Setup did not receive all required POST parameters.';
			} else {
				$eacct = plexcel_get_account($px, $distinguishedName, NULL);
				if (is_array($eacct) == FALSE) {
					$err .= "<p/>An unexpected Plexcel error occured getting account $distinguishedName: <pre>" . plexcel_status($px) . "</pre>";
				}

				if (isset($eacct['userAccountControl'])) {
					$uac = $eacct['userAccountControl'];
					if ($uac & PLEXCEL_ACCOUNTDISABLE)
						$msg .= '<p/>This account is disabled.';
					if ($uac & PLEXCEL_LOCKOUT)
						$msg .= '<p/>This account is locked out.';
					if (($uac & PLEXCEL_DONT_EXPIRE_PASSWORD) == 0)
						$msg .= '<p/>This account\'s password can expire.';
				}

				$has_pass = $ktacct &&
							$ktacct['distinguishedName'] == $distinguishedName;
				$has_spn = isset($eacct['servicePrincipalName']) &&
							in_array($spn, $eacct['servicePrincipalName']);

				if ($has_pass && $has_spn) {
					$msg .= '<p/>This service account appears to be correct.';
				} else {
					if ($has_pass) {
						$msg .= '<p/>This service account appears to have a valid password.';
					} else if ($tmpktexists) {
						$msg .= '<p/>A password has been reset but the web server must be restarted to read it.';
					} else {
						$msg .= '<p/>This service account does not appear to have a valid password.';
					}
					if ($has_spn) {
						$msg .= "<p/>This service account has the required SPN <i>$spn</i>.";
					} else {
						$err .= "<p/>This service account does not have the required SPN <i>$spn</i>.";
					}
				}
			}
		} else {
			if (count($spnmatch) == 0) {
				$msg .= "<p/>No accounts with the target SPN of <i>$spn</i> were " .
							"found. Please select one of the options below.";
			} else if (count($spnmatch) == 1) {
				if ($tmpktexists) {
					$msg .= '<p/>A password has been reset but the web server must be restarted to read it.';
				}
				if ($ktacct) {
					if (isset($ktacct['servicePrincipalName']) &&
								in_array($spn, $ktacct['servicePrincipalName'])) {
						$msg .= '<p/>The service account <i>' .
									$ktacct['distinguishedName'] .
									'</i> appears to be correct.';
					} else {
						$msg .= '<p/>The service account <i>' .
									$ktacct['distinguishedName'] .
									'</i> appears to have a valid password but it does not have the required SPN.';
					}
				} else if ($ktexists) {
					$msg .= '<p/>The current password appears to be invalid.';
				} else {
					$spnacct = $spnmatch[0];
					$sdn = $spnacct['distinguishedName'];
					$has_pass = $sdn == $distinguishedName;
					$has_spn = isset($spnacct['servicePrincipalName']) &&
								in_array($spn, $spnacct['servicePrincipalName']);

					if ($has_pass) {
						$msg .= "<p/>The service account <i>$sdn</i> appears to have a valid password.";
					} else if ($tmpktexists == FALSE) {
						$msg .= "<p/>The service account <i>$sdn</i> does not appear " .
								"to have a valid password.";
					}
					if ($has_spn) {
						$msg .= "<p/>The service account <i>$sdn</i> has the required SPN.";
					} else {
						$msg .= "<p/>The service account <i>$sdn</i> does not have the required SPN.";
					}
				}
			}
			if ($locale === 'C') {
				$msg .= '<p/>This web server appears to be running in the \'C\' locale. The web server must run in a UTF-8 locale for Plexcel to function properly with non-ASCII characters. Please read the I18N section in the Plexcel Operator\'s Manual.';
			}
		}

		if ($action == 'search') {
			$search_expr = plexcel_get_param('p_search_expr', '');
			$cc = count_chars($search_expr);
			if ($cc[ord('*')] == strlen($search_expr)) {
				$err .= '<p/>The search expression must contain at least one character.';
			} else {
				$search_expr = str_replace('(', '\\28', $search_expr);
				$search_expr = str_replace(')', '\\29', $search_expr);
				$search_expr = str_replace('\\', '\\5c', $search_expr);
				$search_expr = str_replace('/', '\\2f', $search_expr);
				$_params = array('scope' => 'sub',
					'filter' => "(&(objectClass=user)(!(objectClass=computer))(cn=$search_expr))");
				$saccts = plexcel_search_objects($px, $_params);
				if (is_array($saccts) == FALSE) {
					$err .= 'An unexpected Plexcel error occured searching accounts: <pre>' .
							plexcel_status($px) . '</pre>';
				}
			}
		}

		$_params = array('scope' => 'sub',
					'filter' => '(|(cn=http_sso_*)(cn=host_*))');
		$possible = plexcel_search_objects($px, $_params);

		if (is_array($spnmatch)) {
			foreach ($spnmatch as $idx => $_acct) {
				if ($ktacct['distinguishedName'] == $_acct['distinguishedName']) {
					unset($spnmatch[$idx]);
				}
			}
		}
		if (is_array($possible)) {
			foreach ($possible as $idx => $_acct) {
				if ($ktacct['distinguishedName'] == $_acct['distinguishedName']) {
					unset($possible[$idx]);
				} else {
					foreach ($spnmatch as $_sacct) {
						if ($_sacct['distinguishedName'] == $_acct['distinguishedName']) {
							unset($possible[$idx]);
							break;
						}
					}
				}
			}
		}
	}
	if ($action == 'vcreate') {
		if ($distinguishedName == NULL) {
			// Try to guess a suitable DN by
			// looking at existing non-computer accounts with servicePrincipalNames
			// (preferrably ones that begin with HTTP/).

			$_params = array('scope' => 'sub',
					'filter' => '(&(objectClass=user)' .
							'(!(objectClass=computer))' .
							'(servicePrincipalName=*))');
			$objs = plexcel_search_objects($px, $_params);
			if (is_array($objs) == FALSE) {
				$err .= 'An unexpected Plexcel error occured searching accounts: <pre>' .
							plexcel_status($px) . '</pre>';
			} else {
				$_suffix = ',OU=ABC,DC=example,DC=com';
				$distinguishedName = $_suffix;
				foreach ($objs as $_acct) {
					$distinguishedName = $_acct['distinguishedName'];
					$_spn = $_acct['servicePrincipalName'][0];
					if (strncasecmp($_spn, 'HTTP/', 5) == 0) {
						break;
					}
				}
				$_i = 0;
				$_p = strpos($distinguishedName, ',CN=');
				if (!$_p)
					$_p = strpos($distinguishedName, ',OU=');
				if ($_p)
					$_suffix = substr($distinguishedName, $_p);

				$name = substr($hostname, 0, strpos($hostname, '.'));
				do {
					$sAMAccountName = "http_sso_$name";
					$limit = $_i == 0 ? 20 : 18;
					if (strlen($sAMAccountName) > $limit)
						$sAMAccountName = substr($sAMAccountName, 0, $limit);
					if ($_i > 0)
						$sAMAccountName .= "_$_i";
					$_i++;
					$distinguishedName = "CN=$sAMAccountName$_suffix";
					$_acct = plexcel_get_account($px,
								$distinguishedName,
								array("servicePrincipalName"));
				} while ($_acct);
			}
		}
	}
}

// END WORK / START OUTPUT

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="style.css?v=1">
<script type="text/javascript">
function act(a) {
	document.setup.p_action.value = a;
	document.setup.submit();
}
function delete_account() {
	var doit = confirm("Are you certain that you want to delete the following account?\n\n" + document.setup.p_distinguishedName.value);
	if (doit) {
		document.setup.p_action.value = 'delete_account';
		document.setup.submit();
	}
}
function password_onfocus() {
	frm = document.setup;
	frm.p_is_rand_pass.checked = false;
	frm.p_spassword.style.background = "#ffffff";
	frm.p_spassword2.style.background = "#ffffff";
}
function is_rand_pass_onclick() {
	frm = document.setup;
	is_rand_pass = frm.p_is_rand_pass;
	if (is_rand_pass.checked) {
		frm.p_is_set_in_dir.checked = true;
		frm.p_spassword.style.background = "#eeeeee";
		frm.p_spassword2.style.background = "#eeeeee";
	} else {
		frm.p_spassword.style.background = "#ffffff";
		frm.p_spassword2.style.background = "#ffffff";
	}
}
function is_set_in_dir_onclick() {
	frm = document.setup;
	is_rand_pass = frm.p_is_rand_pass;
	is_set_in_dir = frm.p_is_set_in_dir;
	is_rand_pass.checked = is_set_in_dir.checked;
	is_rand_pass_onclick();
}
function set_password() {
	if (document.setup.p_spassword.value !=
				document.setup.p_spassword2.value) {
		alert("Password mismatch. Please reenter and try again.");
		document.setup.p_spassword.value = '';
		document.setup.p_spassword2.value = '';
		return;
	}
	act('set_password');
}
</script>
</head>
<body>
<form name="setup" method="POST" style="margin: 0px; padding: 0px;">
<table border="0" width='600'>

<?php
echo "<input type='hidden' name='p_action' value='$action'/>\n";
echo "<input type='hidden' name='p_tok' value='" . plexcel_token('p_tok') . "'/>\n";

$pff = new PlexcelFieldFormatter();

echo "<tr><td colspan='2' nowrap><span class='ps'>Plexcel Setup</span></td>\n";

if ($authority == NULL) {

	// allows changing DC without loosing logon
//	echo "<input type=\"hidden\" name=\"p_username\" value=\"$username\"/>\n";

	echo "<td colspan='2'/></tr>\n";
	echo "<tr><td colspan='4' style='background-color: #e0e0e0'></td>\n";

	if ($err)
		echo "<tr><td colspan='4'><span class='err'>$err</span></td></tr>";
	if ($msg)
		echo "<tr><td colspan='4'><span class='blue'>$msg</span></td></tr>";

	echo "<tr><td colspan='4'>Please enter the Windows DNS domain (e.g. example.com) or the fully qualified domain name of a specific domain controller (e.g. dc1.example.com) that is authoritative for the HTTP service account being queried or created.</td></tr>";

	$authority = plexcel_get_param('p_authority');
	echo $pff->toxml($authority, 'authority', 0, array('label' => 'DC', 'size' => 20));
	echo "<tr><td width='150'></td><td><input type='submit' class='button' value='Find DC'/></td><td></td><td></td></tr>\n";

} else if ($is_authenticated == FALSE) {

	// maintain authority while trying logon
	echo "<input type=\"hidden\" name=\"p_authority\" value=\"$authority\"/>\n";

	echo "<td colspan='2' style='vertical-align: top; text-align: right; white-space: nowrap;'><small>Domain Controller: <a href=\"javascript:act('change_authority')\">$authority</a><br/>\n";
	echo "</small></td></tr>\n";

	echo "<td colspan='2'/></tr>\n";
	echo "<tr><td colspan='4' style='background-color: #e0e0e0'></td>\n";

	if ($err)
		echo "<tr><td colspan='4'><span class='err'>$err</span></td></tr>";

	echo "<tr><td colspan='4'>Please enter your Windows credentials. If you will be creating or modifying a service account in the directory, you will need to supply credentials that have appropriate privileges. The username must be specified in UPN form (e.g. alice@example.com).</td></tr>";

	if ($username == NULL) {
		$username = "alice@example.com";
	}
	echo $pff->toxml($username, 'username', 0, array('label' => 'Username', 'size' => 20));
	echo $pff->toxml('', 'password', 0, array('label' => 'Password', 'size' => 20, 'type' => 'password'));
	echo "<tr><td width='150'></td><td><input type='submit' class='button' value='Logon'/></td><td colspan='2'/></tr>\n";

} else {

	// change to directory authority
	$authority = plexcel_get_authority($px, FALSE);

	// maintain authority and username at all times
	echo "<input type='hidden' name='p_authority' value='$authority'/>\n";
	echo "<input type='hidden' name='p_username' value='$username'/>\n";
	if ($action != 'vcreate')
		echo "<input type='hidden' name='p_distinguishedName' value='$distinguishedName'/>\n";

	echo "<td colspan='2' style='vertical-align: top; text-align: right; white-space: nowrap;'><small>Domain Controller: <a href=\"javascript:act('change_authority')\">$authority</a><br/>\n";
	if ($is_authenticated) {
		echo "Operator: <a href=\"javascript:act('default');\">$operator</a>";
	}
	echo "</small></td></tr>\n";

	echo "<tr><td colspan='2' class='bluebar' style='text-align: left; padding-left: 5px;'>IOPLEX Software</td>\n";
	echo "<td colspan='2' class='bluebar'>\n";
	echo "<a href=\"javascript:act('default')\">Options</a> | ";
	echo "<a href=\"examples/index.php\">Examples</a> | ";
	echo "<a href=\"javascript:act('$action');\">Refresh</a> | ";
	if ($is_authenticated) {
		echo "<a href=\"javascript:act('logoff');\">Logoff</a>";
	}
	echo "</td></tr>\n";

	if ($err)
		echo "<tr><td colspan='4'><span class='err'>$err</span></td></tr>";
	if ($msg)
		echo "<tr><td colspan='4'><span class='blue'>$msg</span></td></tr>";

	if ($action == 'default' || $action == 'search') {
		echo "<tr><td colspan='4'>\n";
		echo "<ul type='square'>\n";

		if ($ktacct) {
			$dn = str_replace('\\', '\\\\', htmlesc($ktacct['distinguishedName']));
			echo "<li><a href=\"javascript:document.setup.p_distinguishedName.value='".
						$dn . "';act('vedit')\">" .
						$ktacct['distinguishedName'] . "</a><br/>\n" .
			"<small>This account appears to have a valid password.</small>\n";
		}

		if (is_array($spnmatch)) {
			foreach ($spnmatch as $_acct) {
				$dn = str_replace('\\', '\\\\', htmlesc($_acct['distinguishedName']));
				echo "<li><a href=\"javascript:document.setup.p_distinguishedName.value='" .
						$dn . "';act('vedit')\">" .
						$_acct['distinguishedName'] . "</a><br/>\n" .
						"<small>This account has the required SPN.</small>\n";
			}
			echo "</ul><ul type='square'>\n";
		}

		echo "<li><a href=\"javascript:document.setup.p_distinguishedName.value='';act('vcreate')\">Create a new account</a>\n";
		echo "<li>Search existing accounts by name:<br/>\n";
		$search_expr = plexcel_get_param('p_search_expr', 'http_sso_*');
		echo "<input name='p_search_expr' type='text' size='40' value='$search_expr'/><br/>\n";
		echo "<small>Enter CN with optional wildcard.</small><br/>\n";

		echo "<input type='button' class='button' value='Search Accounts' onClick=\"act('search')\"/>";

		echo "</ul>\n";

		if ($action == 'search') {
			if (count($saccts) > 0) {
				echo '<p/>' . count($saccts) . " entries found:\n";
				echo "<ol>\n";
				foreach ($saccts as $_acct) {
					$dn = str_replace('\\', '\\\\', htmlesc($_acct['distinguishedName']));
					echo "<li><a href=\"javascript:document.setup.p_distinguishedName.value='" .
						$dn . "';act('vedit')\">" .
						$_acct['distinguishedName'] . "</a>\n";
				}
				echo "</ol>\n";
			} else {
				echo "No entries found.\n";
			}
		}

		if (is_array($possible) && count($possible) > 0) {
			echo '<p/>The following accounts look like HTTP service accounts.';

			echo "<ul type='square'>\n";
			foreach ($possible as $_acct) {
				$dn = str_replace('\\', '\\\\', htmlesc($_acct['distinguishedName']));
				echo "<li><a href=\"javascript:document.setup.p_distinguishedName.value='" .
						$dn . "';act('vedit')\">" .
						$_acct['distinguishedName'] . "</a>\n";
			}
			echo "</ul>\n";
		}

		echo "</td></tr>";
	}
	if ($action == 'vedit') {
		$acct = $eacct;
		echo "<tr><td></td><td></td><td></td><td></td></tr>\n";
		echo "<tr><td><h2>" . $acct['cn'] . "</h2></td>\n";
		echo "<td style='text-align: right;' colspan='3'>" . $acct['distinguishedName'] . "</td></tr>\n";

		// LEFT COLUMN
		echo "<tr><td colspan='2' style='vertical-align: top;'><table border='0'>\n";

		echo "<tr><td colspan='4'><h3 class='blueline'>Service Principal Names</h3></td></tr>\n";

		if (isset($acct['servicePrincipalName'])) {
			echo "<tr><td colspan='4'><table border='0' width='100%'>\n";
			foreach($acct['servicePrincipalName'] as $si => $_spn) {
				if ($_spn == $spn)
					$spn = '';
				echo "<tr><td>$_spn</td><td style='text-align: left;'><a href=\"javascript:document.setup.p_spn.value='" . htmlesc($_spn) . "';act('delete_spn')\"><small>Delete</small></a></td></tr>\n";
			}
			echo "</table></td></tr>\n";
		}

		echo $pff->toxml($spn, 'spn', 0, array('label' => ''));
		echo "<tr><td/><td><input type='submit' class='button' value='Add SPN' onClick=\"document.setup.p_action.value='add_spn'\"/></td><td colspan='2'/></tr>\n";

		echo "<tr><td colspan='4'><h3 class='blueline'>Account Options</h3></td></tr>\n";

		echo "<tr><td colspan='4'><table border='0'>\n";
		echo $pff->toxml($acct, 'userAccountControl', 0, array('uacflag' => PLEXCEL_ACCOUNTDISABLE));
		echo $pff->toxml($acct, 'userAccountControl', 0, array('uacflag' => PLEXCEL_LOCKOUT));
		echo $pff->toxml($acct, 'userAccountControl', 0, array('uacflag' => PLEXCEL_TRUSTED_FOR_DELEGATION));
		echo $pff->toxml($acct, 'userAccountControl', 0, array('uacflag' => PLEXCEL_USE_DES_KEY_ONLY));
		echo "</table></td></tr>\n";
		echo "<tr><td/><td colspan='3'><input type='submit' class='button' value='Save' onClick=\"document.setup.p_action.value='set_uac'\"/></td></tr>\n";

		// RIGHT COLUMN
		echo "</table></td><td colspan='2' style='vertical-align: top;'><table border='0'>\n";

		echo "<tr><td colspan='4'><h3 class='blueline'>Password</h3></td></tr>\n";

		echo $pff->toxml($acct, 'pwdLastSet', PFF_NOINPUT | PFF_TIME);

		echo "<tr><td colspan='4'><table border='0'>\n";
		echo "<tr><td><input name='p_is_rand_pass' " .
			"type='checkbox' checked onClick='is_rand_pass_onclick()'" .
			"/></td><td>Generate a long random password (recommended).</td></tr>\n";
		echo "<tr><td><input name='p_is_set_in_dir' " .
			"type='checkbox' checked onClick='is_set_in_dir_onclick()'" .
			"/></td><td>Set password in Active Directory.</td></tr>\n";
		echo "</table>\n";
		$_pa = array('label' => 'Password',
						'type' => 'password',
						'size' => 20,
						'onFocus' => 'password_onfocus()',
						'style' => 'background-color: #eeeeee;');
		echo $pff->toxml('', 'spassword', 0, $_pa);
		$_pa['label'] = 'Password again';
		echo $pff->toxml('', 'spassword2', 0, $_pa);
		echo "<tr><td/><td colspan='3'><input type='button' class='button' value='Set Password' onClick=\"set_password()\"/></td></tr>\n";

		echo "<tr><td colspan='4'><h3 class='blueline'>Delete Account</h3></td></tr>\n";

		echo "<tr><td colspan='4'>Clicking the below button will completely delete this account from the directory.</td></tr>\n";
		echo "<tr><td/><td colspan='3'><input type='button' class='button' value='Delete Account' onClick=\"delete_account()\"/></td></tr>\n";

		echo "</table></td></tr>";
	}
	if ($action == 'vcreate') {
		echo "<tr><td colspan='4'><table border='0'><tr><td>\n";

		echo "<tr><td colspan='4'><h3 class='blueline'>Create HTTP Service Account</h3></td></tr>\n";

		echo "<tr><td colspan='4'>To transparently authenticate clients using Single Sign-On, Plexcel requires an HTTP service account for this host. Use the form below to create this account.</td></tr>\n";

		echo "<tr><td colspan='4' style='height: 20px;'/></tr>\n";

		echo $pff->toxml($distinguishedName, 'distinguishedName', 0,
					array('label' => 'Distinguished Name', 'size' => 70));
		echo $pff->toxml($spn, 'spn', PFF_READONLY,
					array('label' => 'Service Principal Name'));
		echo $pff->toxml($default_uac, 'userAccountControl', 0, array('uacflag' => PLEXCEL_TRUSTED_FOR_DELEGATION));
		echo $pff->toxml($default_uac, 'userAccountControl', 0, array('uacflag' => PLEXCEL_USE_DES_KEY_ONLY));
		echo "<tr><td/><td colspan='3'><input type='button' class='button' value='Create Account' onClick=\"act('create_account')\"/></td></tr>\n";

		echo "</td></tr></table></td></tr>\n";
	}
}

/*
echo "<tr><td colspan='4'>Preameble: <pre>";
print_r($preamble);
echo "</td></tr></pre>";

echo "<tr><td colspan='4'>POST parameters: <pre>";
print_r($_POST);
echo "</td></tr></pre>";
*/

?>

<tr><td colspan="4">
<div class="tail">
&#169; 2010 IOPLEX Software |
<a href="http://www.ioplex.com/support.html">Contact Us</a>
</div></td></tr>

</form>
</table>
</body></html>
