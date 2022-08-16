<?php require("scripts/dbstuff.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Big Texas Counter</title>
</head>

<body>
<h2>Big Texas Game Counts</h2>
<?php
	$sql = "SELECT vendor, name FROM floorUsers WHERE juris=2 ORDER BY vendor";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	while($row = mysql_fetch_assoc($result)) {
		extract($row);
		$vendors[$vendor] = $name;		
	}
	
	$sql = "SELECT `vendor`, COUNT(`game_id`) AS `game_cnt` FROM `floorLocations` WHERE `juris`=2 AND `game_type` = 'BIG TEXAS' AND `vendor`!=5 AND `vendor`!=32 GROUP BY `vendor`";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	while($row = mysql_fetch_assoc($result)) {
		extract($row);
		if ($vendor == 1) { ?>
			<b><u>NTS</u></b><br />
            <?php echo $vendors[$vendor] . " - " . $game_cnt . "<br />"; ?>
            <br />
            <b><u>GAD</u></b><br />
		<?php } else {
			echo $vendors[$vendor] . " - " . $game_cnt . "<br />";
		}
	}
?>
</body>
</html>