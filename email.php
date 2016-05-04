<?php
session_start();

function build_and_send_email($senderId, $toId, $notification, $postID, $pass, $groupID)
{
    $toEmail = get_email_by_id($toId);
    $subject = '';
    if ($notification == 1) {
// comment on post
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "show_post?postID=$postID&email=1";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.playdoe.com/show_post?postID=$postID&email=1";
        }
        if (function_exists(get_users_name_by_id)) {
            $name = get_users_name_by_id($senderId);
        }
        else {
            echo "<script>alert('could not retrieve name for email');</script>";
        }
        if ($senderId == $toId) {
            $name = 'You';
        }
        $text = "$name commented on a <a href='$link'>post</a> you're tagged in.";
        $subject = "
                <div>
                $text
                </div>
                ";
    }
    if ($notification == 2) {
        // post approval
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "show_post?postID=$postID&email=1";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.playdoe.com/show_post?postID=$postID&email=1";
        }
        $name = get_users_name_by_id($senderId);
        if ($senderId == $toId) {
            $name = 'You';
        }
        $text = "$name liked a <a href='$link'>post</a> you're tagged in.";
        $subject = "
                <div>
                $text
                </div>
               ";
    }
    if ($notification == 3) {
// sign up email
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "index.php";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com";
        }
        else {
            $link = "http://www.playdoe.com";
        }
        $name = get_users_name_by_id($toId);
        $nameArray = explode(' ', $name);
        $name = $nameArray[0];
        $subject = "Congratulations $name, you now have a new profile on Playdoe.
                    Start sharing great content, accumulating points and redeeming those points for gift cards and cash!<br/>";
        $subject .= "Your temporary password is <b>$pass</b><br/>";
        $subject .= '<a href = "http://www.playdoe.com">Login</a> to your account now!';
    }
    if ($notification == 4) {
// password recovery
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "index.php";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com";
        }
        else {
            $link = "http://www.playdoe.com";
        }
        $pass = get_password($toId);
        $subject = "Our records indicate you have requested your password. Your passowrd is <b>$pass</b> <br/>";
        $subject .= "Log in <a href = '$link'>here</a>";
    }
    if ($notification == 5) {
// change password
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        $email = get_email_by_id($toId);
        $id = get_id_by_email($email);
        if (strstr($url, "local")) {
            $link = "create_pass.php?email=$email";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/create_pass?email=$email&id=$id";
        }
        else {
            $link = "http://www.playdoe.com/create_pass?email=$email&id=$id";
        }
        $email = get_email_by_id($toId);
        $subject = "Click this <a href = '$link'>link</a> to create a new password. <br/>";
        $subject .= 'If you did not request to change your password, contact support at <a href = "mailto:info@playdoe.com">info@playdoe.com</a>';
    }
    if ($notification == 6) {
// comment with photo or video
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "show_post?postID=$postID&email=1";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.playdoe.com/show_post?postID=$postID&email=1";
        }
        $name = get_users_name_by_id($senderId);
        if ($senderId == $toId) {
            $name = 'You';
        }
        $text = "$name commented with a photo on a <a href='$link'>post</a> you're tagged in.";
        $subject = "
                <div>
                $text
                </div>
                ";
    }
    if ($notification == 7) {
// photo approve
        // media id is the person's id who photo just got liked and is receiving this email
        $ID = $toId;
        // get media contents
        $sql = "SELECT * FROM Media WHERE MediaName = '$postID'";
        $result = mysql_query($sql);
        $rows = mysql_fetch_assoc($result);
        $memberID = $rows['MemberID'];
        $mediaName = $postID;
        $mediaID = $rows['ID'];
        $mediaDate = $rows['MediaDate'];
        $mediaType = $rows['MediaType'];
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "media?id=$memberID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/media.php?id=$memberID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        else {
            $link = "http://www.playdoe.com/media.php?id=$memberID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        $name = get_users_name_by_id($senderId);
        if ($senderId == $toId) {
            $name = 'You';
        }
        $text = "$name liked a <a href='$link'>photo</a> you're tagged in.";
        $subject = "
                <div>
                $text
                </div>
                ";
    }
    if ($notification == 8) {
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        $username = get_username($senderId);

        $groupText = '';
        if (strlen($groupID) > 0) {
            //$groupText = "group";
            $username = $groupID;
        }


        /*if (strlen($groupID) > 1) {
            $username = $groupID;
            $groupText = "group ";
        }*/

        if (strstr($url, "local")) {
            $link = "view_messages/$username";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/view_messages/$username";
        }
        else {
            $link = "http://www.playdoe.com/view_messages/$username";
        }

        $senderName = get_users_name_by_id($senderId);
        $subject = "$senderName has sent you a new <a href='$link'>message</a>";
    }
    if ($notification == 9) {
        // no posts
        $subject = "We noticed you have not created any posts.
                Posts are the best way to get seen by people and become really popular.
                Login and create a post today.";
    }
    if ($notification == 10) {
        // no profile photo notification
        $subject = "We noticed you have not uploaded a profile photo.
                Your profile photo is one of the most important parts to your profile.
                Pictures are EVERYTHING so login and upload your profile photo today.";
    }
    if ($notification == 11) {
        // related post
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {

        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/show_post?postID=$pass&email=1";
        }
        else {
            $link = "http://www.playdoe.com/show_post?postID=$pass&email=1";
        }
        // a post status update related to your service has been posted
        $name = get_users_name_by_id($senderId);
        $subject = "$name just shared a new post. $hashtag. <a href='".$link."'>Click here</a> to view it.
        <br/><br/>You are receiving this because you follow $name";
    }
    if ($notification == 12) {
        // follow
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        $username = get_username($toId);
        $name = get_users_name($senderId);
        if (strstr($url, "local")) {
            $link = "home.php";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/member_follows/$username";
        }
        else {
            $link = "http://www.playdoe.com/member_follows/$username";
        }
        // a post status update related to your service has been posted
        $subject = "$name is now following you. <a href='".$link."'>Click here</a> to see your followers.";
    }


    if ($notification == 13) {
// photo comment
        // media id is the person's id who photo just got liked and is receiving this email
        // get media contents
        $sql = "SELECT * FROM Media WHERE ID = '$postID'";
        $result = mysql_query($sql);
        $rows = mysql_fetch_assoc($result);
        $ID = $rows['Member_ID'];
        $mediaName = $rows['MediaName'];
        $mediaID = $rows['ID'];
        $mediaDate = $rows['MediaDate'];
        $mediaType = $rows['MediaType'];
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;
        if (strstr($url, "local")) {
            $link = "media?id=$ID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.playdoe.com/media.php?id=$ID&mid=$mediaID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        else {
            $link = "http://www.playdoe.com/media.php?id=$mediaID&mediaName=$mediaName&mid=$mediaID&mediaType=$mediaType&mediaDate=$mediaDate&h=0";
        }
        $name = get_users_name_by_id($senderId);
        if ($senderId == $toId) {
            $name = 'You';
        }
        $text = "$name commented a <a href='$link'>photo</a> you're tagged in.";
        $subject = "
                <div>
                $text
                </div>
                ";
    }

    // if we have a notification, then send the email.
    if (strlen($notification) > 0) {
        if (strstr($url, "local")) {
            $link = "index.php";
        }
        else if (strstr($url, "dev")) {
            $profilePhoto = "http://dev.playdoe.com/images/Playdoe-Logo.png";
        }
        else {
            $profilePhoto = "http://www.playdoe.com/images/Playdoe-Logo.png";
        }
        if ($senderId == 0) {
            $profilePhoto = $profilePhoto;
        }
        else {
            $profilePhoto = get_users_photo_by_id($senderId);
        }
        $message = "<html><body>";
        $message .= "<table style = 'border:1px solid lightgray;background: #f6f7f8;'>";
        $message .= "<tr style = 'color:white;'>";
        $message .= "<td><img src = 'http://playdoe.com/images/Playdoe-Logo.png' height='50' width='75' />&nbsp;&nbsp;<img src = 'http://playdoe.com/images/Playdoe-red.png' height='50' width='75' /></td>";
        $message .= "<tr><td><hr /><br/><img src = '$profilePhoto' height='100' width='100' /></td></tr>";
        $message .= "<tr><td>$subject<br/></td></tr>";
        $message .= "<tr><td><hr/>If you received this email in error contact us at <mailto:info@playdoe.com>info@playdoe.com</a>";
        $message .= "<br/>Playdoe LLC, 911 Washington Ave, Suite 501, St.Louis,MO 63101 USA </td></tr>";
        $message .= "</table></body></html>";
        $header = "From: Playdoe <admin@playdoe.com> \r\n";
        $header .= "Content-type: text/html";
        ini_set('sendmail_from', 'info@playdoe.com');
        // set subject
        if (mail($toEmail, 'Notification', $message, $header)) {
            // mail sent
            return true;
        } else {
            echo 'mail sending failed';
            return false;
        }
    }
}
?>