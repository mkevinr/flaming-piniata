
<?php

$server_address = file_get_contents("../server_address");

if(array_key_exists('username', $_REQUEST)){


  $con = mysql_connect("localhost","root","altair8");

  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("driver_site", $con);

  $result = mysql_query("SELECT id,username FROM FLOWER_SHOPS");

  while($row = mysql_fetch_array($result)){

    if($row['username'] == $_REQUEST['username']){

        session_start();
		$_SESSION['flower_shop_id'] = $row['id'];
		
		header("Location: https://" . $server_address . "/flower_shops/");
    }
  }
}

if(array_key_exists('username', $_REQUEST)){

	echo "Invalid user name. Please create an account first";
}

print ("<html>");
print ("<body>");
print ("<a href=\"https://" . $server_address . "/flower_shops/\">Home</a><br><br>");
print ("<b>Login:</b><br><br>");
print ("<form action=\"https://" . $server_address . "/flower_shops/login.php\" method=\"POST\">");
?>

User name: <input type="text" name="username">
<input type="submit" value="Login">
</form>

<body>
<html>

