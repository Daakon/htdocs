<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $mediaPath = trim('/media/');
    $images = trim("/images/");

// serve full video path
if (strstr($url, "local")) {
    $videoPath = "/media/";
}
elseif (strstr($url, "dev")) {
    $videoPath = "http://dev.rapportbook.com/media/";
}
else {
    $videoPath = "http://rapportbook.com/media/";
}
?>