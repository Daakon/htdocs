<?php
session_start();
if (!isset($_SESSION["ID"])) {

    if (isset($_COOKIE['ID']) && !empty($_COOKIE['ID'])) {

        $_SESSION['ID'] = $_COOKIE['ID'];
        $ID = $_SESSION['ID'];

    } else {
        echo "<script>alert('You are not logged in'); location = 'index.php'</script>";
    }
}



?>