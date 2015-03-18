<?php
require '../connect.php';
$email = $_POST['login_email'];

$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = $conn->prepare($sql) or die(mysql_error());
$result->execute();
$rows = $result->fetchAll();
if (count($rows) == 0) {
    echo "true";
} else {
    echo "false";
}
?>