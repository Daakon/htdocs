<?php
require_once 'model_functions.php';

function build_and_send_email($senderId, $toId, $notification, $postID)
{
    $toEmail = get_email_by_id($toId);
    $subject = '';


    if ($notification == 1) {

        $name = get_users_name_by_id($senderId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name commented on a <a href='http://www.rapportbook.com/show_post.php?postID=postID'>post</a> you're tagged in.";

        $subject = "
                <div style='height:20px; padding:10px;'>
                $text

                </div>

                ";
    }
    if ($notification == 2) {

        $name = get_users_name_by_id($senderId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name approved a <a href='http://www.rapportbook.com/show_bulletin.php?bid=$postID'>post</a> you're tagged in.";

        $subject = "
                <div style='height:20px; padding:10px;'>
                $text

                </div>

               ";
    }

    if ($notification == 3) {

        $name = get_users_name_by_id($toId);
        $pass = get_password($toId);
        $nameArray = explode(' ', $name);
        $name = $nameArray[0];
        $subject = "Congratulations $name, you now have a new profile on Rapportbook.<br/>";
        $subject .= "Your password is <b>$pass</b> <br/>";
        $subject .= "Please keep this email for your records <br/><br/>";

        $subject .= "You can now:<br/>";
        $subject .= "<b>Post photo and videos of your talents</b><br/>";
        $subject .= "<b>Text your profile to new people in your life</b><br/>";
        $subject .= "<b>Comment on other people's post with text and or photo and video</b><br/>";
        $subject .= "<b>Direct message people</b><br/>";
        $subject .= "<b>Post Photos and Videos.</b><br/><br/>";
        $subject .= "Start posting content today and become popular.<br/>";
        $subject .= '<a href = "http://www.rapportbook.com">Login</a> to your account today and maximize the tools we have built for you.';
    }

    if ($notification == 4) {

        $pass = get_password($toId);
        $subject = "Our records indicate you have requested your password. Your passowrd is <b>$pass</b> <br/>";
        $subject .= "Log in <a href = 'http://www.rapportbook.com'>here</a>";

    }

    if ($notification == 5) {

        $email = get_email_by_id($toId);


        $subject = "Click this <a href = 'http://www.rapportbook.co/createpass.php?email=$email'>link</a> to create a new password. <br/>";
        $subject .= 'If you did not request to change your password, contact support at <a href = "mailto:info@businessconnect.co">info@businessconnect.co</a>';
    }
    if ($notification == 6) {

        $name = get_users_name_by_id($toId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name commented on a <a href='http://www.rapportbook.com/show_bulletin.php?postID=$postID'>photo</a> you're tagged in.";

        $subject = "
                <div  style='height:20px; padding:10px;'>
                $text

                </div>

                ";
    }
    if ($notification == 7) {

        $name = get_users_name_by_id($senderId);

        if ($senderId == $toId) {
            $name = 'You';
        }

        $text = "$name approved a <a href='http://www.rapportbook.com/show_bulletin.php?postID=$postID'>photo</a> you're tagged in.";

        $subject = "
                <div style='height:20px; padding:10px;'>
                $text

                </div>

                ";
    }

    if ($notification == 8) {
        // 20 = message notification
        $senderName = get_users_name_by_id($senderId);


        $type = "id";

        $subject = "$senderName has sent you a new <a href='http://www.rapportbook.com/messages.php?$type=$toId'>message</a>";
    }


    if ($notification == 9) {
        // no bulletin notification

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


    $message = "<html><body>";
    $message .= "<table style = 'background:red;height:400px;width:600px;border-radius:10px;border:2px solid black;'><tr style = 'color:white;border-radius:10px;'><td>";
    $message .= "<tr><td><img src = 'get_users_photo_by_id($senderId)' height = '200' width = '200' style = 'border:2px solid black' /></td></tr>";
    $message .= "<tr><td style = 'background:silver;padding:20px;border:2px solid black;'>$subject<br/><br/></td></tr>";
    $message .= "<tr><td style = 'background-color:red;color:white'>If you received this email in error contact us at <mailto:info@connectcommunity.com>info@rapportbook.com</a>";
    $message .= "<br/>Rapportbook LLC, 1500 Washington Ave, St.Louis,MO 63103 USA </td></tr>";
    $message .= "</table></body></html>";

    $header = "From: Rapporbook <admin@rapportbook.com> \r\n";
    $header .= "Content-type: text/html";
    ini_set('sendmail_from', 'admin@rapportbook.com');

    if (mail($toEmail, 'Rapportbook: Notification Alert', $message, $header)) {
        // mail sent
        return true;

    } else {
        echo "<script>alert('$message');</script>";
        echo 'mail sending failed';
        return false;
    }
}

?>
