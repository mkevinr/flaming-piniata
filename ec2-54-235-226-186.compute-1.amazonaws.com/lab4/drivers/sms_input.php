<?php

	function distance($lat1, $lng1, $lat2, $lng2, $miles = true)
	{
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$lng1 *= $pi80;
		$lat2 *= $pi80;
		$lng2 *= $pi80;
 
		$r = 6372.797; // mean radius of Earth in km
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$km = $r * $c;
 
		return ($miles ? ($km * 0.621371192) : $km);
	}

	$entityBody = file_get_contents('php://input');

	if($_REQUEST['Body'] == "bid anyway"){
	
		require './twilio-php/Services/Twilio.php';
		
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("driver_site2", $con);
		
		$sql = "SELECT * FROM DRIVERS WHERE phone_number='". $_REQUEST['From'] . "'";
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$driver_id = $row['id'];
		$username = $row['username'];
		$driver_latitude = $row['latitude'];
		$driver_longitude = $row['longitude'];
		
		$sql = "SELECT flower_shop_esl,code,latitude,longitude FROM DELIVERIES_READY WHERE driver_id=" . $driver_id
				. " ORDER BY id DESC";
				
		$result = mysql_query($sql, $con);
		
		if(mysql_num_rows($result) > 0){
		
			$row = mysql_fetch_array($result);
	
			$esl = $row['flower_shop_esl'];
			
			if($driver_latitude == null){
				
				$distance = 5;
			}
			else{
			
				$distance = distance($driver_latitude,$driver_longitude,$row['latitude'],$row['longitude']);
			}

  		    $request = array(
			"_domain" => "rfq"
			, "_name" => "bid_available"
			, "code" => $row['code']
			, "driver_name" => $username
			, "estimated_delivery_time" => $distance * 60);
			
			$request = json_encode($request);
		
			printf("$request: " . $request);

		    $ch = curl_init($esl);
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($request))
		    );
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    curl_exec($ch);
		}
	}
	else if($_REQUEST['Body'] == "complete"){
	
		$sql = "SELECT * FROM DRIVERS WHERE phone_number='". $_REQUEST['From'] . "'";
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$driver_id = $row['id'];
		
		$sql = "SELECT guild_esl FROM DELIVERIES JOIN GUILDS ON DELIVERIES.guild_id=GUILDS.id WHERE DELIVERIES.id=" . $row['current_delivery_id'];
		
		$esl = $row['guild_esl'];
	
		$request = array(
				"_domain" => "delivery"
				, "_name" => "complete");
				
		$request = json_encode($request);
			
		$ch = curl_init($esl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($request))
		);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		
		$sql = "DELETE FROM DELIVERIES WHERE driver_id=" . $driver_id;
			
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$sql = "UPDATE DRIVERS SET current_delivery_id=NULL WHERE driver_id=" . $driver_id;
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
	}
?>
