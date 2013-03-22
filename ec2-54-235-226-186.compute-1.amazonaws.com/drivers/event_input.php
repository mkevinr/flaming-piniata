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
	
	$max_distance = 50;

	$json = $entityBody = file_get_contents('php://input');
	var_dump($json);

	$event = json_decode($json);
	
	if($event->_domain == "rfq" && $event->_name == "delivery_ready"){
	
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("driver_site", $con);
		
		$sql = "SELECT flower_shop_esl,driver_id FROM FLOWER_SHOPS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";
				file_put_contents("driver_site_event_input_test", "\ngets here 1: " . $username, FILE_APPEND);
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$flower_shop_esl = $row['flower_shop_esl'];
		$driver_id = $row['driver_id'];
		
		$sql = "SELECT username,latitude,longitude,phone_number FROM DRIVERS WHERE id=" . $driver_id;
				
		$result = mysql_query($sql, $con);
		
		if(!$result){
	
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$username = $row['username'];
		$phone_number = $row['phone_number'];
		
		$automatic_bid = false;
		$distance = -1;

		if($row['latitude'] == null){
		
			$automatic_bid = false;
		}
		else{
			$distance = distance($row['latitude'], $row['longitude'], $event->flower_shop_latitude, $event->flower_shop_longitude);
			$automatic_bid = $distance < $max_distance;
		}
	
		if($automatic_bid){
		
			$request = json_encode(array(
			"_domain" => "rfq"
			, "_name" => "bid_available"
			, "code" => $event->code
			, "driver_name" => $username
			, "estimated_delivery_time" => 5));

			$ch = curl_init($flower_shop_esl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($request))
			);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			
			$text_message = "Automatically bid on delivery at" . $event->delivery_latitude . "," . $event->delivery_longitude
					. " for flower shop " . $event->flower_shop_name;
			
		}
		else{
			
			$sql = "INSERT INTO DELIVERIES_READY (driver_id,flower_shop_esl,code,latitude,longitude) VALUES ("
					. $driver_id . ",'" . $flower_shop_esl . "','" . $event->code . "'," . $event->delivery_latitude 
					. "," . $event->delivery_longitude . ")";
					
			$result = mysql_query($sql, $con);
	
			if(!$result){
	
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			if($distance == -1){
			
				$distance = "unknown";
			}
			
			$text_message = "Do you want to bid on the delivery at " . $event->delivery_latitude . "," . $event->delivery_longitude
					. " which is " . $distance . " miles away. Flower shop: " . $event->flower_shop_name;
		}
	}	
	
	// send text message to $phone_number
	require "./twilio-php/Services/Twilio.php";
	
	$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
	$authToken = "38353271ed843f8df4d0aac99db0c233";
	
	$client = new Services_Twilio($accountSID, $authToken);
	$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
?>