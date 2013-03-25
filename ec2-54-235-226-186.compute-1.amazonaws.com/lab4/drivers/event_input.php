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

	$json = file_get_contents('php://input');

	$event = json_decode($json);
	
	$con = mysql_connect("localhost", "root", "altair8");
	mysql_select_db("driver_site2", $con);
	
	if($event->_domain == "rfq")
	
		if($event->_name == "delivery_ready"){
	
			$sql = "SELECT id,guild_esl,driver_id FROM GUILDS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$guild_id = $row['guild_id'];
			$guild_esl = $row['guild_esl'];
			$driver_id = $row['driver_id'];
			
			$sql = "SELECT name,username,latitude,longitude,phone_number FROM DRIVERS WHERE id=" . $driver_id;
					
			$result = mysql_query($sql, $con);
			
			if(!$result){
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
			$name = $row['name'];
			$username = $row['username'];
			$phone_number = $row['phone_number'];
			
			$automatic_bid = false;
			$distance = -1;

			if($row['latitude'] == null){
			
				$automatic_bid = false;
			}
			else{
				$distance = distance($row['latitude'], $row['longitude'], $event->delivery_latitude, $event->delivery_longitude);
				$automatic_bid = $distance < $max_distance;
			}
			
			$text_message = "";
		
			if($automatic_bid){
			
				$request = json_encode(array(
				"_domain" => "rfq"
				, "_name" => "bid_available"
				, "code" => $event->code
				, "driver_name" => $name
				, "estimated_delivery_time" => ($distance * 60)));

				$ch = curl_init($guild_esl);
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
						. " distance: " . $distance . " mi Shop: " . $event->flower_shop_name;
				
			}
			else{
				
				$sql = "INSERT INTO DELIVERIES (driver_id,unique_delivery_id,guild_id) VALUES ("
						. $driver_id . ",'" . $event->code . "'," . $guild_id . ")";
						
				if(!mysql_query($sql, $con)){
		
					die("error: " . mysql_error() . " sql: " . $sql);
				}
				
				if($distance == -1){
				
					$distance = "unknown";
				}
				
				$text_message = "Bid? Delivery at: " . $event->delivery_latitude . "," . $event->delivery_longitude
						. " distance: " . $distance . " Shop: " . $event->flower_shop_name;
			}
			
			// send text message to $phone_number
			require "./twilio-php/Services/Twilio.php";

			$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
			$authToken = "38353271ed843f8df4d0aac99db0c233";

			$client = new Services_Twilio($accountSID, $authToken);
			$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
			
		}
		else if($event->_name == "bid_awarded"){
		
			$sql = "SELECT id FROM DELIVERIES WHERE unique_delivery_id='" . $event->code . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);			
			
			$delivery_id = $row['id'];
		
			$sql = "SELECT driver_id FROM GUILDS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$driver_id = $row['driver_id'];
			
			$sql = "UPDATE drivers SET current_delivery_id=" . $delivery_id . " WHERE id=" . $driver_id;
			
			if(!mysql_query($sql, $con)){
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$sql = "SELECT phone_number FROM DRIVERS WHERE id=" . $driver_id;
					
			$result = mysql_query($sql, $con);
			
			if(!$result){
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$phone_number = $row['phone_number'];
			
			$text_message = "Bid awarded from shop: " . $event->flower_shop_name . " at " . $event->flower_shop_latitude . ","
					. $event->flower_shop_longitude . ". Destination: " . $event->destination_latitude . "," . $event->destination_longitude . ".";
			
			// send text message to $phone_number
			require "./twilio-php/Services/Twilio.php";
	
			$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
			$authToken = "38353271ed843f8df4d0aac99db0c233";

			$client = new Services_Twilio($accountSID, $authToken);
			$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
		}
	}	
?>