<?php
require 'imports.php';
session_start();


$limit = 10;
$lastPostID = $_GET['lastPostID'];
$username = $_GET['username'];
$profileID = get_id_from_username($username);

$lastPostCondition = "And (Posts.ID < $lastPostID) ";

require 'post-feed.php';

?>
