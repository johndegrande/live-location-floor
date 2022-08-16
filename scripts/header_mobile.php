<?php session_start();
include("scripts/dbstuff.php");

include("scripts/initialize.php");
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="scripts/themes/webFloor_styles.min.css" />
<link rel="stylesheet" href="scripts/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="scripts/jquery.mobile.structure-1.4.3.min.css" />
<!--<link rel="stylesheet" href="scripts/jquery.mobile-1.4.3.min.css" />-->
<link rel="stylesheet" href="scripts/mobile.responsive.css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<script src="scripts/jquery-2.1.1.min.js"></script>
<script src="scripts/jquery.mobile-1.4.3.min.js"></script>
<title><?php echo $portal_title_prefix; ?> Operator Portal</title>
</head>

<body<?php if ($page_title == "webFloor_mobile") { ?> onLoad="update_games(); setInterval('update_games()', 30000);"<?php } ?>>
<div data-role="page" data-theme="<?php echo $theme_name; ?>">
<?php if ($page_title != "login") { ?>
<div data-role="panel" data-position="right" data-position-fixed="true" data-theme="<?php echo $theme_name; ?>" data-display="overlay" id="opt_panel" style="background: <?php echo $menu_bg_color; ?>;">
    <ul data-role="listview">
        <li data-icon="delete"><a href="#" data-rel="close">Close Menu</a></li>
        <?php if ($page_title == "webFloor_mobile" and $mode == "grid") { 
			if (isset($_GET['sort']) or (isset($_GET['sort']) and $_GET['sort'] != "name")) { ?><li data-icon="bars"><a href="webFloor2_mobile.php?sort=name" target="_self">Sort By: Name</a></li><?php }
			if ($_GET['sort'] != "city") { ?><li data-icon="bars"><a href="webFloor2_mobile.php?sort=city" target="_self">Sort By: City</a></li><?php }
			if ($_GET['sort'] != "vendor" and ($_SESSION['access_level'] == "admin" or $_SESSION['access_level'] == "overlord")) { ?><li data-icon="bars"><a href="webFloor2_mobile.php?sort=vendor" target="_self">Sort By: Vendor</a></li><?php }
		} else if ($page_title == "webFloor_mobile" and $mode == "floors") {
			for ($i=0;$i<sizeof($location_list);$i++) { ?>
				<li data-icon="location"><a href="webFloor2_mobile.php?loc=<?php echo $location_list[$i]['id']; ?>" target="_self"><?php echo $location_list[$i]['name']; ?></a></li>	
			<?php }
		}
        if ($_SESSION['juris'] != "1" and $_SESSION['access_level'] == "admin") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=1" target="_self">Juris: 1 - ECS</a></li><?php }
        if ($_SESSION['juris'] != "2" and $_SESSION['access_level'] == "admin") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=2" target="_self">Juris: 2 - NE Bankshot</a></li><?php }
        if ($_SESSION['juris'] != "3" and $_SESSION['access_level'] == "admin") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=3" target="_self">Juris: 3 - Michigan</a></li><?php }
		if ($_SESSION['juris'] != "5" and $_SESSION['access_level'] == "admin") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=5" target="_self">Juris: 5 - Iowa</a></li><?php }
        if ($_SESSION['juris'] != "8" and $_SESSION['access_level'] == "admin") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=8" target="_self">Juris: 8 - Playmoore's</a></li><?php } 
		if ($_SESSION['juris'] != "2" and $_SESSION['juris_plus'] == 5) { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=2" target="_self">Nebraska Locations</a></li><?php }
		if ($_SESSION['juris'] != "5" and $_SESSION['juris_plus'] == "5") { ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?juris=5" target="_self">Iowa Locations</a></li><?php } ?>
        <?php if ($page_title == "webFloor_mobile") { ?><li data-icon="action"><a href="webFloor2.php" target="_self">Switch to Web Version</a></li><?php } ?>
        <li data-icon="back"><a href="logout.php" data-rel="external">Log Out</a></li>
    </ul>
</div>
<!-- /panel -->
<?php } ?>
<div data-role="header" data-position="fixed">
    <?php if ($page_title != "portal_home") { ?><a href="index.php" target="_self" class="ui-btn-left ui-btn ui-shadow ui-corner-all ui-icon-home ui-btn-icon-notext ui-btn-inline">Home</a><?php } ?>
    <h1 style="white-space: normal; margin: 0 38px 0 38px;"><?php if ($page_title == 'login') { echo "webFloor 2.0"; } else { echo $_SESSION['name']; } ?></h1>
    <?php if ($page_title != "login") { ?><a href="#opt_panel" class="ui-btn-right ui-btn ui-shadow ui-corner-all ui-icon-bars ui-btn-icon-notext ui-btn-inline">Options</a><?php } ?>
</div>
<!-- /header -->

<div role="main" class="ui-content">