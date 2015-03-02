<?php
$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 // get proper file path
        if (strstr($url, 'localhost:8888')) {
        
        $mediapath = trim('media/');

       }
       // test mobile writes to test desktop
       elseif (strstr($url, 'mdev.rapportbook.com') || strstr($url, "dev.rapportbook.com")) {
           
            $mediapath = trim('http://dev.rapportbook.com/media/');
        }
        else {
            // live desktop and mobile
        $mediapath = trim('http://businessconnect.co/media/');

       }
?>