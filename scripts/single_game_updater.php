<?php
require("dbstuff.php");
include("status_codes.php");

date_default_timezone_set($_GET['timezone']);

if (date(I) == 1) { 
	$time_start = date(P)-"1:00";
	$time_offset = $time_start . ":00";
	$last_updated = date('m/d H:i:s', time()-3600);
} else {
	$time_offset = date(P);
	$last_updated = date('m/d H:i:s');
}

$juris_select = mysql_real_escape_string($_GET['juris']);	
$location_select = mysql_real_escape_string($_GET['loc']);
$game_select = mysql_real_escape_string($_GET['game']);

$sql = "SELECT status, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen, escrow FROM floorLocations WHERE juris='" . $juris_select . "' AND id='" . $location_select . "' AND game_id='" . $game_select . "' LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");

extract(mysql_fetch_assoc($result));
$game['status'] = $status_codes[$status]['status'];
$game['style'] = $status_codes[$status]['style'];
$game['lastseen'] = $lastseen;
$game['escrow'] = number_format(($escrow * 0.01), 2);

if ($ten != 1) { 
	$game['style'] = "red";
	$game['conn_status'] = "DISCONNECTED";
	$game['status'] = "????? Status Unknown, Location Disconnected";
	$game['conn_style'] = "red";
} else if ($five != 1) { 
	$game['conn_status'] = "DISCONNECTED";
	$game['conn_style'] = "gold";
} else { 
	$game['conn_status'] = "Connected";
	$game['conn_style'] = "black";
}

$sql = "SELECT convert_tz(lastdisconnect, 'system', '$time_offset') AS lastdisconnect FROM floorDisconnectLog WHERE juris='" . $juris_select . "' AND id='" . $location_select . "' LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");

extract(mysql_fetch_assoc($result));
$game['lastdisconnect'] = $lastdisconnect;

$sql = "SELECT * FROM floorSuperhelps WHERE id=$location_select AND juris=$juris_select LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
extract(mysql_fetch_assoc($result));

$game['server'] = $server;
if ($guidemon == 0) { $game['guidemon'] = "Running"; } else { $game['guidemon'] = "<span class='red'>NOT RUNNING</span>"; }
if ($resultserver == 0) { $game['resultserver'] = "Running"; } else { $game['resultserver'] = "<span class='red'>NOT RUNNING</span>"; }
if ($machtcp == 0) { $game['machtcp'] = "Running"; } else { $game['machtcp'] = "<span class='red'>NOT RUNNING</span>"; }
if ($ips == 0) { $game['ips'] = "OK"; } else { $game['ips'] = "<span class='red'>OUT OF IPS</span>"; }
if ($raid == 0) { $game['raid'] = "Running"; } else { $game['raid'] = "<span class='red'>NOT RUNNING</span>"; }
if ($drbd == 0) { $game['drbd'] = "Running"; } else { $game['drbd'] = "<span class='red'>NOT RUNNING</span>"; }
if ($power == 0) { $game['power'] = "Running"; } else { $game['power'] = "<span class='red'>NOT RUNNING</span>"; }
if ($percentdown < 30) { $game['percentdown'] = $percentdown.'%'; } else { $game['percentdown'] = "<span class='red'>".$percentdown."%</span>"; }
$game['load5s'] = $load5s;
$game['load5m'] = $load5m;
$game['load20m'] = $load20m;
if ($spaceboot < 95) { $game['spaceboot'] = $spaceboot.'%'; } else { $game['spaceboot'] = "<span class='red'>".$spaceboot."%</span>"; }
if ($spaceaav < 95) { $game['spaceaav'] = $spaceaav.'%'; } else { $game['spaceaav'] = "<span class='red'>".$spaceaav."%</span>"; }
$game['serverlastseen'] = $lastseen;

echo "{\"game\": " . json_encode($game) . "}";
?>