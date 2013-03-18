<?php

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

  session_start();
  
  $server_address = file_get_contents("../server_address");
  
  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("driver_site", $con);	
  
  if(array_key_exists('save', $_REQUEST) && $_REQUEST['save'] == true){
  
	$sql = "UPDATE FLOWER_SHOPS SET name='" . $_REQUEST['flower_shop_name'] . "',latitude=" . $_REQUEST['flower_shop_latitude']
			. ",longitude=" . $_REQUEST['flower_shop_longitude'] . ",flower_shop_esl='" . $_REQUEST['flower_shop_esl'] . "'"
			. " WHERE driver_id=" . $_SESSION['driver_id'];
			
	if(!mysql_query($sql, $con)){
	
	  die("error: " . mysql_error() . " sql: " . $sql);
	}
  }
  
  $sql = "SELECT username,phone_number,latitude,longitude FROM DRIVERS WHERE id=" . $_REQUEST['driver_id'];
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $username = $row['username'];
  $phone_number = $row['phone_number'];
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];

  print("<p><a href=\"https://" . $server_address . "/drivers/\"><b>Home</b></a></p>");
  print("<b>Username: </b>" . $username . "</b><br/>");
  
  if(array_key_exists('driver_id', $_SESSION) && array_key_exists('driver_id', $_REQUEST) && $_SESSION['driver_id'] == $_REQUEST['driver_id']){
  
  	if(array_key_exists('phone_number', $_REQUEST)){

	  $sql = "UPDATE DRIVERS SET phone_number='" . $_REQUEST['phone_number']
	  . "' WHERE id=" . $_SESSION['driver_id'];
	  $result = mysql_query($sql);

	  if(!$result){

		die("error: " . mysql_error() . "sql: " . $sql);
	  }
	  
	  $phone_number = $_REQUEST['phone_number'];
	}
	
    print("<b>Current Location: </b>Latitude: " . $latitude . " Longitude: " . $longitude. "<br/>");
    print("<form action=\"https://" . $server_address . "/drivers/driver_profile.php?driver_id=" . $_REQUEST['driver_id'] . "\" method=\"POST\">");
    print("<b>Phone number: </b><input type=\"text\" name=\"phone_number\" size=15 value="
	  . $phone_number . "><br>");
    print("<input type=\"submit\" value=\"Save\"></form><br/><br/>");
    print("<b>Flower shops to listen to events from:<br/><br/>");
	
    $sql = "SELECT * FROM FLOWER_SHOPS WHERE driver_id=" . $_SESSION['driver_id'];
	$result = mysql_query($sql);
	
	if(!$result){
			
		die("error: " . mysql_error() . "sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("Name: " . $row['name'] . " Latitude: " . $row['latitude'] . " Longitude: " . $row['longitude']
			. "<br/>");
		print("Your esl: https://" . $server_address . "/drivers/input_event.php?" . $row['driver_esl_token'] . "<br/>");
		print("Flower shop's esl: " . $row['flower_shop_esl'] . "<br/><br/>");
	}
	
	if(array_key_exists('add', $_REQUEST) && $_REQUEST['add'] == true){
	
		$driver_esl_token = getGUID();
		$driver_esl = "https://" . $server_address . "/drivers/input_event.php?" . $driver_esl_token;
		
		$sql = "INSERT INTO FLOWER_SHOPS (driver_id,driver_esl_token) VALUES (" . $_SESSION['driver_id'] . ",'" . $driver_esl_token . "')";
		if(!mysql_query($sql, $con)){
		
			die("error: " . mysql_error() . "sql: " . $sql);
		}
		
		$sql = "SELECT $id FROM FLOWER_SHOPS ORDER BY $id DESC LIMIT 1";
		$result = mysql_query($sql, $con);
		if(!$result){
		
			die("error: " . mysql_error() . "sql: " . $sql);
		}
		
		$row = mysql_fetch_array($result);
		$flower_shop_id = $row['id'];
	
		print("<b>Your esl: " . $driver_esl . "</b><br/>");
		print("<form action=\"https://" . $server_address . "/drivers/driver_profile.php?save=true&driver_id=" . $_REQUEST['driver_id']
				. "&flower_shop_id=" . $flower_shop_id . "\" method=\"POST\">");
		print("<b>Flower shop name: </b><input type=\"text\" name=\"flower_shop_name\" size=50><br>");
		print("<b>Latitude: </b><input type=\"text\" name=\"flower_shop_latitude\" size=50><br>");
		print("<b>Longitude: </b><input type=\"text\" name=\"flower_shop_longitude\" size=50><br>");
		print("<b>Flower shop's esl: </b><input type=\"text\" name=\"flower_shop_esl\" size=50><br>");
		print("<input type=\"submit\" value=\"Save\">");
	}
	else{
		print("<br/>");
		print("<form action=\"https://" . $server_address . "/drivers/driver_profile.php?add=true&driver_id=" . $_REQUEST['driver_id'] 
				. "\" method=\"POST\">");
		print("<input type=\"submit\" value=\"Add\"></form>");
	}
	
	mysql_close($con);
  }
?>

