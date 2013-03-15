<?php

  $server_address = file_get_contents("../server_address");
  echo "<p>hello</p>";
  if(strlen($_REQUEST['code']) <= 0){

	echo "<p>hello 2</p>";
    session_start();
    $_SESSION['create_username'] = $_REQUEST['username'];
	
	echo $server_address;
	
	$redirect_location = "Location: https://foursquare.com/oauth2/authenticate?"
			. "client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF&response_type=code&redirect_uri=" 
			. "http://" . $server_address . "/drivers/oauth.php";
			
	echo "<br>" . $redirect_location;

    header($redirect_location);
	
	echo "hello 4";
  }
  else{

    session_start();

	echo "<p>hello 3</p>";
	//$curl_command = "https://foursquare.com/oauth2/access_token"
    //. "?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF"
    //. "&client_secret=J52ZJITMDYABWYUWIIQB5WPDQ4I3DJP5GJBDZLUJRB3CMDY5"
    //. "&grant_type=authorization_code"
    //. "&redirect_uri=http://" . $server_address . "/drivers/oauth.php"
    //. "&code=" . $_REQUEST['code'];
	//echo "<p>curl command: " . $curl_command . "</p>";
    $ch = curl_init("https://foursquare.com/oauth2/access_token"
    . "?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF"
    . "&client_secret=J52ZJITMDYABWYUWIIQB5WPDQ4I3DJP5GJBDZLUJRB3CMDY5"
    . "&grant_type=authorization_code"
    . "&redirect_uri=http://" . $server_address . "/drivers/oauth.php"
    . "&code=" . $_REQUEST['code']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $access_token_json = curl_exec($ch);
	echo "<br><p>first access token: " . $access_token . "</p>";
    $access_token = json_decode($access_token_json);
	echo "<br><p>second access token: " . $access_token . "</p>";
    $access_token = $access_token->access_token;
	echo "<br><p>third access token: " . $access_token . "</p>";
    curl_close($ch);
	
	$con = mysql_connect("localhost","root","altair8");
    mysql_select_db("driver_site", $con);	

    $sql = "UPDATE DRIVERS SET four_square_auth_token='" . $access_token .
		"' WHERE username='" . $_SESSION['create_username'] . "'";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    session_unset('create_username');
    header("Location: /drivers/");
  }

?>
