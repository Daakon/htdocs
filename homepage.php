<?php

require 'connect.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
session_start();
if (isset($_SESSION["ID"]) && !empty($_SESSION['ID'])) {
    $ID = $_SESSION['ID'];

    $sql = "SELECT Username FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows =  mysql_fetch_assoc($result);
    $username = $rows['Username'];

    echo "<script>location='/$username';</script>";
}
else {
    echo "<script>location = '/learn_more'</script>";
}


?>