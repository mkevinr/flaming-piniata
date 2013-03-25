<html>
<body>

<?php

	session_start();

	$server_address = file_get_contents("../server_address");
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/\"><b>Home</b></a><br/>");
	print("<a href=\"https://" . $server_address . "/lab4/flower_shops/flower_shop_profile.php?flower_shop_id="
		. $_SESSION['flower_shop_id'] . "\"><b>Back</b></a><br/><br/>");
	
	print("<b>Deliveries:</b><br/><br/>");

    $con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site2", $con);
	
	$sql = "SELECT * FROM DELIVERIES WHERE flower_shop_id=" . $_SESSION['flower_shop_id'];

	$result = mysql_query($sql,$con);
	
	if(!$result){
	
		die("error: " . mysql_error() . " sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("<b>Destination Latitude:</b> " . $row['delivery_latitude'] . "<br/>");
		print("<b>Destination Longitude:</b> " . $row['delivery_longitude'] . "<br/>");
		print("<b>Delivery Status:</b> " . $row['status'] . "<br/><br/>");
		print("<b>Assigned Driver:</b> " . $row['assigned_driver_name'] . "<br/>");
		
		if($row['assigned_driver_name'] == null){
		
			print("<form action=\"https://" . $server_address . "/lab4/flower_shops/view_bids.php?delivery_id=" . $row['id']
					. "\" method=\"POST\">");
			print("<input type=\"submit\" value=\"View Bids\"></form><br/>");
		}
	}
?>

</body>
</html>