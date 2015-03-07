<?php
require 'connect.php';
require 'mediaPath.php';

//These functions will help us refactor
if (session_id() == '') {
    session_start();
}

function get_users_id()
{
    return $_SESSION['ID'];
}

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

    $sql = "SELECT DISTINCT MediaName FROM Media WHERE ID =$user_id And IsProfilePhoto = 1 ";
    $result = mysql_query($sql) or die(mysql_error());
    while ($rows = mysql_fetch_assoc($result)) {
        $photo = $rows['MediaName'];
        echo "<img src='$mediaPath$photo' width=52 height=52/>";
    }

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


function get_users_photo_by_id_raw($user_id, $user_type)
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

