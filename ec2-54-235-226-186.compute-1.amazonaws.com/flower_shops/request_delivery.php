<?php

  session_start();

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
  print("<a href=\"https://" . $server_address . "/flower_shops/\"><b>Home</b></a><br/>");
  print("<a href=\"https://" . $server_address . "/flower_shops/flower_shop_profile.php?flower_shop_id="
		. $_SESSION['flower_shop_id'] . "\"><b>Back</b></a><br/><br/>");

  if(array_key_exists('delivery_latitude', $_REQUEST) > 0){

    $con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("flower_shop_site", $con);
	
	$GUID = getGUID();
	
	$sql = "INSERT INTO DELIVERIES (guid,flower_shop_id,delivery_latitude,delivery_longitude) VALUES"
			. "('" . $GUID . "'," . $_SESSION['flower_shop_id'] . "," . $_REQUEST['delivery_latitude'] . "," . $_REQUEST['delivery_longitude'] . ")";
	
	if(!mysql_query($sql, $con)){
	
      die("error: " . mysql_error() . " sql: " . $sql);		
	}
	
	$sql = "SELECT name,latitude,longitude FROM FLOWER_SHOPS WHERE id=" . $_SESSION['flower_shop_id'];
	
	$result = mysql_query($sql, $con);

    if(!$result){

      die("error: " . mysql_error() . " sql: " . $sql);
    }
	
	$row = mysql_fetch_array($result);
	$flower_shop_latitude = $row['latitude'];
	$flower_shop_longitude = $row['longitude'];
	$flower_shop_name = $row['name'];

    $sql = "SELECT driver_esl FROM DRIVERS WHERE flower_shop_id=" . $_SESSION['flower_shop_id'];
    $result = mysql_query($sql);

    if(!$result){

      die("error: " . mysql_error() . " sql: " . $sql);
    }

    while($row = mysql_fetch_array($result)){


      $esl = $row['driver_esl'];

      $request = json_encode(array(
        "_domain" => "rfq"
        , "_name" => "delivery_ready"
		, "code" => $GUID
		, "flower_shop_name" => $flower_shop_name;
        , "flower_shop_latitude" => $flower_shop_latitude
    	, "flower_shop_longitude" => $flower_shop_longitude
        , "delivery_latitude" => $_REQUEST['delivery_latitude']
	    , "delivery_longitude" => $_REQUEST['delivery_longitude']));
	
		printf("$request: " . $request);

      $ch = curl_init($esl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	'Content-Type: application/json',
    	'Content-Length: ' . strlen($request))
	  );
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    }

	header("Location: https://" . $server_address . "/flower_shops/flower_shop_profile?flower_shop_id=" . $_SESSION['flower_shop_id']);
  }
  
  print("<p><b>Delivery request:</b></p>");
  print("<form action=\"https://" . $server_address . "/flower_shops/request_delivery.php?flower_shop_id=" . $_SESSION['flower_shop_id']
		. "\" method=\"POST\">");
?>

Delivery Latitude: <input type="text" name="delivery_latitude"><br/>
Delivery Longitude:  <input type="text" name="delivery_longitude"><br/>
<br/><input type="submit" value="Send Delivery Request"></form>

</body>
</html>



