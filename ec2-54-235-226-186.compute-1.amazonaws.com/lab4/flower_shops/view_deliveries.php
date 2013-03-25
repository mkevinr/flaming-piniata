<html>
<body>

<?php

    $con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site2", $con);

	session_start();
	
	if(array_key_exists("pickup", $_REQUEST) && $_REQUEST['pickup'] == true){
	
		$sql = "SELECT * FROM DELIVERIES WHERE id=" . $_REQUEST['delivery_id'];
		
		$result = mysql_query($sql,$con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$code = $row['guid'];
		$driver_guid = $row['assigned_driver_guid'];
		
		$sql = "SELECT * FROM GUILDS WHERE id=" . $row['assigned_guild_id'];
		
		$result = mysql_query($sql,$con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		
		$sql = "UPDATE DELIVERIES SET status='picked_up' WHERE id=" . $_REQUEST['delivery_id'];
		
		if(!mysql_query($sql,$con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}		
		
		$guild_esl = $row['guild_esl'];
		
		$request = json_encode(array(
				"_domain" => "delivery"
				, "_name" => "picked_up"
				, "code" => $code
				, "driver_universal_id" => $driver_guid));

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
	}

	$server_address = file_get_contents("../server_address");
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/\"><b>Home</b></a><br/>");
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/flower_shop_profile.php?flower_shop_id="
		. $_SESSION['flower_shop_id'] . "\"><b>Back</b></a><br/><br/>");
	
	print("<b>Deliveries:</b><br/><br/>");

	$sql = "SELECT * FROM DELIVERIES WHERE flower_shop_id=" . $_SESSION['flower_shop_id'];

	$result = mysql_query($sql,$con);
	
	if(!$result){
	
		die("error: " . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("<b>Destination Latitude:</b> " . $row['destination_latitude'] . "<br/>");
		print("<b>Destination Longitude:</b> " . $row['destination_longitude'] . "<br/>");
		print("<b>Delivery Status:</b> " . $row['status'] . "<br/>");
		print("<b>Assigned Driver:</b> " . $row['assigned_driver_name'] . "<br/><br/>");
		
		if($row['assigned_driver_name'] == null){
		
			print("<form action=\"https://" . $server_address . "/lab4/flower_shops/view_bids.php?delivery_id=" . $row['id']
					. "\" method=\"POST\">");
			print("<input type=\"submit\" value=\"View Bids\"></form><br/>");
		}
		else if($row['status'] == "driver_assigned"){
		
			print("<form action=\"https://" . $server_address . "/lab4/flower_shops/view_deliveries.php?delivery_id=" 
					. $row['id']. "&pickup=true\" method=\"POST\">");
			print("<input type=\"submit\" value=\"Notify Pickup\"></form><br/>");			
		}
	}
?>

</body>
</html>