<?php

   session_start();
   $_SESSION['driver_id'] = '';

   header("Location: /drivers/");
?>
