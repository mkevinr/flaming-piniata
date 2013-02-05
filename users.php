<html>
<body>
<p><b>Users:</b></p>
<?php

$decoded = file_get_contents("./users.json");
$decoded = json_decode($decoded, true);

foreach($decoded as $user => $access_token){

    echo "<a href=\"http://ec2-54-234-155-254.compute-1.amazonaws.com/profile.php?username=" . $user . "\">" . $user . "</a><br>";
}
?>

</body>
</html>

