<?php

  if(strlen($_REQUEST['code']) <= 0){

    session_start();
    $_SESSION[create_username'] = $_REQUEST['username'];

    header("Location: https://foursquare.com/oauth2/authenticate?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF&response_type=code&redirect_uri=http://ec2-54-234-155-254.compute-1.amazonaws.com/oauth.php");
  }
  else{

    $ch = curl_init("https://foursquare.com/oauth2/access_token"
    . "?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF"
    . "&client_secret=J52ZJITMDYABWYUWIIQB5WPDQ4I3DJP5GJBDZLUJRB3CMDY5"
    . "&grant_type=authorization_code"
    . "&redirect_uri=http://ec2-54-234-155-254.compute-1.amazonaws.com/oauth.php"
    . "&code=" . $_REQUEST['code']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $access_token = curl_exec($ch);
    $access_token = json_decode($access_token);
    $access_token = $access_token->access_token;
    curl_close($ch);

    $file = file_get_contents('./users.json');
    if($file === false){

      $users = array("users" => array($_SESSION['create_username'] => $access_token));
    }
    else{

      $users = json_decode(file_get_contents('./users.json'));
      $users->users[$_SESSION['create_username']] = $access_token;
    }

    //file_put_contents('./users.json', json_encode($users));

    //session_unset('create_username');
    header("Location: /");
  }

?>
