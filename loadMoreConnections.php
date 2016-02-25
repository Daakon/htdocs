
<?php
require 'imports.php';
session_start();


$limit = 10;
$lastPostID = $_GET['lastPostID'];
$lastPostCondition = "And (Posts.ID < $lastPostID) ";

require 'connection-feed.php';

?>


