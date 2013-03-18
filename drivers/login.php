
<?php

if(strlen($_REQUEST['username']) > 0){


  $con = mysql_connect("localhost","root","altair8");

  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("driver_site", $con);

  $result = mysql_query("SELECT id,username FROM DRIVERS");

  while($row = mysql_fetch_array($result)){

  	  printf("row['username']: " . $row['username'] . " row['id']: " . $row['id']);
      if($row['username'] == $_REQUEST['username']){

        session_start();
		$_SESSION['driver_id'] = $row['id'];
		printf("session['driver_id']: " . $_SESSION['driver_id']);
		
		//header("Location: /drivers/");
    }
  }
}

if(strlen($_REQUEST['username']) > 0){

	echo "Invalid user name. Please create an account first";
}
?>


<html>
<body>
<a href="/drivers/">Home</a><br><br>
<b>Login:</b><br><br>
<form action="/drivers/login.php" method="POST">
User name: <input type="text" name="username">
<input type="submit" value="Login">
</form>

<body>
<html>


