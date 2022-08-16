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

if ($_SESSION['access_level'] == "admin") {
	$sql = "SELECT * FROM floorSuperhelps WHERE id=$location_select AND juris=$juris_select LIMIT 1";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	$server_info = mysql_fetch_array($result, MYSQL_BOTH);
}

$sql = "SELECT convert_tz(lastdisconnect, 'system', '$time_offset') AS lastdisconnect FROM floorDisconnectLog WHERE id=$location_select AND juris=$juris_select LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
extract(mysql_fetch_assoc($result));

$sql = "SELECT name AS vendor_name FROM floorUsers WHERE juris='$juris_select' AND vendor='$location_info[vendor]' LIMIT 1";
$result = mysql_query($sql) or die ("Couldn't execute query.");
extract(mysql_fetch_assoc($result));

//grab floor options so we know the default of everything
if (isset($_SESSION['juris'])) {
	$sql = "SELECT * FROM floorOptions WHERE juris = " . $_SESSION['juris'];
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	extract(mysql_fetch_assoc($result));
	$floor_name = $name;
	$floor_mode = $mode;
} else { //these are just basic defaults, should never need these
	$portal_title_prefix = "AAI";
	$logo = "aai_logo.png";
	$theme_name = "c";
	$menu_bg_color = "#1f4d6b";
	$bg_color = "#cccccc";
	$floor_name = "webFloor 2.0";
}

//game numbers
$sql_numbers = "SELECT * FROM floorNumbers WHERE juris='$juris_select' AND id='$location_select' AND game_id='$game_select' LIMIT 1";
$result_numbers = mysql_query($sql_numbers) or die ("Couldn't execute query.");
?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.min.css" />
<link rel="stylesheet" href="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.theme.min.css" />
<link rel="stylesheet" href="scripts/main.css" />
<link rel="stylesheet" href="scripts/status.css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<script src="scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="scripts/xstooltip.js"></script>
<script src="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.min.js"></script>
<title>Location Information</title>
</head>

<script type = "text/javascript">
$(function() {
	$( "#accordion" ).accordion({ heightStyle: "content" });
	$( ".button" ).button();
});

function update_game() {
	var updater_url = "scripts/single_game_updater.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?>&loc=<?php echo $location_select; ?>&game=<?php echo $game_select; ?>";
	
	$.getJSON(updater_url, function(data) {
		document.getElementById('time').innerHTML = data.game.lastseen;
		document.getElementById('disconnect').innerHTML = data.game.lastdisconnect;
		document.getElementById('status').innerHTML = data.game.status;
		document.getElementById('conn_status').innerHTML = data.game.conn_status;
		document.getElementById('gameInfo').className = data.game.style + ' ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active';
		document.getElementById('locInfo').className = data.game.conn_style + ' ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active';
		
		<?php if ($_SESSION['access_level'] == "admin") { ?>
		document.getElementById('server').innerHTML = data.game.server;
		document.getElementById('guidemon').innerHTML = data.game.guidemon;
		document.getElementById('resultserver').innerHTML = data.game.resultserver;
		document.getElementById('machtcp').innerHTML = data.game.machtcp;
		document.getElementById('ips').innerHTML = data.game.ips;
		document.getElementById('raid').innerHTML = data.game.raid;
		document.getElementById('drbd').innerHTML = data.game.drbd;
		document.getElementById('power').innerHTML = data.game.power;
		document.getElementById('percentdown').innerHTML = data.game.percentdown;
		document.getElementById('load5s').innerHTML = data.game.load5s;
		document.getElementById('load5m').innerHTML = data.game.load5m;
		document.getElementById('load20m').innerHTML = data.game.load20m;
		document.getElementById('spaceboot').innerHTML = data.game.spaceboot;
		document.getElementById('spaceaav').innerHTML = data.game.spaceaav;
		document.getElementById('serverlastseen').innerHTML = data.game.serverlastseen;
		document.getElementById('escrow').innerHTML = data.game.escrow;
		<?php } ?>
	});
}
</script>

<body onLoad="var singleGameRefresh = setInterval('update_game()', 30000);" style="font-weight: normal;" bgcolor="<?php echo $bg_color; ?>" class="web">
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td width="50%" valign="top">
    	<div id="accordion">
          <h3>Location Info</h3>
          <div id="locInfo" class="<?php if ($game_info['ten'] != 1) { echo "red"; } else if ($game_info['five'] != 1) { echo "gold"; } else { echo "black"; } ?>">
            <p><b>Server ID:</b> <?php echo $location_info['id']; ?></p>
            <p><b>Vendor:</b> <?php echo $vendor_name; ?></p>
            <p><b>Internet Connection Status:</b> <span id="conn_status"><?php if ($game_info['ten'] != 1) { echo "DISCONNECTED"; } else if ($game_info['five'] != 1) { echo "DISCONNECTED"; } else { echo "Connected"; } ?></span></p>
            <p><b>Last Connected:</b> <span id="time"><?php echo $game_info['lastseen']; ?></span></p>
            <p><b>Last Disconnect:</b> <span id="disconnect"><?php echo $lastdisconnect; ?></span></p>
          </div>
          <h3>Game Info</h3>
          <div id="gameInfo" class="<?php if ($game_info['ten'] != 1) { echo "red"; } else { echo $status_codes[$game_info['status']]['style']; } ?>">
            <p><b>Game Name:</b> <?php echo $game_info['game_name']; ?></p>
            <p><b>Game Type:</b> <?php echo $game_info['game_type']; ?></p>
            <p><b>Status:</b> <span id="status"><?php if ($game_info['ten'] != 1) { echo "????? Status Unknown, Location Disconnected"; } else if ($game_info['five'] != 1) { echo $status_codes[$game_info['status']]['status']; } else { echo $status_codes[$game_info['status']]['status']; } ?></span></p>
            <?php if ($_SESSION['access_level'] == "admin") { ?><p><b>Escrow:</b> <span id="escrow"><?php echo number_format(($game_info['escrow'] * 0.01), 2); ?></span></p>
			<p><b>Software:</b> <?php echo $game_info['software']; ?></p>
            <p><b>Config:</b> <?php echo $game_info['config']; ?></p><?php } ?>
          </div>
		  <?php if ($_SESSION['access_level'] == "admin") { ?>
		  <h3>Server Info</h3>
          <div id="gameInfo" class="black">
            <p><b>Server:</b> FU<span id="server"><?php echo $server_info['server']; ?></span></p>
            <p><b>GUIDemon:</b> <span id="guidemon"><?php if ($server_info['guidemon'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>ResultServer:</b> <span id="resultserver"><?php if ($server_info['resultserver'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>MachTCP:</b> <span id="machtcp"><?php if ($server_info['machtcp'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>IPs:</b> <span id="ips"><?php if ($server_info['ips'] == 0) { echo "OK"; } else { echo "<span class='red'>OUT OF IPS</span>"; } ?></span></p>
			<p><b>RAID Status:</b> <span id="raid"><?php if ($server_info['raid'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>DRBD Status:</b> <span id="drbd"><?php if ($server_info['drbd'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>UPS Power:</b> <span id="power"><?php if ($server_info['power'] == 0) { echo "Running"; } else { echo "<span class='red'>NOT RUNNING</span>"; } ?></span></p>
			<p><b>Percent Down:</b> <span id="percentdown"><?php if ($server_info['percentdown'] < 30) { echo $server_info['percentdown'].'%'; } else { echo "<span class='red'>".$server_info['percentdown']."%</span>"; } ?></span></p>
			<p><b>Load 5s:</b> <span id="load5s"><?php echo $server_info['load5s']; ?></span></p>
			<p><b>Load 5m:</b> <span id="load5m"><?php echo $server_info['load5m']; ?></span></p>
			<p><b>Load 20m:</b> <span id="load20m"><?php echo $server_info['load20m']; ?></span></p>
			<p><b>HD Space - Boot:</b> <span id="spaceboot"><?php if ($server_info['spaceboot'] < 95) { echo $server_info['spaceboot'].'%'; } else { echo "<span class='red'>".$server_info['spaceboot']."%</span>"; } ?></span></p>
			<p><b>HD Space - AAV:</b> <span id="spaceaav"><?php if ($server_info['spaceaav'] < 95) { echo $server_info['spaceaav'].'%'; } else { echo "<span class='red'>".$server_info['spaceaav']."%</span>"; } ?></span></p>
			<p><b>Last Update:</b> <span id="serverlastseen"><?php echo $server_info['lastseen']; ?></span></p>
          </div>
		  <?php } ?>
		  <?php if (mysql_num_rows($result_numbers) > 0) { 
			while($row = mysql_fetch_assoc($result_numbers)) { ?>
		  <h3>Current Meters</h3>
		  <div id="metersInfo" class="black">
			<p><b>Date/Time Recorded:</b> <?php echo $row['time_data']; ?></p>
			<p><b>Credits In:</b> $<?php echo $row['credits_in']; ?></p>
			<p><b>Credits Out:</b> $<?php echo $row['credits_out']; ?></p>
			<p><b>Handpays:</b> $<?php echo $row['handpays']; ?></p>
			<p><b>Credits Played:</b> $<?php echo $row['credits_played']; ?></p>
			<p><b>Credits Won:</b> $<?php echo $row['credits_won']; ?></p>
			<p><b>Net Credits:</b> $<?php echo $row['net_credits']; ?></p>
		  </div>
			<?php } 
		  } ?>
        </div>
		<br />
		<?php if ($_SESSION['access_level'] == "admin") { ?><a href="aavssh://ecs<?php if ($juris_select > 1) { echo $juris_select; } ?>-<?php echo $location_select; ?><?php if ($floor_mode == "floors") { echo "a"; } ?>.aav.local" class="button">Connect to <?php if ($floor_mode == "floors") { echo "FU1"; } else { echo "Server"; } ?></a> <?php if ($floor_mode == "floors") { ?><a href="aavssh://ecs<?php if ($juris_select > 1) { echo $juris_select; } ?>-<?php echo $location_select; ?>b.aav.local" class="button">Connect to FU2</a><?php } ?><?php } ?>
    </td>
    <td width="50%">
    	<iframe src="locate.php?id=<?php echo $location_info['id']; ?>&juris=<?php echo $location_info['juris']; ?>" width="100%" height="555px"></iframe>
    </td>
  </tr>
</table>
</body>
</html>

<?php } ?>