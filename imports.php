<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require 'connect.php';
require 'mediaPath.php';
require 'model_functions.php';
if (strstr($url, "learn_more") || strstr($url, "login") || strstr($url, "signup") || strstr($url, "terms") || strstr($url, "show_post")) {
    // dont check session
}
else {
    require 'getSession.php';
}
require 'category.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'calendar.php';
require 'getState.php';
require 'memory_settings.php';

if (strstr($url, "login-mobile.php")) {
    require 'checkLogin.php';
}
?>