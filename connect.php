<?php
error_reporting(E_ERROR);
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$localhost = 'localhost';

if (strstr($url, "localhost")) {
    $username = 'root';
    $rapportbook = 'rapportbook';
    $pass = 'admin10';
}
elseif (strstr($url, "dev")) {
    $username = 'rapportb_rapport';
    $rapportbook = 'rapportb_devrapportbook';
    $pass = 'admin10';
}
else {
    $username = 'rapportb_rapport';
    $rapportbook = 'rapportb_rapportbook';
    $pass = 'admin10';
}


$conn = mysql_connect($localhost, $username, $pass);
if (!$conn) {
    die(mysql_error());
} else {
    mysql_selectdb($rapportbook, $conn);
}

?>