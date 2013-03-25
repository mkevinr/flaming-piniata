<html>
<body>
<?php
$server_address = file_get_contents("../server_address");
print("<a href=\"https://" . $server_address . "/lab4/drivers/\">Home</a><br>");
print("<p><b>Users:</b></p>");

$con = mysql_connect("localhost","root","altair8");
mysql_select_db("driver_site2", $con);	

$sql = "SELECT id,name FROM DRIVERS";

$result = mysql_query($sql,$con);

if (!$result)
{
  die('Error: ' . mysql_error() . " sql: " . $sql);
}

while($row = mysql_fetch_array($result)){

	$user_id = $row['id'];
	echo "<a href=\"https://" . $server_address. "/lab4/drivers/driver_profile.php?driver_id=" . $user_id . "\">" . $row['name'] . "</a><br>";
}
?>

</body>
</html>

