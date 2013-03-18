<?php

  session_start();
  
  $con = mysql_connect("localhost","root","altair8");

  mysql_select_db("driver_site", $con);	

  $sql = "SELECT driver_esl,phone_number,latitude,longitude FROM DRIVERS WHERE username='" . $_SESSION['username'] . "'";
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $esl = $row['driver_esl'];
  $phone_number = $row['phone_number'];
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];

  if(strlen($_REQUEST['phone_number']) > 0){

    $sql = "UPDATE DRIVERS SET phone_number='" . $_REQUEST['phone_number']
	. "' WHERE user_id=" . $user_id;
    mysql_query($sql);

   if(!$result){

      die("error: " . mysql_error() . "sql: " . $sql);
    }

    mysql_close($con);
  }

  print("<p><a href=\"/drivers/\"><b>Home</b></a></p><br/>");
  print("<b>Your esl: " . $esl . "</b><br/>");
  print("<b>Current Location:</b><br/>Latitude: " . $latitude . "<br/>Longitude: " . $longitude. "<br/>");
  print("<form action=\"/driver_profile.php\" method=\"POST\">");
  print("<b>Phone number: </b><input type=\"text\" name=\"phone_number\" size=15 value="
	. $phone_number . "><br>");
  print("<input type=\"submit\" value=\"Save\">");
?>

