<script>
$(function() {
	$(document).tooltip();
	<?php if ($_SESSION['access_level'] == "admin" or $_SESSION['juris_plus'] == 5) { ?>$("#btn_jurisdiction").buttonset();<?php } ?>
	<?php if ($mode == "floors") { ?>
	$("#btn_location").selectmenu({
      change: function( event, data ) {
        if (data.item.value != "-1") {
		  location.href="webFloor2.php?loc="+data.item.value;	
		}
      }
     });
	<?php } ?>
	<?php if ($mode == "grid") { ?>$("#btn_sort").buttonset();<?php } ?>
	<?php if ($mode == "floors" and isset($get_location)) { ?>$("#btn_floor").buttonset();<?php } ?>
	$("#btn_nav").buttonset();
	<?php if ($mode == "floors" and isset($get_location)) { ?>$("#btn_builder").button();<?php } ?>
	$( "#dino" ).button().click(function( event ) {
        if ($("#dino-btn").hasClass("ui-state-active")) {
			ajax_updater = setInterval('update_games()', 30000);
			$("#dino").attr("title", "Auto-Updating is Disabled");
		} else {
			clearInterval(ajax_updater);
			$("#dino").attr("title", "Auto-Updating is Enabled");
		}
		$('#dino').tooltip();
    });
});

function locSelect() {
	var new_loc = $("#btn_location").val();
	if (new_loc != "-1") {
		location.href="webFloor2.php?loc="+new_loc;	
	}
}
</script>
		<div id="toolbar" class="ui-widget-header<?php if ($mode == "grid") { echo " toolbar-help"; } ?>" style="padding-left: 15px;">
          <div style="width: 1200px;">
              <?php if ($_SESSION['access_level'] == "admin") { ?>
              <span id="btn_jurisdiction">
                <span title="Juris: 1 - ECS"><input type="radio" id="juris1" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 1) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=1'"><label for="juris1">1</label></span>
                <span title="Juris: 2 - NE Bankshot"><input type="radio" id="juris2" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 2) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=2'"><label for="juris2">2</label></span>
                <span title="Juris: 3 - Michigan"><input type="radio" id="juris3" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 3) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=3'"><label for="juris3">3</label></span>
				<span title="Juris: 5 - Iowa"><input type="radio" id="juris5" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 5) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=5'"><label for="juris5">5</label></span>
                <span title="Juris: 8 - Playmoore's"><input type="radio" id="juris8" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 8) { ?> checked="checked"<?php } ?>onclick="location.href='webFloor2.php?juris=8'"><label for="juris8">8</label></span>
              </span>
              <?php } else if ($_SESSION['juris_plus'] == 5) { ?>
			  <span id="btn_jurisdiction">
				<span title="Nebraska"><input type="radio" id="juris2" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 2) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=2'"><label for="juris2">NE</label></span>
				<span title="Iowa"><input type="radio" id="juris5" name="btn_jurisdiction"<?php if ($_SESSION['juris'] == 5) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?juris=5'"><label for="juris5">IA</label></span>
			  </span>
			  <?php } ?>
              
              <?php if ($mode == "floors") { ?>
			  <span title="Select a Location">
              	<select name="btn_location" id="btn_location" title="Select a Location" style="z-index: 9999;">
                	<option value="-1">Select a Location</option>
                    <?php for ($i=0;$i<sizeof($location_list);$i++) { ?>	
                    <option value="<?php echo $location_list[$i]['id']; ?>"<?php if ($get_location == $location_list[$i]['id']) { echo " SELECTED"; } ?>><?php echo $location_list[$i]['name']; ?></option>
                    <?php } ?>
                </select>
              </span>
              <?php } ?>
              
              <?php if ($mode == "grid") { ?>
              <span id="btn_sort">
                <span title="Sort by Name"><input type="radio" id="sort_name" name="btn_sort"<?php if (!isset($_GET['sort']) or (isset($_GET['sort']) and $_GET['sort'] == "name")) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=name'"><label for="sort_name">N</label></span>
                <span title="Sort by City"><input type="radio" id="sort_city" name="btn_sort"<?php if (isset($_GET['sort']) and $_GET['sort'] == "city") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=city'"><label for="sort_city">C</label></span>
                <?php if ($_SESSION['access_level'] == "admin" or $_SESSION['access_level'] == "overlord") { ?><span title="Sort by Vendor"><input type="radio" id="sort_vendor" name="btn_sort"<?php if (isset($_GET['sort']) and $_GET['sort'] == "vendor") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=vendor'"><label for="sort_vendor">V</label></span><?php } ?>
                <span title="Sort by Status"><input type="radio" id="sort_status" name="btn_sort"<?php if (isset($_GET['sort']) and $_GET['sort'] == "status") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=status'"><label for="sort_status">S</label></span>
                <span title="Sort by Game Type"><input type="radio" id="sort_gametype" name="btn_sort"<?php if (isset($_GET['sort']) and $_GET['sort'] == "type") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=type'"><label for="sort_gametype">G</label></span>
                <?php if ($_SESSION['access_level'] == "admin") { ?><span title="Sort by Server ID"><input type="radio" id="sort_id" name="btn_sort"<?php if (isset($_GET['sort']) and $_GET['sort'] == "id") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?sort=id'"><label for="sort_id">I</label></span><?php } ?>
              </span>
              <?php } ?>
              
              <?php if ($mode == "floors" and isset($get_location)) { ?>
              <span id="btn_floor">
                <span title="Floor Layout"><input type="radio" id="nav_floor" name="btn_floor"<?php if (!isset($_GET['floor'])) { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?loc=<?php echo $get_location; ?>'"><label for="nav_floor">Floor</label></span>
                <span title="Grid Layout"><input type="radio" id="nav_grid" name="btn_floor"<?php if (isset($_GET['floor']) and $_GET['floor'] == "grid") { ?> checked="checked"<?php } ?> onclick="location.href='webFloor2.php?floor=grid&loc=<?php echo $get_location; ?>'"><label for="nav_grid">Grid</label></span>
              </span>
              <?php } ?>
              
              <span id="btn_nav">
                <span title="Home"><input type="radio" id="nav_home" name="btn_nav" onclick="location.href='index.php'"><label for="nav_home">Home</label></span>
                <span title="Switch to Mobile Version"><input type="radio" id="nav_mobile" name="btn_nav" onclick="location.href='webFloor2_mobile.php'"><label for="nav_mobile">Mobile</label></span>
                <span title="Log Out"><input type="radio" id="nav_logout" name="btn_nav" onclick="location.href='logout.php'"><label for="nav_logout">Log Out</label></span>
              </span>
              
              <?php if ($mode == "floors" and isset($get_location)) { ?>
              <button id="btn_builder" title="Open this location in FloorBuilder" onclick="location.href='floorBuilder.php?loc=<?php echo $get_location; ?>';">FloorBuilder</button>
              <?php } ?>
			  
			  <?php $dino_names = array(
				'01' => 'Y2K Mode',
				'02' => 'Dinosaur Mode',
				'03' => 'AMD Duron Mode',
				'04' => 'Windows 3.1 Mode',
				'05' => 'Windows 6.1 Mode',
				'06' => 'Broken Heart Mode',
				'07' => 'Dinosaur Mode',
				'08' => 'Kyles Couch Mode',
				'09' => 'Turtle Mode',
				'10' => 'Pentium 2 Mode',
				'11' => 'Drunk Mode',
				'12' => '486 Mode',
				'13' => 'Dinosaur Mode',
				'14' => 'Has this gotten old yet?',
				'15' => 'Dinosaur Mode',
				'16' => 'HTML4 Mode',
				'17' => 'Land Before Time Mode',
				'18' => 'Kyles Server Farm Mode',
				'19' => 'Nursery Mode',
				'20' => 'Saved by the Bell Mode',
				'21' => 'Windows 98 Mode',
				'22' => 'Kyles Couch Mode',
				'23' => 'Hot Carl Mode',
				'24' => 'Dinosaur Mode',
				'25' => 'Kenny Mode (Remember Him?)',
				'26' => 'Tonn Mode',
				'27' => 'Confederate Flag Mode',
				'28' => 'Sparkler Mode',
				'29' => 'Anniversary Mode',
				'30' => 'Dinosaur Mode',
				'31' => 'Dinosaur Mode',
				'32' => 'Windows 98 Mode',
				'33' => 'Pentium 4 Mode',
				'34' => 'Kyles Couch Mode',
				'35' => 'Firefox Mode',
				'36' => 'Kyles Server Farm Mode',
				'37' => 'Nursery Mode',
				'38' => 'Are You Still Laughing?',
				'39' => 'Why So Serious? Mode',
				'40' => 'Jurassic Park III Mode',
				'41' => 'Stegosaurus Mode',
				'42' => 'Windows 98 Mode',
				'43' => 'Windows ME Mode',
				'44' => 'Trick-r-Treat Mode',
				'45' => 'Hairy Beast Mode',
				'46' => 'Carl Mode',
				'47' => 'Pentium 3 Mode',
				'48' => 'Windows 98 Mode',
				'49' => 'Windows 98 Mode',
				'50' => 'Celeron Mode',
				'51' => 'Santas Broken Sleigh Mode',
				'52' => 'New Years Morning Mode'
			  ); ?>
			  <input type="checkbox" id="dino" title="Auto-Updating is Disabled"><label for="dino" id="dino-btn"><?php if ($_SESSION['access_level'] == "admin" and $_SESSION['vendor'] == 0) { echo $dino_names[date('W')]; } else { ?>Dinosaur Mode<?php } ?></label>
          </div>
        </div>