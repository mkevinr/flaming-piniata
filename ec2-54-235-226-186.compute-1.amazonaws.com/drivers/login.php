
<?php

$server_address = file_get_contents("../server_address");

if(strlen($_REQUEST['username']) > 0){


  $con = mysql_connect("localhost","root","altair8");

  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("driver_site", $con);

  $result = mysql_query("SELECT id,username FROM DRIVERS");

  while($row = mysql_fetch_array($result)){

      if($row['username'] == $_REQUEST['username']){

        session_start();
		$_SESSION['driver_id'] = $row['id'];
		
		header("Location: https://" . $server_address . "/drivers/");
    }
  }
}

if(strlen($_REQUEST['username']) > 0){

	echo "Invalid user name. Please create an account first";
}

print ("<html>");
print ("<body>");
print ("<a href=\"https://" . $server_address . "/drivers/\">Home</a><br><br>");
print ("<b>Login:</b><br><br>");
print ("<form action=\https://" . $server_address . "/drivers/login.php\" method=\"POST\">");
?>


<b>Login:</b><br><br>
<form action="/drivers/login.php" method="POST">
User name: <input type="text" name="username">
<input type="submit" value="Login">
</form>

<body>
<html>


