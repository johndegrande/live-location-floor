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

if (isset($_GET['juris'])) {
	$juris_select = mysql_real_escape_string($_GET['juris']);	
}

$vendor_select = '';
$location_select = '';

if (isset($_GET['vendor'])) {
	$vendors = preg_split("/,/", $_GET['vendor']);
	for ($i=0;$i<sizeof($vendors);$i++) {
		if ($i == 0) {
			$vendor_select = " AND (vendor = '" . $vendors[$i] . "'";
		} else {
			$vendor_select .= " OR vendor = '" . $vendors[$i] . "'";
		}
	}
	$vendor_select .= ")";
}

if (isset($_GET['loc']) and is_numeric($_GET['loc'])) {
	$get_location = mysql_real_escape_string($_GET['loc']);
	$location_select = " AND id='$get_location' ";
}

$sql = "SELECT id, game_id, game_name, game_type, status, chippies, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen, escrow FROM floorLocations WHERE juris='" . $juris_select . "'" . $vendor_select . '' . $location_select . " ORDER BY name, game_name, game_id";
$result = mysql_query($sql) or die ("Couldn't execute query.");

$sql = "SELECT id, needsnc, needswap, needshelp FROM floorSuperhelps WHERE juris='" . $juris_select . "'";
$result_needs = mysql_query($sql);
while($row = mysql_fetch_assoc($result_needs)) {
	extract($row);
	$needs[$id]['nc'] = $needsnc;
	$needs[$id]['wap'] = $needswap;
	$needs[$id]['help'] = $needshelp;
}

$i=1; 
while($row = mysql_fetch_assoc($result)) {
	extract($row);
	$game['id'] = $id;
	$game['game_id'] = $game_id;
	$game['status'] = $status_codes[$status]['status'];
	$game['style'] = $status_codes[$status]['style'];
	$game['lastseen'] = $lastseen;
	$game['lastseen_short'] = date('m/d H:i', strtotime($lastseen));
	$game['game_type'] = $game_type;
	$game['escrow'] = number_format(($escrow * 0.01), 2);
	
	$sql_disconnect = "SELECT lastdisconnect FROM floorDisconnectLog WHERE juris='" . $juris_select . "' AND id='" . $game['id'] . "' LIMIT 1";
	$result_disconnect = mysql_query($sql_disconnect) or die ("Couldn't execute query.");
	extract(mysql_fetch_assoc($result_disconnect));
	$game['last_disconnect'] = $lastdisconnect;
	
	if ($ten != 1) { 
		$game['conn_style'] = "red";
		$game['conn_status'] = "DISCONNECTED";
		$game['status'] = "????? Status Unknown Location Disconnected";
		$game['style'] = "red";
	} else if ($five != 1) { 
		$game['conn_style'] = "gold";
		$game['conn_status'] = "DISCONNECTED";
	} else { 
		$game['conn_style'] = "black"; 
		$game['conn_status'] = "Connected";
	}
	
	$game['needsnc'] = $needs[$id]['nc'];
	$game['needswap'] = $needs[$id]['wap'];
	$game['needshelp'] = $needs[$id]['help'];
	
	$games[$i] = $game;
	$games['lastseen'] = $lastseen;
	
	$i++;
}
echo "{\"globals\": {\"game_cnt\": \"" . mysql_num_rows($result) . "\", \"update_time\": \"" . date('Y-m-d H:i:s') . "\"}, \"games\": " . json_encode($games) . "}";
?>