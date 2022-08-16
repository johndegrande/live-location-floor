<?php
if (isset($_GET['string']) and $_GET['string']!='') {
	if (CRYPT_SHA256 == 1) {
		$password_hash['hash_string'] = crypt($_GET['string'], '$5$rounds=5000$fridayjanuary05oftheyear2012$');
	}
} else {
	$password_hash['hash_string'] = "Invalid Data. Try Again!";	
} 
echo json_encode($password_hash);
?>