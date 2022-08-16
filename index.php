<?php $page_title = "portal_home";
require("scripts/header_mobile.php"); 
include('scripts/mobile_detect.php');
$detect = new Mobile_Detect(); ?>
<p>
<h2>webFloor v2.0</h2>
<?php if ($_SESSION['access_level'] == "admin") { ?>
	<a href="webFloor2.php" rel="external" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-grid">Launch<br />webFloor v2.0 Web</a>
    <a href="webFloor2_mobile.php" rel="external" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-grid">Launch<br />webFloor v2.0 Mobile</a>
<?php } else { ?>
	<a href="webFloor2<?php if ($detect->isMobile()) { echo "_mobile"; } ?>.php" rel="external" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-grid">Launch<br />webFloor v2.0</a>
<?php } ?>
</p>
<hr />
<p>
<h2>Messages</h2>
<?php
	$sql = "SELECT message FROM floorUsers WHERE username = '$_SESSION[username]' AND password_hash = '$_SESSION[password]' LIMIT 1";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	extract(mysql_fetch_assoc($result));
	if ($message == '') {
		echo "You currently have no new messages.";
	} else {
		echo $message;
	}
?>
</p>
<hr />
<p>
<h2>Documents</h2>
<?php
	$sql = "SELECT * FROM floorPortalLinks WHERE juris = " . $_SESSION['juris'] . " AND access_level LIKE '%" . $_SESSION['access_level'] . "%' ORDER BY name";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	while($row=mysql_fetch_assoc($result)) {
		extract($row); ?>
        <a href="<?php echo $url; ?>" data-rel="external" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-info" target="_blank"><?php echo $name; ?></a>	
<?php } ?>
</p>
<?php if ($_SESSION['access_level'] == "admin") { ?>
<hr />
<p>
<h2>Admin Tools</h2>
<a href="http://www.bankshotgame.com/feelmypain" data-rel="external" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-edit" target="_blank">phpMyAdmin</a>
<h3>Hasher Tool</h3>
<label for="hasher_string">Hash String:</label>
<input name="hasher_string" id="hasher_string" type="text" size="20" maxlength="32" />
<a onclick="hash_string()" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-action">Generate Hash</a>
<script type = "text/javascript">
function hash_string() {
	var hashURL = "scripts/json_hasher.php?string="+document.getElementById('hasher_string').value;
	$.getJSON(hashURL, function(data) {
		document.getElementById('hasher_string').value = data.hash_string;
		//window.alert(data.hash_string); //uncomment this for debug
	});
} //end hash_string
</script>
</p>
<?php } ?>
<?php require("scripts/footer_mobile.php"); ?>