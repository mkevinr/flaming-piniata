<html>
<body>
<p><b>Drivers Home</b></p>

<?php
	$server_address = file_get_contents("../server_address");
	echo "<a href=\"http://" . $server_address . "/drivers/login.php\"><b>Login</b></a><br>";
	echo "<a href=\"http://" . $server_address . "/drivers/create_account.php\"><b>Create Account</b></a><br>";
	echo "<a href=\"http://" . $server_address . "/drivers/driver_profile.php\"><b>Profile</b></a><br>";
?>
</body>
</html>