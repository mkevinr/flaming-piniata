<?php

	file_put_contents("sms_input_test", "gets to sms_input");

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

	file_put_contents("sms_input_test", "\nwas called!", FILE_APPEND);
	
	$entityBody = file_get_contents('php://input');
	
	file_put_contents("sms_input_test", "\nrequest: " . print_r($_REQUEST), FILE_APPEND);
	file_put_contents("sms_input_test", "\nentity body: " . $entityBody, FILE_APPEND);
	file_put_contents("sms_input_test", "\nrequest['body']: " . $_REQUEST['Body'], FILE_APPEND);

	if($_REQUEST['Body'] == "bid anyway"){
	
		file_put_contents("sms_input_test", "\ngets in if", FILE_APPEND);
	
		require './twilio-php/Services/Twilio.php';
		
		file_put_contents("sms_input_test", "\ngets in if 2", FILE_APPEND);
		
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("driver_site", $con);
		
		file_put_contents("sms_input_test", "\ngets in if 3", FILE_APPEND);
		
		$sql = "SELECT * FROM DRIVERS WHERE phone_number='". $_REQUEST['From'] . "'";
		
		file_put_contents("sms_input_test", "\ngets in if 4", FILE_APPEND);
		$result = mysql_query($sql, $con);
		
		file_put_contents("sms_input_test", "\ngets in if 5", FILE_APPEND);
		
		if(!$result){
		
			file_put_contents("sms_input_test", "\ngets in if  6", FILE_APPEND);
			file_put_contents("sms_input_test", "\nsql error: " . mysql_error() . " sql: " . $sql, FILE_APPEND);
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		file_put_contents("sms_input_test", "\ngets to after sql query", FILE_APPEND);
		
		$row = mysql_fetch_array($result);
		
		$driver_id = $row['id'];
		$username = $row['username'];
		$driver_latitude = $row['latitude'];
		$driver_longitude = $row['longitude'];
		
		$sql = "SELECT flower_shop_esl,code,latitude,longitude FROM DELIVERIES_READY WHERE driver_id=" . $driver_id;
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("sms_input_test", "\ngets to after sql query 2", FILE_APPEND);
		
		/*if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}*/

		if(mysql_num_rows($result) > 0){
		
			file_put_contents("sms_input_test", "\ngets in if 2", FILE_APPEND);
		
			$row = mysql_fetch_array($result);
	
			$esl = $row['flower_shop_esl'];
			
			if($driver_latitude == null){
				
				$distance = 5;
			}
			else{
			
				$distance = distance($driver_latitude,$driver_longitude,$row['latitude'],$row['longitude']);
			}

  		    $request = json_encode(array(
			"_domain" => "rfq"
			, "_name" => "bid_available"
			, "code" => $row['code']
			, "driver_name" => $username
			, "estimated_delivery_time" => $distance * 60));
		
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
			
			file_put_contents("sms_input_test", "\nexecuted curl", FILE_APPEND);
		}
	}

?>
