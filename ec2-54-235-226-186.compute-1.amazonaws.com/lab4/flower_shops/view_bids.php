<html>
<body>

<?php

	$server_address = file_get_contents("../server_address");
	
	$con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site2", $con);
	
	if(array_key_exists('bid_id', $_REQUEST)){
	
		$sql = "SELECT guid,flower_shop_id,destination_latitude,destination_longitude,guild_id,driver_name,universal_driver_id"
				. " FROM DELIVERIES JOIN BIDS ON DELIVERIES.id=BIDS.delivery_id WHERE BIDS.id=" . $_REQUEST['bid_id'];
				
		$result = mysql_query($sql,$con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$code = $row['guid'];
		$flower_shop_id = $row['flower_shop_id'];
		$destination_latitude = $row['destination_latitude'];
		$destination_longitude = $row['destination_longitude'];
		$guild_id = $row['guild_id'];
		$driver_name = $row['driver_name'];
		$universal_driver_id = $row['universal_driver_id'];
		
		$sql = "SELECT name,latitude,longitude FROM FLOWER_SHOPS WHERE id=" . $flower_shop_id;
		
		$result = mysql_query($sql,$con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
	
		$flower_shop_name = $row['name'];
		$flower_shop_latitude = $row['latitude'];
		$flower_shop_longitude = $row['longitude'];
		
		$sql = "UPDATE DELIVERIES SET assigned_driver_name='" . $driver_name . "',assigned_driver_guid='" . $universal_driver_id
				. "',assigned_guild_id=" . $guild_id . ",status='driver_assigned' WHERE id=" . $_REQUEST['delivery_id'];
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$sql = "DELETE FROM BIDS WHERE delivery_id=" . $_REQUEST['delivery_id'];
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$request = json_encode(array(
				"_domain" => "rfq"
				, "_name" => "bid_awarded"
				, "code" => $code
				, "flower_shop_name" => $flower_shop_name
				, "flower_shop_latitude" => $flower_shop_latitude
				, "flower_shop_longitude" => $flower_shop_longitude
				, "destination_latitude" => $destination_latitude
				, "destination_longitude" => $destination_longitude
				, "driver_name" => $driver_name
				, "universal_driver_id" => $universal_driver_id));

		$ch = curl_init($guild_esl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($request))
		);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
	
		header("Location: https://" . $server_address . "/lab4/flower_shops/view_deliveries.php");
	}
	
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/\"><b>Home</b></a><br/>");
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/view_deliveries.php\"><b>Back</b></a><br/><br/>");
	
	print("<b>Bids:</b><br/><br/>");
	
	session_start();

	$sql = "SELECT * FROM BIDS WHERE delivery_id=" . $_REQUEST['delivery_id'];
			
	$result = mysql_query($sql,$con);
	
	if(!$result){
	
		die("error: " . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("<b>Assigned Driver:</b> " . $row['driver_name'] . "<br/>");
		print("<b>Driver Rating:</b> " . $row['rating'] . "<br/>");
		print("<b>Estimated Delivery Time:</b> " . $row['estimated_delivery_time'] . "<br/>");
		print("<form action=\"https://" . $server_address . "/lab4/flower_shops/view_bids.php?delivery_id=" . $_REQUEST['delivery_id'] 
				. "&bid_id=" . $row['id'] . "\" method=\"POST\">");
		print("<input type=\"submit\" value=\"Choose this Bid\"></form><br/><br/>");
		}
	}
?>

</body>
</html>