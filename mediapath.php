<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $mediaPath = trim('/media/');
    $imagesPath = trim("/images/");
    $docPath = trim("/doc/");

// serve full video path
if (strstr($url, "local")) {
    $videoPath = "/media/";
    $posterPath = "/poster/";
    $docPath = "/doc/";
}
elseif (strstr($url, "dev")) {
    $videoPath = "http://dev.rapportbook.com/media/";
    $posterPath = "/home/rapportbook/dev/poster/";
    $imagesPath = "http://dev.rapportbook.com/images/";
    $mediaPath = "http://dev.rapportbook.com/media/";
    $docPath = "http://dev.rapportbook.com/doc/";
    $postPath = "http://dev.rapportbook.com/";
}
else {
    $videoPath = "http://rapportbook.com/media/";
    $posterPath = "/home/rapportbook/public_html/poster/";
    $imagesPath = "http://rapportbook.com/images/";
    $mediaPath = "http://rapportbook.com/media/";
    $docPath = "http://rapportbook.com/doc/";
    $postPath = "http://rapportbook.com/";
}

function getPostPath() {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (strstr($url, "local")) {
    }
    elseif (strstr($url, "dev")) {
        $postPath = "http://dev.rapportbook.com/";
    }
    else {
        $postPath = "http://rapportbook.com/";
    }
    return $postPath;
}

?>