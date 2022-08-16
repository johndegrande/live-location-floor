<?php session_start();
include("scripts/dbstuff.php");

include("scripts/initialize.php");

if ($_SESSION['access_level'] == "admin" && $mode == "floors") {
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
<title>floorBuilder</title>

</head>

<?php $zindex = 9999; ?>
<body bgcolor="<?php echo $bg_color; ?>" class="web">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="position: fixed; z-index: <?php echo $zindex; ?>;" bgcolor="<?php echo $menu_bg_color; ?>">
  <tr>
    <td height="55px" valign="middle" align="left">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 1000px;">
          <tr>
            <td valign="middle" style="padding-left: 15px;" width="25px"><img src="images/<?php echo $logo; ?>" style="max-height: 50px;" /></td>
            <td style="padding-left: 15px;">
            	<span style="font-size: 20px;"><?php if ($mode == "grid") { echo $_SESSION['name']; } else if ($mode == "floors" and !isset($_GET['loc'])) { echo $floor_name; } else if ($mode == "floors" and isset($_GET['loc'])) { echo $cur_location_name; } ?>&nbsp;&nbsp;&nbsp;</span>
                floorBuilder &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php echo sizeof($search_array) . " Games"; ?>
            </td>
          </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td>
    	<script>
		$(function() {
			$(document).tooltip();
			$("#btn_save").button();
			$("#btn_cancel").button();
			$("#btn_grid").button();
			$("#btn_reset").button();
			
			$('.gameBox').draggable({
				grid: [ 17.5, 17 ],
				containment: "#gameFloor"			
			});
		});
		
		function updateXY(element, juris, id, game_id) {
			var top = document.getElementById(element).style.top;
			var left = document.getElementById(element).style.left;
			var coords = left+', '+top+', '+juris+', '+id+', '+game_id;
			document.getElementById('coord_'+element).value = coords;
		}
		</script>
        <br />
        <div id="toolbar" class="ui-widget-header" style="padding-left: 15px;">
          <div style="width: 1200px;">
          	<button id="btn_save" title="Save Floor Layout" onclick="document.getElementById('submit').click();">Save</button>
            <button id="btn_cancel" title="Cancel" onclick="location.href='webFloor2.php?loc=<?php echo $_GET['loc']; ?>';">Cancel</button>
            <button id="btn_grid" title="Organize games if rows of 15. Not final until you click Save." onclick="location.href='floorBuilder.php?loc=<?php echo $_GET['loc']; ?>&mode=grid';">Auto-Grid</button>
            <button id="btn_reset" title="Reset floor. Not final until you click Save." onclick="location.href='floorBuilder.php?loc=<?php echo $_GET['loc']; ?>&mode=reset';">Reset</button>
          </div>
        </div>
    </td>
  </tr>
</table>
<div style="height: 135px;"></div>
<form action="webFloor2.php?loc=<?php echo $_GET['loc']; ?>" method="post" id="gameCoords" name="gameCoords">
<div id="gameFloor" name="gameFloor" style="width: 3000px; height: 3000px; padding: 0px;">
<?php
$cur_mark = '';
$cur_cnt = 0;
$cur_x = 0;
$cur_y = 135;
if (!($mode == "floors" and !isset($_GET['loc']))) {
while($row = mysql_fetch_assoc($result)) {
	extract($row);
	if ($zindex != 9999) { $cur_x = $cur_x + 70; }
	if ($cur_cnt == 15) { $cur_cnt = 0; $cur_y = $cur_y + 68; $cur_x = 0; }
	$cur_cnt++;
	$zindex--; ?>
    
	<div class="gameBox" id="<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; padding: 0px; margin: 0px 5px 5px 5px; height: 68px; left: <?php if ($_GET['mode'] == 'reset') { $xpos = 0; } else if ($_GET['mode'] == 'grid') { $xpos = $cur_x; } echo $xpos; ?>px; top: <?php if ($ypos == 0) { $ypos = 135; } if ($_GET['mode'] == 'reset') { $ypos = 135; } else if ($_GET['mode'] == 'grid') { $ypos = $cur_y; } echo $ypos; ?>px;" onmouseup="updateXY('<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>', '<?php echo $juris; ?>', '<?php echo $id; ?>', '<?php echo $game_id; ?>');">
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td>
            	<div id="game_<?php echo $id; ?>_<?php echo $game_id; ?>"  class="<?php if ($ten != 1) { echo "red"; } else { echo $status_codes[$status]['style']; } ?>">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="50px"><img src="images/game_icons/<?php if ($status=='-2' or $status=='1') { echo "jackpot"; } else { echo $game_type; } ?>.png" width="48px" height="48px" /></td>
                        <td rowspan="2" align="center" valign="bottom">
                            <?php if ($chippies > 0 ) { ?>
                            <?php if ($main_vendor == 0) { ?><img src="images/chips/<?php echo $chippies; ?>.gif" /><?php } ?>
                            <?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td><?php echo $game_name; ?></td>
                      </tr>
                    </table>
                </div>
            </td>
          </tr>
        </table>
    </div>
    <input type="hidden" name="coord_<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>" id="coord_<?php echo "game_" . $juris . "_" . $id . "_" . $game_id; ?>" value="<?php echo $xpos . ", " . $ypos . ", " . $juris . ", " . $id . ", " . $game_id . ', ' . $lastseen; ?>" />
<?php } 
} ?>
</div>
<input type="submit" id="submit" name="submit" value="submit" style="display: none;" />
</form>
</body>
</html>
<?php } ?>