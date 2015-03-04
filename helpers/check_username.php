<?php
require '../connect.php';
$username = $_POST['username'];

$sql = "SELECT * FROM Members WHERE Username = '$username'";
$result = $conn->prepare($sql) or die(mysql_error());
$result->execute();
$rows = $result->fetchAll();
if (count($rows) == 0) {
    echo "true";
} else {
    echo "false";
}
?>