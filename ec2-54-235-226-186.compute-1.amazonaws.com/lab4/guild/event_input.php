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
	
			$sql = "SELECT flower_shop_id FROM FLOWER_SHOPS WHERE guild_esl_token='" . $_REQUEST['esl_token'] . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
			file_put_contents("guild_event_input_test", "\nAfter first sql", FILE_APPEND);
			
			$sql = "INSERT INTO DELIVERIES (flower_shop_id,guid) VALUES (" . $row['flower_shop_id'] . ",'" . $event->code . "')";
			
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
		
			$sql = "SELECT id,driver_esl FROM DRIVERS WHERE universal_id='" . $event->universal_driver_id . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$driver_esl = $row['driver_esl'];
			
			$sql = "UPDATE DELIVERIES SET driver_assigned_id=" . $row['id'] . ",bid_awarded_time=NOW() WHERE guid='" . $event->code . "'";
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
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
		}
		else if($event->_name == "bid_available"){
		
			$sql = "SELECT universal_id FROM DRIVERS where guild_esl_token='" . $_REQUEST['esl_token'] . "'";

			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			$universal_id = $row['universal_id'];

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
					, "code" => $GUID
					, "driver_name" => $event->driver_name
					, "driver_universal_id" => $universal_id
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
	
		if($event->_name == "picked_up"){
		
			$sql = "UPDATE DELIVERIES SET pick_up_time=NOW() WHERE guid='" . $event->code . "'";
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
		}
		else if($event->_name == "complete"){
		
			$sql = "SELECT rating,universal_id,id FROM DRIVERS WHERE guild_esl_token='" . $_REQUEST['esl_token'] . "'";
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
			//$universal_id = $row['universal_id'];
			$driver_id = $row['driver_id'];
			$rating = $row['rating'];
		
			$sql = "UPDATE DELIVERIES SET completed_time=NOW() WHERE driver_assigned_id=" . $driver_id;
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$rating = $rating + 1;
			
			$sql = "UPDATE DRIVERS SET rating=" . $rating . " WHERE id=" . $driver_id;
			
			if(!mysql_query($sql, $con)){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$sql = "SELECT flower_shop_esl,guid FROM DELIVERIES JOIN FLOWER_SHOPS ON DELIVERIES.flower_shop_id=FLOWER_SHOPS.id"
					. " WHERE assigned_driver_id=" . $driver_id;
			
			$result = mysql_query($sql, $con);
			
			if(!$result){
			
				die("error: " . mysql_error() . " sql: " . $sql);
			}
			
			$row = mysql_fetch_array($result);
			
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
		}
	}
?>