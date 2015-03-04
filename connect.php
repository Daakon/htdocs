<?php
    $host = 'localhost';
    $username = 'rapportbook';
    $pass = 'admin10';
    $dbname = 'rapportbook';
    $conn = new PDO("mysql:host=".$host.";dbname=".$dbname, $username, $pass); 
    if (!$conn) {
       die(mysql_error()); 
    }

?>