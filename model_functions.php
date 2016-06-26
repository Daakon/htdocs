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
        $name = trim($name);
    }
    else {
        $name = $rows['FirstName'];
        $name = trim($name);
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

function check_referral_ID($referralID) {
    $sql = "SELECT Username FROM Members WHERE Username = '$referralID' ";
    $result = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($result) > 0) {
       return true;
    }
    else {
        return false;
    }

}

function get_users_name_by_id($user_id)
{

    $sql = "SELECT FirstName, LastName FROM Members where ID = $user_id";

    $result = mysql_query($sql) or die(logError(mysql_error(), "model_functions", "get_users_name_by_id() failed"));
    while ($rows = mysql_fetch_assoc($result)) {
        $firstName = $rows['FirstName'];
        $lastName = $rows['LastName'];
        if (strlen($lastName) > 0) {
            $name = $firstName.' '.$lastName;
        }
        else {
            $name = $firstName;
        }
        return trim($name);
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
        $fullMediaPath = "http://dev.playdoe.com/media/$photo";
    } else {
        $fullMediaPath = "http://playdoe.com/media/$photo";
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

function isAdmin($ID) {
    $sql = "SELECT IsAdmin FROM Members WHERE ID = $ID And IsAdmin = 1 ";
    $result = mysql_query($sql) or die(mysql_error());

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

function shortenUrl($url) {
    // Create instance with key
    require_once('GoogleUrlApi.php');

    $key = 'AIzaSyDDJWMzugYylMWrc1T5VW2KNszzI-9s3v4';
    $google = new GoogleUrlApi($key);

// Test: Shorten a URL
    $shortURL = $google->shorten($url);
    return $shortURL; // returns http://goo.gl/DbkFol

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
        if (checkBlock($ID, $rows['ThreadOwner_ID']) == false) {
            array_push($profile_ids, $rows['ThreadOwner_ID']);
        }
        }


//Boil the ids down to unique values bc we dont want it send double emails or notifications
    $profile_ids = array_unique($profile_ids);
//Send consumer notifications
    $profilePic = '';

    $counter = 0;
    foreach ($profile_ids as $item) {
        if ($counter <= 4) {
        if (!empty($item) && $item != $ID) {
            // only send email if account & email active
            $profilePic .= "<image src= '/media/".get_users_photo_by_id_raw($item)."' style='float:left;' $width $height  alt='' />";
            $counter++;
            }
        }
    }

    return $profilePic;
}

// text function for direct messages
function text_notification($receiverID, $senderID, $groupID)
{
    require_once('class-Clockwork.php');
    if (checkSMSActive($receiverID)) {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $result = mysql_query("SELECT Phone FROM Profile WHERE Member_ID = $receiverID") or die(logError(mysql_error(), "model_functions", "checkSMSActive() failed"));
        $row = mysql_fetch_assoc($result);
        $number = $row['Phone'];
        $number = preg_replace('/\D+/', '', $number);
        $number = "1" . $number;
        $senderName = get_users_name($senderID);
        $receiverName = get_users_name($receiverID);
        $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';

            // Create a Clockwork object using your API key
            $clockwork = new Clockwork($API_KEY);
            $domain = null;
            $username = get_username($senderID);
            // check if group message
            if (strlen($groupID) > 0) {
                $username = $groupID;
            }
            if (strstr($url, "dev")) {
                $domain = "dev.playdoe.com/view_messages/$username";
            } else {
                $domain = "http://playdoe.com/view_messages/$username";
            }
                $domain = shortenUrl($domain);

            $text = "You have a new message from $senderName on Playdoe: $domain";

            // Setup and send a message
            $message = array('to' => $number, 'message' => $text);
            $result = $clockwork->send($message);
            // Check if the send was successful
        if (strlen($groupID) > 0) {} else {
            if ($result['success']) {
                //echo 'Message sent - ID: ' . $result['id'];
                //echo "<script>alert('$receiverName was sent an SMS');</script>";
            } else {
                $error = $result['error_message'];
                //echo "<script>alert('Message failed - Error: $error');</script>";
            }
        }

    }
    return true;
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


function checkBlock($ID, $memberID) {
    // redeclare session ID as it has a tendency to get lost with this function
    $ID = $_SESSION['ID'];
    $sql = "SELECT EXISTS (SELECT BlockedID FROM Blocks WHERE (BlockerID = $ID AND BlockedID = $memberID) Or (BlockerID = $memberID and BlockedID = $ID))";
    $result = mysql_query($sql) or die(mysql_error());
    $count = mysql_num_rows($result);
    // if this query returns true, then this user is blocked
    // now we must run the actual query
    if ($count == 1) {

        $sql1 = "SELECT BlockedID FROM Blocks WHERE (BlockerID = $ID AND BlockedID = $memberID) Or (BlockerID = $memberID And BlockedID = $ID)";
        $result1 = mysql_query($sql1) or die(mysql_error());
        if (mysql_num_rows($result1) > 0) {
            return true;
        }
        else { return false; }
     }
    else {
        return false;
    }
}

function getRedeemPoints($ID, $username) {

    // get referral money
    $sql3 = "SELECT COUNT( Referrals.ID ) AS ReferralCount
            FROM Referrals, Members
            WHERE Referrals.Referral_ID =  '$username'
            AND Referrals.IsRedeemed =0
            AND Referrals.Signup_ID = Members.ID
            AND Referrals.Signup_ID
            IN (
            SELECT Posts.Member_ID
            FROM Posts
            WHERE Posts.IsDeleted =0
            GROUP BY Posts.ID
            HAVING COUNT( Posts.ID ) >=5
            )
            AND Members.IsEmailValidated =1 ";
    $result3 = mysql_query($sql3) or die(mysql_error());
    $rows3 = mysql_fetch_assoc($result3);
    $referralCount = $rows3['ReferralCount'];
    $referralMoney = $referralCount * 1;

    $sql = "Select count(PostApprovals.ID) as LikeCount
    From PostApprovals, Members
    Where (PostApprovals.Owner_ID = $ID) and (PostApprovals.IsRedeemed = 0)
    And PostApprovals.Member_ID = Members.ID
    And Members.IsEmailValidated = 1";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $likeCount = $rows['LikeCount'];
    $likeMoney = $likeCount * 0.04;

    $sql2 = "Select Count(PostComments.ID) As CommentCount
    FROM PostComments, Members
    WHERE PostComments.Owner_ID = $ID And PostComments.IsRedeemed = 0
    And PostComments.Member_ID = Members.ID
    And Members.IsEmailValidated = 1";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $commentCount = $rows['CommentCount'];
    $commentMoney = $commentCount * 0.04;

    $sql1 = "SELECT COUNT(Follows.ID) AS FollowerCount
    FROM Follows, Members
    WHERE Follows.Followed_ID = $ID
    AND IsRedeemed =0
    And Follows.Follower_ID = Members.ID
    And Members.IsEmailValidated = 1";
    $result1 = mysql_query($sql1) or die(mysql_error());
    $rows1 = mysql_fetch_assoc($result1);
    $followerCount = $rows1['FollowerCount'];
    $followerMoney = $followerCount * 0.05;

    $addedMoney = $referralMoney + $likeMoney + $commentMoney + $followerMoney;
    $totalMoney =  money_format('$%i', $addedMoney);

    // tally redemption points
    return $totalMoney;
}

function hasTenPost($ID) {
    $sql = "Select count(ID) as PostCount FROM Posts Where (Member_ID = $ID)
    And (IsRedeemed = 0) And (DATE(PostDate) = DATE(NOW()))  And (IsDeleted = 0)";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $postCount = $rows['PostCount'];

    if ($postCount > 9) {
        return true;
    }
    else {
        return false;
    }
}

function isEmailValidated($ID) {
    $sql = "Select IsEmailValidated From Members Where ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $isEmailValidated = $rows['IsEmailValidated'];

    if ($isEmailValidated == 0) {
        return false;
    }
    else {
        return true;
    }
}

function isSuspended($ID) {
    $sql = "Select IsSuspended From Members Where ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $isSuspended = $rows['IsSuspended'];

    if ($isSuspended == 0) {
        return false;
    }
    else {
        return true;
    }
}

function hasHourPast($ID) {
    $sql = "SELECT PostDate From Posts Where Member_ID = $ID And IsDeleted = 0 Order by PostDate DESC LIMIT 1";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $timeOfLastPost = $rows['PostDate'];
    // get server time
    date_default_timezone_set('America/Chicago'); // CDT
    $currentTime = date('Y-m-d H:i:s');

    $timeElapsed = strtotime($currentTime) - strtotime($timeOfLastPost);

    if ($timeElapsed >= 3600) {
        return true;
    }
    else {
        return false;
    }
}

/// Find hash tags
function hashtag_links($string) {
    $result = '';
    $words = explode(" ", $string);
    $ascii = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
        "video/quicktime", "video/webm", "video/x-matroska",
        "video/x-ms-wmw");

    foreach ($words as $word) {
        // check for any special ascii characters that might come back in a link title
        // before building the hashtag link.
        if (strstr($word, '#039') || strstr($word, '#8217') || strstr($word, '#032') || strstr($word, '#033')
            || strstr($word, '#034') || strstr($word, '#035') || strstr($word, '#036') || strstr($word, '#037')
            || strstr($word, '#038') || strstr($word, '#039') || strstr($word, '#040') || strstr($word, '#041')
            || strstr($word, '#042') || strstr($word, '#043') || strstr($word, '#044') || strstr($word, '#045')
            || strstr($word, '#046') || strstr($word, '#047') || strstr($word, '#048') || strstr($word, '#049')
            || strstr($word, '#050') || strstr($word, '#051') || strstr($word, '#052') || strstr($word, '#053')
            || strstr($word, '#054') || strstr($word, '#055') || strstr($word, '#056') || strstr($word, '#057')
            || strstr($word, '#058') || strstr($word, '#059') || strstr($word, '#060') || strstr($word, '#061')
            || strstr($word, '#062') || strstr($word, '#063')) { } else {
            $word = preg_replace('/#(\w+)/', ' <a href="/hashtag?hashtag=$1" style="padding-right:2px;">#$1</a>', $word);
            $word = str_replace("</a>  <a href", "</a>&nbsp;<a href", $word);
            $result .= $word . ' ';
        }
    }

    return $result;
}

// text function to all service providers for related service post
function alert_followers($postID)
{
    session_start();
    $ID = $_SESSION['ID'];
    require 'class-Clockwork.php';
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $result = mysql_query("SELECT Follower_ID FROM Follows WHERE Followed_ID = $ID");
    $hashtag = $_SESSION['Hashtag'];

    if (mysql_num_rows($result) > 0) {
        // stuff all of the members into an array
        while ($rows = mysql_fetch_assoc($result)) {
            $followerID = $rows['Follower_ID'];

            // send an SMS
            $followerResults = mysql_query("SELECT Post_Notification_Date, Phone FROM Profile WHERE Member_ID = $followerID And DATE(Post_Notification_Date) < DATE(NOW())  ");

            if (mysql_num_rows($followerResults) > 0) {
                while ($followerRows = mysql_fetch_assoc($followerResults)) {
                    $number = $followerRows['Phone'];
                    $number = preg_replace('/\D+/', '', $number);
                    $number = "1" . $number;
                    $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';

                    try {
                        // Create a Clockwork object using your API key
                        $clockwork = new Clockwork($API_KEY);
                        $domain = null;
                        if (strstr($url, "dev")) {
                            $domain = "http://dev.playdoe.com/show_post.php?postID=$postID&email=1";
                        } else {
                            $domain = "http://playdoe.com/show_post.php?postID=$postID&email=1";
                        }
                        $domain = shortenUrl($domain);

                        // Setup and send a message
                        $name = get_users_name_by_id($ID);
                        $name = trim($name);
                        $text = "$name just shared a new post. $domain";
                        $text = trim($text);
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
                    }
                }

                // send out an email after text
                if (checkEmailActive($followerID)) {
                    build_and_send_email($ID, $followerID, 11, null, $postID);
                }
                // update post notification date
                $sql = "Update Profile Set Post_Notification_Date = CURDATE() WHERE Member_ID = $followerID ";
                mysql_query($sql) or die(mysql_error());
            }

        }
    }
}