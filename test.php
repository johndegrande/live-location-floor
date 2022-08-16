<?php 
include("scripts/dbstuff.php");
//check the credentials on each page load
if ($page_title != "login") {
	print_r($_POST);
	if (isset($_POST['uname']) and isset($_POST['upass'])) {
		echo "test ";
		$uname = mysql_real_escape_string(strtolower($_POST['uname']));
		$upass = mysql_real_escape_string(strtolower($_POST['upass']));
		if ($uname != '' and $upass != '') {
			echo "test2 ";
			$sql = "SELECT * FROM floorUsers WHERE username = '$uname' LIMIT 1";
			$result = mysql_query($sql) or die ("Couldn't execute query.");
			if (mysql_num_rows($result) == 0) {
				echo "redirect1 ";
				//header("Location: login.php?uname=$uname");
			} else {
				$row = mysql_fetch_assoc($result);
				extract($row);
				$password_rehash = crypt($upass, $password_hash);
				echo $password_hash.' - '.$password_rehash;
				if ($password_rehash == $password_hash) {
					echo "test3 ";
					$_SESSION['vendor'] = $vendor;
					$_SESSION['juris'] = $juris;
					$_SESSION['name'] = $name;
					$_SESSION['username'] = $username;
					$_SESSION['password'] = $password_hash;
					$_SESSION['access_level'] = $access_level;
					if ($_SESSION['access_level'] == 'overlord' and $overlord_vendors !='') {
						$_SESSION['overlord_vendors'] = preg_split("/,/", $overlord_vendors);
					}
					print_r($_SESSION);
				} else {
					echo "redirect2 ";
					//header("Location: login.php?uname=$uname");	
				}
			}
		} else {
			echo "redirect3 ";
			//header("Location: login.php");
		}
	}
}
print_r($_POST); ?>