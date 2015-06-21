<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $mediaPath = trim('/media/');
    $images = trim("/images/");

// serve full video path
if (strstr($url, "local")) {
    $videoPath = "/media/";
    $posterPath = "/poster/";
}
elseif (strstr($url, "dev")) {
    $videoPath = "http://dev.rapportbook.com/media/";
    $posterPath = "/home/rapportbook/dev/poster/";
}
else {
    $videoPath = "http://rapportbook.com/media/";
    $posterPath = "/home/rapportbook/public_html/poster/";
}


?>