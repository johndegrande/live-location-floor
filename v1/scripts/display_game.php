		<div id="<?php echo "tooltip_" . $juris . "_" . $id . "_" . $game_id; ?>" class="xstooltip" style="background: <?php if ($ten != 1) { echo "#FF0000"; } else if ($status_codes[$status]['color'] == "#000000") { echo "#FFFFFF"; } else {echo $status_codes[$status]['color']; } ?>;">
			<b>Game Name:</b> <?php echo $game_name; ?><br/>
			<b>Game Type:</b> <?php echo $game_type; ?><br />
			<b>Status:</b> <?php if ($ten != 1) { echo "?"; } else { echo $status_codes[$status]['status']; } ?><br />
		</div> 
		<div class="gameBox" id="<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>" onMouseOver="xstooltip_show('<?php echo "tooltip_" . $juris . "_" . $id . "_" . $game_id; ?>', '<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>', 0, 65);" onMouseOut="xstooltip_hide('<?php echo "tooltip_" . $juris . "_" . $id . "_" . $game_id; ?>');">
			<table width="100%" border="1" cellspacing="0" cellpadding="0">
			  <tr>
				<td bgcolor="<?php if ($ten != 1) { echo "#FF0000"; } else { echo $status_codes[$status]['color']; } ?>">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td height="50px"><img src="images/icons/<?php if ($status=='-2' or $status=='1') { echo "jackpot"; } else { echo $game_type; } ?>.png" /></td>
						<td rowspan="2" align="center" valign="bottom">
							<?php if ($chippies > 0 ) { ?>
							<?php if ($main_vendor == 0) { ?><img src="images/chips<?php echo $chippies; ?>.gif" /><?php } ?>
							<?php } ?>
						</td>
					  </tr>
					  <tr>
						<td <?php if ($status == '-5' and ($ten == 1 or $five == 1)) { echo "class=\"greenies\""; } ?>><?php echo $game_name; ?></td>
					  </tr>
					</table>
				</td>
			  </tr>
			</table>
		</div>