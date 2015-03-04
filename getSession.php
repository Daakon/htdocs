<?php
if(session_id() == '') {
    session_start();

    if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
        if (isset($_COOKIE['ID']) || !empty($_COOKIE['ID'])) {
            $_SESSION['ID'] = $_COOKIE['ID'];
        }
    }
    else {
        echo "<script>alert('You are not logged in');</script>";
    }
  }

?>