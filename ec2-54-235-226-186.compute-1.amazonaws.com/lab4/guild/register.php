<html>
<body>
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

  $server_address = file_get_contents("../server_address");

  print("<a href=\"https://" . $server_address . "/lab4/guild/\">Home</a>");
  print("<br><br>");
  print("<b>Register:</b>");
  print("<br><br>");

  if(array_key_exists('name', $_REQUEST)){
  
  	$con = mysql_connect("localhost","root","altair8");
	mysql_select_db("guild_site", $con);	
  
    if($_REQUEST['user_type'] == "flower_shop"){
	
		$sql = "INSERT INTO FLOWER_SHOPS (name,guild_esl_token,flower_shop_esl) VALUES ('" . $_REQUEST['name']
				. "','" . $_REQUEST['guild_esl_token'] . "','" . $_REQUEST['flower_shop_esl'] . "')";
				
		if(!mysql_query($sql, $con)){
		
			die('Error: ' . mysql_error() . " sql: " . $sql);
		}

		header("Location: https://" . $server_address . "/lab4/guild/");
	}
	else{
	
		$base_rating = 500;
		$sql = "INSERT INTO DRIVERS (name,universal_id,rating,guild_esl_token,driver_esl) VALUES ('" . $_REQUEST['name']
				. "','" . $_REQUEST['universal_id'] . "'," . $base_rating . ",'" . $_REQUEST['guild_esl_token'] . "','" 
				. $_REQUEST['driver_esl'] . "')";

		if (!mysql_query($sql,$con))
		{
		  die('Error: ' . mysql_error() . " sql: " . $sql);
		}
		
		header("Location: https://" . $server_address . "/lab4/guild/");		
	}
  } 
  
  if(array_key_exists('failed', $_REQUEST) && $_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
  
  if(!array_key_exists('user_type', $_REQUEST)){
  
	  print("<form action=\"https://" . $server_address . "/lab4/guild/register.php\" method=\"POST\">");
	  print("<select name=\"user_type\">");
	  print("<option value=\"flower_shop\" selected>Flower Shop</option>");
	  print("<option value=\"driver\">Driver</option>");
	  print("</select><br/>");
	  print("<input type=\"submit\" value=\"Create\"/>");
	  print("</form>");
  }
?>

<?php
	if(array_key_exists('user_type', $_REQUEST) && $_REQUEST['user_type'] == "flower_shop"){
		
		$flower_shop_esl_token = getGUID();
		print("<b>Guild's esl for you:</b> https://" . $server_address . "/lab4/guild/event_input.php?esl_token=" . $flower_shop_esl_token);
		print("<form action=\"https://" . $server_address . "/lab4/guild/register.php?user_type=flower_shop&guild_esl_token="
				. $flower_shop_esl_token . "\" method=\"POST\">");
		print("<b>Name:</b> <input type=\"text\" name=\"name\"><br>");
		print("<b>Your esl:</b> <input type=\"text\" name=\"flower_shop_esl\"><br>");
		print("<input type=\"submit\" value=\"Submit\">");
		print("</form>");
	}
	else if(array_key_exists('user_type', $_REQUEST) && $_REQUEST['user_type'] == "driver"){
	
		$universal_id = getGUID();
		$driver_esl_token = getGUID();
		print("<b>Your universal identifier:</b> " . $universal_identifier . "<br/>");
		print("<b>Guild's esl for you:</b> https://" . $server_address . "/lab4/guild/event_input.php?esl_token=" . $driver_esl_token . "<br/>");
		print("<form action=\"https://" . $server_address . "/lab4/guild/register.php?user_type=driver&universal_id="
				. $universal_id . "&guild_esl_token=" . $driver_esl_token . "\" method=\"POST\">");
		print("<b>Name:</b> <input type=\"text\" name=\"name\"><br/>");
		print("<b>Your esl:</b> <input type=\"text\" name=\"driver_esl\"><br/>");
		print("<input type=\"submit\" value=\"Submit\">");
		print("</form>");
	}
?>

<body>
</html>	