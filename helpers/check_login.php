<?php
require '../connect.php';
$generic = $_POST['generic'];
$pass = $_POST['login_password'];

$sql = "SELECT * FROM Members WHERE Email = '$generic' Or username='$generic' And password = '$pass'";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

if (mysql_numrows($result) == 0) {

    echo 'false';
} else {
	echo 'true';
}
?>