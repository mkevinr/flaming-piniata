<html>
<body>
<p><b>Drivers Home</b></p>

<?php
	$server_address = file_get_contents("../server_address");
	echo "<a href=\"https://" . $server_address . "/lab4/drivers/login.php\"><b>Login</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/lab4/drivers/create_account.php\"><b>Create Account</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/lab4/drivers/drivers.php\"><b>Driver Profiles</b></a><br>";
	echo "<a href=\"https://" . $server_address . "/lab4/drivers/logout.php\"><b>Logout</b></a><br>";
?>
</body>
</html>