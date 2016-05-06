<?php

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// where ffmpeg is located
$ffmpeg = '/usr/local/bin/ffmpeg';

require 'connect.php';
require 'mediapath.php';

require 'model_functions.php';

if (strstr($url, "forgot-password") || strstr($url, "learn_more")
    || strstr($url, "login") || strstr($url, "signup")
    || strstr($url, "terms") || strstr($url, "show_post")
    || strstr($url, "support") || strstr($url, "homepage")
    || strstr($url, "create_pass") || strstr($url, "something_happened")
    || strstr($url, "hashtag_codes")
) {
    // dont check session
}
else if ($_SESSION['IsProfilePage'] == true) {
    // allow access to public profile page
}
else {
    require 'getSession.php';
}
require 'category.php';

require 'html_functions.php';
require 'findURL.php';

if (strstr($url, "learn_more")) {} else {
    require 'email.php';
}

require 'calendar.php';
require 'getState.php';
require 'memory_settings.php';

if (strstr($url, "login-mobile.php")) {
    require 'checkLogin.php';
}


if (IsSuspended($ID)) {
    echo "<script>alert('This account has been suspended. Please contact support: support@playdoe.com'); location = '/logout.php';</script>";

}

?>