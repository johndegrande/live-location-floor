<?php
session_start();
require("scripts/dbstuff.php");
include("scripts/user_check.php");
include("scripts/get_timezone.php");
include("scripts/status_codes.php");

$time_offset = date(P);
$main_vendor = $_SESSION['vendor'];
$main_juris = $_SESSION['juris'];
if ($main_juris == 0) { $main_juris = 1; }
$main_name = $_SESSION['name'];
$last_updated = date('m/d H:i:s');

if (!isset($_SESSION['autoRefresh'])) {
	$_SESSION['autoRefresh'] = "on";
}
if (isset($_GET['autoRefreshOff'])) {
	$_SESSION['autoRefresh'] = "off";
} else if (isset($_GET['autoRefreshOn'])) {
	$_SESSION['autoRefresh'] = "on";
}
if (isset($_GET['autoRefresh']) and ($_GET['autoRefresh'] == "on" or $_GET['autoRefresh'] == "off")) {
	$_SESSION['autoRefresh'] = $_GET['autoRefresh'];
}

if (isset($_GET['locid']) and is_numeric($_GET['locid'])) {
	$loc_key = mysql_real_escape_string($_GET['locid']);
} else { $loc_key = 100; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<meta http-equiv="refresh" content="15">-->
<title>AAI WebFloor</title>
<link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" type="text/css" href="scripts/main.css" />
<link rel="stylesheet" href="fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript" src="scripts/xstooltip.js"></script>
<script type="text/javascript" src="scripts/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("a.frame").fancybox({
		'width'				: '75%',
		'height'			: '75%',
		'padding'			: 0,
        'autoScale'     	: false,
        'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'type'				: 'iframe',
		'overlayColor'		: '#000',
		<?php if ($_SESSION['autoRefresh'] == "on") { ?>
		'onComplete'		: function() {
								noRefresh();
							},
		'onClosed'			: function() {
								autoRefresh();
							}
		<?php } ?>
	});
	
	<?php if ($_SESSION['autoRefresh'] == "on") { ?> autoRefresh(); <?php } ?>
});
</script>
<script type="text/javascript">
	function autoRefresh () {
		theInt = setInterval(function(){
			document.auto_refresh.submit();
		}, 60000);
	};
	
	function noRefresh () {
		clearInterval(theInt);
	};
</script>
<body bgcolor="#000000">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="topBar">
  <tr height="62px">
    <td align="left" valign="middle">
    	<form action="webFloor.php" id="auto_refresh" name="auto_refresh" method="get">
    	<table border="0" cellspacing="0" cellpadding="10">
          <tr>
            <td><a href="logout.php"><img src="images/btn_logout.png" border="0" /></a></td>
            <td>
            <?php if ($_SESSION['autoRefresh'] == "on") { ?>
            	<input type="submit" class="auto_btn_off" src="images/btn_turn_off.png" name="autoRefreshOff" id="autoRefreshOff" value="">
            <?php } else { ?>
            	<input type="submit" class="auto_btn_on" src="images/btn_turn_on.png" name="autoRefreshOn" id="autoRefreshOn" value="">
            <?php } ?>
            </td>
            <td><a href="help.php"><img src="images/btn_help.png" border="0" /></a></td>
            <td valign="middle">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="middle"><img src="images/label_sortby.png" /></td>
                    <td valign="middle">
                    	<select name="locid" id="locid" class="sort_select" onChange="document.auto_refresh.submit()">
                             <?php
							$sql_locs = "SELECT DISTINCT id, name FROM floorLocations WHERE vendor=1 AND juris=1 ORDER BY name";
							$result_locs = mysql_query($sql_locs) or die ("Couldn't execute query.");
							while ($row = mysql_fetch_assoc($result_locs)) { 
								extract($row); ?>
								<option value="<?php echo $id; ?>"<?php if ($loc_key == $id) { echo " SELECTED"; } ?>><?php if ($id == 751) { echo "Speaking Rock 2"; } else { echo $name; } ?></option>
							<?php if ($loc_key == $id) { 
								$sql_loc_info = "SELECT name, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen FROM floorLocations where vendor=1 AND juris=1 AND id='$loc_key' ORDER BY lastseen DESC LIMIT 1";
								$result_loc_info = mysql_query($sql_loc_info) or die ("Couldn't execute query.");
								extract(mysql_fetch_assoc($result_loc_info));
								if ($id == 751) { $loc_name = "Speaking Rock 2"; } else { $loc_name = $name; }
								$loc_lastseen = $lastseen;
							  }
							} ?>
                        </select>
                        
					</td>
                  </tr>
                </table>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr height="13px"><td></td></tr>
</table>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" background="images/bg.jpg" align="center">
  <tr height="65px"><td bgcolor="#000000" width="5%"></td><td width="90%"></td><td bgcolor="#000000" width="5%"></td></tr>
  <tr height="150px">
  	<td bgcolor="#000000" rowspan="3"></td>
    <td valign="top" width="90%">
    	<table border="0" cellspacing="10px" cellpadding="0">
          <tr>
            <td align="center"><img src="images/logo.png" /><br /><img src="images/label_webfloor.png" /><br /></td>
            <td width="700px">
            	<?php
					$loc_cnt = 0;
					$game_cnt = 1;
					$id_cnt = 0;
					$sql = "SELECT juris, vendor, id, name, city, game_id, game_name, game_type, status, chippies, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen FROM floorLocations WHERE juris=1 and id='$loc_key' ORDER BY game_id, game_name";
					$result = mysql_query($sql) or die ("Couldn't execute query.");
					
					$lastseen_split = preg_split("/ /", $loc_lastseen);
					$lastseen_date = preg_split("/-/", $lastseen_split[0]);
					$lastseen_time = preg_split("/:/", $lastseen_split[1]);
					$show_time = $lastseen_date[1] . "/" . $lastseen_date[2] . " " . $lastseen_time[0] . ":" . $lastseen_time[1];
					if ($show_time == "/ :") { $show_time = "0/0 00:00"; }
				?>
            	Welcome <?php echo $main_name; ?><br />
                <table width="100%" border="0" cellspacing="5" cellpadding="0">
                  <tr>
                    <td align="left" valign="bottom">
                    	<font color="#000000">Location Name: </font><?php echo $loc_name; ?><br />
                    	<font color="#000000">Game Count: </font><?php echo mysql_num_rows($result); ?><br />
                    	<br />
                    	<font color="#000000">Last Updated: </font><?php echo $last_updated; ?>
                    </td>
                    <td align="left" valign="bottom">
                    	<font color="#000000">Last Connected: </font><?php echo $show_time; ?><br />
                    	<font color="#000000">Internet Connection Status: </font><?php if ($five != 1 OR $ten != 1) { echo "<font color='#FF0000'>DISCONNECTED</font>"; } else { echo "Connected"; } ?><br />
                    	<br />
                    	<font color="#000000">Server ID: </font><?php echo $loc_key; ?>
                    </td>
                    <td align="center" valign="middle"><a href="locate.php?id=<?php echo $loc_key; ?>&juris=1" class="frame" caption="Click outside of the map to return to webFloor."><img src="images/mapit.png" border="0" /></a></td>
                  </tr>
                </table>

                
            </td>
          </tr>
        </table>
    </td>
    <td bgcolor="#000000" rowspan="3"></td>
  </tr>
  <tr height="100%">
    <td align="center" valign="top">
    	<table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <?php
                while($row = mysql_fetch_assoc($result)) {
				extract($row);
				if ($vendor_key != $vendor) {
					$vendor_key = $vendor;
					if ($vendor_key != 1) {
						echo "</td></tr>\n";
					}
					echo "<tr><td class='sort_header'>" . $vendor_list[$vendor_key] . "</td></tr>\n";
					echo "<tr><td>\n";
				}
                    include("scripts/display_game.php");
                } ?>
                </table>
            </td>
          </tr>
        </table>
    </td>
  </tr>
  <tr height="25px">
    <td align="left" style="padding-left: 20px;">Last Updated:  <?php echo $last_updated; ?></td>
  </tr>
  <tr height="62px">
    <td background="images/wood_bar.jpg" colspan="3"></td>
  </tr>
</table>
</body>