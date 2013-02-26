<?php

  session_start();

  $access_token = json_decode(file_get_contents('./users.json'), true);
  $access_token = $access_token[$_REQUEST['username']];
  $ch = curl_init("https://api.foursquare.com/v2/users/self/checkins?oauth_token=" . $access_token);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  $response = json_decode(curl_exec($ch), true);
  curl_close($ch);
  $items = $response['response']['checkins']['items'];

  echo "<a href=\"/\">Home</a><br>";
  echo "<a href=\"/users.php\">Back</a><br>";
  echo "<br><b>Venues:</b><br><br>";

  if($_SESSION['username'] == $_REQUEST['username']){

    foreach($items as $item){

      $venue = $item['venue'];
      echo "Venue: " . $venue['name'];
      echo " Location: " . $venue['location']['city'];
      echo ", " . $venue['location']['state'];
      echo "<br>";
    }
  }
  else{

    $venue = $items[0]['venue'];
    echo "Venue: " . $venue['name'];
    echo " Location: " . $venue['location']['city'];
    echo ", " . $venue['location']['state'];
    echo "<br>";
  }
?>
