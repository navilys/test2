<?php
//
// Print the currentTime attribute from the RootDSE of
// the default server.
//

//
// Connect to RootDSE of the default server.
//
$px = plexcel_new("ldap:///RootDSE", NULL);
if ($px == FALSE) {
  die("Plexcel error: <pre>" . plexcel_status(NULL) . "</pre>");
}
//
// Automatically convert to milliseconds since Jan 1, 1970 by
// explicitly setting the attribute definition with the
// PLEXCEL_CONV_TIME1970M_X_TIMEUTC conversion flag.
//
$attrdefs = array(
  "currentTime" => array(
  "type" => PLEXCEL_TYPE_TIME,
  "flags" => PLEXCEL_SINGLE_VALUED,
  "conv" => PLEXCEL_CONV_TIME1970M_X_TIMEUTC));
  if (plexcel_set_attrdefs($px, $attrdefs) == FALSE) {
    die("<pre>" . plexcel_status($px) . "</pre>");
  }
//
// Execute the search
//
$params = array('attrs' => array('currentTime'));
$objs = plexcel_search_objects($px, $params);
if (is_array($objs) == FALSE) {
  die("Plexcel error: <pre>" . plexcel_status($px) . "</pre>");
} 

if (count($objs) != 1) {
  die("No objects found!?");
}

$millis = $objs[0]['currentTime'];

$gtod = gettimeofday();
$localm = $gtod['sec'] * 1000.0 + $gtod['usec'] / 1000.0;

$ADdate  = date('M j, Y g:i:s A', $millis / 1000.0);
$WebDate = date('M j, Y g:i:s A', $localm / 1000.0);

$skew = abs($millis - $localm);

printf ( "<h2>ClockSkew</h2>\n<table>" );
printf ( "<tr><td>Active Directory Server Time:</td><td>%s</td></tr>\n", $ADdate );
printf ( "<tr><td>Linux Web Server Time:</td><td>%s</td></tr>\n", $WebDate );
printf ( "<tr><td>clock skew:</td><td>%d ms.  ( %3.1f s.) </td></tr>\n", (int)$skew, $skew / 1000 );

if ($skew > (1000 * 60 * 5)) // 5 minutes
  printf ( "<tr><td/><td>WARNING: clock skew is large.</td></tr>");
printf ("</table>" );

