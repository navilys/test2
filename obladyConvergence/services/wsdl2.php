<?
$filewsdl = PATH_PLUGINS . "obladyConvergence/services" . PATH_SEP . "pmos2.wsdl";
$content = file_get_contents( $filewsdl );
$lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

$http = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$endpoint = $http . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . $lang . '/classic/obladyConvergence/services/soapWS';

$content = str_replace( "___SOAP_ADDRESS___", $endpoint, $content );

header( "Content-Type: application/xml;" );

print $content;
