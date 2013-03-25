<html>
<body>
<?php

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/lab4/drivers/\">Home</a>");
  print("<br><br>");
  print("<b>Create Account:</b>");
  print("<br><br>");
  
  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("driver_site2", $con);	

  session_start();
  if(array_key_exists('oauth', $_REQUEST) && $_REQUEST['oauth'] == 'finished'){
  
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
	
	$sql = "UPDATE DRIVERS SET four_square_user_id=" . $user_id . " WHERE id=" . $row['id'];
	if(!mysql_query($sql, $con)){
	
		die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	session_unset('create_username');
	header("Location: https://" . $server_address . "/lab4/drivers/");
  }
	
  if(array_key_exists('username', $_REQUEST)){
  
    $sql = "INSERT INTO DRIVERS (name, username, phone_number)"
		. " VALUES ('". $_REQUEST['name'] . "','" . $_REQUEST['username'] . "', '" . $_REQUEST['phone_number'] . "')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }
		   
	$_SESSION['create_username'] = $_REQUEST['username'];
	
	var_dump($_SESSION['create_username']);

    header("Location: https://" . $server_address . "/lab4/drivers/oauth.php");
  } 

  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.<br/>";
  }
  
  print("<form action=\"https://" . $server_address . "/lab4/drivers/create_account.php\" method=\"POST\">");
?>

Name: <input type="text" name="name"><br>
User name: <input type="text" name="username"><br>
Phone number: <input type="text" name="phone_number"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
