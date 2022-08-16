<?php
	session_start();
	require("scripts/dbstuff.php");
	include("scripts/get_timezone.php");
	
	if (isset($_POST['uname']) and isset($_POST['upass'])) {
		$uname = mysql_real_escape_string(strtolower($_POST['uname']));
		$upass = mysql_real_escape_string(strtolower($_POST['upass']));
		if ($uname != '' and $upass != '') {
			$sql = "SELECT * FROM floorUsers WHERE username = '$uname' AND password = '$upass' LIMIT 1";
			$result = mysql_query($sql) or die ("Couldn't execute query.");
			if (mysql_num_rows($result) == 0) {
				header("Location: login.php");
			} else {
				$row = mysql_fetch_assoc($result);
				extract($row);
				$_SESSION['vendor'] = $vendor;
				$_SESSION['juris'] = $juris;
				$_SESSION['name'] = $name;
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
			}
		} else {
			header("Location: login.php");
		}
	}

	include("scripts/user_check.php");
	header("Location: webFloor.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="scripts/main.css" />
<title>AAI Operator Portal</title>
</head>
<style>
li {
	font-size: 16px;
}
</style>
<body bgcolor="#000000">

</body>
</html>