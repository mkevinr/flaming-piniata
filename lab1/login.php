
<?php

if(strlen($_REQUEST['username']) > 0){

  $decoded = file_get_contents("./users.json");
  $decoded = json_decode($decoded);

  foreach($decoded as $user => $access_token){

    if($_REQUEST['username']  == $user){

      session_start();
      $_SESSION['username'] = $user;

      header("Location: users.php");
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


