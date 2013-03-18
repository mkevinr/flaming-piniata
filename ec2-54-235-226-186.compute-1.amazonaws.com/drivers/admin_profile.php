<?php

  if(strlen($_REQUEST['flower_shop_address']) > 0){

    session_start();

    $con = mysql_connect("localhost", "root", "altair8");
    mysql_select_db("master", $con);

    $sql = "SELECT esl FROM DRIVERS";
    $result = mysql_query($sql);

    if(!$result){

      die("error: " . mysql_error() . " sql: " . $sql);
    }

    while($row = mysql_fetch_array($result)){

      $esl = $row['esl'];

      $request = json_encode(array(
        "_domain" => "rfq"
        , "_name" => "delivery_ready"
        , "flower_shop_address" => $_REQUEST['flower_shop_address']
    	, "pickup_time" => $_REQUEST['pickup_time']
        , "delivery_address" => $_REQUEST['delivery_address']
	, "delivery_time" => $_REQUEST['delivery_time']));


      $ch = curl_init($esl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	'Content-Type: application/json',
    	'Content-Length: ' . strlen($request))
	);
      curl_exec($ch);
    } 
  }
?>

<p><b>Delivery request:</b></p>

<form action="/admin_profile.php" method="POST">
Flower Shop Address: <input type="text" name="flower_shop_address"><br/>
Pickup Time: <input type="text" name="pickup_time"><br/>
Delivery Address: <input type="text" name="delivery_address"><br/>
Delivery Time:  <input type="text" name="delivery_time"><br/>
<input type="submit" value="Send Delivery Request">



