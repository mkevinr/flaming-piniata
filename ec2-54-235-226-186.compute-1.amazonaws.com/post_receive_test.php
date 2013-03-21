<?php

	$entityBody = file_get_contents('php://input');
	
	print("<p>" . $entityBody . "/p");
	print("<p>Request['test']: " . $_REQUEST['test']);
?>