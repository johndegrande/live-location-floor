<?php session_start();
include("scripts/dbstuff.php");

if (isset($_POST['submit'])) {
	foreach($_POST as $key) {
		if ($key != 'submit') {
			$splits = explode(', ', $key);
			$lastseen = strtotime($splits[5]);
			$lastseen = date('Y-m-d H:i:s', $lastseen);
			$sql = "UPDATE floorLocations SET xpos='$splits[0]', ypos='$splits[1]' WHERE juris='$splits[2]' AND id='$splits[3]' AND game_id='$splits[4]'";
			$result = mysql_query($sql) or die ("Couldn't execute query.");
		}
	}
	$error_output = "The floor positioning for this location has been updated.";
}

include("scripts/initialize.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.min.css" />
<link rel="stylesheet" href="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.theme.min.css" />
<link rel="stylesheet" href="scripts/main.css" />
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />	
<link rel="stylesheet" href="scripts/status.css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<script src="scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="scripts/xstooltip.js"></script>
<script src="scripts/themes/<?php echo $theme_name; ?>/jquery-ui.min.js"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="scripts/fancybox_options.js"></script>
<title><?php echo $portal_title_prefix; ?> Operator Portal</title>

<script type = "text/javascript">
var ajax_updater = 0;
$( document ).ready(function() {
	update_games();
	ajax_updater = setInterval('update_games()', 30000);
});

var game_cnt = 0;

function update_games() {
	
	var updater_url = "scripts/game_updater.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?><?php if ($vendor_select != '') { echo "&vendor=" . $_SESSION['vendor']; } if ($location_select != '') { echo "&loc=" . $get_location; } if ($_SESSION['access_level'] == "overlord") { echo "," . $overlord_vendors; } ?>";
	
	$.getJSON(updater_url, function(data) {
		if (game_cnt == 0) {
			game_cnt = data.globals.game_cnt;
		} else {
			if (data.globals.game_cnt != game_cnt) {
				location.reload();
			}
		}
		
		document.getElementById('main_time').innerHTML = data.globals.update_time;
		
		var source = '';
		
		for(var i = 1; i <= game_cnt; i++){
			if (data.games[i].lastseen != null && document.getElementById('time_'+data.games[i].id)) { document.getElementById('time_'+data.games[i].id).innerHTML = data.games[i].lastseen_short; }
			if (data.games[i].lastseen != null) { document.getElementById('time_long_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].lastseen; }
			document.getElementById('status_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].status;
			if (data.games[i].lastseen != null && document.getElementById('loc_'+data.games[i].id)) { document.getElementById('loc_'+data.games[i].id).className = data.games[i].conn_style; }
			document.getElementById('game_'+data.games[i].id+'_'+data.games[i].game_id).className = data.games[i].style;
			document.getElementById('tooltip_'+data.games[i].id+'_'+data.games[i].game_id).className = data.games[i].style+' xstooltip';
			if (data.games[i].lastseen != null) { document.getElementById('conn_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].conn_status; }
			if (data.games[i].lastseen != null) { document.getElementById('disconnect_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].last_disconnect; }
			source = 'images/game_icons/'+data.games[i].game_type+'.png';
			if (source != $('#icon_'+data.games[i].id+'_'+data.games[i].game_id).attr('src')) {
				document.getElementById('gametype_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].game_type;
				$('#icon_'+data.games[i].id+'_'+data.games[i].game_id).attr('src', source);
			}
			
			<?php if ($_SESSION['access_level'] == "admin") { ?>
			if (data.games[i].needsnc != null) {
				if (data.games[i].needsnc == '1') {
					$('.superhelps'+data.games[i].id+' .needsnc').addClass('true');
				} else {
					$('.superhelps'+data.games[i].id+' .needsnc').removeClass('true');
				}
			}
			if (data.games[i].needswap != null) {
				if (data.games[i].needswap == '1') {
					$('.superhelps'+data.games[i].id+' .needswap').addClass('true');
				} else {
					$('.superhelps'+data.games[i].id+' .needswap').removeClass('true');
				}
			}
			if (data.games[i].needshelp != null) {
				if (data.games[i].needshelp == '1') {
					$('.superhelps'+data.games[i].id+' .needshelp').addClass('true');
				} else {
					$('.superhelps'+data.games[i].id+' .needshelp').removeClass('true');
				}
			}
			document.getElementById('escrow_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].escrow;
			<?php } ?>
		}
		
		<?php if ($mode == "floors" and isset($_GET['loc'])) { ?>if (data.games['lastseen']) { document.getElementById('connect_time').innerHTML = data.games['lastseen']; }<?php } ?>
	});
}
</script>

</head>

<body bgcolor="<?php echo $bg_color; ?>" class="web">
<?php $zindex = 8000; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="position: fixed; z-index: <?php echo $zindex; ?>;" bgcolor="<?php echo $menu_bg_color; ?>">
  <tr>
    <td height="55px" valign="middle" align="left">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 1000px;">
          <tr>
            <td valign="middle" style="padding-left: 15px;" width="25px"><img src="images/<?php echo $logo; ?>" style="max-height: 50px;" /></td>
            <td style="padding-left: 15px;">
            	<span style="font-size: 20px;"><?php if ($mode == "grid") { echo $_SESSION['name']; } else if ($mode == "floors" and !isset($_GET['loc'])) { echo $floor_name; } else if ($mode == "floors" and isset($_GET['loc'])) { echo $cur_location_name; } ?>&nbsp;&nbsp;&nbsp;</span>
                <?php if ($mode == "grid" || ($mode == "floors" and !isset($_GET['loc']))) { echo $distinct_locations . " Locations &nbsp;&nbsp;|&nbsp;&nbsp; "; }
                if ($mode == "grid" || ($mode == "floors" and isset($_GET['loc']))) { echo sizeof($search_array) . " Games &nbsp;&nbsp;|&nbsp;&nbsp; "; } ?>
				<?php if ($mode == "floors" and isset($_GET['loc'])) { ?>Last Connected: <?php if ($_SESSION['access_level'] == "admin") { ?><a href="aavssh://ecs<?php if ($juris_select > 1) { echo $juris_select; } ?>-<?php echo $_GET['loc']; ?>a.aav.local"><?php } ?><span id="connect_time"></span><?php if ($_SESSION['access_level'] == "admin") { ?></a><?php } ?>&nbsp;&nbsp;|&nbsp;&nbsp; <?php } ?>
                Last Updated: <span id="main_time"><?php echo date('Y-m-d H:i:s'); ?></span>
            </td>
          </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td>
    	<?php include("scripts/web_toolbar.php"); ?>
    </td>
  </tr>
  <?php if (isset($error_output) and $error_output != '') { ?>
  <tr>
    <td align="center" bgcolor="#000000" style="color: #FF0;"><div style='padding: 5px;'><b><?php echo $error_output; ?></b></div></td>
  </tr>
  <?php } ?>
</table>
<div style="height: 135px;"></div>

<?php
$cur_mark = '';	

if (!($mode == "floors" and !isset($_GET['loc']))) {
while($row = mysql_fetch_assoc($result)) {
	extract($row);
	
	$escrow = number_format(($escrow * 0.01), 2);
	
	if ($sortby_type == "city") {
		if ($city != $cur_mark) {
			$cur_mark = $city;
			$counter = search_result_array($search_array, $cur_mark, 'city');
			echo "<div class='sort_divider'><h3>" . $city . " (" . $counter . ")</h3></div>";	
		}
	} else if ($sortby_type == "vendor") {
		if ($vendor != $cur_mark) {
			$cur_mark = $vendor;
			$counter = search_result_array($search_array, $cur_mark, 'vendor');
			echo "<div class='sort_divider'><h3>" . $vendor_list[$vendor] . " (" . $counter . ")</h3></div>";	
		}
	} else if ($sortby_type == "status") {
		if ($ten != 1) {
			if ($cur_mark != "-9999") {
				$cur_mark = "-9999";
				$counter = search_result_array($search_array, 0, 'ten');
				echo "<div class='sort_divider'><h3>????? Status Unknown Location Disconnected (" . $counter . ")</h3></div>";		
			}
		} else if ($five != 1) {
			if ($cur_mark != "-9998") {
				$cur_mark = "-9998";
				$counter_five = search_result_array($search_array, 0, 'five');
				$counter_ten = search_result_array($search_array, 0, 'ten');
				$counter = $counter_five - $counter_ten;
				echo "<div class='sort_divider'><h3>Disconnected? - No update received in the last 5-10 minutes (" . $counter . ")</h3></div>";		
			}
		} else if ($status != $cur_mark) {
			$cur_mark = $status;
			$counter = search_result_array($search_array, $cur_mark, 'status');
			echo "<div class='sort_divider'><h3>" . $status_codes[$cur_mark]['status'] . " (" . $counter . ")</h3></div>";	
		}
	} else if ($sortby_type == "type") {
		if ($cur_mark != $game_type) {
			$cur_mark = $game_type;
			$counter = search_result_array($search_array, $cur_mark, 'game_type');
			echo "<div class='sort_divider'><h3>" . $game_type . " (" . $counter . ")</h3></div>";
		}
	} 
	
	$zindex--; ?>
    <div id="<?php echo "tooltip_" . $id . "_" . $game_id; ?>" class="xstooltip <?php if ($ten != 1) { echo "red"; } else { echo $status_codes[$status]['style']; } ?>">
		<?php if ($_SESSION['access_level'] == "admin") { ?><b>Server ID:</b> <?php echo $id; ?><br/><?php } ?>
        <b>Location Name:</b> <?php echo $name; ?><br/>
        <b>City:</b> <?php echo $city; ?><br/>
        <?php if ($_SESSION['access_level'] == "admin" or $_SESSION['access_level'] == "overlord") { ?><b>Vendor:</b> <?php echo $vendor_list[$vendor]; ?><br /><?php } ?>
        <b>Game Name:</b> <?php echo $game_name; ?><br/>
        <b>Game Type:</b> <span id="gametype_<?php echo $id; ?>_<?php echo $game_id; ?>"><?php echo $game_type; ?></span><br />
        <b>Status:</b> <span id="status_<?php echo $id; ?>_<?php echo $game_id; ?>" style="font-weight: normal;"><?php if ($ten != 1) { echo "????? Status Unknown Location Disconnected"; } else { echo $status_codes[$status]['status']; } ?></span><br />
        <?php if ($_SESSION['access_level'] == "admin") { ?><b>Escrow:</b> <span id="escrow_<?php echo $id; ?>_<?php echo $game_id; ?>"><?php echo $escrow; ?></span><br /><?php } ?>
        <b>Internet Connection:</b> <span id="conn_<?php echo $id; ?>_<?php echo $game_id; ?>"><?php if ($five != 1 OR $ten != 1) { echo "DISCONNECTED"; } else { echo "Connected"; } ?></span><br> 
        <b>Last Connected:</b> <span id="time_long_<?php echo $id; ?>_<?php echo $game_id; ?>"><?php echo $lastseen; ?></span><br />
        <?php
            $sql_lastdisconnect = "SELECT convert_tz(lastdisconnect, 'system', '$time_offset') AS lastdisconnect FROM floorDisconnectLog WHERE id=$id AND juris=$juris LIMIT 1";
            $result_lastdisconnect = mysql_query($sql_lastdisconnect) or die ("Couldn't execute query.2");
            extract(mysql_fetch_assoc($result_lastdisconnect));
        ?>
        <b>Last Disconnect:</b> <span id="disconnect_<?php echo $id; ?>_<?php echo $game_id; ?>"><?php echo $lastdisconnect; ?></span>
    </div>
    
	<div class="gameBox" id="<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>" onMouseOver="xstooltip_show('<?php echo "tooltip_" . $id . "_" . $game_id; ?>', '<?php echo "game_" . $id . "_" . $game_id; ?>', 0, <?php if ($mode == "floors") { echo "68"; } else { echo "83"; } ?>);" onMouseOut="xstooltip_hide('<?php echo "tooltip_" . $id . "_" . $game_id; ?>');" <?php if ($mode == "floors" and $_GET['floor'] != 'grid' and !isset($force_grid)) { ?>style="position: absolute; left: <?php echo $xpos; ?>px; top: <?php echo $ypos; ?>px; z-index: <?php echo $zindex; ?>; padding-bottom: 0px; margin-bottom: 0px;" <?php } ?>>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td>
            	<div id="game_<?php echo $id; ?>_<?php echo $game_id; ?>"  class="<?php if ($ten != 1) { echo "red"; } else { echo $status_codes[$status]['style']; } ?>">
                	<?php echo "<a href=\"loc_info.php?timezone=" . $_SESSION['timezone'] . "&juris=" . $juris_select . "&loc=" . $id . "&game=" . $game_id . "\" class=\"frame\" caption=\"Click outside of the map to return to webFloor.\">"; ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="50px"><img src="images/game_icons/<?php if ($status=='-2' or $status=='1') { echo "jackpot"; } else { echo $game_type; } ?>.png" width="48px" height="48px" id="icon_<?php echo $id; ?>_<?php echo $game_id; ?>" /></td>
                        <td rowspan="2" align="center" valign="bottom">
                            <?php if ($chippies > 0 ) { ?>
                            <?php if ($main_vendor == 0) { ?><img src="images/chips/<?php echo $chippies; ?>.gif" /><?php } ?>
                            <?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td><?php if ($sortby_type == "id") { echo $id; } else { echo $game_name; } ?></td>
                      </tr>
                    </table>
                    <?php echo "</a>"; ?>
                </div>
            </td>
          </tr>
          <?php 
          if ($id == $id_cnt) {
            $game_cnt++;
          } else {
            $game_cnt = 1;
            $id_cnt = $id;
            $loc_cnt++;
          }
          if ($game_cnt == 1 and $mode != "floors") { ?>
          <tr>
            <td>
            	<div id="loc_<?php echo $id; ?>" class="<?php if ($ten != 1) { echo "red"; } else if ($five != 1) { echo "yellow"; } else { echo "black"; } ?>">
                <?php
                    $lastseen_split = preg_split("/ /", $lastseen);
                    $lastseen_date = preg_split("/-/", $lastseen_split[0]);
                    $lastseen_time = preg_split("/:/", $lastseen_split[1]);
                    $show_time = $lastseen_date[1] . "/" . $lastseen_date[2] . " " . $lastseen_time[0] . ":" . $lastseen_time[1];
                    if ($show_time == "/ :") { $show_time = "0/0 00:00"; }
                    if ($juris == 6) {
                        if ($id == 13) { $id = 318; } else if ($id == 23) { $id = 736; }
                    }
					if ($_SESSION['access_level'] == "admin") { 
						echo "<a href=\"aavssh://ecs".$juris_select."-".$id.".aav.local\"><div id='time_" . $id . "'>" . $show_time . "</div></a>";
					} else {
						echo "<a href=\"loc_info.php?timezone=" . $_SESSION['timezone'] . "&juris=" . $juris_select . "&loc=" . $id . "&game=" . $game_id . "\" class=\"frame\" caption=\"Click outside of the map to return to webFloor.\"><div id='time_" . $id . "'>" . $show_time . "</div></a>";
					}
                ?>
                </div>
            </td>
          </tr>
		  <?php if ($_SESSION['access_level'] == "admin") { ?>
		  <tr class="superhelps<?php echo $id; ?>">
			<td class="superhelps">
				<div class="needsnc"></div><div class="needswap"></div><div class="needshelp"></div>
			</td>
		  </tr>
		  <?php } ?>
		  <?php } ?>
        </table>
    </div>
<?php }
} ?>
</body>
</html>