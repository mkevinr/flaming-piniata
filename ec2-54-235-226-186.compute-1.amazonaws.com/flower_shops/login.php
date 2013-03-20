
<?php

$server_address = file_get_contents("../server_address");

if(array_key_exists('username', $_REQUEST)){


  $con = mysql_connect("localhost","root","altair8");

  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("flower_shop_site", $con);

  $result = mysql_query("SELECT id,username,privileges FROM FLOWER_SHOPS", $con);

  while($row = mysql_fetch_array($result)){

    if($row['username'] == $_REQUEST['username']){

        session_start();
		session_unset('driver_id');
		session_unset('flower_shop_id');
		
		if($row['privileges'] == "flower_shop"){
		
			$_SESSION['flower_shop_id'] = $row['id'];
		}
		else{
		
			$_SESSION['driver_id'] = $row['id'];
		}
		
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


