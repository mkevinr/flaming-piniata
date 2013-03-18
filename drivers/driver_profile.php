<?php

  session_start();
  
  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("driver_site", $con);	

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

  print("<p><a href=\"/drivers/\"><b>Home</b></a></p>");
  print("<b>esl: </b>" . $esl . "</b><br/>");
  
  if($_SESSION['driver_id'] == $_REQUEST['driver_id']){
  
  	if(strlen($_REQUEST['phone_number']) > 0){

	  $sql = "UPDATE DRIVERS SET phone_number='" . $_REQUEST['phone_number']
	  . "' WHERE id=" . $_SESSION['driver_id'];
	  $result = mysql_query($sql);

	  if(!$result){

		die("error: " . mysql_error() . "sql: " . $sql);
	  }
	}

    print("<b>Current Location: </b>Latitude: " . $latitude . " Longitude: " . $longitude. "<br/>");
    print("<form action=\"/drivers/driver_profile.php\" method=\"POST\">");
    print("<b>Phone number: </b><input type=\"text\" name=\"phone_number\" size=15 value="
	  . $phone_number . "><br>");
    print("<input type=\"submit\" value=\"Save\"><br/><br/>");
    print("<b>Flower shops to listen to events from:<br/><br/>");
	
    $sql = "SELECT * FROM DRIVER_TO_FLOWER_SHOP_MAP WHERE driver_id=" . $_SESSION['driver_id'];
	$result = mysql_query($sql);
	
	if(!$result){
		
		die("error: " . mysql_error() . "sql: " . $sql);
	}
	
	while($row = msql_fetch_array(
	
		$sql = "SELECT * FROM FLOWER_SHOPS WHERE id=" . $row['flower_shop_id'];
		$result = mysql_query($sql);
		
		if(!$result){
		
		  die("error: " . mysql_error() . "sql: " . $sql);	
		}
		
		$flower_shop_row = mysql_fetch_array($result);
		print("Name: " . flower_shop_row['name'] . " Latitude: " . $flower_shop_row['latitude'] . " Longitude: " . $flower_shop_row['longitude']";
	}
	
	print("<br/><br/>");
	print("<form action=\"/drivers/flower_shops.php?add=true\" method=\"POST\">");
    print("<input type=\"submit\" value=\"Add\">);
	
	mysql_close($con);
  }
?>

