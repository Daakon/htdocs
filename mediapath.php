<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $mediaPath = trim('/media/');
    $imagesPath = trim("/images/");

// serve full video path
if (strstr($url, "local")) {
    $videoPath = "/media/";
    $posterPath = "/poster/";
}
elseif (strstr($url, "dev")) {
    $videoPath = "http://dev.rapportbook.com/media/";
    $posterPath = "/home/rapportbook/dev/poster/";
    $imagesPath = "http://dev.rapportbook.com/images";
}
else {
    $videoPath = "http://rapportbook.com/media/";
    $posterPath = "/home/rapportbook/public_html/poster/";
    $imagesPath = "http://rapportbook.com/images/";
}


?>