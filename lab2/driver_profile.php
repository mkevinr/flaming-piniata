<?php

  session_start();

  $con = mysql_connect("localhost","root","altair8");
  mysql_select_db("master",$con);
  $sql = "SELECT id FROM USERS WHERE username='" . $_SESSION['username']
	. "'";
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . "sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $user_id = $row['id'];

  $sql = "SELECT esl FROM DRIVERS WHERE user_id=" . $user_id;
  $result = mysql_query($sql);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $esl = $row['esl'];

  if(strlen($_REQUEST['esl']) > 0){

    $sql = "UPDATE DRIVERS SET esl='" . $_REQUEST['esl']
	. "' WHERE user_id=" . $user_id;
    mysql_query($sql);

   if(!$result){

      die("error: " . mysql_error() . "sql: " . $sql);
    }

    mysql_close($con);
  }

  print("<form action=\"/driver_profile.php\" method=\"POST\">");
  print("Your esl: <input type=\"text\" name=\"esl\" size=75 value="
	. $esl . "><br>");
  print("<input type=\"submit\" value=\"Save\">");
?>

