<?php
include("scripts/get_timezone.php");
include("scripts/status_codes.php");

//setting the date
if (date(I) == 1) { 
	$time_start = date(P)-"1:00";
	$time_offset = $time_start . ":00";
	$last_updated = date('m/d H:i:s', time()-3600);
} else {
	$time_offset = date(P);
	$last_updated = date('m/d H:i:s');
}

//change the jurisdiction if proper conditions are met
if (isset($_GET['juris']) and is_numeric($_GET['juris'])) {
	$juris_select = mysql_real_escape_string($_GET['juris']);
	$sql = "SELECT juris FROM floorOptions WHERE juris = '$juris_select'";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	if (mysql_num_rows($result) == 1) {
		$_SESSION['juris'] = $juris_select;	
	}
}
$juris_select = $_SESSION['juris'];

//check the credentials on each page load
if ($page_title != "login") {
	if (isset($_POST['uname']) and isset($_POST['upass'])) {
		$uname = mysql_real_escape_string(strtolower($_POST['uname']));
		$upass = mysql_real_escape_string(strtolower($_POST['upass']));
		if ($uname != '' and $upass != '') {
			$sql = "SELECT * FROM floorUsers WHERE username = '$uname' LIMIT 1";
			$result = mysql_query($sql) or die ("Couldn't execute query.");
			if (mysql_num_rows($result) == 0) {
				header("Location: login.php?uname=$uname");
			} else {
				$row = mysql_fetch_assoc($result);
				extract($row);
				$password_rehash = crypt($upass, $password_hash);
				if ($password_rehash == $password_hash) {
					$_SESSION['vendor'] = $vendor;
					$_SESSION['juris'] = $juris;
					$_SESSION['juris_plus'] = $juris_plus;
					$_SESSION['name'] = $name;
					$_SESSION['username'] = $username;
					$_SESSION['password'] = $password_hash;
					$_SESSION['access_level'] = $access_level;
					if ($_SESSION['access_level'] == 'overlord' and $overlord_vendors !='') {
						$_SESSION['overlord_vendors'] = preg_split("/,/", $overlord_vendors);
					}
				} else {
					header("Location: login.php?uname=$uname");	
				}
			}
		} else {
			header("Location: login.php");
		}
	}
	
	include("scripts/user_check.php");
}

//grab floor options so we know the default of everything
if (isset($_SESSION['juris'])) {
	$sql = "SELECT * FROM floorOptions WHERE juris = " . $_SESSION['juris'];
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	extract(mysql_fetch_assoc($result));
	$floor_name = $name;
} else { //these are just basic defaults, should never need these
	$portal_title_prefix = "AAI";
	$logo = "aai_logo.png";
	$theme_name = "c";
	$menu_bg_color = "#1f4d6b";
	$bg_color = "#cccccc";
	$floor_name = "webFloor 2.0";
}

if ($mode == "grid") { //make a vendor list, this will be used for several things
	$sql = "SELECT vendor, name FROM floorUsers WHERE juris='$_SESSION[juris]' OR juris_plus='$_SESSION[juris]' ORDER BY vendor";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	while ($row = mysql_fetch_assoc($result)) {
		extract($row);
		$vendor_list[$vendor] = $name;	
	}
} else if ($mode == "floors") { //get a location list, this will be used for several things
	$sql = "SELECT id, name FROM locations WHERE juris='$_SESSION[juris]' AND active='1' ORDER BY name, id";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	$i=0;
	while ($row = mysql_fetch_assoc($result)) {
		extract($row);
		$location_list[$i]['id'] = $id;
		$location_list[$i]['name'] = $name;
		if (isset($_GET['loc']) and is_numeric($_GET['loc']) and $_GET['loc'] == $id) {
			$cur_location_name = $name;
		}
		$i++;
	}	
}

//initiate some defaults for our upcoming SQL
if ($mode == "grid") {
	$sortby = "game_name, game_id, name ASC";
} else {
	$sortby = "game_id, game_name, name ASC";	
}
$vendor_select = '';
$location_select = '';

if ($mode == "grid") {
	if (isset($_GET['sort'])) { //various sorting techniques, feel free to add more
		if ($_GET['sort'] == "city") {
			$sortby = "city, name, game_id, game_name ASC";
			$sortby_type = "city";
		} else if ($_GET['sort'] == "vendor" and ($_SESSION['access_level'] == "admin" or $_SESSION['access_level'] == "overlord")) {
			$sortby = "vendor, name, game_id, game_name ASC";
			$sortby_type = "vendor";
		} else if ($_GET['sort'] == "status") {
			$sortby = "ten, five, status, game_name, game_id, name ASC";
			$sortby_type = "status";
		} else if ($_GET['sort'] == "type") {
			$sortby = "game_type, game_name, game_id, name ASC";
			$sortby_type = "type";
		} else if ($_GET['sort'] == "id" and ($_SESSION['access_level'] == "admin")) {
			$sortby = "id, game_id, game_name, name ASC";
			$sortby_type = "id";
		}
	}
	
	//determine if the user has access to multiple vendors data and then prep the SQL for this parameter
	if ($_SESSION['access_level'] == "vendor" or $_SESSION['access_level'] == "overlord") {
		$vendor_select = " AND (vendor = '$_SESSION[vendor]'";
		if ($_SESSION['access_level'] == "overlord") {
			for ($i=0;$i<sizeof($_SESSION['overlord_vendors']);$i++) {
				$vendor_select .= " OR vendor = '" . $_SESSION['overlord_vendors'][$i] . "'";
			}
		}	
		$vendor_select .= ")";
	}
} else if ($mode == "floors") { //adds a location number to the SQL
	if (isset($_GET['loc']) and is_numeric($_GET['loc'])) {
		$get_location = mysql_real_escape_string($_GET['loc']);
		$location_select = " AND id='$get_location' ";
	}
}

//we'll just put this here
$distinct_loc_array = array();

//let's build out the SQL
$sql = "SELECT juris, vendor, id, name, city, game_id, game_name, game_type, status, chippies, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -300) AS five, (TIMESTAMPDIFF(SECOND,now(),lastseen) > -600) AS ten, convert_tz(lastseen, 'system', '$time_offset') AS lastseen, xpos, ypos, escrow FROM floorLocations WHERE juris='" . $juris_select . "'" . $vendor_select . '' . $location_select . " ORDER BY " . $sortby;
$result = mysql_query($sql) or die ("Couldn't execute query.");

//first, we're going to pass through and create a search array, this will be used in several places to do search counts
while($row = mysql_fetch_assoc($result)) {
	if ($row['ypos'] == 0) {
		$row['pos_set'] = 0;
	} else {
		$row['pos_set'] = 1;
	}
	
	$search_array[] = $row;
	
	if (!in_array($row['id'], $distinct_loc_array)) {
		array_push($distinct_loc_array, $row['id']); 
	}
}
$distinct_locations = sizeof($distinct_loc_array);

//let's run the SQL again so we can use it to set up the floor
$result = mysql_query($sql) or die ("Couldn't execute query.");

//this is the function that does search counts
function search_result_array($search_array, $match, $column) {
     $count = 0;
	 for($i=0;$i<sizeof($search_array);$i++) {
	 	if ($search_array[$i][$column] == $match) { $count++; }
	 }
     return $count;
}

//let's do some conditionals to test for certain circumstances to output an message to the user
if ($sortby_type == "status") {
	$error_output = "NOTE: The Status categories and the individual games assigned to them do no update automatically. A page reload is required to re-sort the games.";
} else if ($mode == "floors" and !isset($_GET['loc'])) {
	$error_output = "NOTE: Please select a location from the menu to view games.";
} else if ($mode == "floors" and $_GET['floor'] != "grid") {
	$counter = search_result_array($search_array, 0, 'pos_set');
	if ($counter > 0) { $force_grid = 1; }
	if ($counter > 0 and $_SESSION['access_level'] == "admin") {
		$error_output = "NOTE: We have detected that the game positioning has not been setup. Please use the FloorBuilder to arrange the games. In the mean time, this location will display in Grid mode.";
	}
} 
?>