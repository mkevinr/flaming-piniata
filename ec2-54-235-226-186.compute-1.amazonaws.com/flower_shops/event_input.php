<?php

    file_put_contents("flower_shop_event_input_test", "got_here");

	$json = $entityBody = file_get_contents('php://input');

	$event = json_decode($json);
	
	file_put_contents("flower_shop_event_input_test", "\ngot_here 2", FILE_APPEND);
	
	if($event->_domain == "rfq" && $event->_name == "bid_available"){
	
		file_put_contents("flower_shop_event_input_test", "\ngot in if", FILE_APPEND);
	
		$con = mysql_connect("localhost", "root", "altair8");
		mysql_select_db("flower_shop_site", $con);
		
		$sql = "SELECT id FROM DELIVERIES WHERE guid='" . $event->code . "'";
		
		file_put_contents("flower_shop_event_input_test", "\nevent->code: " . $event->code, FILE_APPEND);
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("flower_shop_event_input_test", "\ngot_here 3", FILE_APPEND);
		
		if(!$result){
		
			file_put_contents("flower_shop_event_input_test", "\ngot_here 6", FILE_APPEND);
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		file_put_contents("flower_shop_event_input_test", "\ngot_here 7", FILE_APPEND);
		$delivery_id = $row['id'];
		
		file_put_contents("flower_shop_event_input_test", "\ngot_here 8", FILE_APPEND);
		$sql = "SELECT driver_id FROM DRIVERS WHERE flower_shop_esl_token='" . $_REQUEST['esl_token'] . "'";

		file_put_contents("flower_shop_event_input_test", "\ngot_here 9", FILE_APPEND);		
		file_put_contents("flower_shop_event_input_test", "\nrequest['esl_token']: " . $_REQUEST['esl_token'], FILE_APPEND);		
		file_put_contents("flower_shop_event_input_test", "\n" . $sql, FILE_APPEND);		
		$result = mysql_query($sql, $con);
		
		file_put_contents("flower_shop_event_input_test", "\ngot_here 4", FILE_APPEND);
		
		if(!$result){
		
			file_put_contents("flower_shop_event_input_test", "\ngot_here 10", FILE_APPEND);
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		file_put_contents("flower_shop_event_input_test", "\ngot_here 11", FILE_APPEND);
		$driver_id = $row['driver_id'];
		file_put_contents("flower_shop_event_input_test", "\ngot_here 12", FILE_APPEND);
		$estimated_delivery_time = $event->estimated_delivery_time;
		file_put_contents("flower_shop_event_input_test", "\ngot_here 13", FILE_APPEND);
		
		$sql = "INSERT INTO BIDS (delivery_id,driver_id,estimated_delivery_time) VALUES (" . $delivery_id . ","
				. $driver_id . "," . $estimated_delivery_time . ")";
				
		file_put_contents("flower_shop_event_input_test", "\ngot_here 14", FILE_APPEND);
				
		if(!mysql_query($sql, $con)){
		
			file_put_contents("flower_shop_event_input_test", "\ngot_here 15", FILE_APPEND);
			file_put_contents("flower_shop_event_input_test", "\ngot_here 15: " . mysql_error(), FILE_APPEND);
			file_put_contents("flower_shop_event_input_test", "\nsql: " . $sql, FILE_APPEND);
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		file_put_contents("flower_shop_event_input_test", "\ngot_here 5", FILE_APPEND);
	}
	file_put_contents("flower_shop_event_input_test", "\ngot_here 16", FILE_APPEND);
?>