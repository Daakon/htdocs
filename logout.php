<?php
/*Destroy user session */
session_start();
unset($_SESSION['ID']);
session_destroy();


// clear cookie
setcookie("ID", "", time()-3600);

header('location:index.php');

?>