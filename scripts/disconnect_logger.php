<?php include("dbstuff.php");

$sql = "SELECT * FROM floorLocations";
$result = mysql_query($sql) or die ("Couldn't execute query.");

while($row = mysql_fetch_assoc($result)) {
	extract($row);

	$sql_lastdisconnect = "SELECT convert_tz(lastdisconnect, 'system', '$time_offset') AS lastdisconnect FROM floorDisconnectLog WHERE id=$id AND juris=$juris LIMIT 1";
	$result_lastdisconnect = mysql_query($sql_lastdisconnect) or die ("Couldn't execute query.2");
	if (mysql_num_rows($result_lastdisconnect) == 0) {
		$sql_lastseen = "SELECT lastseen AS lastseen_unconvert FROM floorLocations WHERE id=$id AND vendor=$vendor AND juris=$juris LIMIT 1";
		$result_lastseen = mysql_query($sql_lastseen) or die ("Couldn't execute query.3");
		extract(mysql_fetch_assoc($result_lastseen));
		$lastdisconnect = $lastseen;
		$sql_lastdisconnect = "INSERT INTO floorDisconnectLog VALUES ('$juris', '$id', '$lastdisconnect')";
		$result_lastdisconnect = mysql_query($sql_lastdisconnect) or die ("Couldn't execute query.4");
	} else {
		if ($ten!=1 and $lastseen != $lastdisconnect) {
			$sql_lastseen = "SELECT lastseen AS lastseen_unconvert FROM floorLocations WHERE id=$id AND vendor=$vendor AND juris=$juris LIMIT 1";
			$result_lastseen = mysql_query($sql_lastseen) or die ("Couldn't execute query.6");
			extract(mysql_fetch_assoc($result_lastseen));
			$sql_lastdisconnect = "UPDATE floorDisconnectLog SET lastdisconnect='$lastseen_unconvert' WHERE id=$id AND juris=$juris";
			$result_lastdisconnect = mysql_query($sql_lastdisconnect) or die ("Couldn't execute query.7");
		}
		extract(mysql_fetch_assoc($result_lastdisconnect));
	}
}
?>