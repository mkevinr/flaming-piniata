<html>
<body>
<a href="/">Home</a>
<br><br>
<b>Create Account:</b>
<br><br>
<?php

  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("master", $con);

  if(strlen($_REQUEST['username']) > 0){

    $sql = "INSERT INTO USERS (username, password, privileges)"
	. " VALUES ('" . $_REQUEST['username'] . "', '', 'driver')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    $sql = "SELECT id FROM USERS WHERE username='" . $_REQUEST['username'] 
           . "'";

    mysql_query($sql,$con);

    $result = mysql_query($sql, $con);

    if (!$result)
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    $row = mysql_fetch_array($result);
    $id = $row['id'];

    $sql = "INSERT INTO DRIVERS VALUES (" . $id . ",'')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    header("Location: /index.php");
  } 

  if($_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
?>

<form action="/create_account.php" method="POST">
User name: <input type="text" name="username"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
