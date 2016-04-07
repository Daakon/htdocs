<?php

/*if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {
    echo "<script>alert('Rapportbook can only be viewed in Chrome or Safari');location='http://google.com'</script>";
}
elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) {
    //For Supporting IE 11
    echo "<script>alert('Rapportbook can only be viewed in Chrome or Safari');location='http://google.com'</script>";
}
elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
    echo "<script>alert('Rapportbook can only be viewed in Chrome or Safari');location='http://google.com'</script>";
}
elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE) {
    echo "<script>alert('Rapportbook can only be viewed in Chrome or Safari');location='http://google.com'</script>";
}
elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE) {
    echo "<script>alert('Rapportbook can only be viewed in Chrome or Safari');location='http://google.com'</script>";
}*/


error_reporting(E_ERROR);
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$localhost = 'localhost';

if (strstr($url, "localhost")) {
    $username = 'root';
    $playdoe = 'playdoe';
    $pass = 'admin10';
}
elseif (strstr($url, "dev")) {
    $username = 'playdoe_playdoe';
    $playdoe = 'playdoe_devplaydoe';
    $pass = 'admin@10';
}
else {
    $username = 'playdoe_playdoe';
    $playdoe = 'playdoe_playdoe';
    $pass = 'admin@10';
}


$conn = mysql_connect($localhost, $username, $pass);
if (!$conn) {
    die(mysql_error());
} else {
    mysql_select_db($playdoe, $conn);
}

?>