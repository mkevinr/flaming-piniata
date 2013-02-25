
<?php

if(strlen($_REQUEST['username']) > 0){


  $con = mysql_connect("localhost","root","altair8");

  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("master", $con);

  $result = mysql_query("SELECT username, privileges FROM USERS");

  while($row = mysql_fetch_array($result)){

      if($row['username'] == $_REQUEST['username']){

        session_start();
	$_SESSION['username'] = $_REQUEST['username'];
	
	if($row['privileges'] == "admin"){

	  $_SESSION['privileges'] = "admin";
	  header("Location: admin_profile.php");
	}
        else if($row['privileges'] == "driver"){

	  $_SESSION['privileges'] = "driver";
	  header("Location: driver_profile.php");
	}
    }
  }
}

if(strlen($_REQUEST['username']) > 0){

	echo "Invalid user name. Please create an account first";
}
?>


<html>
<body>
<a href="/">Home</a><br><br>
<b>Login:</b><br><br>
<form action="/login.php" method="POST">
User name: <input type="text" name="username">
<input type="submit" value="Submit">
</form>

<body>
<html>


