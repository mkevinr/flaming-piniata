
<?php


if(strlen($_REQUEST['username']) > 0){

  $decoded = file_get_contents("./users.json");
  $decoded = json_decode($decoded);
  $decoded = $decoded->users;
  

  foreach($decoded as $user){

    if($_REQUEST['username']  == $user){
      
      session_start();
      $_SESSION['username'] = $user->name;

      header("Location: users.php");
    }
  }
}

if(strlen($_REQUEST['username']) > 0){

	echo "Invalid user name. Please create an account first";
}

print_r($_REQUEST);
$name = $_REQUEST['username'];
?>

<html>
<body>
<form action="/login.php" method="POST">
User name: <input type="text" name="username">
<input type="submit" value="Submit">
</form>

<body>
<html>


