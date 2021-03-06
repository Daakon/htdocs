<?php

require 'imports.php';

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$email = trim($_POST['email']);
$referredBy = trim($_POST['referredBy']);
$emailSplit = explode("@", $email);
$username = trim($emailSplit[0]);

$referredBy = strtolower($referredBy);

if (check_referral_ID($referredBy) == false) {
    echo "<script>alert('Referral ID not found. Please check back with the person who referred you.'); location ='/learn_more'</script>";
    exit;
}

// capitalize first letter only
$fName = ucfirst(strtolower($username));
$lName = ' ';
$username = strtolower($username);

// check if email exists
$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Checking if email exists"));

if (mysql_num_rows($result) > 0) {

    echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
    exit;
}

$unique = 1;
a:
// check if username exists
$sql = "SELECT * FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Checking if username exists"));

if (mysql_num_rows($result) > 0) {
    $username = $username . $unique;
    $unique++;
    goto a;
}



$sql = "INSERT INTO Members (FirstName, LastName, Email,    Username,   ReferralID,    Password,        SignupDate,   IsSuspended, EmailActive, LastLogin     )
Values 			            ('$fName', '$lName', '$email', '$username', '$username', '".md5($pass)."',  CURRENT_DATE(),   0,           1,       CURRENT_DATE() )";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting new member account"));

$ID = mysql_insert_id();

$sql = "SELECT * FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Get profile from brand new member ID"));
$rows = mysql_fetch_assoc($result);

/*Set two session variable which will be needed throughout the program */
session_start();
$_SESSION['ID'] = $rows['ID'];
setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

//sign up date

$date = date('Y-m-d H:i:s');
$sql = "UPDATE Members SET SignupDate = '$date' WHERE ID = '$ID' ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting sign up date"));

// insert default profile pic into profile table
$sql = "INSERT INTO Profile (Member_ID,  Poster,               ProfileVideo ) Values
                            ($ID,       'default_photo.png', 'default_video.png' )    ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting default photo"));


// insert Referral ID if exists
if (strlen($referredBy) > 0) {
    $sql = "INSERT INTO Referrals (Signup_ID, Referral_ID, ReferralDate) Values ($ID, '$referredBy', NOW()) ";
    $result = mysql_query($sql) or die(mysql_error());
}

// assign default follows
// Team Playdoe
$sql = "INSERT INTO Follows (Followed_ID, Follower_ID, New, FollowDate) Values (22, $ID, 1, CURDATE())";
mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting new follower"));

// Playdoe Marketing
$sql = "INSERT INTO Follows (Followed_ID, Follower_ID, New, FollowDate) Values (720, $ID, 1, CURDATE())";
mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting new follower"));

// Send out sign up email
$toId = $rows['ID'];
build_and_send_email(0,$ID, 3, null,$pass);

$firstName = get_user_firstName($ID);
// DM new member with instructions
$message = "
<img src='/images/giftcards-horizontal.jpg' height='100%' width='100%' />
<br/>
<p>Hey $firstName, Playdoe is a gamification app that offers fun way to earn money, discounts and win free stuff.
        <h4>Reward Campaigns</h4>
        Reward Campaigns are where members on Playdoe offer you money, discounts and free stuff to help promote their products and services.
        They will all have their own set of rules and rewards for their various campaigns. We will repost campaigns to make sure
        you don't miss any of them. Stay posted to your feed.

        <br/>
        If you have any questions, just reply to this message anytime or checkout the FAQ below.
        </p>";

$message = mysql_escape_string($message);
$subject = "Welcome to Playdoe";
$receiverID = $ID;
$rInitialMessage = 1;
$rFirstMessage = 1;
$supportID = 22;
// create thread for receiver
$sql = "INSERT INTO Messages  (ThreadOwner_ID, Sender_ID,   Receiver_ID,  Subject,    Message,         InitialMessage,      New,        FirstMessage,       MessageDate   ) VALUES
                              ($receiverID,    $supportID,  $receiverID, '$subject', '$message',     '$rInitialMessage',    '1',        $rFirstMessage,     CURRENT_TIMESTAMP ) ";
mysql_query($sql) or die(mysql_error());
?>

    <!--track sign ups through Google-->
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt=""
                 src="//www.googleadservices.com/pagead/conversion/951358222/?label=jDs3CNyP4GAQjqbSxQM&guid=ON&script=0"/>
        </div>
    </noscript>

<?php
echo '<script>alert("Your profile was successfully set up");location = "home.php"</script>';

?>