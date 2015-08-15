<?php
require 'connect.php';
require 'mediaPath.php';



function checkActive($user_id)
{
    $sql = "SELECT IsActive FROM Members where ID = $user_id ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    if ($rows['IsActive'] == 1) {
        return true;
    } else {
        return false;
    }
}

function checkEmailActive($user_id)
{
    $sql = "SELECT EmailActive FROM Members where ID = $user_id ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    if ($rows['EmailActive'] == 1) {
        return true;
    } else {
        return false;
    }
}

function get_users_name($user_id)
{

    $sql = "SELECT FirstName, LastName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['FirstName'] . ' ' . $rows['LastName'];

}

function get_username($user_id)
{

    $sql = "SELECT Username FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['Username'];

}

function get_users_name_by_id($user_id)
{

    $sql = "SELECT FirstName, LastName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(mysql_error());
    while ($rows = mysql_fetch_assoc($result)) {
        return $rows['FirstName'] . ' ' . $rows['LastName'];
    }
}

function get_users_photo_by_id($user_id)
{

    $sql = "SELECT DISTINCT Poster FROM Profile WHERE Member_ID = $user_id Order By Member_ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);

    $photo = $rows['Poster'];

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $fullMediaPath = "";

    if (strstr($url, "localhost")) {
        $fullMediaPath = "/poster/$photo";
    } elseif (strstr($url, "dev")) {
        $fullMediaPath = "http://dev.rapportbook.com/poster/$photo";
    } else {
        $fullMediaPath = "http://rapportbook.com/poster/$photo";
    }
    return $fullMediaPath;
}


function check_password($user_id, $pass)
{

    $sql = "SELECT DISTINCT Password FROM Members WHERE ID =$user_id";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    if ($pass == $rows['Password']) {
        return true;
    } else {
        return false;
    }
}


function get_users_photo_by_id_raw($user_id)
{

    $sql = "SELECT DISTINCT MediaName FROM Media WHERE ID = $user_id";
    $result = mysql_query($sql) or die(mysql_error());
    while ($rows = mysql_fetch_assoc($result)) {
        return $rows['MediaName'];
    }
}

function get_password($user_id)
{
    $sql = "SELECT DISTINCT Password FROM Members WHERE ID = '$user_id' ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['Password'];
}

function get_email_by_id($user_id)
{

    $sql = "SELECT Email from Members where ID = $user_id";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['Email'];

}

function get_id_by_email($email)
{

    $sql = "SELECT ID FROM Members WHERE Email = '$email' ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['ID'];
}

function get_is_service_provider($ID) {
    $sql = "SELECT IsServiceProvider FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['IsServiceProvider'];
}

function check_service_is_provided($ID) {
    $sql = "SELECT Service FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $service =  $rows['Service'];;
    echo "<script>alrt('$service');</script>";
    return $rows['Service'];
}

function check_phone($ID) {
    $sql = "SELECT Phone FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['Phone'];
}

