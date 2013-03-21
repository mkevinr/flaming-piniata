<?php

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
		
		$sql = "SELECT username FROM DRIVERS WHERE id=" . $driver_id;
		
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
		
		file_put_contents("driver_site_event_input_test", "\ngets here 5: " . $username, FILE_APPEND);
		file_put_contents("driver_site_event_input_test", "\nusername: " . $username, FILE_APPEND);
	
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
	}	
	
	print("<p><b>after if</b></p>");
?>