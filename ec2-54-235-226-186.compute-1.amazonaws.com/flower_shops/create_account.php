<html>
<body>
<?php

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/flower_shops/\">Home</a>");
  print("<br><br>");
  print("<b>Create Account:</b>");
  print("<br><br>");

  if(array_key_exists('username', $_REQUEST)){
  
  	$con = mysql_connect("localhost","root","altair8");
	mysql_select_db("flower_shop_site", $con);	
  
    if($_REQUEST['user_type'] == "flower_shop"){
  
		$sql = "INSERT INTO USERS (username,privileges) VALUES ('" . $_REQUEST['username'] . "','flower_shop')";
		if(!mysql_query($sql, $con)){
		
			die('Error: ' . mysql_error() . " sql: " . $sql);
		}
		
		$sql = "SELECT id FROM USERS WHERE username=" . $_REQUEST['username'];
		$result = mysql_query($sql, $con);
		if(!$result){
		
			die('Error: ' . mysql_error() . " sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$id = $row['id'];
		
		$sql = "INSERT INTO FLOWER_SHOPS (id, name, latitude, longitude)"
			. " VALUES (" . $id . ",'" . $_REQUEST['name'] . "\"," . $_REQUEST['latitude'] . "," . $_REQUEST['longitude'] . ")";

		if (!mysql_query($sql,$con))
		{
		  die('Error: ' . mysql_error() . " sql: " . $sql);
		}
		header("Location: https://" . $server_address . "/flower_shops/");
	}
	else{

		$sql = "INSERT INTO USERS (usernames, privileges) VALUES ('" . $_REQUEST['username'] . "','driver')";

		if (!mysql_query($sql,$con))
		{
		  die('Error: ' . mysql_error() . " sql: " . $sql);
		}
		header("Location: https://" . $server_address . "/flower_shops/");		
	}
  } 
  
  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
  
  print("<form action=\"https://" . $server_address . "/flower_shops/create_account.php\" method=\"POST\">");
  print("<select name=\"user_type\">");
  if(array_key_exists('user_type', $_REQUEST) && $_REQUEST['user_type'] == "flower_shop"){
	
	print("<option value=\"flower_shop\" selected>Flower Shop</option>");
  }
  else{
  
    print("<option value=\"flower_shop\" selected>Flower Shop</option>");
  }
  
  if(array_key_exists('user_type', $_REQUEST) && $_REQUEST['user_type'] == "driver"){
  
    print("<option value=\"driver\" selected>Driver</option>");  
  }
  else{
  
	print("<option value=\"driver\">Driver</option>");
  }
?>

</select>
</form>

<?php
	if($_REQUEST['user_type'] == "flower_shop"){
	
		print("<form action=\"https://" . $server_address . "/flower_shops/create_account.php?user_type=flower_shop\" method=\"POST\">");
		print("User Name: <input type=\"text\" name=\"username\"><br>");
		print("Name: <input type=\"text\" name=\"name\"><br>");
		print("Latitude: <input type=\"text\" name=\"latitude\"><br>");
		print("Longitude: <input type=\"text\" name=\"longitude\"><br>");
		print("<input type=\"submit\" value=\"Submit\">");
		print("</form>");
	}
	else if($_REQUEST['user_type'] == "driver"){
	
		print("<form action=\"https://" . $server_address . "/flower_shops/create_account.php?user_type=driver\" method=\"POST\">");
		print("User Name: <input type=\"text\" name=\"username\"><br>");		
		print("</form>");
	}
?>

<body>
</html>	
