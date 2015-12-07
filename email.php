<?php



function build_and_send_email($senderId, $toId, $notification, $postID, $pass)

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
            $link = "http://dev.rapportbook.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.rapportbook.com/show_post?postID=$postID&email=1";
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
            $link = "http://dev.rapportbook.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.rapportbook.com/show_post?postID=$postID&email=1";
        }

        $name = get_users_name_by_id($senderId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name approved a <a href='$link'>post</a> you're tagged in.";

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
            $link = "http://dev.rapportbook.com";
        }
        else {
            $link = "http://www.rapportbook.com";
        }

        $name = get_users_name_by_id($toId);
        $nameArray = explode(' ', $name);
        $name = $nameArray[0];
        $subject = "Congratulations $name, you now have a new profile on Rapportbook.<br/>";
        $subject .= "Your temporary password is <b>$pass</b><br/>";
        $subject .= '<a href = "http://www.rapportbook.com">Login</a> to your account now!';
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
            $link = "http://dev.rapportbook.com";
        }
        else {
            $link = "http://www.rapportbook.com";
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

        if (strstr($url, "local")) {
            $link = "create_pass.php?email=$email";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.rapportbook.com/create_pass.php?email=$email";
        }
        else {
            $link = "http://www.rapportbook.com/create_pass.php?email=$email";
        }
        $email = get_email_by_id($toId);


        $subject = "Click this <a href = '$link'>link</a> to create a new password. <br/>";
        $subject .= 'If you did not request to change your password, contact support at <a href = "mailto:info@rapportbook.com">info@rapportbook.com</a>';
    }

    if ($notification == 6) {
// photo comment
        //  message notification
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;

        if (strstr($url, "local")) {
            $link = "show_post?postID=$postID&email=1";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.rapportbook.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.rapportbook.com/show_post?postID=$postID&email=1";
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
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $link;

        if (strstr($url, "local")) {
            $link = "show_post?postID=$postID&email=1";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.rapportbook.com/show_post?postID=$postID&email=1";
        }
        else {
            $link = "http://www.rapportbook.com/show_post?postID=$postID&email=1";
        }

        $name = get_users_name_by_id($senderId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name approved a <a href='$link'>photo</a> you're tagged in.";

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
        if (strstr($url, "local")) {
            $link = "view_messages/$username";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.rapportbook.com/view_messages/$username";
        }
        else {
            $link = "http://www.rapportbook.com/view_messages/$username";
        }

        $senderName = get_users_name_by_id($senderId);


        $type = "id";

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
            $link = "home.php";
        }
        else if (strstr($url, "dev")) {
            $link = "http://dev.rapportbook.com/home?scrollx=630&scrolly=630";
        }
        else {
            $link = "http://www.rapportbook.com/home?scrollx=630&scrolly=630";
        }

        // a post status update related to your service has been posted
        $subject = "Someone just shared a post related to your interest. <a href='".$link."'>Click here</a> to see the post";
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
            $link = "http://dev.rapportbook.com/member_follows/$username";
        }
        else {
            $link = "http://www.rapportbook.com/member_folows/$username";
        }

        // a post status update related to your service has been posted
        $subject = "$name just followed you. <a href='".$link."'>Click here</a> to see your followers.";
    }

    // if we have a notification, then send the email.

    if (strlen($notification) > 0) {

        if (strstr($url, "local")) {
            $link = "index.php";
        }
        else if (strstr($url, "dev")) {
            $profilePhoto = "http://dev.rapportbook.com/images/Rapportbook-Logo.png";
        }
        else {
            $profilePhoto = "http://www.rapportbook.com/images/Rapportbook-Logo.png";
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
        $message .= "<td><img src = 'http://rapportbook.com/images/Rapportbook-Logo.png' height='25' width='25' /><br/></td>";
        $message .= "<tr><td><hr /><br/><img src = '$profilePhoto' height='100' width='100' /></td></tr>";
        $message .= "<tr><td>$subject<br/></td></tr>";
        $message .= "<tr><td><hr/>If you received this email in error contact us at <mailto:info@rapportbook.com>info@rapportbook.com</a>";
        $message .= "<br/>Rapportbook LLC, 911 Washington Ave, Suite 501, St.Louis,MO 63101 USA </td></tr>";
        $message .= "</table></body></html>";

        $header = "From: Rapportbook <admin@rapportbook.com> \r\n";
        $header .= "Content-type: text/html";
        ini_set('sendmail_from', 'info@rapportbook.com');

        if (mail($toEmail, 'Rapportbook: Notification Alert', $message, $header)) {
            // mail sent
            return true;


        } else {
            echo 'mail sending failed';
            return false;
        }
    }
}

?>
