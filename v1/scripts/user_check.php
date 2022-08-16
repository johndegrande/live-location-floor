<?php
	if (!isset($_SESSION['vendor']) or !isset($_SESSION['juris']) or !isset($_SESSION['name']) or !isset($_SESSION['username']) or !isset($_SESSION['password'])) {
		header("Location: login.php");
	} else {
		$sql = "SELECT * FROM floorUsers WHERE username = '$_SESSION[username]' AND password = '$_SESSION[password]' LIMIT 1";
		$result = mysql_query($sql) or die ("Couldn't execute query.1");
		if (mysql_num_rows($result) == 0) {
			header("Location: login.php");
		} else {
			$row = mysql_fetch_assoc($result);
			extract($row);
			if ($vendor != $_SESSION['vendor'] or $juris != $_SESSION['juris'] or $name != $_SESSION['name'] or $username != $_SESSION['username'] or $password != $_SESSION['password']) {
				header("Location: login.php");
			}
		}
	}
?>