<?php

   $server_address = file_get_contents("../server_address");

   session_start();
   $_SESSION['driver_id'] = '';

   header("Location: https://" . $server_address . "/drivers/");
?>
