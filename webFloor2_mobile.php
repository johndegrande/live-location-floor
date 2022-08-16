<?php $page_title = "webFloor_mobile";
require("scripts/header_mobile.php"); ?>
<link rel="stylesheet" href="scripts/status.css" />
<script type = "text/javascript">

var game_cnt = 0;
function update_games() {
	var updater_url = "scripts/game_updater.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?><?php if ($vendor_select != '') { echo "&vendor=" . $_SESSION['vendor']; } if ($location_select != '') { echo "&loc=" . $get_location; } if ($_SESSION['access_level'] == "overlord") { echo "," . $overlord_vendors; } ?>";
	var source = '';
	
	$.getJSON(updater_url, function(data) {
		if (game_cnt == 0) {
			game_cnt = data.globals.game_cnt;
		} else {
			if (data.globals.game_cnt != game_cnt) {
				location.reload();
			}
		}
		for(var i = 1; i <= game_cnt; i++){
			if (data.games[i].lastseen != null) { document.getElementById('time_'+data.games[i].id).innerHTML = data.games[i].lastseen; }
			document.getElementById('status_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = '<strong>'+data.games[i].status+'</strong>'; 
			if (data.games[i].lastseen != null) { document.getElementById('loc_'+data.games[i].id).className = data.games[i].conn_style+' ui-li-divider '; }
			document.getElementById('game_'+data.games[i].id+'_'+data.games[i].game_id).className = data.games[i].style+' ui-li-has-thumb wf_mobile';
			source = 'images/game_icons/'+data.games[i].game_type+'.png';
			if (source != $('#icon_'+data.games[i].id+'_'+data.games[i].game_id).attr('src')) {
				document.getElementById('gametype_'+data.games[i].id+'_'+data.games[i].game_id).innerHTML = data.games[i].game_type;
				$('#icon_'+data.games[i].id+'_'+data.games[i].game_id).attr('src', source);
			}
		}
	});
}

function go(loc){
    document.getElementById('frame1').src = loc;
}

$(function() {
	$("#popup_location").on("popupafterclose", function(event, ui) { 
		document.getElementById('frame1').src = '';
	});
});
</script>

<?php if (isset($error_output) and $error_output != '') { ?>
	<p align="center" bgcolor="#000000" style="color: #FF0;"><b><?php echo $error_output; ?></b></p>
<?php } ?>

<?php if (!($mode == "floors" and !isset($_GET['loc']))) { ?>
<form>
    <input id="filterTable-input" data-type="search" placeholder="Search Games">
</form>
<table data-role="table" id="game_table" data-filter="true" data-input="#filterTable-input">
	<thead>
        <tr>
            <th data-priority="persist"></th>
        </tr>
    </thead>
    <tbody>
    <?php $last_id = "";
	$cur_mark = "";
	
	while($row = mysql_fetch_assoc($result)) {
		extract($row); 
		if ($id != $last_id) { 
			if ($ten != 1) { $conn_status = "red"; }
			else if ($five != 1) { $conn_status = "yellow"; }
			else { $conn_status = "black"; }
			
			if ($last_id != "") { ?>
            			
					</ul>
				</th>
			</tr><?php }
			$last_id = $id;
			if ($sortby_type == "city" or $sortby_type == "vendor") {
				if ($sortby_type == "city" and $city != $cur_mark) {
					$cur_mark = $city;	
					$counter = search_result_array($search_array, $cur_mark, 'city');
					echo "<tr><th><h1 align=\"center\"><u>$city (" . $counter . ")</u></h1></th></tr>";
				} else if ($sortby_type == "vendor" and $vendor != $cur_mark) {
					$cur_mark = $vendor;
					$counter = search_result_array($search_array, $cur_mark, 'vendor');
					echo "<tr><th><h1 align=\"center\"><u>$vendor_list[$vendor] (" . $counter . ")</u></h1></th></tr>";
				}
			}?>
			<tr>
				<th>
					<ul data-role="listview" id="list_<?php echo $id; ?>" class="wf_mobile">
						<li data-role="list-divider" id="loc_<?php echo $id; ?>"><div style="max-width: 150px;"><?php echo $name; ?>, <?php echo $city; ?></div><span style="display: none;"><?php echo $vendor_list[$vendor]; ?></span><span class="ui-li-count" id="time_<?php echo $id; ?>" style="font-size: 10px;"><?php echo $lastseen; ?></span></li><?php } ?>
                        <!--<li id="game_<?php echo $id; ?>_<?php echo $game_id; ?>"><a href="#popup_location" data-rel="popup" data-position-to="window" onclick="go('loc_info_mobile.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?>&loc=<?php echo $id; ?>&game=<?php echo $game_id; ?>');">-->
						<li id="game_<?php echo $id; ?>_<?php echo $game_id; ?>"><a target="_blank" href="loc_info_mobile.php?timezone=<?php echo $_SESSION['timezone']; ?>&juris=<?php echo $juris_select; ?>&loc=<?php echo $id; ?>&game=<?php echo $game_id; ?>">
                            <img src="images/game_icons/<?php echo $game_type; ?>.png" style="max-width: 75px;" id="icon_<?php echo $id; ?>_<?php echo $game_id; ?>">
                            <h2><?php echo $game_name; ?></h2>
                            <p id="status_<?php echo $id; ?>_<?php echo $game_id; ?>" style="white-space: normal;"><strong><?php echo $status_codes[$status]['status']; ?></strong></p>
                            </a>
                        </li>
        			<?php } ?>
        			</ul>
			</th>
		</tr>
    </tbody>
</table>
<div data-role="popup" id="popup_location" data-overlay-theme="<?php echo $theme_name; ?>" data-tolerance="15,15" data-history="false" class="ui-content" style="height: 80%;">
 <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
 <iframe height="80%" width="100%" id="frame1" border="0px"></iframe>
</div>
<?php } ?>

<?php require("scripts/footer_mobile.php"); ?>