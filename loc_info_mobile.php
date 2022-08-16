<?php session_start();
include("scripts/dbstuff.php");
include("scripts/status_codes.php");

if (isset($_GET['loc']) and is_numeric($_GET['loc']) and isset($_GET['game']) and is_numeric($_GET['game']) and isset($_SESSION['juris']) and is_numeric($_SESSION['juris']) and isset($_SESSION['access_level']) and isset($_GET['timezone'])) { 

date_default_timezone_set($_GET['timezone']);

if (date(I) == 1) { 
	$time_start = date(P)-"1:00";
	$time_offset = $time_start . ":00";
	$last_updated = date('m/d H:i:s', time()-3600);
} else {
	$time_offset = date(P);
	$last_updated = date('m/d H:i:s');
}

$juris_select = $_SESSION['juris'];
$location_select = mysql_real_escape_string($_GET['loc']);
$game_select = mysql_real_escape_string($_GET['game']);

$sql = "SELECT * FROM locations WHERE id=$location_select AND juris=$juris_select LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
$location_info = mysql_fetch_array($result, MYSQL_BOTH);

$sql = "SELECT vendor, id, name, city, game_id, game_name, game_type, software, config, status, chippies, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen, escrow FROM floorLocations WHERE id=$location_select AND juris=$juris_select AND game_id=$game_select LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
$game_info = mysql_fetch_array($result, MYSQL_BOTH);

$sql = "SELECT convert_tz(lastdisconnect, 'system', '$time_offset') AS lastdisconnect FROM floorDisconnectLog WHERE id=$location_select AND juris=$juris_select LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
extract(mysql_fetch_assoc($result));

$sql = "SELECT name AS vendor_name FROM floorUsers WHERE juris='$juris_select' AND vendor='$location_info[vendor]' LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
extract(mysql_fetch_assoc($result));

//game numbers
$sql_numbers = "SELECT * FROM floorNumbers WHERE juris='$juris_select' AND id='$location_select' AND game_id='$game_select' LIMIT 1";
$result_numbers = mysql_query($sql_numbers) or die ("Couldn't execute query.");
?>

<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="scripts/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="scripts/jquery.mobile.structure-1.4.3.min.css" />
<!--<link rel="stylesheet" href="scripts/jquery.mobile-1.4.3.min.css" />-->
<link rel="stylesheet" href="scripts/mobile.responsive.css" />
<link rel="stylesheet" href="scripts/status.css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<script src="scripts/jquery-2.1.1.min.js"></script>
<script src="scripts/jquery.mobile-1.4.3.min.js"></script>
<title>Location Information</title>
</head>

<script type = "text/javascript">
function update_game() {
	var updater_url = "scripts/single_game_updater.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?>&loc=<?php echo $location_select; ?>&game=<?php echo $game_select; ?>";
	
	$.getJSON(updater_url, function(data) {
		document.getElementById('time').innerHTML = data.game.lastseen;
		document.getElementById('disconnect').innerHTML = data.game.lastdisconnect;
		document.getElementById('status').innerHTML = data.game.status;
		document.getElementById('conn_status').innerHTML = data.game.conn_status;
		document.getElementById('escrow').innerHTML = data.game.escrow;
		document.body.className = data.game.style;
	});
}
</script>

<body class="<?php if ($game_info['ten'] != 1) { echo "red"; } else if ($game_info['five'] != 1) { echo "yellow"; } else { echo $status_codes[$game_info['status']]['style']; } ?>" onLoad="var singleGameRefresh = setInterval('update_game()', 30000);" style="font-family: sans-serif;">
<h2 align="center">Location Info</h2>
<p style="margin: 10px;">
<b><?php echo $location_info['name']; ?></b><br />
<?php echo $location_info['address']; ?><br />
<?php echo $location_info['city']; ?><br />
<a href="locate.php?id=<?php echo $location_select; ?>&juris=<?php echo $juris_select; ?>" target="_blank"><img src="images/btn_map.png" border="0" /></a>
</p>
<p style="margin: 10px;"><b>Server ID</b><br />
<span style="margin-left: 10px;"><?php echo $location_info['id']; ?></span></p>
<p style="margin: 10px;"><b>Vendor</b><br />
<span style="margin-left: 10px;"><?php echo $vendor_name; ?></span></p>
<p style="margin: 10px;"><b>Internet Connection Status</b><br />
<span style="margin-left: 10px;" id="conn_status"><?php if ($game_info['ten'] != 1) { echo "DISCONNECTED"; } else if ($game_info['five'] != 1) { echo "DISCONNECTED"; } else { echo "Connected"; } ?></span></p>
<p style="margin: 10px;"><b>Last Connected</b><br />
<span style="margin-left: 10px;" id="time"><?php echo $game_info['lastseen']; ?></span></p>
<p style="margin: 10px;"><b>Last Disconnect</b><br />
<span style="margin-left: 10px;" id="disconnect"><?php echo $lastdisconnect; ?></span></p>


<p style="margin: 10px;"><b>Game Name</b><br />
<span style="margin-left: 10px;"><?php echo $game_info['game_name']; ?></span></p>
<p style="margin: 10px;"><b>Game Type</b><br />
<span style="margin-left: 10px;"><?php echo $game_info['game_type']; ?></span></p>
<p style="margin: 10px;"><b>Status</b><br />
<span style="margin-left: 10px;" id="status"><?php if ($game_info['ten'] != 1) { echo "????? Status Unknown, Location Disconnected"; } else if ($game_info['five'] != 1) { echo $status_codes[$game_info['status']]['status']; } else { echo $status_codes[$game_info['status']]['status']; } ?></span></p>
<?php if ($_SESSION['access_level'] == "admin") { ?>
<p style="margin: 10px;"><b>Escrow</b><br />
<span style="margin-left: 10px;" id="escrow"><?php echo number_format(($game_info['escrow'] * 0.01), 2); ?></span></p>
<p style="margin: 10px;"><b>Software</b><br />
<span style="margin-left: 10px;"><?php echo $game_info['software']; ?></span></p>
<p style="margin: 10px;"><b>Config</b><br />
<span style="margin-left: 10px;"><?php echo $game_info['config']; ?></span></p><?php } ?>

<?php if (mysql_num_rows($result_numbers) > 0) { 
	while($row = mysql_fetch_assoc($result_numbers)) { ?>
  <h2 align="center">Current Meters</h2>
	<p style="margin: 10px;"><b>Date/Time Recorded</b><br />
	<span style="margin-left: 10px;"><?php echo $row['time_data']; ?></span></p>
	<p style="margin: 10px;"><b>Credits In</b><br />
	<span style="margin-left: 10px;">$<?php echo $row['credits_in']; ?></span></p>
	<p style="margin: 10px;"><b>Credits Out</b><br />
	<span style="margin-left: 10px;">$<?php echo $row['credits_out']; ?></span></p>
	<p style="margin: 10px;"><b>Handpays/b><br />
	<span style="margin-left: 10px;">$<?php echo $row['handpays']; ?></span></p>
	<p style="margin: 10px;"><b>Credits Played</b><br />
	<span style="margin-left: 10px;">$<?php echo $row['credits_played']; ?></span></p>
	<p style="margin: 10px;"><b>Credits Won</b><br />
	<span style="margin-left: 10px;">$<?php echo $row['credits_won']; ?></span></p>
	<p style="margin: 10px;"><b>Net Credits</b><br />
	<span style="margin-left: 10px;">$<?php echo $row['net_credits']; ?></span></p>
  </div>
	<?php } 
  } ?>
</body>
</html>

<?php } ?>