<html>
<body>
<a href="/">Home</a>
<br><br>
<b>Create Account:</b>
<br><br>
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
  
  if(strlen($_REQUEST['username']) > 0){
  
	$server_address = file_get_contents("../server_address");
	$esl = $server_address . "/drivers/input.php?" . getGUID();
		
    $con = mysql_connect("localhost","root","altair8");

    mysql_select_db("driver_site", $con);	

    $sql = "INSERT INTO DRIVERS (username, phone_number, driver_esl)"
		. " VALUES ('" . $_REQUEST['username'] . "', '" . $_REQUEST['phone_number'] . "', '"
		. $esl . "')";

    if (!mysql_query($sql,$con))
    {
      die('Error: ' . mysql_error() . " sql: " . $sql);
    }

    $sql = "SELECT id FROM USERS WHERE username='" . $_REQUEST['username'] 
           . "'";
		   
	session_start();
	$_SESSION['create_username'] = $_REQUEST['username'];

    header("Location: /drivers/oauth.php");
  } 

  if($_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
?>

<form action="/drivers/create_account.php" method="POST">
User name: <input type="text" name="username"><br>
Phone number: <input type="text" name="phone_number"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
