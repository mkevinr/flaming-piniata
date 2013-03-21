<html>
<body>

<?php

	$server_address = file_get_contents("../server_address");
	
	$con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site", $con);
	
	if(array_key_exists('bid_id', $_REQUEST)){
	
	
		$sql = "SELECT driver_id,estimated_delivery_time FROM BID WHERE id=" . $_REQUEST['bid_id'];
		$result = mysql_query($sql,$con);
		
		if(!$result){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$sql = "UPDATE DELIVERIES SET assigned_driver_id=" . $row['driver_id'] . ",estimated_delivery_time=" . $row['estimated_delivery_time'];
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		$sql = "DELETE FROM BIDS WHERE delivery_id=" . $_REQUEST['delivery_id'];
		
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . " sql: " . $sql);
		}
	
		header("Location: https://" . $server_address . "/flower_shops/view_deliveries.php");
	}
	
	print("<a href=\"https://" . $server_address . "/flower_shops/\"><b>Home</b></a><br/>");
	print("<a href=\"https://" . $server_address . "/flower_shops/view_deliveries.php\"><b>Back</b></a><br/><br/>");
	
	print("<b>Bids:</b><br/><br/>");
	
	session_start();

	$sql = "SELECT USERS.username,BIDS.driver_id,BIDS.estimated_delivery_time FROM BIDS INNER JOIN USERS ON BIDS.driver_id=USERS.id"
			. " WHERE delivery_id=" . $_REQUEST['delivery_id'];

	$result = mysql_query($sql,$con);
	
	if(!$result){
	
		die("error: " . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("<b>Assigned Driver:</b> " . $row['username'] . "<br/>");
		print("<b>Estimated Delivery Time:</b> " . $row['estimated_delivery_time'] . "<br/>");
		
		if($row['username'] != null){
		
			print("<form action=\"https://" . $server_address . "/view_bids.php?delivery_id=" . $_REQUEST['delivery_id'] 
					. "&bid_id=" . $row['BIDS.id'] . "\" method=\"POST\">");
			print("<input type=\"submit\" value=\"Select Bid\"></form><br/><br/>");
		}
	}

?>

</body>
</html>