<html>
<body>
<?php

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/lab4/flower_shops/\">Home</a>");
  print("<br><br>");
  print("<b>Create Account:</b>");
  print("<br><br>");

  if(array_key_exists('username', $_REQUEST)){
  
    $con = mysql_connect("localhost","root","altair8");
	mysql_select_db("flower_shop_site2", $con);
  
	$sql = "INSERT INTO FLOWER_SHOPS (username, name, latitude, longitude)"
		. " VALUES ('" . $_REQUEST['username'] . "','" . $_REQUEST['name'] . "'," . $_REQUEST['latitude'] . "," . $_REQUEST['longitude'] . ")";

	if (!mysql_query($sql,$con))
	{
	  die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	header("Location: https://" . $server_address . "/lab4/flower_shops/");
  } 
  
  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
?>

<?php

	print("<form action=\"https://" . $server_address . "/lab4/flower_shops/create_account.php\" method=\"POST\">");
	print("User Name: <input type=\"text\" name=\"username\"><br>");
	print("Name: <input type=\"text\" name=\"name\"><br>");
	print("Latitude: <input type=\"text\" name=\"latitude\"><br>");
	print("Longitude: <input type=\"text\" name=\"longitude\"><br>");
	print("<input type=\"submit\" value=\"Submit\">");
	print("</form>");
?>

<body>
</html>	
