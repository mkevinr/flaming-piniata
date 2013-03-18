<html>
<body>
<a href="/drivers/">Home</a><br>
<p><b>Users:</b></p>
<?php

$con = mysql_connect("localhost","root","altair8");
mysql_select_db("driver_site", $con);	

$sql = "SELECT id FROM DRIVERS";

$result = mysql_query($sql,$con);

if (!$result)
{
  die('Error: ' . mysql_error() . " sql: " . $sql);
}

while($row = mysql_fetch_array($result)){

	$row = mysql_fetch_array($result);
	$user_id = $row['id'];
	echo "<a href=\"/drivers/driver_profile.php?user_id=" . $user_id . "\">" . $user_id . "</a><br>";
}
?>

</body>
</html>

