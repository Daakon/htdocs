<?php
require 'connect.php';

session_start();
if (isset($_SESSION["ID"])) {


$ID = $_SESSION['ID'];
    $sql = "SELECT Username FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows =  mysql_fetch_assoc($result);
    $username = $rows['Username'];

    header("Location: profile.php/$username");
}
else {
    echo "<script>alert('You are not logged in'); location = 'index.php'</script>";
}


?>