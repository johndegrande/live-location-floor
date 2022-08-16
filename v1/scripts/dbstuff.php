<?php
$hostname='localhost';
$username='bankshotgame';
$password='3d512bba20a37f26043bdf73fe6cac30';
$dbname='bankshotgame';
mysql_connect($hostname,$username, $password) OR DIE ('Unable to connect to database! Please try again later.');
mysql_select_db($dbname);
?>