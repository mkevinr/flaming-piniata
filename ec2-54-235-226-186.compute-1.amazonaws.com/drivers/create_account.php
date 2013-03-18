<html>
<body>
<?php

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/drivers/\">Home</a>");
  print("<br><br>");
  print("<b>Create Account:</b>");
  print("<br><br>");

  session_start();
  if(array_key_exists('oauth', $_REQUESt) && $_REQUEST['oauth'] == 'finished'){
  
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("driver_site", $con);
	
	$sql = "SELECT id,four_square_auth_token FROM DRIVERS WHERE username='" . $_SESSION['create_username'] . "'";
	$result = mysql_query($sql, $con);
	
	if(!$result){
	
		die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	$row = mysql_fetch_array($result);

	$ch = curl_init("https://api.foursquare.com/v2/users/self?oauth_token=" . $row['four_square_auth_token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);

	$user_info = curl_exec($ch);
	$user_info = json_decode($user_info);
	
	$user_id = $user_info->response->user->id;
	
	$sql = "UPDATE DRIVERS SET four_square_user_id=" . $user_id . "WHERE id=" . $row['id'];
	if(!mysql_query($sql, $con)){
	
		die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	session_unset('create_username');
	header("Location: https://" . $server_address . "/drivers/");
  }
	
  if(array_key_exists('username', $_REQUEST)){
  
	$server_address = file_get_contents("../server_address");
		
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("driver_site", $con);	

    $sql = "INSERT INTO DRIVERS (username, phone_number)"
		. " VALUES ('" . $_REQUEST['username'] . "', '" . $_REQUEST['phone_number'] . "')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }
		   
	$_SESSION['create_username'] = $_REQUEST['username'];
	
	var_dump($_SESSION['create_username']);

    header("Location: https://" . $server_address . "/drivers/oauth.php");
  } 

  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
  
  print("<form action=\"https://" . $server_address . "/drivers/create_account.php\" method=\"POST\">");
?>

User name: <input type="text" name="username"><br>
Phone number: <input type="text" name="phone_number"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
