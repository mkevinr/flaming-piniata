<html>
<body>
<a href="/drivers/">Home</a>
<br><br>
<b>Create Account:</b>
<br><br>
<?php

  if($_REQUEST['oauth'] == 'finished'){
  
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("driver_site", $con);
	
	$sql = "SELECT id,four_square_auth_token WHERE username='" . $_SESSION['create_username'] . "'";
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
	
	$sql = "UPDATE DRIVERS SET four_square_user_id=$user_id WHERE id=" . $row['id'];
	if(!mysql_query($sql, $con);){
	
		die('Error: ' . mysql_error() . " sql: " . $sql);
	}
	
	session_unset('create_username');
	header("Location: /drivers/");
  }
	
  function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
  }
  
  if(strlen($_REQUEST['username']) > 0){
  
	$server_address = file_get_contents("../server_address");
	$esl = $server_address . "/drivers/input.php?" . getGUID();
		
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("driver_site", $con);	

    $sql = "INSERT INTO DRIVERS (username, phone_number, driver_esl)"
		. " VALUES ('" . $_REQUEST['username'] . "', '" . $_REQUEST['phone_number'] . "', '"
		. $esl . "')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }
		   
	session_start();
	$_SESSION['create_username'] = $_REQUEST['username'];
	
	var_dump($_SESSION['create_username']);

    header("Location: /drivers/oauth.php");
  } 

  if($_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
?>

<form action="/drivers/create_account.php" method="POST">
User name: <input type="text" name="username"><br>
Phone number: <input type="text" name="phone_number"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
