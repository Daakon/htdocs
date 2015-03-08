<?php
$localhost = 'localhost';
$username = 'root';
$pass = 'admin10';
$rapportbook = 'rapportbook';
$conn = mysql_connect($localhost, $username, $pass);
if (!$conn) {
    die(mysql_error());
} else {
    mysql_selectdb($rapportbook, $conn);
}

?>