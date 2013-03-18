<?php

  $server_address = file_get_contents("../server_address");
  session_start();
  
  if(strlen($_REQUEST['code']) <= 0){

	$redirect_location = "Location: https://foursquare.com/oauth2/authenticate?"
			. "client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF&response_type=code&redirect_uri=" 
			. "http://" . $server_address . "/drivers/oauth.php";
			
    header($redirect_location);
  }
  else{

    $ch = curl_init("https://foursquare.com/oauth2/access_token"
    . "?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF"
    . "&client_secret=J52ZJITMDYABWYUWIIQB5WPDQ4I3DJP5GJBDZLUJRB3CMDY5"
    . "&grant_type=authorization_code"
    . "&redirect_uri=http://" . $server_address . "/drivers/oauth.php"
    . "&code=" . $_REQUEST['code']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $access_token = curl_exec($ch);
	curl_close($ch);
    $access_token = json_decode($access_token);
    $access_token = $access_token->access_token;
	
	$con = mysql_connect("localhost","root","altair8");
    mysql_select_db("driver_site", $con);	

    $sql = "UPDATE DRIVERS SET four_square_auth_token='" . $access_token .
		"' WHERE username='" . $_SESSION['create_username'] . "'";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    header("Location: /drivers/create_account.php?oauth=finished");
  }

?>
