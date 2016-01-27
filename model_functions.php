<?php


function checkActive($user_id)
{
    $sql = "SELECT IsActive FROM Members where ID = $user_id ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "checkActive() failed"));
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
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "checkEmailActive() failed"));
    $rows = mysql_fetch_assoc($result);
    if ($rows['EmailActive'] == 1) {
        return true;
    } else {
        return false;
    }
}

function checkSMSActive($user_id)
{
    $sql = "SELECT SMSActive FROM Members where ID = $user_id ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "checkSMSActive() failed"));
    $rows = mysql_fetch_assoc($result);
    if ($rows['SMSActive'] == 1) {
        return true;
    } else {
        return false;
    }
}

function get_users_name($user_id)
{

    $sql = "SELECT FirstName, LastName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_users_name() failed"));
    $rows = mysql_fetch_assoc($result);
    $name = null;
    $lastName = $rows['LastName'];
    if (strlen($lastName) > 0) {
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
    }
    else {
        $name = $rows['FirstName'];
    }

    return $name;

}

function get_user_firstName($user_id)
{

    $sql = "SELECT FirstName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_user_firstName() failed"));
    $rows = mysql_fetch_assoc($result);
    return $rows['FirstName'];

}

function checkNameLength($name) {
    if (strlen($name) > 70) {
        $name = substr($name, 0, 70).'...';
    }
    else {
        $name = $name;
    }
        return $name;
}

function get_username($user_id)
{

    $sql = "SELECT Username FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_username() failed"));
    $rows = mysql_fetch_assoc($result);
    return $rows['Username'];

}

function get_users_name_by_id($user_id)
{

    $sql = "SELECT FirstName, LastName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_users_name_by_id() failed"));
    while ($rows = mysql_fetch_assoc($result)) {
        return $rows['FirstName'] . ' ' . $rows['LastName'];
    }
}

function get_users_photo_by_id($user_id)
{

    $sql = "SELECT DISTINCT ProfilePhoto FROM Profile WHERE Member_ID = $user_id Order By Member_ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_users_photo_by_id() failed"));
    $rows = mysql_fetch_assoc($result);

    $photo = $rows['ProfilePhoto'];

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $fullMediaPath = "";

    if (strstr($url, "localhost")) {
        $fullMediaPath = "/media/$photo";
    } elseif (strstr($url, "dev")) {
        $fullMediaPath = "http://dev.rapportbook.com/media/$photo";
    } else {
        $fullMediaPath = "http://rapportbook.com/media/$photo";
    }
    return $fullMediaPath;
}


function check_password($user_id, $pass)
{

    $sql = "SELECT DISTINCT Password FROM Members WHERE ID =$user_id";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "check_password() failed"));
    $rows = mysql_fetch_assoc($result);
    if ($pass == $rows['Password']) {
        return true;
    } else {
        return false;
    }
}


function get_users_photo_by_id_raw($user_id)
{

    $sql = "SELECT DISTINCT ProfilePhoto FROM Profile WHERE Member_ID = $user_id";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_users_photo_by_id_raw() failed"));
    while ($rows = mysql_fetch_assoc($result)) {
        return $rows['ProfilePhoto'];
    }
}

function get_password($user_id)
{
    $sql = "SELECT DISTINCT Password FROM Members WHERE ID = '$user_id' ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_password()"));
    $rows = mysql_fetch_assoc($result);
    return $rows['Password'];
}

function get_email_by_id($user_id)
{

    $sql = "SELECT Email from Members where ID = $user_id";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_email_by_Id() failed"));
    $rows = mysql_fetch_assoc($result);
    return $rows['Email'];

}

function get_id_by_email($email)
{

    $sql = "SELECT ID FROM Members WHERE Email = '$email' ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_id_by_email() failed"));
    $rows = mysql_fetch_assoc($result);
    return $rows['ID'];
}


function getGender($ID) {
    // return member gender
    $sql = "SELECT Gender FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "getGender() failed"));
    $row = mysql_fetch_assoc($result);
    $gender = $row['Gender'];
    return $gender;
}



function getAge($ID) {
    // returns member age
    $sql = "SELECT DOB FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "getAge() failed"));
    $row = mysql_fetch_assoc($result);
    $birthDate = $row['DOB'];

    $age = date_diff(date_create($birthDate), date_create('today'))->y;
    $format = 'Y-m-j G:i:s';
    $date = date($format);
    return $age;
}


function getMemberState($ID) {
    // returns member state
    $sql = "SELECT State FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "getMemberState() failed"));
    $row = mysql_fetch_assoc($result);
    $state = $row['State'];
    return $state;
}

function getMemberCity($ID) {
    // returns member state
    $sql = "SELECT City FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "getMemberCity() failed"));
    $row = mysql_fetch_assoc($result);
    $city = $row['City'];
    return $city;
}

function get_id_from_username($username) {
    // returns member state
    $sql = "SELECT ID FROM Members WHERE Username = '$username' ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_id_from_username() failed"));
    $row = mysql_fetch_assoc($result);
    $id = $row['ID'];
    return $id;
}

function get_username_from_url() {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    preg_match("/[^\/]+$/", $url, $match);
    $username = $match[0];
    return $username;
}

function get_interest($ID) {
    // returns business category
    $sql = "SELECT Interest FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_interest() failed"));
    $row = mysql_fetch_assoc($result);
    $interest = $row['Interest'];
    return $interest;
}

function check_phone($ID) {
    $sql = "SELECT Phone FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "check_phone() failed"));
    $rows = mysql_fetch_assoc($result);
    return $rows['Phone'];
}

function check_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return true;
    }
    else {
        return false;
    }
}

function is_existing_email($email, $ID) {
    $sql = "SELECT Email FROM Members WHERE Email = '$email' AND ID != $ID  ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "is_existing_email() failed"));
    $rows = mysql_fetch_assoc($result);
    if (mysql_num_rows($result) > 0) {
        return true;
    }
    else {
        return false;
    }
}

function check_demographics($ID) {
    $sql = "SELECT * FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "check_demographics() failed"));
    $rows = mysql_fetch_assoc($result);
    $about = $rows['About'];
    $city = $rows['City'];
    $state = $rows['State'];
    $zip = $rows['Zip'];
    $date = date('Y-m-d');

    if ($_SESSION['DATE'] != $date) {
        if ($city == '') {
            echo "<script>alert('Please provide your current city in your profile');</script>";
            $_SESSION['DATE'] = $date;
        }
        elseif ($state == '') {
            echo "<script>alert('Please provide your current state in your profile');</script>";
            $_SESSION['DATE'] = $date;
        }
        elseif ($zip == '') {
            echo "<script>alert('Please provide your current zip code in your profile');</script>";
            $_SESSION['DATE'] = $date;
        }
        elseif ($about == '') {
            echo "<script>alert('The about section in your profile is empty. Tell everyone a little about yourself');</script>";
            $_SESSION['DATE'] = $date;
        }
    }


}

function isProfilePhoto($imagePath) {
    $sql = "Select ProfilePhoto From Profile Where ProfilePhoto = '$imagePath' ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "isProfilePhoto() failed"));
    $row = mysql_fetch_assoc($result);

    if (mysql_num_rows($result) > 0) {
        return true;
    }
    else {
        return false;
    }
}

function isProfileVideo($videoPath) {
    $sql = "Select ProfileVideo From Profile Where ProfileVideo = '$videoPath' ";
    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "isProfilePhoto() failed"));
    $row = mysql_fetch_assoc($result);

    if (mysql_num_rows($result) > 0) {
        return true;
    }
    else {
        return false;
    }
}


function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.'-'.$lastFour;
    }

    return $phoneNumber;
}

function getChatProfilePic($groupID, $ID) {
    $sql = "SELECT ThreadOwner_ID FROM Messages Where GroupID = '$groupID' ";
    $result = mysql_query($sql);
    $width = '';
    $height = '';

    if (mysql_num_rows($result) > 2) { $width = 'width="50%"'; $height='height="33px"'; }

    $profile_ids = array();
    //Iterate over the results and sort out the biz ids from the consumer ones.
    while ($rows = mysql_fetch_assoc($result)) {
        array_push($profile_ids, $rows['ThreadOwner_ID']);
    }

//Boil the ids down to unique values bc we dont want it send double emails or notifications
    $profile_ids = array_unique($profile_ids);
//Send consumer notifications
    $profilePic = '';

    foreach ($profile_ids as $item) {

        if (!empty($item) && $item != $ID) {
            // only send email if account & email active
            $profilePic .= "<image src= '/media/".get_users_photo_by_id_raw($item)."' style='float:left;' $width $height  alt='' />";
        }
    }

    return $profilePic;
}

// text function for direct messages
function text_notification($receiverID, $senderID)
{
    if (checkSMSActive($receiverID)) {
        require 'class-Clockwork.php';
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $result = mysql_query("SELECT Phone FROM Profile WHERE Member_ID = $receiverID") or die(logError(mysql_error(), "model_functions", "checkSMSActive() failed"));
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
            $username = get_username($senderID);
            if (strstr($url, "dev")) {
                $domain = "http://dev.rapportbook.com/view_messages/$username";
            } else {
                $domain = "http://rapportbook.com/view_messages/$username";
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
            // dont want to display failures in the browser
            //echo 'Exception sending SMS: ' . $e->getMessage();
        }
    }
}

// close opened html tags
function closetags($html)
{
    #put all opened tags into an array
    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
    $openedtags = $result[1];
    #put all closed tags into an array
    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
    $closedtags = $result[1];
    $len_opened = count ( $openedtags );
    # all tags are closed
    if( count ( $closedtags ) == $len_opened )
    {
        return $html;
    }
    $openedtags = array_reverse ( $openedtags );
    # close tags
    for( $i = 0; $i < $len_opened; $i++ )
    {
        if ( !in_array ( $openedtags[$i], $closedtags ) )
        {
            $html .= "</" . $openedtags[$i] . ">";
        }
        else
        {
            unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
        }
    }
    return $html;
}


// log errors
function logError($error, $page, $object) {
    $sql = "INSERT INTO Log (Error, Page, Object, LogDate) Values ('$error', '$page', '$object', CURDATE) ";
    mysql_query($sql) or die(mysql_error());
    echo "<script>location='/something_happened'</script>";
    exit;
}

// text function to all service providers for related service post
function alert_all_matching_interests($interest, $state)
{
    session_start();
    $ID = $_SESSION['ID'];
    require 'class-Clockwork.php';
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $result = mysql_query("SELECT ID, Interest FROM Members WHERE Interest = '$interest' And ID NOT IN ($ID)");

    if (mysql_num_rows($result) > 0) {
        // stuff all of the members into an array
        while ($rows = mysql_fetch_assoc($result)) {
            $interestID = $rows['ID'];

            // send all of the service providers with a phone an SMS
            $interestResults = mysql_query("SELECT Phone FROM Profile WHERE Member_ID = $interestID And State = '$state'");

            if (mysql_num_rows($interestResults) > 0) {
                while ($interestRows = mysql_fetch_assoc($interestResults)) {

                    //$number = $interestRows['Phone'];
                    //$number = preg_replace('/\D+/', '', $number);
                    //$number = "1" . $number;
                    //$API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';

                   /* try {
                        // Create a Clockwork object using your API key
                        $clockwork = new Clockwork($API_KEY);
                        $domain;
                        if (strstr($url, "dev")) {
                            $domain = "http://dev.rapportbook.com/home?scrollx=630&scrolly=630";
                        } else {
                            $domain = "http://rapportbook.com/home?scrollx=630&scrolly=630";
                        }
                        // Setup and send a message
                        $text = "There is a new post that matches your interest on Rapportbook. $domain";
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
                        // dont want to display failures in the browser
                        //echo 'Exception sending SMS: ' . $e->getMessage();
                    } */
                }
            }
            // send out an email after text
            if (checkEmailActive($interestID)) {
                build_and_send_email(0, $interestID, 11, null, $interest);
            }
        }
    }
}