<?php

$decoded = file_get_contents("users.json");
$decoded = json_decode($file);
$decoded = $decoded->users;

$foundUser = true;
$accessToken

foreach($decoded as $user){

	if($name == $user->name){
		
		$foundUser = true;
		$accessToken = $user->access_token
		break;
  	}
}

if($foundUser){

	
}
else{
	echo "Invalid user name. Please create an account first"
}

print_r($_REQUEST);
$name = $_REQUEST['username'];
?>

<html>
<body>
<form method="POST">
User name: <input type="text" name="username">
<input type="submit" value="Submit">
</form>

<body>
<html>


