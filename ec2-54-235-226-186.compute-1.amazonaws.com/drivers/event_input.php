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

	file_put_contents("driver_site_event_input_test", "got_here");

	$json = $entityBody = file_get_contents('php://input');
	file_put_contents("driver_site_event_input_test",$json);
	var_dump($json);

	$event = json_decode($json);
	file_put_contents("driver_site_event_input_test", "\n" .var_dump($event), FILE_APPEND);
	
	if($event->_domain == "rfq" && $event->_name == "delivery_ready"){
	
		file_put_contents("driver_site_event_input_test", "\nGets in if!", FILE_APPEND);
	
		print("<p><b>in if</b></p><br/>");
	
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("driver_site", $con);
		
		file_put_contents("driver_site_event_input_test", "\ndriver_esl_token: " . $_REQUEST['esl_token'], FILE_APPEND);
		
		$sql = "SELECT flower_shop_esl,driver_id FROM FLOWER_SHOPS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";
				file_put_contents("driver_site_event_input_test", "\ngets here 1: " . $username, FILE_APPEND);
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		file_put_contents("driver_site_event_input_test", "\number of rows from first query: " . mysql_num_rows($result), FILE_APPEND);
		
		$row = mysql_fetch_array($result);
		$flower_shop_esl = $row['flower_shop_esl'];
		$driver_id = $row['driver_id'];
		
		$sql = "SELECT username,latitude,longitude,phone_number FROM DRIVERS WHERE id=" . $driver_id;
				
		file_put_contents("driver_site_event_input_test", "\ndriver_id: " . $driver_id, FILE_APPEND);
		file_put_contents("driver_site_event_input_test", "\ngets here 2: " . $username, FILE_APPEND);
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("driver_site_event_input_test", "\ngets here 4: " . $username, FILE_APPEND);
	
		if(!$result){
	
			file_put_contents("driver_site_event_input_test", "\ngets here 7: " . $username, FILE_APPEND);
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		file_put_contents("driver_site_event_input_test", "\ngets here 6: " . $username, FILE_APPEND);
		
		$row = mysql_fetch_array($result);
		$username = $row['username'];
		$phone_number = $row['phone_number'];
		
		$automatic_bid = false;
		file_put_contents("driver_site_event_input_test", "\nrow['latitude']: " . $row['latitude'], FILE_APPEND);
		if($row['latitude'] == null){
		
			$automatic_bid = false;
			file_put_contents("driver_site_event_input_test", "\n doesn't go to esle for latitude if", FILE_APPEND);
		}
		else{
			file_put_contents("driver_site_event_input_test", "\ngoes to eslse for latitude if", FILE_APPEND);
			$automatic_bid = distance($row['latitude'], $row['longitude'], $event->flower_shop_latitude, $event->flower_shop_longitude)
					< $max_distance;
		}
		
		file_put_contents("driver_site_event_input_test", "\ngets here 5: " . $username, FILE_APPEND);
		file_put_contents("driver_site_event_input_test", "\nusername: " . $username, FILE_APPEND);
		file_put_contents("driver_site_event_input_test", "\nflower_shop_esl: " . $flower_shop_esl, FILE_APPEND);
		file_put_contents("driver_site_event_input_test", "\nautomatic_bid: : " . $automatic_bid, FILE_APPEND);
	
		if($automatic_bid){
		
			file_put_contents("driver_site_event_input_test", "\ngets to automatic bid at beginning: " . $flower_shop_esl, FILE_APPEND);
		
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
			file_put_contents("driver_site_event_input_test", "Just before curl exec", FILE_APPEND);
			curl_exec($ch);
			
			$text_message = "Automatically bid on delivery at" . $event->delivery_latitude . "," . $event->delivery_longitude
					. " for flower shop " . $event->flower_shop_name;
			
		}
		else{
			
			file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid beginning", FILE_APPEND);
			$sql = "INSERT INTO DELIVERIES_READY (driver_id,flower_shop_esl,code,latitude,longitude) VALUES ("
					. $driver_id . "," . $flower_shop_esl . "," . $event->code . "," . $event->delivery_latitude 
					. "," . $event->delivery_longitude . ")";
					
			file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid 2", FILE_APPEND);
					
			$result = mysql_query($sql, $con);
		
			file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid 3", FILE_APPEND);
	
			if(!$result){
	
				file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid 4", FILE_APPEND);
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid 5", FILE_APPEND);
			$text_message = "Do you want to bid on a delivery outside of your automatic bid radius?"
					. "The delivery address is " . $event->delivery_latitude . "," . $event->delivery_longitude
					. " for flower shop " . $event->flower_shop_name;
					
			file_put_contents("driver_site_event_input_test", "\ndidn't automatic bid", FILE_APPEND);
		}
	}	
	
	file_put_contents("driver_site_event_input_test", "\ngets to just before sending text message", FILE_APPEND);
	// send text message to $phone_number
	require "services-php/Services/Twilio.php";
	
	$accountSID = "ACc4c3a904e660afb30533bdcf81e6e5fe";
	$authToken = "38353271ed843f8df4d0aac99db0c233";
	
	$client = new Services_Twilio($accountSID, $authToken);
	$sms = $client->account->sms_messages->create("+12086470634", $phone_number, $text_message);
	print("<p><b>after if</b></p>");
?>