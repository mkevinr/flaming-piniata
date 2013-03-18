<html>
<body>
<?php

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/flower_shops/\">Home</a>");
  print("<br><br>");
  print("<b>Create Account:</b>");
  print("<br><br>");

  if(array_key_exists('username', $_REQUEST)){
  
	$server_address = file_get_contents("../server_address");
		
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("flower_shop_site", $con);	

    $sql = "INSERT INTO FLOWER_SHOPS (name, latitude, longitude)"
		. " VALUES ('" . $_REQUEST['username'] . "','" . $_REQUEST['name'] . "'," . $_REQUEST['latitude'] . "," . $_REQUEST['longitude'] . ")";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }
    header("Location: https://" . $server_address . "/flower_shops/");
  } 

  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
  
  print("<form action=\"https://" . $server_address . "/flower_shops/create_account.php\" method=\"POST\">");
?>

User Name: <input type="text" name="username"><br>
Name: <input type="text" name="name"><br>
Latitude: <input type="text" name="latitude"><br>
Longitude: <input type="text" name="longitude"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
