<?php

   $server_address = file_get_contents("../server_address");

   session_start();
   session_unset('flower_shop_id');

   header("Location: https://" . $server_address . "/flower_shops/");
?>
