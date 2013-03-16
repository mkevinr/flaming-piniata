<html>
<body>
<a href="/drivers/">Home</a><br>
<p><b>Users:</b></p>
<?php

$con = mysql_connect("localhost","root","altair8");
mysql_select_db("driver_site", $con);	

$sql = "SELECT username FROM DRIVERS";

$result = mysql_query($sql,$con);

if (!$result)
{
  die('Error: ' . mysql_error() . " sql: " . $sql);
}

for($i = 0; i < mysql_num_rows(); i++){

	$row = mysql_fetch_array($result);
	$user = $row['username'];
	echo "<a href=\"/drivers/driver_profile.php?username=" . $user . "\">" . $user . "</a><br>";
}
?>

</body>
</html>

