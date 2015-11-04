<?php

session_start();

if (isset($_COOKIE['ID']) || isset($_SESSION['ID'])) {
echo "<script>location='/home'</script>";
}
    ?>
