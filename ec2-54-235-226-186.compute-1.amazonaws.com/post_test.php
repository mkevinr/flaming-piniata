<?php

	
	if(array_key_exists('send', $_REQUEST)){
		print("sent: " . $_REQUEST['send'];
		$request = "hello";
		
		/*$url="https://ec2-54-235-226-186.compute-1.amazonaws.com/post_receive_test.php?test=yes";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($request))
		);
		curl_exec($ch);
		print("after sending curl request");*/
	}
	print('after if');
?>
<form action="https://ec2-54-235-226-186.compute-1.amazonaws.com/post_test.php?send=true">
<input type="submit" value="Send"></form>