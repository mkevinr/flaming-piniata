<?php

	file_put_contents("flower_shop_event_input_test","Gets to flower_shop_input!");
	$json = file_get_contents('php://input');
	$event = json_decode($json);
	
	$con = mysql_connect("localhost", "root", "altair8");
	mysql_select_db("flower_shop_site2", $con);
	
	file_put_contents("flower_shop_event_input_test","\nGets to before first if", FILE_APPEND);
	if($event->_domain == "rfq" && $event->_name == "bid_available"){
	
		file_put_contents("flower_shop_event_input_test","\nGets in if", FILE_APPEND);
	
		$sql = "SELECT id FROM DELIVERIES WHERE guid='" . $event->code . "'";
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		file_put_contents("flower_shop_event_input_test","\nGets after first sql", FILE_APPEND);
		$delivery_id = $row['id'];
		file_put_contents("flower_shop_event_input_test","\nGets here 1", FILE_APPEND);
		
		$sql = "SELECT id FROM GUILDS WHERE flower_shop_esl_token='" . $_REQUEST['esl_token'] . "'";
		
		file_put_contents("flower_shop_event_input_test","\nGets here 2", FILE_APPEND);
		
		$result = mysql_query($sql, $con);
		
		file_put_contents("flower_shop_event_input_test","\nGets here 3", FILE_APPEND);
		
		if(!$result){
			
			file_put_contents("flower_shop_event_input_test","\nGets here 4", FILE_APPEND);
			file_put_contents("flower_shop_event_input_test","\nsql error: " . mysql_error() . " sql: " . $sql, FILE_APPEND);
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		file_put_contents("flower_shop_event_input_test","\nGets here 5", FILE_APPEND);
		
		$row = mysql_fetch_array($result);
		
		file_put_contents("flower_shop_event_input_test","\nGets after second sql", FILE_APPEND);
		
		$guild_id = $row['id'];
				
		$sql = "INSERT INTO BIDS (delivery_id,guild_id,driver_name,universal_driver_id,estimated_delivery_time,rating) VALUES ("
				. $delivery_id . "," . $guild_id . ",'" . $event->driver_name . "','" . $event->driver_universal_id . "'," 
				. $event->estimated_delivery_time . "," . $event->rating . ")";
				
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		file_put_contents("flower_shop_event_input_test","\nGets after second sql", FILE_APPEND);
	}
	else if($event->_domain == "delivery" && $event->_name == "complete"){
	
		file_put_contents("flower_shop_event_input_test","\nGets into complete", FILE_APPEND);
		$sql = "UPDATE DELIVERIES SET status='complete' WHERE guid='" . $event->code . "'";
		
		file_put_contents("flower_shop_event_input_test","\nGets into complete 2", FILE_APPEND);
		if(!mysql_query($sql, $con)){
		
			file_put_contents("flower_shop_event_input_test","\nGets into complete 3", FILE_APPEND);
			file_put_contents("flower_shop_event_input_test","\nGets into complete sql error: " . mysql_error() . " sql: " . $sql, FILE_APPEND);
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		file_put_contents("flower_shop_event_input_test","\nGets into complete 4", FILE_APPEND);
	}
?>