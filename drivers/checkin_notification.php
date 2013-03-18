<?php

	file_put_contents("checkin_test.txt", "Called checkin_notification.php!");

	/*$checkin = json_decode($_REQUEST['checkin']);
	$four_square_user_id = $checkin->user->id;
	$location = $checkin->venue->location;
	$latitude = $location->lat;
	$longitude = $location->lng;
	
	$con = mysql_connect("localhost","root","altair8");
    mysql_select_db("driver_site", $con);	
	
	$sql = "SELECT id FROM DRIVERS WHERE four_square_user_id=" . $four_square_user_id;
	$result = mysql_query($sql, $con);
	
	if(!$result){
	
		die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		$sql = "UPDATE DRIVERS SET latitude=" . $latitude ",longitude=" . $longitude;
		if(!mysql_query($sql, $con)){
		
			die('Error: ' . mysql_error() . " sql: " . $sql);
		}
	}*/
?>