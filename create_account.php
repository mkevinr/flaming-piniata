<html>
<body>
<a href="/">Home</a>

<?php

  if($_REQUEST['failed'] === true){

    echo "Account creation failed! Try again.";
  }
?>

<form action="/oauth.php" method="POST">
User name: <input type="text" name="username"><br>
<input type="submit" value="Submit">
</form>
<body>
</html>
