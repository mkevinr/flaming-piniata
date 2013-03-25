<?php

	$json = file_get_contents('php://input');

	$event = json_decode($json);
	
	$con = mysql_connect("localhost", "root", "altair8");
	mysql_select_db("guild_site", $con);
	
	file_put_contents("guild_event_input_test", "Gets to before first if");
	
	if($event->_domain == "rfq"){
	
		file_put_contents("guild_event_input_test", "\nGets in first if", FILE_APPEND);
		
		if($event->_name == "delivery_ready"){
		
			file_put_contents("guild_event_input_test", "\nGets in second if", FILE_APPEND);
	
			$sql = "SELECT id FROM FLOWER_SHOPS WHERE guild_esl_token='" . $_REQUEST['esl_token'] . "'";
			
			file_put_contents("guild_event_input_test", "\nGets here 1", FILE_APPEND);
			
			$result = mysql_query($sql, $con);
			
			file_put_contents("guild_event_input_test", "\nGets here 2", FILE_APPEND);
			
			if(!$result){
			
				file_put_contents("guild_event_input_test", "\nGets here 3", FILE_APPEND);
				file_put_contents("guild_event_input_test", "\nsql error: " . mysql_error() . " sql: " . $sql, FILE_APPEND);
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\nGets here 4", FILE_APPEND);
			
			$row = mysql_fetch_array($result);
			
			file_put_contents("guild_event_input_test", "\nAfter first sql", FILE_APPEND);
			
			$sql = "INSERT INTO DELIVERIES (flower_shop_id,guid) VALUES (" . $row['id'] . ",'" . $event->code . "')";
			
			if(!mysql_query($sql, $con)){
		
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$sql = "SELECT driver_esl FROM DRIVERS ORDER BY rating DESC LIMIT 3";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\nAfter second sql", FILE_APPEND);
			
			while($row = mysql_fetch_array($result)){
			
				file_put_contents("guild_event_input_test", "\nIn while loop", FILE_APPEND);
			
				$request = $json;

				$ch = curl_init($row['driver_esl']);
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
			
			file_put_contents("guild_event_input_test", "\nAfter while loop", FILE_APPEND);
		}
		else if($event->_name == "bid_awarded"){
		
			file_put_contents("guild_event_input_test", "\nGets in bid_awarded if", FILE_APPEND);
		
			$sql = "SELECT id,driver_esl FROM DRIVERS WHERE universal_id='" . $event->universal_driver_id . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			file_put_contents("guild_event_input_test", "\nGets after bid_awarded sql 1", FILE_APPEND);
			$driver_esl = $row['driver_esl'];
			
			$sql = "UPDATE DELIVERIES SET driver_assigned_id=" . $row['id'] . ",bid_awarded_time=NOW() WHERE guid='" . $event->code . "'";
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\nGets after bid_awarded sql 2", FILE_APPEND);
			
			$request = $json;

			$ch = curl_init($driver_esl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($request))
			);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);							
			file_put_contents("guild_event_input_test", "\nGets after bid_awarded curl", FILE_APPEND);
		}
		else if($event->_name == "bid_available"){
		
			$sql = "SELECT universal_id,rating FROM DRIVERS where guild_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$universal_id = $row['universal_id'];
			$rating = $row['rating'];

			$sql = "SELECT flower_shop_esl FROM DELIVERIES JOIN FLOWER_SHOPS ON DELIVERIES.flower_shop_id=FLOWER_SHOPS.id"
					. " WHERE guid='" . $event->code . "'";
					
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
			$request = json_encode(array(
					"_domain" => "rfq"
					, "_name" => "bid_available"
					, "code" => $event->code
					, "driver_name" => $event->driver_name
					, "driver_universal_id" => $universal_id
					, "rating" => $rating
					, "estimated_delivery_time" => $event->estimated_delivery_time));

			$ch = curl_init($row['flower_shop_esl']);
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
	}
	else if($event->_domain == "delivery"){
	
		file_put_contents("guild_event_input_test", "\nGets in delivery if", FILE_APPEND);
		if($event->_name == "picked_up"){
		
			$sql = "UPDATE DELIVERIES SET pick_up_time=NOW() WHERE guid='" . $event->code . "'";
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
		}
		else if($event->_name == "complete"){
		
			file_put_contents("guild_event_input_test", "\nGets in complete if", FILE_APPEND);
		
			$sql = "SELECT rating,universal_id,id FROM DRIVERS WHERE guild_esl_token='" . $_REQUEST['esl_token'] . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
			file_put_contents("guild_event_input_test", "\ncomplete after sql 1", FILE_APPEND);
			
			//$universal_id = $row['universal_id'];
			$driver_id = $row['id'];
			$rating = $row['rating'];
		
			$sql = "UPDATE DELIVERIES SET completed_time=NOW() WHERE driver_assigned_id=" . $driver_id;
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\ncomplete after sql 2", FILE_APPEND);
			
			$rating = $rating + 1;
			
			$sql = "UPDATE DRIVERS SET rating=" . $rating . " WHERE id=" . $driver_id;
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\ncomplete after sql 3", FILE_APPEND);
			
			$sql = "SELECT flower_shop_esl,guid FROM DELIVERIES JOIN FLOWER_SHOPS ON DELIVERIES.flower_shop_id=FLOWER_SHOPS.id"
					. " WHERE assigned_driver_id=" . $driver_id;
					
			file_put_contents("guild_event_input_test", "\ncomplete gets here 1", FILE_APPEND);
			
			$result = mysql_query($sql, $con);
			
			file_put_contents("guild_event_input_test", "\ncomplete gets here 2", FILE_APPEND);
			
			if(!$result){
			
				file_put_contents("guild_event_input_test", "\ncomplete gets here 3", FILE_APPEND);
				file_put_contents("guild_event_input_test", "\ncomplete sql error: " . mysql_error() . " sql: " . $sql, FILE_APPEND);
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			file_put_contents("guild_event_input_test", "\ncomplete gets here 4", FILE_APPEND);
			
			$row = mysql_fetch_array($result);
			
			file_put_contents("guild_event_input_test", "\ncomplete after sql 4", FILE_APPEND);
			
			$guid = $row['guid'];
			$flower_shop_esl = $row['flower_shop_esl'];
			
			$request = json_encode(array(
					"_domain" => "delivery"
					, "_name" => "complete"
					, "code" => $guid
					/*, "driver_name" => $event->driver_name
					, "driver_universal_id" => $universal_id*/));

			$ch = curl_init($flower_shop_esl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($request))
			);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);	
			file_put_contents("guild_event_input_test", "\ncomplete after curl", FILE_APPEND);
		}
	}
?>