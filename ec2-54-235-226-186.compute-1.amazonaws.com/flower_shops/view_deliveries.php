<html>
<body>

<?php

	$server_address = file_get_contents("../server_address");
	print("<a href=\"https://" . $server_address . "/flower_shops/\"><b>Home</b></a><br/>")
	print("<a href=\"https://" . $server_address . "/flower_shops/flower_shop_profile.php?" . $_SESSION['flower_shop_id'] . "\"><b>Back</b></a><br/><br/>");
	
	print("<b>Deliveries:</b><br/><br/>");

	session_start();

    $con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site", $con);
	
	$sql = "SELECT * FROM DELIVERIES,USERS WHERE DELIVERIES.assigned_driver_id=USERS.id,flower_shop_id=" . $_SESSION['flower_shop_id'];

	$result = mysql_query($sql,$con);
	
	if(!$result){
	
		die("error: " . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("<b>Delivery Latitude:</b> " . $row['delivery_latitude'] . "<br/>");
		print("<b>Delivery Longitude:</b> " . $row['delivery_longitude'] . "<br/>");
		print("<b>Assigned Driver:</b> " . $row['username'] . "<br/>");
		print("<b>Estimated Delivery Time:</b> " . $row['estimated_delivery_time'] . "<br/>");
		print("<b>Actual Delivery Time:</b> " . $row['actual_delivery_time'] . "<br/>");
		
		if(row['username'] != null){
		
			print("<form action=\"https://" . $server_address . "/view_bids.php?delivery_id=" . $row['DELIVERIES.id'] . "\" method=\"POST\">");
			print("<input type=\"submit\" value=\"View Bids\"></form><br/><br/>");
		}
	}
?>

</body>
</html>