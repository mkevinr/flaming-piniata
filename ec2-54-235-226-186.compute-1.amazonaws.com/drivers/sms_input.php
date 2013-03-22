<?php

	file_put_contents("sms_input_test", "was called!");
	
	$entityBody = file_get_contents('php://input');
	
	file_put_contents("sms_input_test", "\nrequest: " . print_r($_REQUEST), FILE_APPEND);
	file_put_contents("sms_input_test", "\n" . $entityBody, FILE_APPEND);

    //require '/path/to/twilio-php/Services/Twilio.php';
?>
