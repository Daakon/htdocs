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

function getGender($ID) {
    // return member gender
    $sql = "SELECT Gender FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $gender = $row['Gender'];
    return $gender;
}



function getAge($ID) {
    // returns member age
    $sql = "SELECT DOB FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $birthDate = $row['DOB'];

    $age = date_diff(date_create($birthDate), date_create('today'))->y;
    $format = 'Y-m-j G:i:s';
    $date = date($format);
    return $age;
}

function getMemberState($ID) {
    // returns member state
    $sql = "SELECT State FROM Profile WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $state = $row['State'];
    return $state;
}

function check_phone($ID) {
    $sql = "SELECT Phone FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    return $rows['Phone'];
}

// text function to all service providers for related service post

// text function for direct messages
function text_notification($receiverID, $senderID)
{
    require 'class-Clockwork.php';
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $result = mysql_query("SELECT Phone FROM Profile WHERE Member_ID = $receiverID");
        $row = mysql_fetch_assoc($result);
        $number = $row['Phone'];
        $number = preg_replace('/\D+/', '', $number);
        $number = "1" . $number;
        $senderName = get_users_name($senderID);
        $receiverName = get_users_name($receiverID);
        $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';
        try {
            // Create a Clockwork object using your API key
            $clockwork = new Clockwork($API_KEY);
            $domain;
            if (strstr($url, "dev")) {
                $domain = "http://dev.rapportbook.com/messages.php";
            } else {
                $domain = "http://rapportbook.com/messages.php";
            }
            // Setup and send a message
            $text = "$senderName sent you a new message on Rapportbook. $domain";
            $message = array('to' => $number, 'message' => $text);
            $result = $clockwork->send($message);
            // Check if the send was successful
            if ($result['success']) {
                //echo 'Message sent - ID: ' . $result['id'];
                echo "<script>alert('$receiverName was also sent an SMS');</script>";
            } else {
                $error = $result['error_message'];
                echo "<script>alert('Message failed - Error: $error');</script>";
            }
        } catch (ClockworkException $e) {
            echo 'Exception sending SMS: ' . $e->getMessage();
        }
}

function alert_all_matching_service_providers($service, $state)
{
    require 'class-Clockwork.php';
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $result = mysql_query("SELECT ID FROM Members WHERE Service = '$service'");

    if (mysql_num_rows($result) > 0) {
        // stuff all of the service providers into an array
        while ($rows = mysql_fetch_assoc($result)) {
            $serviceID = $rows['ID'];
            // send all of the service providers with a phone an SMS
            $serviceResults = mysql_query("SELECT Phone FROM Profile WHERE Member_ID = $serviceID And State = '$state'");

            if (mysql_num_rows($serviceResults) > 0) {
                while ($serviceRows = mysql_fetch_assoc($serviceResults)) {

                    $number = $serviceRows['Phone'];
                    $number = preg_replace('/\D+/', '', $number);
                    $number = "1" . $number;
                    $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';

                    try {
                        // Create a Clockwork object using your API key
                        $clockwork = new Clockwork($API_KEY);
                        $domain;
                        if (strstr($url, "dev")) {
                            $domain = "http://dev.rapportbook.com/messages.php";
                        } else {
                            $domain = "http://rapportbook.com/messages.php";
                        }
                        // Setup and send a message
                        $text = "There is a new post that matches your service on Rapportbook. $domain";
                        $message = array('to' => $number, 'message' => $text);
                        $result = $clockwork->send($message);

                        // Check if the send was successful
                        if ($result['success']) {
                            // success but no echo, we only want to know if SMS failed
                        } else {
                            $error = $result['error_message'];
                            echo "<script>alert('Message failed - Error: $error');</script>";
                        }
                    } catch (ClockworkException $e) {
                        echo 'Exception sending SMS: ' . $e->getMessage();
                    }
                }
            }
        }
    }
}