<html>
<body>
<b>Welcome!<br/><br/>
<?php

	$server_address = file_get_contents("../server_address");
	print("<a href=\"https://" . $server_address . "/drivers/\">Driver Site</a><br/>");
	print("<a href=\"https://" . $server_address . "/flower_shops/\">Flower Shop Site</a><br/>");
?>
</b>