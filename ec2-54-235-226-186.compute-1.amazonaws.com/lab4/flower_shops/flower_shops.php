<html>
<body>
<?php
$server_address = file_get_contents("../server_address");
print("<a href=\"https://" . $server_address . "/lab4/flower_shops/\">Home</a><br>");
print("<p><b>Users:</b></p>");

$con = mysql_connect("localhost","root","altair8");
mysql_select_db("flower_shop_site2", $con);	

$sql = "SELECT id,name FROM FLOWER_SHOPS";

$result = mysql_query($sql,$con);

if (!$result)
{
  die('Error: ' . mysql_error() . " sql: " . $sql);
}

while($row = mysql_fetch_array($result)){

	echo "<a href=\"https://" . $server_address. "/lab4/flower_shops/flower_shop_profile.php?flower_shop_id=" . $row['id'] . "\">" 
			. $row['name'] . "</a><br>";
}
?>

</body>
</html>

