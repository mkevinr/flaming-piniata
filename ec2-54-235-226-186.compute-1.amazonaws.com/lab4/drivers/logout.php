<?php

   $server_address = file_get_contents("../server_address");

   session_start();
   session_unset('driver_id');

   header("Location: https://" . $server_address . "/lab4/drivers/");
?>
