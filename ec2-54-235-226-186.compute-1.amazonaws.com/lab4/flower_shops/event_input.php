<?php

	$json = file_get_contents('php://input');
	$event = json_decode($json);
	
	$con = mysql_connect("localhost", "root", "altair8");
	mysql_select_db("flower_shop_site2", $con);
	
	if($event->_domain == "rfq" && $event->_name == "bid_available"){
	
		$sql = "SELECT id FROM DELIVERIES WHERE guid='" . $event->code . "'";
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$delivery_id = $row['id'];
		
		$sql = "SELECT id FROM GUILDS WHERE flower_shop_esl_token='" . $_REQUEST['esl_token'] . "'";
		
		$result = mysql_query($sql, $con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$guild_id = $row['guild_id'];
				
		$sql = "INSERT INTO BIDS (delivery_id,guild_id,driver_name,universal_driver_id,estimated_delivery_time,rating) VALUES ("
				. $delivery_id . "," . $guild_id . ",'" . $event->driver_name . "','" . $event->driver_universal_id . "'," 
				. $event->estimated_delivery_time . "," . $event->$rating . ")";
				
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
	}
	else if($event->_domain == "delivery" && $event->_name == "complete"){
	
		$sql = "UPDATE DELIVERIES SET status='complete' WHERE guid='" . $event->code . "'";
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
	}
?>