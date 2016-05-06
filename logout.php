<?php
/*Destroy user session */

session_start();

// destroy all session variables
session_unset();


// clear cookie
setcookie("ID", "", time()-3600);

echo "<script>location = '../' </script>";

?>