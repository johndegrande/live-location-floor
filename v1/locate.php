<link rel="stylesheet" href="scripts/main.css" type="text/css" media="all" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<?php
	require("scripts/dbstuff.php");
	$location_id = mysql_real_escape_string($_GET['id']);
	$juris_id = mysql_real_escape_string($_GET['juris']);
	$sql = "SELECT * FROM locations WHERE id = '$location_id' AND juris = '$juris_id' LIMIT 1";
	$result = mysql_query($sql) or die ("Couldn't execute query.");
	$row = mysql_fetch_assoc($result);
	extract($row);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BankShot Operator</title>
</head>

<body onLoad="initialize()">
<?php if ($lat != '' AND $lng != '' AND $location_id != '') { ?>
	<script type="text/javascript">
	  var infowindow;
	(function () {
	
	  google.maps.Map.prototype.markers = new Array();
		
	  google.maps.Map.prototype.addMarker = function(marker) {
		this.markers[this.markers.length] = marker;
	  };
		
	  google.maps.Map.prototype.getMarkers = function() {
		return this.markers
	  };
		
	  google.maps.Map.prototype.clearMarkers = function() {
		if(infowindow) {
		  infowindow.close();
		}
		
		for(var i=0; i<this.markers.length; i++){
		  this.markers[i].set_map(null);
		}
	  };
	})();
	  
	  function initialize() {
		var latlng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);
		var myOptions = {
		  zoom: 16,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		  //mapTypeId: google.maps.MapTypeId.SATELLITE
		};
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		var a = new Array();
		var t =  new Object();
		t.name = "<?php echo $name; ?>"
		t.info = "<?php echo "<span class='mapstyle'><h3>" . $name . "</h3>" . $address . "<br>" . $city . "<br>" . $phone . "</span>"; ?>"
		t.lat =  "<?php echo $lat; ?>"
		t.lng =  "<?php echo $lng; ?>"
		a[0] = t;
	  
		for (var i = 0; i < a.length; i++) {
			var latlng = new google.maps.LatLng(a[i].lat, a[i].lng);
			map.addMarker(createMarker(a[i].name,latlng,a[i].info));
		 }
		console.log(map.getMarkers());    
		
		console.log(map.getMarkers());    
	  }
	  
	  function createMarker(name, latlng, info) {
		var marker = new google.maps.Marker({position: latlng, map: map, title: name});
		//This part is only for the singular locations, it will automatically open the infowindow upon loading
		if (infowindow) infowindow.close();
		infowindow = new google.maps.InfoWindow({content: info});
		infowindow.open(map, marker);
		google.maps.event.addListener(marker, "click", function() {
		  if (infowindow) infowindow.close();
		  infowindow = new google.maps.InfoWindow({content: info});
		  infowindow.open(map, marker);
		});
		return marker;
	  }
	
	</script>
	<div id="map_canvas" style="width: 100%; height: 100%"></div>
    <?php } else { echo "<span class='mapstyle'>Location is not defined. Please try again later.</span>\n"; } ?>
</body>
</html>