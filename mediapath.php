<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// only used for existing photos not the upload process

if (strstr($url, 'localhost:8888')) {

    $mediaPath = trim('/media/');

} // test mobile writes to test desktop
elseif (strstr($url, 'mdev.rapportbook.com') || strstr($url, "dev.rapportbook.com")) {

    $mediaPath = trim('http://dev.rapportbook.com/media/');
} else {
    // live desktop and mobile
    $mediaPath = trim('http://rapportbook.com/media/');

}
?>