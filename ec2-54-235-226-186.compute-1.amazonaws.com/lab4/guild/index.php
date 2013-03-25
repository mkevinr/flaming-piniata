<html>
<body>
<b>Welcome to the Driver's Guild site!</b><br/><br/>

<?php

	$server_address = file_get_contents("../server_address");
	
	print("<a href=\"https://" . $server_address . "/lab4/guild/register.php\">Register</a>");
	
?>

</html>
</body>
