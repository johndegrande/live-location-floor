<?php
	if (!isset($_SESSION['vendor']) or !isset($_SESSION['juris']) or !isset($_SESSION['name']) or !isset($_SESSION['username']) or !isset($_SESSION['password']) or !isset($_SESSION['access_level'])) {
		header("Location: login.php");
	} else {
		$sql = "SELECT * FROM floorUsers WHERE username = '$_SESSION[username]' AND password_hash = '$_SESSION[password]' LIMIT 1";
		$result = mysql_query($sql) or die ("Couldn't execute query.5");
		if (mysql_num_rows($result) == 0) {
			header("Location: login.php");
		} else {
			$row = mysql_fetch_assoc($result);
			extract($row);
			if ($vendor != $_SESSION['vendor'] or $name != $_SESSION['name'] or $username != $_SESSION['username'] or $password_hash != $_SESSION['password'] or $access_level != $_SESSION['access_level']) {
				header("Location: login.php");
			}
		}
	}
?>