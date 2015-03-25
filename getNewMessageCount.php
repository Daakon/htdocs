<?php
require 'connect.php';
require 'getSession.php';
$ID = $_SESSION['ID'];

$sql = "SELECT * FROM Messages WHERE (ThreadOwner_ID = '$ID') And (New = 1)";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_numrows($result) > 0 && isset($_SESSION['ID'])) {
echo '<a href="/messages.php">Message(s)<span style = "background-color:red;border:1px solid black;border-radius:10px;color:white;padding-left:5px;padding-right:5px;padding-top:3px;margin-left:5px;">'.mysql_numrows($result).'</a></span>';
}

?>