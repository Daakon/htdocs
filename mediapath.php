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
    $videoPath = "http://dev.playdoe.com/media/";
    $posterPath = "/home/playdoe/dev/poster/";
    $imagesPath = "http://dev.playdoe.com/images/";
    $mediaPath = "http://dev.playdoe.com/media/";
    $docPath = "http://dev.playdoe.com/doc/";
    $postPath = "http://dev.playdoe.com/";
}
else {
    $videoPath = "http://playdoe.com/media/";
    $posterPath = "/home/playdoe/public_html/poster/";
    $imagesPath = "http://playdoe.com/images/";
    $mediaPath = "http://playdoe.com/media/";
    $docPath = "http://playdoe.com/doc/";
    $postPath = "http://playdoe.com/";
}

function getPostPath() {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (strstr($url, "local")) {
    }
    elseif (strstr($url, "dev")) {
        $postPath = "http://dev.playdoe.com/";
    }
    else {
        $postPath = "http://playdoe.com/";
    }
    return $postPath;
}

?>