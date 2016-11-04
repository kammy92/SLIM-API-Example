<?php  
ob_start();
function getZoneByLatLng($latLng) {
	$userLocality=array();
 	$sFile = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$latLng&key=AIzaSyCqEFJfP_14dLP-VdKOobqTV1c3MKIkKwU", False);
	$data=json_decode($sFile);
	$extract=$data->results[0]->address_components;
	//echo "<pre>";
	//print_r($extract);
	//echo "</pre>";
	$count=count($extract);
	for($i=0; $i<$count; $i++) {
		$types=$extract[$i]->types;
		$countypes=count($types);
		for($j=0; $j<$countypes; $j++) {
	 		$locality= $types[$j];
	 		if($locality=="administrative_area_level_1") {
		 		$userLocality['circle']=$extract[$i]->long_name;
	 		}
			if($locality=="administrative_area_level_2") {
		 		$userLocality['zone']=$extract[$i]->long_name;
	 		}
	  		if($locality=="locality") {
		 		$userLocality['locality']=$extract[$i]->long_name;
	 		}
	 		if($locality=="sublocality_level_1") {
		 		$userLocality['sublocality_level']=$extract[$i]->long_name;
	 		}
		}
	}
	return $userLocality;
}

function getZoneIdByZone($zone) {
	$qry=mysql_query("SELECT id FROM service_region WHERE region_name='$zone'");
	$fetch=mysql_fetch_array($qry);
 	$zoneId=$fetch['id'];
	return $zoneId;
}


ob_flush();
?>