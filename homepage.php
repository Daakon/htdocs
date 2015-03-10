<?php

session_start();
if (!isset($_SESSION["ID"])) {

if (isset($_COOKIE['ID']) && !empty($_COOKIE['ID'])) {

$_SESSION['ID'] = $_COOKIE['ID'];
$ID = $_SESSION['ID'];
    $sql = "SELECT Username FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows =  mysql_fetch_assoc($result);
    $username = $rows['Username'];

    header('profile.php');
}
} else {
echo "<script>alert('You are not logged in'); location = 'index.php'</script>";
}


?>