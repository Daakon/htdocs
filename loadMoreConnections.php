
<?php
require 'imports.php';
session_start();


$limit = 10;
$lastPostID = $_GET['lastPostID'];
$genre = $_GET['genre'];


if (isset($genre) && !empty($genre)) {
    if ($genre == 'Show All') {} else {
        $genreCondition = "And Posts.Category = '$genre' ";
    }
}

$lastPostCondition = "And (Posts.ID < $lastPostID) ";

require 'connection-feed.php';

?>


