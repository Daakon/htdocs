<?php
$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 // get proper file path
        if (strstr($url, 'localhost:8888')) {
        
        $mediaPath = trim('media/');

       }
       // test mobile writes to test desktop
       elseif (strstr($url, 'mdev.rapportbook.com') || strstr($url, "dev.rapportbook.com")) {
           
            $mediaPath = trim('http://dev.rapportbook.com/media/');
        }
        else {
            // live desktop and mobile
        $mediaPath = trim('http://businessconnect.co/media/');

       }
?>