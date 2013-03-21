<?php

    file_put_contents("flower_shop_event_input_test", "got_here");

	$json = $entityBody = file_get_contents('php://input');

	$event = json_decode($json);
	
	file_put_contents("flower_shop_event_input_test", "got_here 2", FILE_APPEND);
	
	if($event->_domain == "rfq" && $event->_name == "bid_available"){
	
		file_put_contents("flower_shop_event_input_test", "got in if", FILE_APPEND);
	
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("flower_shop_site", $con);
		
		$sql = "SELECT id FROM DELIVERIES WHERE guid=" . $event->code;
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("flower_shop_event_input_test", "got_here 3", FILE_APPEND);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$delivery_id = $row['id'];
		
		$sql = "SELECT driver_id FROM DRIVERS WHERE flower_shop_esl_token='" . $_REQUEST['esl_token'];
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("flower_shop_event_input_test", "got_here 4", FILE_APPEND);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$driver_id = $row['driver_id'];
		$estimated_delivery_time = $event->estimated_delivery_time;
		
		$sql = "INSERT INTO BIDS (delivery_id,driver_id,estimated_delivery_time) VALUES (" . $delivery_id . ","
				. $driver_id . "," . $estimated_delivery_time . ")";
				
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		file_put_contents("flower_shop_event_input_test", "got_here 5", FILE_APPEND);
	}
?>