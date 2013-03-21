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
		
		$sql = "SELECT driver_id FROM FLOWER_SHOPS WHERE driver_esl_token='" . $_REQUEST['esl_token'] . "'";
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$driver_id = $row['driver_id'];
		
		$sql = "SELECT username FROM DRIVERS WHERE id=" . $driver_id;
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$username = $row['username'];
	
		$request = json_encode(array(
        "_domain" => "rfq"
        , "_name" => "bid_available"
		, "code" => $event->code
		, "driver_name" => $username
        , "estimated_delivery_time" => 5));

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
	
	print("<p><b>after if</b></p>");
?>