<html>
<body>
<?php
$server_address = file_get_contents("../server_address");
print("<a href=\"https://" . $server_address . "/drivers/\">Home</a><br>");
print("<p><b>Users:</b></p>");

$con = mysql_connect("localhost","root","altair8");
mysql_select_db("driver_site", $con);	

$sql = "SELECT id,username FROM DRIVERS";

$result = mysql_query($sql,$con);

if (!$result)
{
  die('Error: ' . mysql_error() . " sql: " . $sql);
}

while($row = mysql_fetch_array($result)){

	$user_id = $row['id'];
	echo "<a href=\"https://" . $server_address. "/drivers/driver_profile.php?driver_id=" . $user_id . "\">" . $row['username'] . "</a><br>";
}
?>

</body>
</html>

