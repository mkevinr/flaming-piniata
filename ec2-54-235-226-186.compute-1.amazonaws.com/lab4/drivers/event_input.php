<?php

	file_put_contents("driver_event_input_test", "Gets to driver_event_input_test!");

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
	
	file_put_contents("driver_event_input_test", "\nGets to before if 1", FILE_APPEND);
	if($event->_domain == "rfq"){
	
		file_put_contents("driver_event_input_test", "\nGets in if 1", FILE_APPEND);
		if($event->_name == "delivery_ready"){
	
			$sql = "SELECT id,guild_esl,driver_id FROM GUILDS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$guild_id = $row['id'];
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
				
				if($distance == -1){
				
					$distance = "unknown";
				}
				
				$text_message = "Bid? Delivery at: " . $event->delivery_latitude . "," . $event->delivery_longitude
						. " distance: " . $distance . " Shop: " . $event->flower_shop_name;
			}
			
			$sql = "INSERT INTO DELIVERIES (driver_id,unique_delivery_id,guild_id,destination_latitude,destination_longitude) VALUES ("
					. $driver_id . ",'" . $event->code . "'," . $guild_id . "," . $event->delivery_latitude . ","
					. $event->delivery_longitude . ")";
						
			if(!mysql_query($sql, $con)){
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			// send text message to $phone_number
			require "./twilio-php/Services/Twilio.php";

			$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
			$authToken = "38353271ed843f8df4d0aac99db0c233";

			$client = new Services_Twilio($accountSID, $authToken);
			$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
			
		}
		else if($event->_name == "bid_awarded"){
		
			file_put_contents("debug_test", "gets into bid_awarded");
			file_put_contents("driver_event_input_test", "\nGets in bid awarded", FILE_APPEND);
		
			$sql = "SELECT id FROM DELIVERIES WHERE unique_delivery_id='" . $event->code . "'";
			
			file_put_contents("driver_event_input_test", "\nbid awarded $event->code: " . $event->code, FILE_APPEND);
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);			
			
			file_put_contents("driver_event_input_test", "\nbid awarded after sql 1", FILE_APPEND);
			
			$delivery_id = $row['id'];
		
			$sql = "SELECT driver_id FROM GUILDS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			file_put_contents("driver_event_input_test", "\nbid awarded after sql 2", FILE_APPEND);
			$driver_id = $row['driver_id'];
			file_put_contents("driver_event_input_test", "\nbid awarded gets here 1", FILE_APPEND);
			file_put_contents("debug_test", "delivery_id: " . $delivery_id . " driver_id: " . $driver_id, FILE_APPEND);
			$sql = "UPDATE DRIVERS SET current_delivery_id=" . $delivery_id . " WHERE id=" . $driver_id;
			file_put_contents("driver_event_input_test", "\nbid awarded gets here 2", FILE_APPEND);
			
			if(!mysql_query($sql, $con)){
			
				file_put_contents("driver_event_input_test", "\nbid awarded gets here 3", FILE_APPEND);
				file_put_contents("driver_event_input_test", "\nbid awarded sql error: " . mysql_error()
						. " sql: " . $sql, FILE_APPEND);
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("driver_event_input_test", "\nbid awarded gets here 4", FILE_APPEND);
			$sql = "SELECT phone_number FROM DRIVERS WHERE id=" . $driver_id;
			file_put_contents("driver_event_input_test", "\nbid awarded gets here 5", FILE_APPEND);
					
			$result = mysql_query($sql, $con);
			file_put_contents("driver_event_input_test", "\nbid awarded gets here 6", FILE_APPEND);
			
			if(!$result){
		
				file_put_contents("driver_event_input_test", "\nbid awarded gets here 7", FILE_APPEND);
				file_put_contents("driver_event_input_test", "\nbid awarded sql error: " . mysql_error()
						. " sql: " . $sql, FILE_APPEND);
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			file_put_contents("driver_event_input_test", "\nbid awarded after sql 3", FILE_APPEND);
			$phone_number = $row['phone_number'];
			
			$text_message = "Bid awarded from shop: " . $event->flower_shop_name . " at " . $event->flower_shop_latitude . ","
					. $event->flower_shop_longitude . ". Destination: " . $event->destination_latitude . "," . $event->destination_longitude . ".";
			
			// send text message to $phone_number
			require "./twilio-php/Services/Twilio.php";
	
			$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
			$authToken = "38353271ed843f8df4d0aac99db0c233";

			$client = new Services_Twilio($accountSID, $authToken);
			$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
			file_put_contents("driver_event_input_test", "\nbid awarded after text_message", FILE_APPEND);
		}
	}	
?>