
<?php
require 'imports.php';
session_start();


$limit = 10;
$lastPostID = $_GET['lastPostID'];
$hashtag = $_GET['hashtag'];


if (isset($hashtag) && !empty($hashtag)) {
    if ($hashtag == 'Show All') {} else {
        $genreCondition = "And Posts.Category = '$hashtag' ";
    }
}

$lastPostCondition = "And (Posts.ID < $lastPostID) ";

require 'connection-feed.php';

?>


