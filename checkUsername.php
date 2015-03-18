<?php

session_start();

require 'getSession.php';

$ID = $_SESSION['ID'];

$sql = "SELECT Username FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

if ($rows['Username'] != $username) {
    echo "<script>alert('Sorry but it appears this is not your profile so we can you let you view it.'); location = '/index.php'</script>";
    exit;
}


?>