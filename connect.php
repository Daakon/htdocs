<?php
<<<<<<< HEAD
$localhost = 'localhost';
$username = 'root';
$pass = 'admin10';
$rapportbook = 'Rapportbook';
$conn = mysql_connect($localhost,$username,$pass);
if (!$conn) {
    die(mysql_error());
}
else {
    mysql_selectdb($rapportbook, $conn);
}
=======
    $host = 'localhost';
    $username = 'root';
    $pass = 'admin10';
    $dbname = 'rapportbook';
    $conn = new PDO("mysql:host=".$host.";dbname=".$dbname, $username, $pass); 
    if (!$conn) {
       die(mysql_error()); 
    }
>>>>>>> origin/master

?>