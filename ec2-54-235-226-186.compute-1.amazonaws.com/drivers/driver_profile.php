<?php

  print("add: " . $_REQUEST['add']);

  session_start();
  
  $server_address = file_get_contents("../server_address");
  
  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("driver_site", $con);	
  
  if(array_key_exists('save', $_REQUEST) && $_REQUEST['save'] == true){
  
	$sql = "INSERT INTO FLOWER_SHOPS (driver_id,name,latitude,longitude,esl) VALUES(". $_SESSION['driver_id'] . 
		",'" . $_REQUEST['flower_shop_name'] . "'," . $_REQUEST['flower_shop_latitude'] . "," . $_REQUEST['flower_shop_longitude']
		. ",'" . $_REQUEST['flower_shop_esl'] . "')";
		
	if(!mysql_query($sql, $con)){
	
	  die("error: " . mysql_error() . " sql: " . $sql);
	}
  }
  
  $sql = "SELECT driver_esl,phone_number,latitude,longitude FROM DRIVERS WHERE id=" . $_REQUEST['driver_id'];
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $esl = $row['driver_esl'];
  $phone_number = $row['phone_number'];
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];

  print("<p><a href=\"https://" . $server_address . "/drivers/\"><b>Home</b></a></p>");
  print("<b>esl: </b>" . $esl . "</b><br/>");
  
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
	
	print("request['phone_number']: " . $_REQUEST['phone_number']);
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
	
		/*$sql = "SELECT * FROM FLOWER_SHOPS WHERE id=" . $row['flower_shop_id'];
		$result = mysql_query($sql);
		
		if(!$result){
		
		  die("error: " . mysql_error() . "sql: " . $sql);	
		}*/
		
		$flower_shop_row = mysql_fetch_array($result);
		print("Name: " . $ow['name'] . " Latitude: " . $row['latitude'] . " Longitude: " . $row['longitude']
			. "<br/>");
		print("esl: " . $row['esl'] . "<br/>");
	}
	
	print("add: " . $_REQUEST['add']);
	if(array_key_exists('add', $_REQUEST) && $_REQUEST['add'] == true){
	
		print("<br/><br/>");
		print("<form action=\"https://" . $server_address . "/drivers/driver_profile.php?save=true&driver_id=" . $_REQUEST['driver_id']
				. "\" method=\"POST\">");
		print("<b>Flower shop name: </b><input type=\"text\" name=\"flower_shop_name\" size=50><br>");
		print("<b>Latitude: </b><input type=\"text\" name=\"flower_shop_latitude\" size=50><br>");
		print("<b>Longitude: </b><input type=\"text\" name=\"flower_shop_longitude\" size=50><br>");
		print("<b>esl: </b><input type=\"text\" name=\"flower_shop_esl\" size=50><br>");
		print("<input type=\"submit\" value=\"Save\">");		
	}
	else{
		print("gets here 2");
		print("<br/><br/>");
		print("<form action=\"https://" . $server_address . "/drivers/driver_profile.php?add=true&driver_id=" . $_REQUEST['driver_id'] 
				. "\" method=\"POST\">");
		print("<input type=\"submit\" value=\"Add\"></form>");
	}
	
	mysql_close($con);
  }
?>

