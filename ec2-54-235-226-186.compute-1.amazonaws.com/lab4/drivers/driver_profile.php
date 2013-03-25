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
	
  mysql_select_db("driver_site2", $con);	
  
  if(array_key_exists('save', $_REQUEST) && $_REQUEST['save'] == true){
  
	$sql = "INSERT INTO GUILDS (driver_id,guild_name,driver_esl_token,guild_esl) VALUES (" . $_REQUEST['driver_id'] . ",'"
			. $_REQUEST['guild_name'] . "','" . $_REQUEST['driver_esl_token'] . "','" . $_REQUEST['guild_esl'] . "')";
				
	if(!mysql_query($sql, $con)){
	
	  die("error: " . mysql_error() . " sql: " . $sql);
	}
  }
  
  $sql = "SELECT name,username,phone_number,latitude,longitude FROM DRIVERS WHERE id=" . $_REQUEST['driver_id'];
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  
  $name = $row['name'];
  $username = $row['username'];
  $phone_number = $row['phone_number'];
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];

  print("<p><a href=\"https://" . $server_address . "/lab4/drivers/\"><b>Home</b></a></p>");
  print("<b>Name: </b>" . $name . "</b><br/>");
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
    print("<form action=\"https://" . $server_address . "/lab4/drivers/driver_profile.php?driver_id=" . $_REQUEST['driver_id']
			. "\" method=\"POST\">");
    print("<b>Phone number: </b><input type=\"text\" name=\"phone_number\" size=15 value="
	  . $phone_number . "><br/><br/>");
    print("<input type=\"submit\" value=\"Save\"></form><br/>");
    print("<b>Guilds to listen to events from:<br/><br/>");
	
    $sql = "SELECT * FROM GUILDS WHERE driver_id=" . $_SESSION['driver_id'];
	$result = mysql_query($sql);
	
	if(!$result){
			
		die("error: " . mysql_error() . "sql: " . $sql);
	}
	
	while($row = mysql_fetch_array($result)){
	
		print("Name: " . $row['guild_name'] . "<br/>");
		print("Your esl: https://" . $server_address . "/lab4/drivers/event_input.php?esl_token=" . $row['driver_esl_token'] . "<br/>");
		print("Guild's esl: " . $row['guild_esl'] . "<br/><br/>");
	}
	
	if(array_key_exists('add', $_REQUEST) && $_REQUEST['add'] == true){
	
		$driver_esl_token = getGUID();
		$driver_esl = "https://" . $server_address . "/lab4/drivers/event_input.php?esl_token=" . $driver_esl_token;
		
		print("<b>Your esl: " . $driver_esl . "</b><br/>");
		print("<form action=\"https://" . $server_address . "/lab4/drivers/driver_profile.php?save=true&driver_id=" . $_REQUEST['driver_id']
				. "&driver_esl_token=" . $driver_esl_token . "\" method=\"POST\">");
		print("<b>Guild Name: </b><input type=\"text\" name=\"guild_name\" size=50><br>");
		print("<b>Guild Esl: </b><input type=\"text\" name=\"guild_esl\" size=50><br>");
		print("<input type=\"submit\" value=\"Save\">");
	}
	else{
		print("<form action=\"https://" . $server_address . "/lab4/drivers/driver_profile.php?add=true&driver_id=" . $_REQUEST['driver_id'] 
				. "\" method=\"POST\">");
		print("<input type=\"submit\" value=\"Add\"></form>");
	}
	
	mysql_close($con);
  }
?>

