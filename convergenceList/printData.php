<?php 

$array = Array();

$array[] = $_GET['array'];
$body = "<table width='30%'>";

$data = json_decode ( $_GET ['array'] );

foreach ( $data as $name => $value ) 
{
	foreach ( $value as $entry ) 
	{
		$body .= "<tr><td>";
		$body .= $entry->value;
		$body .= "</td></tr>";
	}
}
$body .= "</table>";
print($body);
die;

?>