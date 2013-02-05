<html>
<body>
<a href="/">Home</a><br>
<p><b>Users:</b></p>
<?php

$decoded = file_get_contents("./users.json");
$decoded = json_decode($decoded, true);

foreach($decoded as $user => $access_token){

    echo "<a href=\"/profile.php?username=" . $user . "\">" . $user . "</a><br>";
}
?>

</body>
</html>

