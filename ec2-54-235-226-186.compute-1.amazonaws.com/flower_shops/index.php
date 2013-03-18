<html>
<body>
<p><b>Flower Shops Home</b></p>

<?php
	$server_address = file_get_contents("../server_address");
	echo "<a href=\"https://" . $server_address . "/flower_shops/login.php\"><b>Login</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/flower_shops/create_account.php\"><b>Create Account</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/flower_shops/flower_shops.php\"><b>Flower Shop Profiles</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/flower_shops/logout.php\"><b>Logout</b></a><br>";
?>
</body>
</html>