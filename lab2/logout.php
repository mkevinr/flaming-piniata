<?php

   session_start();
   $_SESSION['username'] = '';
   $_SESSION['privileges'] = '';

   header("Location: /");
?>
