<?php

  session_start();

  $con = mysql_connect("localhost","root","altair8");
  mysql_select_db("driver_site",$con);
  $sql = "SELECT id FROM USERS WHERE username='" . $_SESSION['username']
	. "'";
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . "sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $user_id = $row['id'];

  $sql = "SELECT esl, phone_number FROM DRIVERS WHERE user_id=" . $user_id;
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $esl = $row['esl'];
  $phone_number = $row['phone_number'];

  if(strlen($_REQUEST['phone_number']) > 0){

    $sql = "UPDATE DRIVERS SET phone_number='" . $_REQUEST['phone_number']
	. "' WHERE user_id=" . $user_id;
    mysql_query($sql);

   if(!$result){

      die("error: " . mysql_error() . "sql: " . $sql);
    }

    mysql_close($con);
  }

  print("Your esl: " . $esl . "<br>);
  print("Current Location: Latitude: " . $latitude . " longitude: " . longitude);
  print("<form action=\"/driver_profile.php\" method=\"POST\">");
  print("Phone number: <input type=\"text\" name=\"phone_number\" size=75 value="
	. $esl . "><br>");
  print("<input type=\"submit\" value=\"Save\">");
?>

