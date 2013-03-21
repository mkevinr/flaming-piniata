<?php

	$entityBody = file_get_contents('php://input');
	file_put_contents("entity body: " . $entityBody);
	
	print("<p>" . $entityBody . "/p");
	print("<p>Request['test']: " . $_REQUEST['test']);
?>