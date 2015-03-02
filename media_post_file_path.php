<?php
$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(session_id() == '') {
    session_start();
}
// get proper media post file path

        if (strstr($url, 'localhost:8888')) {

        $postMediaFilePath = trim("media/$postMediaName");

       }
       // test mobile writes to test desktop
       elseif (strstr($url, 'mdev.rapportbook.com') || strstr($url, "dev.rapportbook.com")) {

            $postMediaFilePath = trim("/var/www/dev/media/$postMediaName");
        }
        else {
            // live desktop and mobile
        $postMediaFilePath = trim("/var/www/rapportbook/media/$postMediaName");
       }

?>