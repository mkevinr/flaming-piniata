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

  mysql_select_db("flower_shop_site2", $con);	
  
  $sql = "SELECT * FROM FLOWER_SHOPS WHERE id=" . $_REQUEST['flower_shop_id'];
  $result = mysql_query($sql, $con);

  if(!$result){

    die("error: " . mysql_error() . " sql: " . $sql);
  }

  $row = mysql_fetch_array($result);
  $name = $row['name'];
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];

  print("<p><a href=\"https://" . $server_address . "/lab4/flower_shops/\"><b>Home</b></a></p>");
  print("<b>Name: </b>" . $name . "</b><br/>");
  print("<b>Location: </b>Latitude: " . $latitude . " Longitude: " . $longitude. "<br/>");

  if(array_key_exists('flower_shop_id', $_SESSION) && array_key_exists('flower_shop_id', $_REQUEST) 
		&& $_SESSION['flower_shop_id'] == $_REQUEST['flower_shop_id']){
		
	print("<br/><a href=\"https://" . $server_address . "/lab4/flower_shops/request_delivery.php\">Request new delivery</a>");
	print("<br/><a href=\"https://" . $server_address . "/lab4/flower_shops/view_deliveries.php\">View all deliveries</a>");
	
	mysql_close($con);
  }  
  else{
  
	  if(array_key_exists('save', $_REQUEST)){
	  
		$sql = "INSERT INTO GUILDS (flower_shop_id,name,flower_shop_esl_token,guild_esl) VALUES(" . $_REQUEST['flower_shop_id']
				. ",'" . $_REQUEST['name']	. "','" . $_REQUEST['flower_shop_esl_token'] . "','" . $_REQUEST['guild_esl'] . "')";
				
		if(!mysql_query($sql, $con)){
		
		  die("error: " . mysql_error() . " sql: " . $sql);
		}
		
		header("Location: https://" . $server_address . "/lab4/flower_shops/flower_shops.php");
	  }
	  else if(array_key_exists('register', $_REQUEST)){
	  
		// Show form for the driver to register.
		$flower_shop_esl_token = getGUID();
		
		print("<br/><b>Your Username:</b> " . $driver_username);
		print("<br/><b>Flower Shop's esl:</b> " . "https://" . $server_address . "/lab4/flower_shops/event_input.php?esl_token=" 
				. $flower_shop_esl_token);
		print("<form action=\"https://" . $server_address . "/lab4/flower_shops/flower_shop_profile.php?save=true&flower_shop_id="
				. $_REQUEST['flower_shop_id'] . "&flower_shop_esl_token=" . $flower_shop_esl_token . "\" method=\"POST\">");
		print("<br/>Guild name: </b><input type=\"text\" name=\"name\"/>");
		print("<br/>Your esl: </b><input type=\"text\" name=\"guild_esl\"/>");
		print("<br/><input type=\"submit\" value=\"Save\"/></form>");
	  }
	  else{
	  
	  	// Add a button the driver can use to register.
		print("<form action=\"https://" . $server_address . "/lab4/flower_shops/flower_shop_profile.php?register=true&flower_shop_id="
				. $_REQUEST['flower_shop_id'] . "\" method=\"POST\">");
		print("<br/><input type=\"submit\" value=\"Register\"/></form>");
	  }
  }
?>

